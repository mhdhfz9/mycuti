---
id: SPEC-20260903-03
title: LeaveRequest model, migration and factory
status: implemented
created: 2026-09-03
---

# LeaveRequest model, migration and factory

## Intent

The core record of the leave-management domain: an employee's request to
take leave of a given type, over a date range, with a status that a manager
will later act on. Approver eligibility is deliberately resolved via
`$user->manager` (see SPEC-20260903-01) rather than stored redundantly on
the request itself — `approved_by`/`approved_at` only record who actually
acted, for audit, not who is *allowed* to act.

## Scope

### In scope
- `LeaveRequest` belonging to a requester (`User`) and a `LeaveType`.
- A status lifecycle: Pending / Approved / Rejected / Cancelled.
- Audit fields for who approved/rejected and when.

### Out of scope
- Validation of the request itself (end date ≥ start date, overlap with
  existing requests, entitlement/balance checks) — deliberately deferred to
  the submission UI layer, not the model.
- The approval workflow (who is authorized to transition status, and the
  UI/action for it) — not built yet.
- Notifications on status change.

## Requirements

- REQ-1: `LeaveRequest` belongs to one `User` (requester, via `user_id`) and
  one `LeaveType` (via `leave_type_id`).
- REQ-2: `status` is cast to `App\Enums\LeaveRequestStatus`, defaulting to
  `pending` at the schema level.
- REQ-3: `start_date` and `end_date` are cast as `date`; `approved_at` is
  cast as `datetime`.
- REQ-4: `approved_by` is a nullable FK to `users`, set to `null` if that
  user is deleted (`nullOnDelete`); `approver()` resolves it.
- REQ-5: `reason` is optional (nullable text).
- REQ-6: Deleting the requester or the leave type cascades to delete the
  leave request (`cascadeOnDelete` on `user_id`/`leave_type_id`).

## Acceptance criteria

- AC-1: Given a new `LeaveRequest` is created without an explicit `status`,
  when it is persisted, then `status` reads back as
  `LeaveRequestStatus::Pending`.
- AC-2: Given a `LeaveRequest` with `start_date`/`end_date` set, when the
  model is loaded, then both are `Carbon`/date instances (not raw strings).
- AC-3: Given a `LeaveRequest`'s `leaveType`/`user`/`approver` relations are
  accessed, when the request has all three set, then each resolves to the
  correct related model.
- AC-4: Given a `LeaveRequest` with `approved_by` pointing at user `M`, when
  `M` is deleted, then the request's `approved_by` becomes `null` (audit
  trail loosened, not the request itself deleted).
- AC-5: Given the requesting `User` is deleted, when the deletion completes,
  then their `LeaveRequest` rows are also deleted (cascade), not left
  orphaned.

## Data / interface changes

- Migration `2026_09_03_074635_create_leave_requests_table.php`:
  `leave_requests` table — `user_id`/`leave_type_id` (FKs, cascade delete),
  `start_date`, `end_date`, `reason` (nullable), `status` (string, default
  `pending`), `approved_by` (nullable FK, null on delete), `approved_at`
  (nullable), timestamps.
- `App\Models\LeaveRequest` with `user()`, `leaveType()`, `approver()`
  relations.
- `App\Enums\LeaveRequestStatus`: `Pending` / `Approved` / `Rejected` /
  `Cancelled`.
- `User::leaveRequests(): HasMany` (added on `User` alongside this feature).

## Assumptions & open questions

- No constraint yet preventing a request with `end_date < start_date`, or
  overlapping requests for the same user — explicitly deferred to the
  submission UI's validation layer per the proposal that originally built
  this (not a gap in this spec, a stated non-goal).
- Whether cancellation (`Cancelled`) is requester-initiated,
  manager-initiated, or both is not yet decided — the enum case exists but
  no transition logic has been built.

## Traceability

- Discuss proposal: none — retroactive spec, written by spec-maker scan mode
  on 2026-09-03 (original feature was built before `spec-maker` existed).
- Implementing commit(s): `cb11934` (feat(leave-request): add LeaveRequest
  model, migration and factory)
- Tests: `tests/Feature/LeaveRequestTest.php` (pre-existing, not yet
  cross-checked against AC IDs above)
