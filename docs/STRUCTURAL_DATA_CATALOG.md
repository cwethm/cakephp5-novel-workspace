# Structural Data Catalog (SG-01)

Slice ID: SG-01
Development phase: Phase 1 planning gate
Current migration head: 20260821190000_InitialNovelWorkspace

## Scope

This ledger inventories value-bearing fields from:

- current migration authority (`config/Migrations/20260821190000_InitialNovelWorkspace.php`)
- current runtime validation/registries (`src/Model/Table/*`, `src/Domain/Registry/CardTypeRegistry.php`)
- future-state reference schema (`config/schema/novel_factory_cakephp5_schema.sql`)

The reference SQL is evidence for planned fields only. It does not override the
current migration/runtime authority.

## SG-01 Approval Gate

- This document records repository-grounded field decisions; it does not
  self-approve the SG-01 slice.
- `Approved` in a ledger row means the cited repository sources already define
  that field's contract. It does not mean this document has received review
  approval.
- SG-01 remains incomplete until this documentation-only change is reviewed and
  approved. No migration, runtime implementation, catalog framework, data row,
  or UI is authorized by this ledger.

## Machine Keys vs Display Labels

- Machine keys are stored values used for logic (for example `active`).
- Display labels are presentation strings (for example `Active`) and are not
  currently standardized for most fields.
- Unless otherwise noted, display labels are decision-required at the UI/i18n
  layer and must not replace persisted machine keys.

## Coverage Audit

The candidate-field audit covered the current migration, current table
validators, `CardTypeRegistry`, and every column in the reference schema whose
name represents a status, type, category, role, purpose, proficiency,
importance, lifecycle state, machine operation, or interactive option.

- The reference schema candidates are all represented below.
- `users.status` is the only current-only candidate because the reference
  schema does not define the current authentication tables.
- No separate `category`, `lifecycle`, `operation`, or `option` column exists in
  the audited sources. Lifecycle and interactive choices currently appear as
  `status`, discriminator, and boolean fields already listed below.

## Structural Field Ledger

