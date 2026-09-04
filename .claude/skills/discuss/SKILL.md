---
name: discuss
description: Use before implementing any high-stakes change — broad architecture or data-model changes, security-sensitive work, or anything touching persistent/session-spanning config (hooks, settings.json, CLAUDE.md, PROJECT_STATUS.md-style systems) — or whenever the user types /discuss. Lays out a written strategy proposal (what, which files/locations, how it satisfies the requirement, how to verify) and waits for an explicit go-ahead before touching any files. Not for small, single-fork decisions inside otherwise-approved work — use AskUserQuestion for those.
---

# Discuss: propose → review → execute-on-command

A governance mode, not a feature. When active, the rule is simple: **lay out the plan in writing first, then wait.** Don't write, edit, run migrations, or otherwise change project state until the user gives a clear go-ahead. The content of the proposal changes with whatever is being discussed — the process doesn't.

## When to use it

1. **Manual** — the user types `/discuss <topic>`.
2. **Auto** — proactively enter this mode, without being asked, when the task at hand is high-stakes:
   - Touches multiple files across different areas of the app (not a single-file fix)
   - Changes architecture or the data model in a way that's hard to reverse later
   - Is security-sensitive (auth, encryption, permissions, credentials)
   - Creates or modifies persistent/session-spanning tooling (hooks, `.claude/settings.json`, `CLAUDE.md`/`AGENTS.md` protocol instructions, anything meant to survive `/clear`)
   - The user's request is itself a *strategy* ask ("how should we...", "I want to build a system for...") rather than a concrete, scoped instruction

Do **not** use it for: single-file edits, bug fixes, styling tweaks, or anything where the "right" approach is obvious/uncontested. Those just get done. This mode is for decisions that are expensive to undo or that the user explicitly wants oversight on before they exist.

## What the proposal must contain

Plain prose, not a wall of every implementation detail:

1. **What** — the shape of the solution, in a sentence or two per component.
2. **Files & locations** — every file that will be created or changed, with its path, and a one-line reason each.
3. **How it satisfies the requirement** — explicitly connect the design back to what the user asked for, especially any constraint they stated (e.g. "must survive /clear").
4. **How it will be verified** — what you'll actually test/run to prove it works, before claiming it's done. If the user asked for evidence, say what evidence you'll produce (pipe-tests, running the test suite, a before/after check) — not just "it should work."

Keep it scannable: headers, short paragraphs, a table for file lists. The point is to respect the user's time reading it, not to prove thoroughness through length.

## Proposal ID

Every proposal gets an ID before it's presented: `PROP-YYYYMMDD-NN`, where `YYYYMMDD` is today's date and `NN` is a two-digit sequence number (`01`, `02`, ...) among proposals raised that same day — check `PENDING.md` and today's `PROJECT_STATUS.md` session-log entries for the highest existing number that day and increment. Put the ID in the heading of the presented proposal (e.g. "Proposal: skill X — **PROP-20260903-02**") so it's visible without needing PENDING.md.

This ID is what lets other tooling — notably the `commit` skill — trace a later change back to the proposal that produced it, and what makes `git log --grep`/`git revert` line up with "undo just this one feature."

## Tracking in PENDING.md

Immediately after presenting the proposal (this one edit is exempt from "don't change project state while waiting" below — it's bookkeeping, not the change itself), append one entry to `PENDING.md` under "Open items": date, kind `proposal` with its ID (`[PROP-YYYYMMDD-NN]`), and a one-line description of what's being proposed. This is what lets `/pending-task` (or any fresh session/subagent) see the proposal is outstanding without needing this conversation's history.

When the user later gives a clear go-ahead and you execute, or explicitly declines/drops it, remove that entry from `PENDING.md` as part of the same turn — don't leave resolved proposals listed as open. The ID itself isn't lost when the entry is removed: it lives on in the commit message(s) that implement the proposal (see the `commit` skill), so `git log --grep=PROP-YYYYMMDD-NN` still finds the work later.

## Waiting

After presenting the proposal (and recording it in `PENDING.md`), stop. Do not call any tool that changes project state (Write, Edit, Bash commands that mutate files/DB, migrations, etc.) — read-only exploration to *inform* the proposal is fine before or during, but not after presenting it while awaiting a decision.

If the user gives feedback that isn't a clear go-ahead (asks a question, requests a change, pushes back on a piece of it), treat it as another round: revise the proposal, present the updated version, wait again. This can take multiple turns or even multiple sessions — that's expected, not a failure mode.

**Accepted execute signals**: "please execute", "execute", "ok build it", "buat", "go ahead", or anything else unambiguously approving the proposal just presented. If it's ambiguous whether they're approving the whole thing or just commenting on one part, ask which (in one short sentence, not another full proposal round). Once execution starts, remove the corresponding `PENDING.md` entry (see above).

**Spec before code**: the moment an execute signal is accepted, before touching any implementation file, invoke the `spec-maker` skill (Mode 1) to write the full spec for this proposal to `.ai/specs/<PROP-ID>-<slug>.md`. Implementation only begins once that file exists. This is not optional and does not need a separate ask — it's part of what "please execute" triggers. Skip it only for proposals that produce no implementation of their own (e.g. a proposal whose entire content *is* a spec-maker/discuss process change) — use judgment, but the default is spec-first.

## Small decisions found *during* an approved plan

Once execution has started, small contained forks (a field name, an enum's exact values, a minor UX choice) don't need another `/discuss` round — use `AskUserQuestion` with a recommended default, or just make the reasonable call and note it in the summary afterward. `/discuss` is for the shape of the plan itself, not every micro-decision inside it. Don't turn a single high-stakes decision into a barrage of tool-based questions either — if there are several real architectural forks at once, that's still one `/discuss` proposal (present the options in prose, or in one batched `AskUserQuestion` call at most), not several separate interruptions.

## After execution: evidence

Once the user approves and you build it, actually verify it before declaring done — run the tests, pipe-test the hook, check the migration ran, whatever is concretely checkable — and report what you checked, not just what you built. "I wrote X" and "I confirmed X works, here's how" are different claims; only make the second one when it's true.
