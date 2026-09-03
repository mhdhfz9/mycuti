---
name: commit
description: Use when the user asks to commit pending work — "commit this", "commit ni", or /commit. Splits the current uncommitted changes into one git commit per feature/proposal (never a single catch-all commit when multiple features are mixed together), with each commit message tagged by the originating /discuss proposal ID (PROP-YYYYMMDD-NN) or a minted spec ID, then pushes the current branch (private remotes only by default). Grouping comes from the calling session's own memory of what was done for what, not from guessing at the diff alone. Behavior is the same regardless of who invokes it — an interactive user or an agent — there is no elevated-trust invoker mode yet. Not for a single already-obvious one-shot commit with no ambiguity — the normal git commit flow (see the top-level "Committing changes with git" instructions) still covers that; reach for this skill specifically when several distinct features/fixes are mixed into the working tree and need separating, or whenever a push should follow.
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

**Hard rule — skill files are never bundled**: if a group's files include a new or modified `.claude/skills/<name>/SKILL.md`, split that file out into its own group, on its own, regardless of what other grouping logic would otherwise suggest. This applies even when the skill file was authored in the same breath as other files for what feels like one feature. The reason: `git log -- .claude/skills/<name>/` needs to show that skill's own history in isolation, uncontaminated by unrelated file changes riding in the same commit.

## Step 3 — Assign an ID to each group

- If the group traces to an approved `/discuss` proposal, use that proposal's `PROP-YYYYMMDD-NN` ID.
- Otherwise, mint a `SPEC-<slug>` ID from the feature's name (e.g. `SPEC-statusline-fix`). Keep the slug short and stable — it's what a future `git log --grep` search will use.

## Step 4 — Security sweep

Before staging anything, scan the actual diff for each group (`git diff -- <files>` for tracked changes, and read new/untracked files directly) for likely secrets:

- Private-key headers (`-----BEGIN ... PRIVATE KEY-----`)
- Cloud-provider key patterns (e.g. `AKIA[0-9A-Z]{16}` for AWS access keys, `AIza[0-9A-Za-z_-]{35}` for Google API keys, `ghp_`/`gho_`/`github_pat_` GitHub tokens, `sk-[A-Za-z0-9]{20,}` style API keys)
- A literal secret assigned in code or config: `password\s*=\s*['"][^'"]+['"]`, `api[_-]?key\s*=\s*['"][^'"]+['"]`, `secret\s*=\s*['"][^'"]+['"]` — as opposed to a variable read from `env()`/`config()`/`process.env` etc., which is fine
- A new file that looks like it holds real credentials (`.env`, `*.pem`, `*.key`, `credentials.json` and similar) being staged rather than ignored

If anything matches, **stop** — do not stage or commit that file. Report the file and line (redact the actual secret value, don't echo it back in full) and ask the user to confirm it's safe, or fix it (move to `.env`, add to `.gitignore`, rotate the credential) before continuing. This applies per group, every time the skill runs — not just once.

## Step 5 — Present the plan before committing

List each proposed commit: files included, one-line summary, and its ID. This is the "show evidence before acting" step — for a repo with existing uncommitted work, or whenever it's not unambiguous which group a file belongs to, present the grouping and wait for confirmation before running any `git add`/`git commit`. Skip the wait only when the user's own invocation already unambiguously specified the grouping (e.g. they named exactly which files go with which feature).

## Step 6 — Commit each group separately

For each group, in a sensible order (typically the order the features were completed in):

1. `git add` **only the files in that group** — never `git add -A` or `git add .`.
2. `git status` to confirm exactly the intended files are staged (catch anything that slipped in).
3. Commit with a heredoc message that combines a conventional-commit summary line (machine/AI-parseable — this is what `git log --grep`, changelog tooling, and other agents key off) with a plain-prose body (human-readable — what changed and why, in real sentences, not a terse diff summary):

```
git commit -m "$(cat <<'EOF'
<type>(<scope>): <one-line summary>

<body: what changed and why, 1-3 sentences of plain prose a human can read
without needing to open the diff>

Proposal: <PROP-YYYYMMDD-NN or SPEC-slug>

Co-Authored-By: Claude Sonnet 5 <noreply@anthropic.com>
Claude-Session: <this session's URL, from the system reminder>
EOF
)"
```

Use `<type>` from conventional-commit prefixes (`feat`, `fix`, `refactor`, `chore`, `docs`, `test`) matching what the change actually is. `<scope>` is the affected area (e.g. `leave-request`, `statusline`, `discuss-skill`). Both halves are mandatory — the summary line alone isn't enough for a human skimming `git log`, and prose alone isn't enough for tooling that parses conventional-commit types.

4. Commit on the **current branch** (`git rev-parse --abbrev-ref HEAD`) unless the user named a different branch explicitly for this invocation — never auto-switch or auto-create a branch on your own judgment.
5. Never `--amend`, never `--no-verify`, never force-push. If a pre-commit hook fails, fix the underlying issue and create a **new** commit — don't retry with `--no-verify`.

## Step 7 — Push

Once every group for this invocation is committed, push the current branch — this is the default now, not something that needs a separate ask each time (the skill invocation itself is the ask), except where this step itself says to stop.

1. **Remote check**: confirm a remote exists for the current branch (`git remote -v`; typically `origin`, or whatever the user named). No remote configured → **stop, ask** — do not run `git remote add` or `gh repo create` on your own judgment; that's the user's call.
2. **Visibility check, fresh every time** (no caching/recording — re-verify on every push, per the user's explicit choice): determine whether the remote repo is private.
   - If `gh` is available and authenticated: `gh repo view --json isPrivate -q .isPrivate`.
   - Otherwise, for a `github.com` remote: an unauthenticated `curl -s https://api.github.com/repos/<owner>/<repo>` — HTTP 200 with `"private": false` means public; HTTP 200 with `"private": true` means private; anything else (404, network error, non-GitHub host) is inconclusive.
   - `private:true` confirmed → proceed to push (this is the default-allowed case; the vast majority of this project's work is private, so this is the common path, not a rare exception).
   - `private:false` confirmed, or visibility inconclusive → **stop, ask** for explicit confirmation before pushing. Only push a public (or unverified) remote when the user explicitly says so for that push — a prior "yes" for a previous push does not carry forward.
3. Push only the **current branch**, never other branches, never tags, never `--force`: `git push -u origin <branch>` if the branch has no upstream yet, otherwise plain `git push`.
4. If the security sweep (Step 4) flagged something for any group that wasn't explicitly cleared by the user, do not push — even if the commit already happened locally — until it's resolved.

## Step 8 — Report

After all commits (and any push) land, run `git log --oneline -n <count>` and `git status` to confirm a clean result, and report back: each commit's short hash, its ID, and its one-line summary, plus whether a push happened (and to where) or why it was skipped — so the user has everything needed to `git revert` or `git checkout` a specific feature later without re-deriving it from a big diff.

## Safety notes

- Committing **and** pushing are both covered by the skill invocation itself as "the user asked for this" — push is the default outcome of running this skill, not a separate ask, except at the explicit stop-and-ask points in Step 7 (no remote, public/unverified visibility, or an unresolved security-sweep flag).
- If unsure whether two files belong in the same commit or separate ones, prefer separating them — an extra small commit is cheap to squash later; a wrongly-merged commit is not cheaply un-merged without rewriting history.
- Do not use this skill to rewrite or reorganize *already-committed* history (no interactive rebase, no amend-chains). It only ever adds new commits on top of the current state.
- Do not treat a "push to public" confirmation from earlier in the same session as blanket permission for later invocations — re-ask every time visibility comes back public/unverified.
