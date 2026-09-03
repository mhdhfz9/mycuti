---
name: commit
description: Use when the user asks to commit pending work — "commit this", "commit ni", or /commit. Splits the current uncommitted changes into one git commit per feature/proposal (never a single catch-all commit when multiple features are mixed together), with each commit message tagged by the originating /discuss proposal ID (PROP-YYYYMMDD-NN) or a minted spec ID. Grouping comes from the calling session's own memory of what was done for what, not from guessing at the diff alone. Not for a single already-obvious one-shot commit with no ambiguity — the normal git commit flow (see the top-level "Committing changes with git" instructions) still covers that; reach for this skill specifically when several distinct features/fixes are mixed into the working tree and need separating.
---

# Commit: one commit per feature, ID-tagged

Goal: turn a working tree that may contain several unrelated pieces of finished work into a clean sequence of commits — one per feature/proposal — each traceable back to the proposal or spec that produced it. This is what makes `git log --grep`, `git revert <hash>`, and `git checkout <hash>` line up cleanly with "undo just this one feature" later.

## Step 1 — See what's actually changed

Run `git status` (and `git diff` / `git diff --staged` as needed) to see the full set of modified/untracked files. Never assume from memory alone — the working tree is ground truth.

## Step 2 — Group changed files by feature

This is the part that can't be done from the diff alone — use what you (the calling session) actually know from this conversation about which files belong to which piece of work. In priority order:

1. **Approved `/discuss` proposals executed this session** — each one has a `PROP-YYYYMMDD-NN` ID (see `discuss` skill's bookkeeping). Match its file list to what changed.
2. **`PROJECT_STATUS.md` Session Log entries** added since the last commit — each bullet there typically corresponds to one discrete feature/fix and names the files involved.
3. **Direct conversation memory** — if a group of edits was clearly done together for one stated purpose in this session but isn't yet reflected in the log, that's still a valid group; mint a spec ID for it (see Step 3).

Every changed file must end up in exactly one group. If a file doesn't clearly belong anywhere — stray edit, leftover from an abandoned approach, something from before this session — **do not** guess or silently fold it into the nearest group or a catch-all commit. Ask the user which group it belongs to (or whether it should be left uncommitted).

## Step 3 — Assign an ID to each group

- If the group traces to an approved `/discuss` proposal, use that proposal's `PROP-YYYYMMDD-NN` ID.
- Otherwise, mint a `SPEC-<slug>` ID from the feature's name (e.g. `SPEC-statusline-fix`). Keep the slug short and stable — it's what a future `git log --grep` search will use.

## Step 4 — Present the plan before committing

List each proposed commit: files included, one-line summary, and its ID. This is the "show evidence before acting" step — for a repo with existing uncommitted work, or whenever it's not unambiguous which group a file belongs to, present the grouping and wait for confirmation before running any `git add`/`git commit`. Skip the wait only when the user's own invocation already unambiguously specified the grouping (e.g. they named exactly which files go with which feature).

## Step 5 — Commit each group separately

For each group, in a sensible order (typically the order the features were completed in):

1. `git add` **only the files in that group** — never `git add -A` or `git add .`.
2. `git status` to confirm exactly the intended files are staged (catch anything that slipped in).
3. Commit with a heredoc message:

```
git commit -m "$(cat <<'EOF'
<type>(<scope>): <one-line summary>

<body: what changed and why, 1-3 sentences>

Proposal: <PROP-YYYYMMDD-NN or SPEC-slug>

Co-Authored-By: Claude Sonnet 5 <noreply@anthropic.com>
Claude-Session: <this session's URL, from the system reminder>
EOF
)"
```

Use `<type>` from conventional-commit prefixes (`feat`, `fix`, `refactor`, `chore`, `docs`, `test`) matching what the change actually is. `<scope>` is the affected area (e.g. `leave-request`, `statusline`, `discuss-skill`).

4. Never `--amend`, never `--no-verify`, never force-push, never push at all unless separately asked. If a pre-commit hook fails, fix the underlying issue and create a **new** commit — don't retry with `--no-verify`.

## Step 6 — Report

After all commits land, run `git log --oneline -n <count>` and `git status` to confirm a clean result, and report back: each commit's short hash, its ID, and its one-line summary — so the user has everything needed to `git revert` or `git checkout` a specific feature later without re-deriving it from a big diff.

## Safety notes

- Committing is a "visible/shared state" action per the standing git-safety rules — only run this skill when the user has actually asked for a commit (the skill invocation itself counts as that ask for the commits it describes; it does not extend to pushing).
- If unsure whether two files belong in the same commit or separate ones, prefer separating them — an extra small commit is cheap to squash later; a wrongly-merged commit is not cheaply un-merged without rewriting history.
- Do not use this skill to rewrite or reorganize *already-committed* history (no interactive rebase, no amend-chains). It only ever adds new commits on top of the current state.
