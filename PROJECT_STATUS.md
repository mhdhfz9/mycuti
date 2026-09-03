# MyCuti — Project Status

> Living document. Read this at the start of every session to pick up where the last one left off. Update it whenever a feature area moves from "not started" to "in progress" or "done" — don't let it go stale.

Last updated: 2026-09-03

## What MyCuti is

A leave management system (Laravel + Livewire + Flux UI). Employees submit leave requests, managers approve them, everyone can see balances and who's out. That's the target product — see "Not yet built" below for how much of it exists today.

## Current state: auth scaffold + landing page only

**No leave-management domain logic exists yet.** The app is currently a fully working authentication system and a marketing landing page, with a dashboard route that renders empty placeholders.

### Implemented

**Authentication & account** (Laravel Fortify)
- Registration — name, email, IC number (12-digit numeric, unique), password
- Login — **IC number + password** (not email — this is a MyCuti-specific customization, see `config/fortify.php` `username`)
- Logout, email verification, password reset/confirmation
- Two-factor authentication (TOTP, QR setup, recovery codes)
- Passkeys (WebAuthn)

**Account settings** (`resources/views/pages/settings/`)
- Profile edit (name/email), password change, security overview, appearance (light/dark/system), account deletion

**Public site**
- Landing page (`resources/views/welcome.blade.php`) — hero, features, how-it-works, FAQ, CTA. Copy describes leave-management features but none are wired to real functionality.
- Light/dark mode with animated circular-reveal toggle (View Transitions API), shared via `resources/views/partials/appearance-toggle.blade.php` and `partials/ambient-background.blade.php`, applied consistently across landing + all auth pages via `layouts/auth/simple.blade.php`.
- Custom MyCuti logo/favicon wired in (`public/images/logo-icon.png`, `app-logo-icon` component).

**Dashboard**
- Route `/dashboard` exists (auth + verified middleware) but `dashboard.blade.php` is still the unmodified Laravel starter-kit placeholder — no real widgets or data.

### Data model (only table beyond framework defaults)

`users`: `id, name, email, ic_number (unique, 12 chars), password, two_factor_*, remember_token, timestamps`
Plus a `passkeys` table for WebAuthn credentials.

**No other domain tables exist** — no leave requests, leave types, balances, departments, or roles.

### Not yet built (the actual product)

| Area | Status |
|---|---|
| Leave requests (submit/view/cancel) | Not started |
| Leave types (annual, sick, unpaid, etc.) | Not started |
| Leave balances / accrual | Not started |
| Approval workflow (manager review) | Not started |
| Roles & permissions (employee/manager/HR admin) | Not started |
| Organizational structure (departments, reporting lines) | Not started |
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
6. Dashboard: replace placeholder with real widgets (balance, pending requests, team calendar) once the above exist
7. Notifications: Laravel notifications for request submitted/approved/declined

## Session log

Keep this short — one or two lines per session, newest first. Not a changelog of every file touched; just enough for the next session to know what happened without re-reading the whole git log.

- 2026-09-03 — Auth/UI polish session: IC-number login/registration, light/dark theme with animated toggle, landing page redesign (parallax background, feature card icons), custom logo/favicon. Ran a full codebase analysis (this document) — confirmed no leave-management domain logic exists yet.
