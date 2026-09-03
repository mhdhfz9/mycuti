---
paths:
  - app/Models/User.php
---

# Models

## ic_number is encrypted — query via ic_number_hash, never directly
`users.ic_number` uses the `encrypted` cast (non-deterministic ciphertext), so `WHERE ic_number = ?` never matches. A `ic_number_hash` column (HMAC-SHA256 via `User::hashIcNumber()`) is auto-populated in `User::booted()`'s `saving` event and is the only column safe to query/unique-check against. Fortify login/password-confirm are wired through `Fortify::authenticateUsing()` / `confirmPasswordsUsing()` in `FortifyServiceProvider` for the same reason — don't revert to the default Eloquent credential lookup. Display via `$user->maskedIcNumber()`, never the raw value.
