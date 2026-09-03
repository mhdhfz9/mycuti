# MyCuti — Project Status

> Living document. Read this at the start of every session to pick up where the last one left off — or better, run `/sambung`, which reads this file fresh and briefs you. Update it whenever a feature area moves from "not started" to "in progress" or "done": refresh the relevant table/section, and always append a line to the Session Log. A `Stop` hook enforces this — if project files changed and this file didn't, the session is blocked from ending until it's updated.

Last updated: 2026-09-03 (LeaveRequest model added)

## What MyCuti is

A leave management system (Laravel + Livewire + Flux UI). Employees submit leave requests, managers approve them, everyone can see balances and who's out. That's the target product — see "Not yet built" below for how much of it exists today.

## Current state: auth + account UI done, leave-management domain in progress (LeaveType + LeaveRequest models)

**Leave-management domain is progressing**: `LeaveType` and `LeaveRequest` model/migration/factory exist (see below). Submission UI, balances, and the approval workflow itself still not started. What else is done: a fully working, polished authentication system, a marketing landing page, and a real account dashboard (profile + security overview) — all with a shared light/dark theme system.

### Implemented

**Authentication & account** (Laravel Fortify)
- Registration — name, email, IC number (12-digit numeric, unique), password
- Login — **IC number + password** (not email — MyCuti-specific customization, see `config/fortify.php` `username`)
- Logout, email verification, password reset/confirmation
- Two-factor authentication (TOTP, QR setup, recovery codes)
- Passkeys (WebAuthn)
- **IC number is encrypted at rest** (`encrypted` cast on `users.ic_number`) with a separate `ic_number_hash` (HMAC-SHA256) blind-index column for login/uniqueness lookups, since encrypted values can't be queried directly. Login and password-confirmation are wired through `Fortify::authenticateUsing()` / `confirmPasswordsUsing()` in `FortifyServiceProvider` to use the hash lookup. See `.ai/rules/models.md` and `.ai/rules/seeders.md` for the traps around this (don't query `ic_number` directly; don't use `WithoutModelEvents` in seeders).

**Account settings** (`resources/views/pages/settings/`)
- Profile edit (name/email), password change, security overview, appearance (light/dark/system), account deletion

**Dashboard** (`resources/views/dashboard.blade.php`, no longer a placeholder)
- Real profile view: name, email, member-since date, the user's **encrypted IC ciphertext** shown in a read-only copyable field (never the plaintext) with a description explaining it's AES-256 encrypted
- Security status card: email-verified / 2FA / passkey-count badges, links to edit profile / manage security
- Sidebar shell (`layouts/app/sidebar.blade.php`) restyled to match the landing page's glass aesthetic (light/dark, ambient background, appearance toggle in both desktop sidebar and mobile header)

**Public site**
- Landing page (`resources/views/welcome.blade.php`) — hero, features, how-it-works, FAQ, CTA. Copy describes leave-management features but none are wired to real functionality yet (informal requirements doc — see below).
- Light/dark mode toggle, shared via `resources/views/partials/appearance-toggle.blade.php` + `partials/ambient-background.blade.php`, used consistently across landing + all auth pages (`layouts/auth/simple.blade.php`) + the dashboard shell.
  - **Toggle animation**: real content circular-reveal via the View Transitions API (`document.startViewTransition()`), light→dark expands, dark→light contracts (shrinks the old snapshot away). Positioning the circle's `at x y` uses **percentages of the viewport, not pixels** — pixel values were landing visibly off-target in this app's layout (root cause not fully identified; percentages fixed it, confirmed by testing). Don't revert that to px without re-verifying against the actual button position.
- Custom MyCuti logo/favicon wired in (`public/images/logo-icon.png`, `app-logo-icon` component).

**Leave domain** (`app/Models/LeaveType.php`)
- `LeaveType`: name, slug (auto-derived from name via `booted()` `saving` hook if not set), description, default_entitlement_days, is_paid, requires_approval, colour
- `LeaveTypeSeeder` (called from `DatabaseSeeder`) seeds 9 standard Malaysian leave types (Annual, Sick, Hospitalisation, Emergency [auto-approved], Maternity, Paternity, Marriage, Compassionate, Unpaid [unpaid]) — idempotent via `updateOrCreate` on slug
- Factory has `unpaid()` and `autoApproved()` states
- Tested in `tests/Feature/LeaveTypeTest.php` (slug derivation, cast coercion, seeder content + idempotency)

