---
description: Resume work on MyCuti — read the project status fresh from disk and brief the user before doing anything else.
---

Before responding to anything else in this message, do the following:

1. Read `PROJECT_STATUS.md` in full, **from disk** (don't rely on a possibly-stale copy already in context — re-read it, since another session may have updated it since this conversation started).
2. Check `git log --oneline -10` and `git status --porcelain` to see if there's any recent activity or uncommitted work not yet reflected in PROJECT_STATUS.md's Session Log — if so, note the discrepancy rather than silently trusting the file.
3. Reply with a short briefing, not a re-analysis of the codebase:
   - What's implemented (one line per area, from the "Implemented" section)
   - What's explicitly not started yet
   - The most recent 1-3 Session Log entries
   - If the user and a previous session discussed specific next steps (check the Session Log / "Suggested build order"), name them
4. Ask what the user wants to work on next, unless the user's message (after "/sambung") already says what they want — in that case, proceed with that, using the status you just gathered as context.

Do not re-run a full codebase exploration/analysis unless PROJECT_STATUS.md is missing, empty, or you find it's clearly contradicted by git history (e.g., claims something is "not started" that git log shows was already built).
