---
name: spec-maker
description: Writes a complete, structured feature spec to .ai/specs/ before implementation begins. Two modes — (1) auto, invoked by the discuss skill's execute step right after the user says "please execute" and before any implementation file is touched; (2) scan, invoked manually ("/spec-maker", "scan missing spec", "cari feature tak ada spec") to find already-implemented features that have no spec file (typically because they were hand-crafted without going through /discuss) and propose writing one retroactively. The spec is the reference point a later test skill (unit/regression/browser) cites via its Acceptance Criteria IDs — do not skip it for anything that went through /discuss.
---

# Spec-maker: write the spec before the code

Goal: every feature that goes through `/discuss` gets a structured spec file *before* a single line of implementation is written, and every feature that didn't (hand-crafted, pre-dates this system) eventually gets one too. The spec is what a future test skill will cite — its Acceptance Criteria IDs are meant to map 1:1 onto test cases, so the format is fixed, not free prose.

## Spec file format

Location: `.ai/specs/<ID>-<slug>.md`, where `<ID>` is the originating `PROP-YYYYMMDD-NN` (from an approved `/discuss` proposal) or, for a retroactive spec written by scan mode, a minted `SPEC-YYYYMMDD-NN`. `<slug>` is a short kebab-case name for the feature.

```markdown
---
id: PROP-YYYYMMDD-NN | SPEC-YYYYMMDD-NN
title: <short feature name>
status: draft | implemented
created: YYYY-MM-DD
---

# <Title>

## Intent

1-2 paragraphs, plain prose: why this exists, what problem it solves. This is
the part a human reads to understand the feature without touching the code.

## Scope

### In scope
- ...

### Out of scope
- ... (explicitly named — what this feature deliberately does *not* cover)

## Requirements

- REQ-1: <atomic, numbered requirement>
- REQ-2: ...

## Acceptance criteria

Given/When/Then, one outcome per criterion — this is what a future test cites directly:

- AC-1: Given <state/precondition>, when <action>, then <observable outcome>.
- AC-2: ...

## Data / interface changes

Only if relevant — new tables/columns, new routes, new public methods. Skip this
section entirely if there are none.

## Assumptions & open questions

- ... (anything left ambiguous or deferred; empty section is fine if none)

## Traceability

- Discuss proposal: PROP-YYYYMMDD-NN (or "none — retroactive spec, written by spec-maker scan mode on YYYY-MM-DD")
- Implementing commit(s): _(filled in after implementation — see "After implementation" below)_
- Tests: _(filled in later by the test skill, once it exists)_
```

Keep Requirements and Acceptance Criteria atomic — one idea per line, one ID each. A requirement with "and" joining two unrelated behaviors should be split into two.

## Mode 1 — Auto, right after "please execute"

This mode is invoked by the `discuss` skill itself (see its "Accepted execute signals" section) — you don't need to trigger it separately when following an approved `/discuss` proposal.

1. Take the proposal exactly as it was approved in the conversation — What / Files & locations / how it satisfies the requirement / how it's verified — and any answers the user gave to clarifying questions during that `/discuss` round.
2. Translate it into the spec format above. This is a formalization step, not a new design step: don't introduce scope, requirements, or acceptance criteria that weren't already implied by the approved proposal. If something in the proposal is too vague to turn into a testable AC, that's a sign the `/discuss` round wasn't actually finished — flag it back rather than inventing detail.
3. Write the file to `.ai/specs/<PROP-ID>-<slug>.md` with `status: draft`.
4. Only after the file is written does implementation begin. This mode does not pause for a separate approval — the underlying feature was already approved in the `/discuss` round; this step formalizes it, it doesn't reopen it.

### After implementation

Once the feature is actually built (and, if the `commit` skill is used, committed), update the spec's `Traceability` section with the implementing commit hash(es) and flip `status: draft` to `status: implemented`. This is a small edit, not a new `/discuss` round.

## Mode 2 — Scan for missing specs

Manual trigger: `/spec-maker`, "scan missing spec", or similar. Read-only investigation first, then one batched proposal — never write a spec file in this mode without that confirmation.

1. **List existing specs**: read `.ai/specs/` (if it exists) and note every ID already covered.
2. **Find candidate features without one**: cross-reference —
   - `PROJECT_STATUS.md`'s "Implemented"/feature-area sections and Session Log entries — anything describing a discrete feature (not a chore/docs/tooling entry) that isn't linked to a spec ID.
   - `git log --oneline` commits that look feature-shaped (`feat:` prefix, or a `Proposal: PROP-...`/`Proposal: SPEC-...` trailer) whose ID has no matching file in `.ai/specs/`.
   Skip anything that's clearly tooling/process (skills, hooks, docs-only commits) — this mode is for *product* features, not the scaffolding around them.
3. **Present the full candidate list in one batch** — feature name, why it was flagged (which commit/status-doc entry), and the `SPEC-YYYYMMDD-NN` ID it would get. Wait for the user to approve the list as a whole (they may also trim it or add one you missed).
4. Once approved, write all specs in the batch, `status: implemented` (since the code already exists — reconstruct Intent/Scope/Requirements/Acceptance Criteria from the actual current behavior, not from memory of an original design that may not have been written down). Where you can't confidently infer something (e.g. an edge case the code doesn't clearly handle one way or the other), say so in "Assumptions & open questions" rather than guessing.
5. Report back the list of spec files written.

## Notes

- Never retroactively edit a spec's Requirements/Acceptance Criteria to match code that changed later without the user asking for that — a spec drifting silently out of sync with reality defeats its purpose. If implementation diverges from an approved spec during Mode 1's build, that's worth surfacing to the user, not silently reconciling by rewriting the spec after the fact.
- This skill only writes to `.ai/specs/`. It never touches implementation files itself.
