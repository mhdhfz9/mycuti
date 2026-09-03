---
paths:
  - 'database/seeders/**'
---

# Seeders

## Don't use WithoutModelEvents in seeders
User::booted() derives `ic_number_hash` from a `saving` event. `WithoutModelEvents` (in DatabaseSeeder by default in the starter kit) silently suppresses that, leaving seeded users unable to log in. Removed from DatabaseSeeder — don't re-add it, and avoid it in any future seeder that creates users.
