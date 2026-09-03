#!/usr/bin/env bash
# Stop hook: if project source files changed this session but
# PROJECT_STATUS.md wasn't touched, block finishing the turn and ask for it
# to be updated first. This is the enforcement layer for "every completed
# feature must be persisted before the session can end" — it fires on
# /clear, /compact, and normal stop alike, per Claude Code's Stop event.
set -euo pipefail

# Not a git repo (or git unavailable) — nothing to check, allow.
if ! git rev-parse --is-inside-work-tree >/dev/null 2>&1; then
    echo '{}'
    exit 0
fi

SOURCE_CHANGED=$(git status --porcelain --untracked-files=all -- app resources database config routes 2>/dev/null || true)
STATUS_CHANGED=$(git status --porcelain -- PROJECT_STATUS.md 2>/dev/null || true)

if [ -n "$SOURCE_CHANGED" ] && [ -z "$STATUS_CHANGED" ]; then
    echo '{"decision":"block","reason":"Project source files changed this session but PROJECT_STATUS.md was not updated. Update its status tables and add a Session Log entry (what was built, what is next) before finishing this turn."}'
else
    echo '{}'
fi
