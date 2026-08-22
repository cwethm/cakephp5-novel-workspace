# Schema Phases Ledger

Current phase: Phase 2 (SG-21)

Current migration head: `20260822160000_AddCharacterProfileSectionsTables`

Phase 2 SG-21 required domain tables:

- `users`
- `novels`
- `cards`
- `tags`
- `cards_tags`
- `characters`
- `character_appearances`
- `character_personalities`
- `character_voices`

Later-phase tables forbidden after SG-21:

- `character_traits`
- `character_skills`
- `character_goals`
- `locations`
- `items`
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