**Roles & manager hierarchy** (`app/Enums/UserRole.php`, `users.role`/`users.manager_id`)
- Decided with the user: simple `role` string column (backed by `App\Enums\UserRole`: `Employee`/`Manager`/`Admin`, default `Employee`) + self-referencing `manager_id` FK on `users` — not a full permissions package (spatie/etc.), not a single-admin-approves-all model. Chosen specifically so `LeaveRequest` (next up) can resolve "who approves this employee's request" via `$user->manager`.
- `User::manager()` (BelongsTo) / `User::directReports()` (HasMany)
- **`role`/`manager_id` are deliberately NOT mass-assignable** (not in `User`'s `#[Fillable]`) — role changes should go through explicit admin code, not request input, once that exists. Factory bypasses this via Laravel's `Model::unguarded()` (factory `role()`/`admin()`/`managedBy()` states work fine).
- Tested in `tests/Feature/UserRoleTest.php` (default role, factory states, manager relationship, direct reports)

**Leave requests** (`app/Models/LeaveRequest.php`, `app/Enums/LeaveRequestStatus.php`)
- `LeaveRequest`: belongs to `User` (requester) and `LeaveType`; `start_date`/`end_date` cast to date, `status` cast to `LeaveRequestStatus` enum (Pending/Approved/Rejected/Cancelled, default Pending), nullable `reason`, nullable `approved_by` (FK to `users`) + `approved_at` for audit
- `User::leaveRequests()` HasMany added; approver is *resolved* via `$user->manager` (not stored as a separate "who can approve" field) — `approved_by` only records who actually acted, for audit
- Factory has `pending()`/`approved()`/`rejected()` states
- No validation/business rules yet (e.g. end_date ≥ start_date, overlap checks) — intentionally deferred to the Form Request/Livewire component that will own the submit UI, not the model
- Tested in `tests/Feature/LeaveRequestTest.php` (relationships, status default/cast, date casts, approved-state factory, `User::leaveRequests()`)

### Data model (only tables beyond framework defaults)

`users`: `id, name, email, ic_number (encrypted, text), ic_number_hash (unique, 64-char HMAC), password, role (string, default employee), manager_id (nullable FK to users), two_factor_*, remember_token, timestamps`
`leave_types`: `id, name, slug (unique), description, default_entitlement_days, is_paid, requires_approval, colour, timestamps`
`leave_requests`: `id, user_id (FK users), leave_type_id (FK leave_types), start_date, end_date, reason (nullable text), status (string, default pending), approved_by (nullable FK users), approved_at (nullable timestamp), timestamps`
Plus a `passkeys` table for WebAuthn credentials.

**Still missing**: leave balances, submission/approval UI, departments/org structure beyond the manager_id chain.

### Not yet built (the actual product)

| Area | Status |
|---|---|
| Leave requests (submit/view/cancel) | Partial — `LeaveRequest` model/migration/factory done; no UI, no submit/cancel flow yet |
| Leave types (annual, sick, unpaid, etc.) | **Done** — `LeaveType` model/migration/seeder |
| Leave balances / accrual | Not started |
| Approval workflow (manager review) | Not started (data model ready: `manager_id` chain + `LeaveRequest.approved_by`/`approved_at` exist) |
| Roles & permissions (employee/manager/HR admin) | **Done** — `UserRole` enum + `manager_id`, no permissions UI yet |
| Organizational structure (departments, reporting lines) | Partial — `manager_id` chain only, no departments/teams concept |
| Calendar / team-availability view | Not started |
| Notifications (submitted/approved/reminders) | Not started |
| Reporting/exports | Not started |
| API (`routes/api.php`) | Doesn't exist |

The landing page copy is effectively an informal requirements doc for these: one-click requests with real-time status, a manager approval queue, live balances, team visibility, instant notifications, policy-aware leave types.

## Suggested build order (not yet started, just a starting point)

1. `LeaveType` model/migration (annual, sick, unpaid, etc. — name, default entitlement)
2. `LeaveRequest` model/migration (user_id, leave_type_id, start_date, end_date, status, reason)
3. Leave balance calculation (derived from entitlement − approved requests, or a dedicated `LeaveBalance` table if accrual rules get complex)
4. Roles: simplest viable start is a `role` enum column on `users` (employee/manager/admin) rather than a full permissions package, unless multi-role-per-user is needed
5. Approval workflow: status enum + a policy gating who can approve, tied to a manager relationship on `users`
6. Dashboard: replace remaining placeholders with real leave-domain widgets (balance, pending requests, team calendar) once the above exist — the profile/security widgets already there can stay alongside them
7. Notifications: Laravel notifications for request submitted/approved/declined

## In progress / next steps discussed with the user

Steps 1 (`LeaveType`), 2 (`LeaveRequest`), and 4 (roles) of the suggested build order are done. Next up: leave balance calculation (step 3 — derived from `LeaveType.default_entitlement_days` minus approved `LeaveRequest`s, or a dedicated `LeaveBalance` table if accrual rules get complex) and the approval workflow itself (step 5 — a policy gating who can approve, using `$user->manager` plus the `approved_by`/`approved_at` columns already on `LeaveRequest`). A submission UI (Livewire) for `LeaveRequest` is also still needed — the model has no validation/overlap rules yet, deliberately deferred to that UI layer.

## Session log

Keep this short — one or two lines per session, newest first. Not a changelog of every file touched; just enough for the next session to know what happened without re-reading the whole git log.

- 2026-09-03 — `commit` skill added (`.claude/skills/commit/SKILL.md`, PROP-20260903-01): splits pending work into one git commit per feature/proposal instead of one catch-all commit, each message tagged with the originating `/discuss` proposal ID or a minted `SPEC-<slug>` ID sourced from the calling session's own context. `discuss`'s own bookkeeping updated to mint that `PROP-YYYYMMDD-NN` ID and put it in the proposal heading + `PENDING.md` entry (`.claude/skills/discuss/SKILL.md`, `PENDING.md`). Verified via a dry-run grouping of this repo's actual uncommitted changes (7 groups identified, including two files needing `git add -p` because they mix hunks from two features) — no commits actually executed yet, awaiting a separate go-ahead to run it for real.
- 2026-09-03 — `pending-task` skill added (`.claude/skills/pending-task/SKILL.md`, `/pending-task`): reads a new `PENDING.md` (open `/discuss` proposals, tasks, decisions) and presents it — works even after `/clear`/new session, and any subagent can read `PENDING.md` directly. `discuss` skill now appends an entry when a proposal is presented and removes it on approval/decline, keeping the list in sync automatically. Verified: ran `/pending-task` (correctly listed the one real open item, no gap vs `PROJECT_STATUS.md`), then walked through a mock append→remove cycle to confirm the sync mechanism works.
- 2026-09-03 — `LeaveRequest` model added (`app/Models/LeaveRequest.php`, `app/Enums/LeaveRequestStatus.php`, migration, factory with pending/approved/rejected states, `User::leaveRequests()`): belongs to `User` + `LeaveType`, status enum defaults to Pending, `approved_by`/`approved_at` for audit (approver eligibility still resolved via `$user->manager`, not stored redundantly). 5 passing feature tests; proposed and approved via `/discuss` before building.
- 2026-09-03 — `discuss` skill added (`.claude/skills/discuss/SKILL.md`): a governance mode for high-stakes changes (broad architecture/data-model, security, or persistent-config work) — propose a written plan (what, files/locations, verification), wait for explicit approval, only then execute. Distinct from `AskUserQuestion`, which stays for small in-plan decisions. Hooked into `CLAUDE.md`/`AGENTS.md` so it applies even after `/clear`.

- 2026-09-03 — Roles + manager hierarchy: `UserRole` enum (Employee/Manager/Admin), `role`/`manager_id` columns on `users`, `manager()`/`directReports()` relationships, factory states, 4 passing tests. User chose this (over a permissions package or single-admin-approves-all) specifically to unblock `LeaveRequest`'s approver resolution next.
- 2026-09-03 — `LeaveType` model/migration/factory/seeder added (first piece of the leave-management domain): name/slug/description/default_entitlement_days/is_paid/requires_approval/colour, auto-slug via `booted()`, seeder for 9 standard Malaysian leave types, 4 passing feature tests.
- 2026-09-03 — Session-continuity system: `/sambung` slash command, `SessionStart` hook (announces recent progress on launch), `Stop` hook (blocks ending a turn if source files changed but this file wasn't updated). See `.claude/settings.json`, `.claude/hooks/`, `.claude/commands/sambung.md`.
- 2026-09-03 — IC number encrypted at rest (blind-index hash for login lookups); dashboard rebuilt from placeholder into a real profile + security page matching the landing page's design; theme-toggle circular reveal reworked several times to fix positioning (final fix: use `%` not `px` for the circle's center) and add an expand/contract distinction between light→dark and dark→light.
- 2026-09-03 — Auth/UI polish session: IC-number login/registration, light/dark theme with animated toggle, landing page redesign (parallax background, feature card icons), custom logo/favicon. Ran a full codebase analysis — confirmed no leave-management domain logic exists yet.