| table.column | Consuming feature and scheduled slice | Source evidence | Ownership | Tenancy scope | Stable machine-key format | Approved keys from repository sources | Display-label source | Custom values allowed | Default and inactive/deprecated behavior | DB constraint or app validation owner | Decision status |
| --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- |
| users.status | Authentication and account state (Phase 0/1 baseline) | Current migration default `active`; `UsersTable::validationDefault()` in-list | Code contract | User row (global/system) | lowercase `snake_case` string | `active`, `disabled` | Not defined in current runtime/templates | No | Default `active`; inactive state `disabled` | Migration default + app validation | Approved |
| novels.status | Novel lifecycle (Phase 0/1 baseline) | Current migration default `planning`; `NovelsTable::STATUSES` + in-list | Code contract | Novel-owned row | lowercase `snake_case` string | `planning`, `drafting`, `revising`, `complete`, `archived` | Hard-coded key-to-label options in Novel add/edit templates | No | Default `planning`; terminal/inactive `archived` | Migration default + app validation | Approved |
| cards.card_type | Card subtype discriminator (Phase 0/1 baseline; consumed by SG-20+) | Current migration column; `CardsTable` validator uses `CardTypeRegistry::has()`; ADR-001 | Code contract | Novel-owned row | lowercase `snake_case` string | `character`, `location`, `item`, `organization` | `CardTypeRegistry` `label` metadata | No (except code release adding new registry key) | No default in current migration | App validation via registry | Approved |
| cards.status | Card lifecycle (Phase 0/1 baseline) | Current migration default `active`; `CardsTable` in-list | Code contract | Novel-owned row | lowercase `snake_case` string | `active`, `archived` | Hard-coded key-to-label options in Card add/edit/index templates | No | Default `active`; inactive state `archived` | Migration default + app validation | Approved |
| cards.importance | Card priority/importance (Phase 0/1 baseline and future filters) | Current migration default `normal`; reference schema default `supporting`; no runtime in-list | Decision required (likely code contract) | Novel-owned row | lowercase `snake_case` string (if closed set) | Known values only: `normal` (current migration), `supporting` (reference SQL) | Not defined | Decision required | Current/reference defaults conflict; no approved inactive/deprecated key | Current migration default only; no runtime list validator | Decision required |
| characters.role | Character role text (SG-20) | Reference schema column; SG-20 structural-data rule keeps role free text unless separately approved | Free text | Novel-owned via `characters.card_id -> cards.novel_id` | n/a (free text) | None (open text) | Author-entered text | Yes | Nullable; no default | None yet (SG-20 implementation) | Approved |
| character_traits.trait_type | Character trait classification (SG-22) | Reference schema comment explicitly lists trait types | Code contract | Novel-owned via character -> card | lowercase `snake_case` string | `strength`, `weakness`, `habit`, `bad_habit`, `fear`, `want`, `need`, `secret`, `personality` | Decision required for human labels; machine keys fixed | No | No default; all persisted keys must be explicit | None yet (must be validated in SG-22) | Approved |
| character_skills.proficiency | Skill proficiency description (SG-22) | Reference schema column; SG-22 allows free text unless separately approved otherwise | Free text | Novel-owned via character -> card | n/a (free text) | None (open text) | Author-entered text | Yes | Nullable; no default | None yet (SG-22 implementation) | Approved |
| character_goals.goal_type | Goal classification (SG-22) | Reference schema default `external`; SG-22 execution approval defines the complete list | Code contract | Novel-owned via character -> card | lowercase `snake_case` string | `external` | `CharacterProfileRegistry` goal type labels | No | Default and only approved key: `external` | SG-22 app validation via registry-backed in-list | Approved |
| character_goals.status | Goal lifecycle/status (SG-22) | Reference schema default `active`; SG-22 execution approval defines the complete list | Code contract | Novel-owned via character -> card | lowercase `snake_case` string | `active` | `CharacterProfileRegistry` goal status labels | No | Default and only approved key: `active` | SG-22 app validation via registry-backed in-list | Approved |
| locations.location_type | Location classification (SG-23) | Reference schema column plus SG-23 execution approval | Code contract | Novel-owned via `locations.card_id -> cards.novel_id` | lowercase `snake_case` string | `settlement`, `structure`, `home`, `body_of_water`, `geological_feature`, `place`, `non_terrestrial`, `terrain` | `LocationTypeRegistry` machine-key labels | No | Nullable; no default | SG-23 app validation via registry-backed in-list | Approved |
| items.item_type | Item classification (SG-24) | Reference schema column plus SG-24 execution approval | Code contract | Novel-owned via `items.card_id -> cards.novel_id` | lowercase `snake_case` string | `weapon`, `armor`, `clothing`, `accessory`, `tool`, `document`, `consumable`, `currency`, `artifact`, `key_item`, `technology`, `vehicle` | `ItemTypeRegistry` machine-key labels | No | Nullable; no default | SG-24 app validation via registry-backed in-list | Approved |
| items.is_unique | Item uniqueness toggle (SG-24) | Reference schema default `0`; SG-24 states boolean business data, not value-set | Code contract (boolean) | Novel-owned via `items.card_id -> cards.novel_id` | boolean (`0`/`1`) | `0` (false), `1` (true) | UI toggle/checkbox text is decision-required | No | Default `0` (false) | Reference-schema default now; runtime validation to be defined in SG-24 | Approved |
| organizations.organization_type | Organization classification (SG-25) | Reference schema column; SG-25 requires SG-01 decision | Decision required | Novel-owned via `organizations.card_id -> cards.novel_id` | lowercase `snake_case` string (if closed set) | None | Decision required | Decision required | Nullable; no default | None yet | Decision required |
| organizations.purpose | Organization purpose narrative (SG-25) | Reference schema column | Free text | Novel-owned via `organizations.card_id -> cards.novel_id` | n/a (free text) | None (open text) | Author-entered text | Yes | Nullable; no default | None yet | Approved |
| character_organizations.role | Membership role description (SG-25) | Reference schema column; SG-25 says descriptive unless explicitly approved otherwise | Free text | Novel-owned via membership -> organization/character -> card -> novel | n/a (free text) | None (open text) | Author-entered text | Yes | Nullable; no default | None yet | Approved |
| character_organizations.status | Membership lifecycle/status (SG-25) | Reference schema default `active`; SG-25 requires approved complete list | Decision required | Novel-owned via membership -> organization/character -> card -> novel | lowercase `snake_case` string (if closed set) | Only default known: `active` | Decision required | Decision required | Default `active` is not a complete allowed set | None yet | Decision required |
| relationships.relationship_type | Relationship type key (SG-30) | Reference schema column; SG-30 requires approved complete list and ownership decision | Decision required (code registry vs DB catalog) | Novel-owned row (`relationships.novel_id`) | lowercase `snake_case` string | None | Decision required | Decision required | Required field, but no approved key list yet | None yet | Decision required |
| relationships.is_reciprocal | Reciprocal-relationship toggle (SG-30) | Reference schema default `0`; SG-30 requires reciprocal semantics definition | Code contract (boolean semantics) | Novel-owned row | boolean (`0`/`1`) | `0` (false), `1` (true) | UI label text decision-required | No | Default `0`; semantic behavior still to be specified in SG-30 | None yet | Decision required |
| relationships.is_secret | Visibility toggle for relationship secrecy (SG-30) | Reference schema default `0`; SG-30 requires unauthorized-view behavior | Code contract (boolean semantics) | Novel-owned row | boolean (`0`/`1`) | `0` (false), `1` (true) | UI label text decision-required | No | Default `0`; secrecy visibility semantics must be specified in SG-30 | None yet | Decision required |
| chapters.status | Chapter lifecycle/status (SG-40) | Reference schema default `outline`; SG-40 requires complete status decision | Decision required | Novel-owned row (`chapters.novel_id`) | lowercase `snake_case` string (if closed set) | Only default known: `outline` | Decision required | Decision required | Default `outline` is not a complete allowed set | None yet | Decision required |
| scenes.scene_type | Scene type classification (SG-40) | Reference schema column; SG-40 requires complete scene_type decision | Decision required | Novel-owned row (`scenes.novel_id`) | lowercase `snake_case` string (if closed set) | None | Decision required | Decision required | Nullable; no default | None yet | Decision required |
| scenes.status | Scene lifecycle/status (SG-40) | Reference schema default `idea`; SG-40 requires complete scene status decision | Decision required | Novel-owned row (`scenes.novel_id`) | lowercase `snake_case` string (if closed set) | Only default known: `idea` | Decision required | Decision required | Default `idea` is not a complete allowed set | None yet | Decision required |
| scenes.purpose | Scene purpose narrative (SG-40) | Reference schema column | Free text | Novel-owned row (`scenes.novel_id`) | n/a (free text) | None (open text) | Author-entered text | Yes | Nullable; no default | None yet | Approved |
| characters_scenes.appearance_role | Scene appearance role key (SG-41) | Reference schema default `supporting`; SG-41 requires complete machine-key list | Decision required | Novel-owned via scene and character ownership | lowercase `snake_case` string (if closed set) | Only default known: `supporting` | Decision required | Decision required | Default `supporting` is not a complete allowed set | None yet | Decision required |
| story_threads.thread_type | Thread classification (SG-50) | Reference schema default `subplot`; SG-50 requires complete set | Decision required | Novel-owned row (`story_threads.novel_id`) | lowercase `snake_case` string (if closed set) | Only default known: `subplot` | Decision required | Decision required | Default `subplot` is not a complete allowed set | None yet | Decision required |
| story_threads.status | Thread lifecycle/status (SG-50) | Reference schema default `planned`; SG-50 requires complete set | Decision required | Novel-owned row (`story_threads.novel_id`) | lowercase `snake_case` string (if closed set) | Only default known: `planned` | Decision required | Decision required | Default `planned` is not a complete allowed set | None yet | Decision required |
| plot_points.plot_type | Plot-point type (SG-51) | Reference schema default `custom`; SG-51 requires complete set/catalog decision | Decision required | Novel-owned row (`plot_points.novel_id`) | lowercase `snake_case` string (if closed set) | Only default known: `custom` | Decision required | Decision required | Default `custom` is not a complete allowed set | None yet | Decision required |
| plot_points.is_resolved | Plot-point resolved-state toggle (SG-51) | Reference schema default `0`; SG-51 requires consistency rules with `resolved_scene_id` | Code contract (boolean semantics) | Novel-owned row (`plot_points.novel_id`) | boolean (`0`/`1`) | `0` (false), `1` (true) | UI label text decision-required | No | Default `0`; transition semantics unresolved until SG-51 rules are approved | None yet | Decision required |
| notes.note_type | Note type key (SG-60) | Reference schema default `general`; SG-60 requires complete list or explicit free-text decision | Decision required | Novel-owned row (`notes.novel_id`) | lowercase `snake_case` string (if keyed) | Only default known: `general` | Decision required | Decision required | Default `general` is not a complete allowed set | None yet | Decision required |
| assets.media_type | File media type metadata (SG-61) | Reference schema column; SG-61 states MIME/media type is file metadata, not user-facing catalog | Free text metadata | Novel-owned row (`assets.novel_id`) | MIME token format (RFC-style string) | None fixed in repository | Rendered from stored metadata | Yes (validated by upload rules, not catalog keys) | Nullable; no default | None yet (to be validated in SG-61 upload path) | Approved |
| assets_cards.purpose | Attachment purpose key (SG-61) | Reference schema default `reference`; SG-61 requires approved complete list | Decision required | Novel-owned via `asset_id/card_id` ownership joins | lowercase `snake_case` string (if closed set) | Only default known: `reference` | Decision required | Decision required | Default `reference` is not a complete allowed set | None yet | Decision required |

