---
id: SPEC-20260903-02
title: LeaveType model, migration, factory and seeder
status: implemented
created: 2026-09-03
---

# LeaveType model, migration, factory and seeder

## Intent

Leave requests need to reference a kind of leave (annual, sick, unpaid, ...)
with its own entitlement, paid/unpaid status, and whether it needs approval.
This models that as a standalone `LeaveType` table, seeded with the 9
standard Malaysian leave types so the rest of the leave-domain work
(`LeaveRequest`, balances) has real data to build against.

## Scope

### In scope
- `LeaveType` model: name, auto-derived slug, description, default
  entitlement (days), paid/unpaid flag, requires-approval flag, a display
  colour.
- Idempotent seeding of the 9 standard types.

### Out of scope
- Per-user leave balance/accrual (entitlement here is a *default*, not a
  balance ledger).
- Any UI for creating/editing leave types (admin-only CRUD not built).
- Leave types varying by employment type, tenure, or country beyond the
  Malaysian set seeded here.

## Requirements

- REQ-1: `slug` is unique and auto-derived from `name` (via `Str::slug`) if
  not explicitly set when saving.
- REQ-2: `default_entitlement_days` is an unsigned small integer.
- REQ-3: `is_paid` and `requires_approval` are booleans, both defaulting to
  `true` at the schema level.
- REQ-4: The seeder is idempotent — re-running it updates existing rows
  (matched by `slug`) rather than duplicating them.
- REQ-5: The seeder produces exactly the 9 standard Malaysian leave types:
  Annual, Sick, Hospitalisation, Emergency (not requiring approval),
  Maternity, Paternity, Marriage, Compassionate, and Unpaid (not paid).

## Acceptance criteria

- AC-1: Given a `LeaveType` is created with `name: 'Annual Leave'` and no
  explicit `slug`, when it is saved, then `slug` is `'annual-leave'`.
- AC-2: Given a `LeaveType` is created with an explicit `slug`, when it is
  saved, then that explicit slug is kept as-is (not overwritten by the
  auto-derivation).
- AC-3: Given the seeder has already run once, when it is run a second time,
  then the `leave_types` table still has exactly 9 rows (no duplicates), and
  any changed field values in the seeder source are reflected on the
  existing rows.
- AC-4: Given the seeder has run, when the `unpaid-leave` row is fetched,
  then `is_paid` is `false` and `default_entitlement_days` is `0`.
- AC-5: Given the seeder has run, when the `emergency-leave` row is fetched,
  then `requires_approval` is `false` (the one auto-approved type).

## Data / interface changes

- Migration `2026_09_03_063634_create_leave_types_table.php`: `leave_types`
  table — `name`, `slug` (unique), `description` (nullable text),
  `default_entitlement_days` (unsigned smallint), `is_paid` (bool, default
  true), `requires_approval` (bool, default true), `colour`, timestamps.
- `App\Models\LeaveType` with a `booted()` `saving` hook for slug
  derivation.
- `Database\Seeders\LeaveTypeSeeder`, wired into `DatabaseSeeder`.

## Assumptions & open questions

- `colour` has no format validation (expected to be a hex string like
  `#2563eb` by convention, per the seeder, but the schema/model don't
  enforce it).

## Traceability

- Discuss proposal: none — retroactive spec, written by spec-maker scan mode
  on 2026-09-03 (original feature was built before `spec-maker` existed).
- Implementing commit(s): `7f775ba` (feat(leave-type): add LeaveType model,
  migration, factory and seeder)
- Tests: `tests/Feature/LeaveTypeTest.php` (pre-existing, not yet
  cross-checked against AC IDs above)
