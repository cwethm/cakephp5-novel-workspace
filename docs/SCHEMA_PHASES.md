# Schema Phases Ledger

Current phase: Phase 2 (SG-24)

Current migration head: `20260823010000_AddItemsTable`

Phase 2 SG-24 required domain tables:

- `users`
- `novels`
- `cards`
- `tags`
- `cards_tags`
- `characters`
- `character_appearances`
- `character_personalities`
- `character_voices`
- `character_traits`
- `character_skills`
- `character_goals`
- `locations`
- `items`

Later-phase tables forbidden after SG-24:

- `organizations`
- `character_organizations`
- `relationships`
- `chapters`
- `scenes`
- `characters_scenes`
- `items_scenes`
- `organizations_scenes`
- `story_threads`
- `scenes_story_threads`
- `plot_points`
- `plot_points_story_threads`
- `characters_plot_points`
- `notes`
- `assets`
- `assets_cards`

Reference schema rule:

- `config/schema/novel_factory_cakephp5_schema.sql` is a future-state reference and is not executed by normal development, test, or CI setup.
- Each later SG-* slice must add a new migration and update this ledger when that slice is implemented.