## Planned Registry Metadata (Non-Persisted)

These values are not table columns, but SG-01 requires them to be recorded as
planned metadata and not mistaken for implemented subtype infrastructure.

| Registry field | Source evidence | Purpose | Decision status |
| --- | --- | --- | --- |
| `CardTypeRegistry[character].table = Characters` | `src/Domain/Registry/CardTypeRegistry.php` | Planned subtype table mapping metadata | Approved as metadata only |
| `CardTypeRegistry[location].table = Locations` | `src/Domain/Registry/CardTypeRegistry.php` | Planned subtype table mapping metadata | Approved as metadata only |
| `CardTypeRegistry[item].table = Items` | `src/Domain/Registry/CardTypeRegistry.php` | Planned subtype table mapping metadata | Approved as metadata only |
| `CardTypeRegistry[organization].table = Organizations` | `src/Domain/Registry/CardTypeRegistry.php` | Planned subtype table mapping metadata | Approved as metadata only |
| `CardTypeRegistry[character].route = characters` | `src/Domain/Registry/CardTypeRegistry.php` | Planned route metadata | Approved as metadata only |
| `CardTypeRegistry[location].route = locations` | `src/Domain/Registry/CardTypeRegistry.php` | Planned route metadata | Approved as metadata only |
| `CardTypeRegistry[item].route = items` | `src/Domain/Registry/CardTypeRegistry.php` | Planned route metadata | Approved as metadata only |
| `CardTypeRegistry[organization].route = organizations` | `src/Domain/Registry/CardTypeRegistry.php` | Planned route metadata | Approved as metadata only |

## Unresolved Decision Blockers by Slice

- Current baseline/future filters: `cards.importance` allowed values and the
  conflicting `normal`/`supporting` defaults
- SG-25: `organizations.organization_type`, `character_organizations.status`
- SG-30: `relationships.relationship_type` ownership and key set;
  boolean semantics for `is_reciprocal` and `is_secret`
- SG-40: `chapters.status`, `scenes.scene_type`, `scenes.status`
- SG-41: `characters_scenes.appearance_role`
- SG-50: `story_threads.thread_type`, `story_threads.status`
- SG-51: `plot_points.plot_type`; resolved-state semantics for
  `plot_points.is_resolved`
- SG-60: `notes.note_type`
- SG-61: `assets_cards.purpose`
