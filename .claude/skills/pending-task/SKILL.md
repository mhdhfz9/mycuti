---
name: pending-task
description: Use whenever the user asks what's still outstanding, unfinished, pending, or hanging in this project — "what's left", "what haven't we done", "apa lagi tergantung" — or whenever the user types /pending-task. Reads PENDING.md (the authoritative list of open /discuss proposals, tasks, and decisions) and presents it, cross-checked against PROJECT_STATUS.md's "next steps" section for anything not yet captured. Works even after /clear or in a fresh session, since it reads from a file rather than conversation history.
---

# Pending Task: show what's still outstanding

`PENDING.md` at the project root is the single source of truth for open work — proposals awaiting a go-ahead, tasks flagged "next up" but not started, and open decisions. This skill reads and presents it; it doesn't invent new items from conversation memory.

## Steps

1. Read `PENDING.md` from disk.
2. Read the "In progress / next steps discussed with the user" section of `PROJECT_STATUS.md`. If it names something not already reflected as an entry in `PENDING.md`, note the gap — don't silently add it, just flag it in the summary so the user can decide whether it belongs.
3. Present the open items as a short list: what it is, what kind (proposal / task / decision), and how long it's been open (from the date). Group by kind if there are several.
4. If `PENDING.md` has no open items, say so plainly — don't pad the response.

## Keeping it accurate

- This skill is read-only — it never edits `PENDING.md` itself. Entries are added/removed by the `discuss` skill (on proposing/resolving) or manually when the user or another session flags something.
- If an item looks stale or already done (e.g. code now exists that satisfies it), say so and ask before removing it — don't silently delete entries.
