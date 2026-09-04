---
id: SPEC-20260903-04
title: Landing page navbar centering fix
status: implemented
created: 2026-09-03
---

# Landing page navbar centering fix

## Intent

The landing page navbar used a flex layout with `justify-between`, which
left the logo and the action buttons drifting apart rather than staying
visually balanced around the centered nav links as the viewport width
changed. Switched to an explicit 3-column grid so each section (logo,
links, actions) stays in its own track.

## Scope

### In scope
- The navbar markup in `resources/views/welcome.blade.php` only.

### Out of scope
- Any other part of the landing page layout.
- Mobile nav behavior beyond what the grid/flex change itself affects.

## Requirements

- REQ-1: The navbar uses a grid layout (`grid-cols-2` on small screens,
  `md:grid-cols-[1fr_auto_1fr]` from `md` breakpoint up) instead of
  `flex ... justify-between`.
- REQ-2: The logo/brand block stays left-aligned (`justify-self-start`); the
  actions block (theme toggle + auth links) stays right-aligned
  (`justify-self-end`).

## Acceptance criteria

- AC-1: Given the landing page is viewed at a `md`-or-wider viewport, when
  the navbar renders, then the center column (nav links) is visually
  centered independent of how wide the logo or actions blocks are.
- AC-2: Given the landing page is viewed at a narrow (below `md`) viewport,
  when the navbar renders, then it falls back to a 2-column grid (logo left,
  actions right) without the center nav-links column, matching the previous
  narrow-viewport behavior.

## Data / interface changes

None — pure CSS/markup change, no backend or data-model impact.

## Assumptions & open questions

- None — this is a small, self-contained visual fix; included in
  `spec-maker`'s retroactive batch at the user's explicit request even
  though a fix this size wouldn't normally warrant a spec.

## Traceability

- Discuss proposal: none — retroactive spec, written by spec-maker scan mode
  on 2026-09-03 (original fix was built before `spec-maker` existed).
- Implementing commit(s): `6d3d39d` (fix(landing): fix navbar centering on
  the landing page)
- Tests: none currently — a visual/layout fix with no existing automated
  coverage; a future browser-based test could assert AC-1/AC-2 directly.
