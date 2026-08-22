# Schema Phases Ledger

Current phase: Phase 0/1

Current migration head: `20260821190000_InitialNovelWorkspace`

Phase 0/1 required domain tables:

- `users`
- `novels`
- `cards`
- `tags`
- `cards_tags`

Later-phase tables forbidden at Phase 0/1:

- `characters`
- `relationships`
- `chapters`
- `scenes`
- `plot_points`
- `notes`
- `assets`

Reference schema rule:

- `config/schema/novel_factory_cakephp5_schema.sql` is a future-state reference and is not executed by normal development, test, or CI setup.
- Each later SG-* slice must add a new migration and update this ledger when that slice is implemented.
