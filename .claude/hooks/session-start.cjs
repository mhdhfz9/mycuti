#!/usr/bin/env node
// SessionStart hook: announces the project's current status (from
// PROJECT_STATUS.md's Session Log) so it's visible immediately, not just
// silently loaded via CLAUDE.md's @PROJECT_STATUS.md import.
'use strict';

const fs = require('fs');
const path = require('path');

const statusPath = path.join(process.cwd(), 'PROJECT_STATUS.md');

let message = '📋 PROJECT_STATUS.md not found in this directory — nothing to resume from.';

try {
    const content = fs.readFileSync(statusPath, 'utf8');
    const logIndex = content.indexOf('## Session log');
    const after = logIndex >= 0 ? content.slice(logIndex) : content;
    const bullets = after
        .split('\n')
        .filter((line) => line.trim().startsWith('- '))
        .slice(0, 2)
        .map((line) => line.trim());

    message = bullets.length > 0
        ? '📋 MyCuti — resuming. Recent progress:\n' + bullets.join('\n') + '\n\nType /sambung for a full status briefing.'
        : '📋 MyCuti — PROJECT_STATUS.md found but no session log entries yet.';
} catch (error) {
    // Keep default "not found" message.
}

process.stdout.write(JSON.stringify({ systemMessage: message }));
