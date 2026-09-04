---
id: SPEC-20260903-01
title: Roles and manager hierarchy
status: implemented
created: 2026-09-03
---

# Roles and manager hierarchy

## Intent

MyCuti needs a way to tell employees, managers, and admins apart, and to know
who approves whose leave requests. This was built as the simplest viable
foundation for that — a `role` column plus a self-referencing `manager_id` on
`users` — chosen over a full permissions package specifically so a later
leave-request approval workflow could resolve "who approves this employee's
request" by walking `$user->manager`.

## Scope

### In scope
- A `role` column on `users` distinguishing Employee / Manager / Admin.
- A `manager_id` self-reference on `users` for a single-level (or chained)
  reporting relationship.
- Relationship accessors to walk that chain in both directions.

### Out of scope
- Any UI for assigning/changing roles or managers.
- Multi-role-per-user (a user has exactly one role).
- Department/org-unit structure beyond the manager chain.
- Enforcement of who *can* approve — this only models the relationship; the
  approval workflow itself is a separate, not-yet-built feature.

## Requirements

- REQ-1: `users.role` is a string column, backed by `App\Enums\UserRole`
  (`Employee` / `Manager` / `Admin`), defaulting to `employee`.
- REQ-2: `users.manager_id` is a nullable foreign key to `users.id`, set to
  `null` if the referenced manager is deleted (`nullOnDelete`).
- REQ-3: `role` and `manager_id` are not mass-assignable on the `User` model —
  changing them must go through explicit code, not raw request input.
- REQ-4: `User::manager()` returns the user's manager (`BelongsTo`), and
  `User::directReports()` returns the users who report to them (`HasMany`).

## Acceptance criteria

- AC-1: Given a newly registered user, when the user is created, then
  `role` defaults to `UserRole::Employee` and `manager_id` is `null`.
- AC-2: Given a user `A` with `manager_id` pointing at user `B`, when
  `A->manager` is accessed, then it returns `B`.
- AC-3: Given a user `B` who is the manager of users `A` and `C`, when
  `B->directReports` is accessed, then it returns a collection containing
  both `A` and `C`.
- AC-4: Given a manager `B` is deleted, when a request for `A->manager_id` is
  made afterwards, then it is `null` (not a dangling foreign key / not a
  cascade-deleted `A`).
- AC-5: Given a mass-assignment attempt (e.g. `User::create([...'role' =>
  'admin'...])` via a fillable-driven path), when the model is saved, then
  `role` is not set from that input (only explicitly-assigned code paths can
  change it).

## Data / interface changes

- Migration `2026_09_03_064450_add_role_and_manager_to_users_table.php` adds
  `role` (string, default `employee`) and `manager_id` (nullable FK to
  `users`, `nullOnDelete`) to `users`.
- `App\Enums\UserRole`: string-backed enum, cases `Employee`, `Manager`,
  `Admin`.
- `User::manager(): BelongsTo`, `User::directReports(): HasMany`.

## Assumptions & open questions

- No enforcement yet of "only a Manager/Admin role may appear as someone's
  `manager_id`" — the schema allows an Employee to be set as another user's
  manager. Left unvalidated deliberately; not yet clear if this needs a
  constraint or is fine as an authoring convention.

## Traceability

- Discuss proposal: none — retroactive spec, written by spec-maker scan mode
  on 2026-09-03 (original feature was built before `spec-maker` existed).
- Implementing commit(s): `cbc2e5d` (feat(users): add role and manager
  hierarchy)
- Tests: `tests/Feature/UserRoleTest.php` (pre-existing, not yet cross-checked
  against AC IDs above — a future test-skill pass should map them)
