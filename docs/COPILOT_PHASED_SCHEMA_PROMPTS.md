# Copilot Prompt Pack: Phased Schema and Structural Data

## Purpose

This prompt pack constrains Copilot to one migration-backed vertical slice at a
time. It prevents the reference SQL schema from becoming an untracked second
schema authority and prevents later-phase tables, values, or UI from leaking
into an earlier development phase.

The current application migration is the Phase 0/1 authority. The file
`config/schema/novel_factory_cakephp5_schema.sql` is a future-state reference
only. It must not be imported into the normal development or test databases.

## Current repository baseline

Re-audit this section against the checked-out branch before every slice. At
this document revision:

- SG-00's migration-only setup, schema ledger, and phase schema test exist.
  Treat SG-00 as an audit/correction task, not as permission to recreate files
  or code that are already present.
- `docs/STRUCTURAL_DATA_CATALOG.md` does not exist yet. That is the expected
  SG-01 deliverable, so every SG-20+ slice remains blocked until SG-01 is
  completed and approved.
- `tests/bootstrap.php` builds the test schema with
  `Migrations\TestSuite\Migrator`. `tests/schema.sql` is an unused placeholder;
  do not populate or load it as a second schema authority.
- Both `default` and `test` are MySQL datasources configured through
  `config/app_local.example.php` and environment variables. Do not substitute
  SQLite or invent another datasource for slice verification.
- The devcontainer and CI select PHP 8.3. The current lockfile selects PHPUnit
  13.3.1, which requires PHP 8.4.1. This is a repository-level verification
  blocker that must be resolved in a separate dependency-maintenance change;
  do not change PHP, PHPUnit, Composer constraints, or the lockfile inside an
  SG feature slice merely to make checks run.
- `CurrentNovel::assertContains()` only handles entities with a direct
  `novel_id`. Card subtype rows inherit tenancy through `cards.novel_id`; do
  not add duplicate `novel_id` columns to subtype tables or call
  `assertContains()` on them without implementing a correctly scoped lookup.
- `CardTypeRegistry` contains future `table` and `route` metadata for all four
  card types. Those strings are declarations, not evidence that the
  corresponding tables, controllers, routes, or templates already exist.
- "Assets" in SG-61 means the future `assets` and `assets_cards` domain tables.
  It does not mean CakePHP's static files under `webroot/css`, `webroot/js`,
  `webroot/img`, or `webroot/font`.

## Reference-only future table inventory

At Phase 0/1, every table below is forbidden. This is the canonical list that
the phase ledger and phase schema test must carry forward, removing only the
tables introduced by an approved, completed slice:

- `characters`
- `character_appearances`
- `character_personalities`
- `character_voices`
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

## How to use this pack

1. Start a fresh Copilot coding task for each slice.
2. Paste the **Mandatory execution contract** followed by exactly one slice
   prompt.
3. Do not ask Copilot to implement multiple slices in one task.
4. Merge and verify one slice before starting the next.
5. If a prompt reaches a stop condition, answer the reported decision before
   asking Copilot to continue.
6. Never tell Copilot to "finish the remaining schema", "prepare future
   phases", or "add anything else useful."

## Slice schedule

| Order | Development phase | Slice | Schema introduced | First consumer |
| --- | --- | --- | --- | --- |
| 0 | Phase 0/1 correction | SG-00 | No new domain tables | Devcontainer setup and CI |
| 1 | Phase 1 planning gate | SG-01 | No schema changes | Structural-data decisions |
| 2 | Phase 2: story world | SG-20 | `characters` | Character identity CRUD |
| 3 | Phase 2: story world | SG-21 | `character_appearances`, `character_personalities`, `character_voices` | Character profile editor |
| 4 | Phase 2: story world | SG-22 | `character_traits`, `character_skills`, `character_goals` | Repeatable character details |
| 5 | Phase 2: story world | SG-23 | `locations` | Location CRUD and hierarchy |
| 6 | Phase 2: story world | SG-24 | `items` | Item CRUD and ownership/location links |
| 7 | Phase 2: story world | SG-25 | `organizations`, `character_organizations` | Organization CRUD and membership |
| 8 | Phase 3: graph | SG-30 | `relationships` and an approved type catalog only if required | Relationship editor |
| 9 | Phase 4: manuscript | SG-40 | `chapters`, `scenes` | Chapter/scene CRUD and ordering |
| 10 | Phase 4: manuscript | SG-41 | `characters_scenes`, `items_scenes`, `organizations_scenes` | Scene participation editor |
| 11 | Phase 5: plotting | SG-50 | `story_threads`, `scenes_story_threads` | Story-thread planner |
| 12 | Phase 5: plotting | SG-51 | `plot_points`, `plot_points_story_threads`, `characters_plot_points` | Plot-point planner |
| 13 | Phase 6: supporting content | SG-60 | `notes` | Contextual notes |
| 14 | Phase 6: supporting content | SG-61 | `assets`, `assets_cards` | Asset upload and attachment |
| 15 | Phase 7: interchange | SG-70 | No schema changes | Import/export ADR and approved design |
| 16 | Phase 7: interchange | SG-71 | Only tables approved by SG-70 | Import job engine |
| 17 | Phase 7: interchange | SG-72 | No speculative schema | Markdown import/export UI |

The order is mandatory. Do not start a slice while any earlier slice is
unmerged or failing.

## Structural-data ownership rules

Every value used by the data layer or interactive behavior must have one
declared owner. Do not mix these categories.

### Code-owned machine contracts

Use a PHP registry, enum, or domain constant when the set is finite and an
application release must define its meaning. Examples include `card_type`,
lifecycle statuses, permission keys, event names, and internal operation
states.

Rules:

- Persist a stable lowercase `snake_case` key.
- Never use a display label as a database key.
- Keep labels, icons, routes, and presentation metadata outside business rows.
- Validate writes through the owning registry.
- Reject unknown keys.
- Do not add a database catalog merely to populate a select control.

### Database-owned system catalogs

Use a catalog table only when values require database identity, foreign keys,
ordering, translation, metadata, activation, or administrator extension.

Rules:

- Use an immutable unique string `key`; never depend on numeric IDs in code.
- Add only fields required by the approved behavior.
- Required rows are installed by a versioned, idempotent data migration.
- Upsert required rows by key.
- Do not silently rename or delete a key that existing business rows may use.
- Do not create a generic EAV or universal `value_sets` table unless an
  approved ADR explicitly requires it.

### Novel-owned editable values

Use novel-scoped rows for tags, categories, genres, or templates that authors
may edit.

Rules:

- Copy defaults when a novel is created; do not make novels depend on mutable
  global rows.
- Record a stable source key or template version only when reconciliation is
  an approved requirement.
- Never overwrite author edits during an upgrade.

### Fixtures and demonstration content

Sample novels, characters, categories, and showcase data are development
fixtures or explicit seeds.

Rules:

- Never install demo content in a production migration.
- Never run demo seeds from devcontainer startup.
- Required system data is not a seed; it belongs in a tracked data migration.

## Mandatory execution contract

Paste this block before exactly one slice prompt.

```text
You are implementing exactly one approved Novel Workspace schema slice.

OPERATING RULES

1. Read these files before proposing or editing anything:
   - README.md
   - CONTRIBUTING.md
   - .github/copilot-instructions.md
   - docs/COPILOT_PHASED_SCHEMA_PROMPTS.md
   - docs/SCHEMA_PHASES.md
   - the current CakePHP migrations
   - config/schema/novel_factory_cakephp5_schema.sql
   - composer.json, composer.lock, tests/bootstrap.php, phpunit.xml.dist, and
     .github/workflows/ci.yml when the slice requires executable checks
   - every model, registry, service, controller, template, and test directly
     related to this slice
   Treat the checked-out repository and this prompt as the only authorities.
   Do not rely on or update agent memory as a substitute for repository
   evidence.
   Use repository search to confirm that every named file, class, route,
   datasource, service, or setting actually exists before planning around it.

2. Begin with a short repository-grounded plan containing:
   - the exact slice ID
   - current migration head
   - for an executable slice, current PHP version and Composer lock platform
     compatibility in the configured devcontainer or CI environment
   - prerequisite tables and features verified as present
   - exact tables, columns, indexes, foreign keys, structural values, model
     classes, routes, UI, fixtures, and tests you intend to change
   - the complete allowed file list for this task
   - an explicit list of later-phase components that you will not touch
   After presenting the plan, edit only the listed files. If another file
   becomes necessary, stop and explain why before changing it.
   If the requested result is already present, do not manufacture a diff;
   report the evidence and any remaining acceptance gap.

3. Stop before editing if:
   - a prerequisite slice is absent
   - the working tree contains unrelated changes
   - an executable slice is requested and the configured PHP runtime cannot
     install or execute composer.lock
   - the approved machine keys or catalog values required by this slice are
     undefined or contradictory
   - the requested behavior requires a schema or architectural decision not
     authorized by this prompt
   - the reference SQL conflicts with current ownership, CurrentNovel, or
     security rules

4. CakePHP migrations are the only normal schema authority.
   - Never import, source, parse, split, or execute the reference SQL.
   - Never modify the reference SQL as part of a feature slice.
   - Never add raw SQL import logic to setup, tests, CI, Composer scripts, or
     application code.
   - Never use CREATE TABLE IF NOT EXISTS to hide drift.
   - Never edit an already-merged migration to add this slice.
   - For a schema-changing slice, add exactly one forward DDL migration. Add a
     separate data migration only when approved required system rows exist.
   - For SG-00, SG-01, SG-70, and any other explicitly design-only/no-schema
     slice, add no migration.

5. Keep the slice vertical and complete.
   - For an implementation slice, add only schema consumed by behavior
     implemented in that slice.
   - For an implementation slice, add the CakePHP entity/table associations,
     validation, build rules, service behavior, authorization/CurrentNovel
     scoping, controller action, template surface, and tests required by the
     slice.
   - In an implementation slice, account for disabled FactoryLocator fallback
     classes: add explicit Table classes for new tables and explicit entity
     mass-assignment allowlists.
   - In an implementation slice, follow the existing authenticated route +
     `currentNovel()` ownership pattern. Do not assume Authorization policies
     exist for domain entities, and do not introduce a new authorization
     architecture unless the slice explicitly requires and approves it.
   - In a subtype implementation slice, scope subtype entities through their
     associated Card. Subtype tables do not gain a duplicate `novel_id`.
   - Do not create placeholder tables, classes, routes, templates, services,
     repositories, feature flags, APIs, or TODO scaffolding for later slices.

6. Structural data must follow the ownership rules in the prompt pack.
   - Do not invent value sets.
   - Do not use labels for machine decisions.
   - Do not hard-code catalog numeric IDs.
   - Do not create demo rows in migrations.
   - If an approved value list is missing, stop and report the exact decision
     needed instead of guessing.

7. Security and tenancy are mandatory.
   - Derive novel ownership from route context and authenticated server-side
     lookup.
   - Never trust posted novel_id, user_id, card_id, or other ownership keys.
   - Return 404 for foreign-owned resources where current behavior does so.
   - Validate that every cross-table association belongs to the same novel.
   - Keep explicit mass-assignment allowlists.

8. Schema change discipline:
   - New populated-table columns start nullable or with a safe compatible
     default.
   - Backfill before adding strict constraints.
   - Add indexes and foreign keys only for behavior in this slice.
   - Do not drop or rename existing data-bearing columns in the same release
     that introduces a replacement.

9. Tests and evidence:
   - Before running executable checks, run `php -v` and
     `composer check-platform-reqs --lock`. If the lockfile is incompatible
     with the configured runtime, stop and report that prerequisite blocker;
     do not change dependencies inside the slice.
   - For schema-changing slices, test a clean migration and an upgrade from the
     immediately previous slice using CI or a clearly isolated disposable test
     database. Never empty, drop, or repurpose a developer's application
     database for acceptance testing.
   - Respect the existing migration-driven test bootstrap. Do not load
     `tests/schema.sql`, duplicate the schema in fixtures, or add a second test
     schema path.
   - For a schema-changing slice, update the phase schema test so the slice's
     tables are required and all remaining tables from the reference-only
     future table inventory remain forbidden. A no-schema gate must not change
     schema expectations.
   - For an implementation slice, add behavior, validation, ownership,
     cross-novel rejection, and service tests appropriate to the slice.
   - For an implementation slice, run the repository's existing
     `composer cs-check`, `composer stan`, and `composer test` commands only
     after platform requirements pass.
   - Do not weaken or delete tests to obtain a pass.

10. Scope control:
    - Do not change dependencies, framework versions, devcontainer services,
      authentication architecture, ownership architecture, unrelated UI, or
      formatting outside touched code.
    - Do not perform broad refactors.
    - Do not add "helpful" future work.
    - Do not continue into the next slice.

FINAL RESPONSE

Report only:
- files changed
- migrations added
- structural keys/catalog rows added
- tests run and their results
- explicit confirmation that no later-phase tables were added
- unresolved decisions or deviations

If any required check fails, do not describe the slice as complete.
```

---

## SG-00 — Restore migration-only setup

```text
SLICE ID: SG-00
DEVELOPMENT PHASE: Phase 0/1 schema-authority correction

OBJECTIVE

Make CakePHP migrations the only schema authority for normal development,
test, and CI setup. Do not add or change domain schema.

REQUIRED CHANGES

1. In .devcontainer/setup.sh:
   - verify writable-directory preparation remains
   - verify Composer installation remains
   - verify MySQL readiness handling remains
   - verify test-database creation and grants remain
   - verify application migrations run
   - verify test-connection migrations run
   - remove SCHEMA_FILE if present
   - remove import_schema() if present
   - remove any reference-table marker query if present
   - remove sed-based SQL rewriting if present
   - remove all raw reference-schema imports if present
   - allow required setup failures to stop the script
   Do not recreate obsolete import identifiers merely so they can be removed.

2. Do not delete config/schema/novel_factory_cakephp5_schema.sql. Document it
   as a non-executable future-state reference.

3. Add docs/SCHEMA_PHASES.md containing:
   - current phase: Phase 0/1
   - current migration head
   - required domain tables: users, novels, cards, tags, cards_tags
   - every table in the reference-only future table inventory forbidden at
     this phase
   - the rule that each later slice adds a new migration and updates this
     ledger

4. Add or correct one database integration test that:
   - obtains the test connection schema collection
   - asserts the five Phase 0/1 domain tables exist
   - asserts every table in the reference-only future table inventory does not
     exist
   - tolerates framework-owned migration metadata tables without treating
     them as domain tables
   - relies on the migration runner already configured in tests/bootstrap.php

5. Update setup documentation only where it currently implies or performs the
   full SQL import.

FORBIDDEN

- No domain migration changes.
- No new domain tables or columns.
- No reference database profile in this slice.
- No automatic database or volume deletion.
- No shell command that drops user data.
- No changes to authentication or application behavior.

ACCEPTANCE

- A clean app database contains only migration-managed Phase 0/1 schema.
- A clean test database contains only migration-managed Phase 0/1 schema.
- Every table in the reference-only future table inventory is absent.
- Re-running setup is idempotent through migration history, not table markers.
- The existing PHP 8.3/Composer lock platform check passes before PHPUnit is
  claimed as verification evidence. If it does not pass, SG-00 remains
  verification-blocked and dependency repair is reported as separate work.
```

## SG-01 — Approve structural-data decisions

```text
SLICE ID: SG-01
DEVELOPMENT PHASE: Phase 1 planning gate

OBJECTIVE

Create a decision ledger for every value-bearing field in the reference
schema before later feature work begins. This is documentation-only.

REQUIRED CHANGES

Create docs/STRUCTURAL_DATA_CATALOG.md. Inventory every current or planned
field that represents a status, type, category, role, purpose, proficiency,
importance, lifecycle state, machine operation, or interactive option. Search
the current migrations and runtime validation/registries first, then the
future-state reference schema.

For each field record:
- table.column
- consuming feature and scheduled slice
- source evidence: current migration, current runtime code, ADR, or reference
  schema only
- ownership: code contract, system catalog, novel-owned value, or free text
- tenancy scope
- stable machine-key format
- approved keys, if already defined by repository sources
- display-label source
- whether custom values are allowed
- default and inactive/deprecated behavior
- database constraint or application validation owner
- decision status: approved or decision required

Use only values already present in current code, migrations, ADRs, or explicit
schema comments. Do not invent missing lists. When the current application and
reference SQL disagree, record both values and mark the decision unresolved;
the reference SQL never overrides the current migration or runtime code.

KNOWN SOURCES TO RECORD

- user status: active, disabled
- novel status: planning, drafting, revising, complete, archived
- card type: character, location, item, organization
- card status: active, archived
- card importance: current migration default normal versus future reference
  default supporting; no complete allowed set exists, so this is decision
  required
- character trait types explicitly listed in the reference schema comment
- defaults explicitly present in the reference schema

Any field with only a default but no complete allowed set remains "decision
required."

`CardTypeRegistry` route and table metadata must be recorded as planned
metadata. Do not cite it as proof that subtype routes, tables, controllers, or
templates are implemented.

FORBIDDEN

- No PHP changes.
- No migration changes.
- No data insertion.
- No generic catalog framework.
- No UI.
- No invented values.

ACCEPTANCE

- Every value-bearing field in the full reference schema is represented.
- Every value-bearing field already enforced or defaulted by the current
  migration/runtime is represented, including current/reference conflicts.
- Undefined choices are clearly listed as blockers for their scheduled slice.
- The document distinguishes machine keys from display labels.
```

## Shared grounding for story-world subtype slices

Apply these rules in addition to the selected slice prompt:

- `characters`, `locations`, `items`, and `organizations` are one-to-one
  extensions of `cards`; they do not have their own `novel_id`.
- Resolve ownership by joining through the associated Card and checking
  `cards.novel_id` against CurrentNovel.
- Phase 0/1 already permits Cards with any `CardTypeRegistry` key, so matching
  Cards may exist before `characters`, `locations`, `items`, or `organizations`
  is introduced. SG-20, SG-23, SG-24, and SG-25 must define and test a safe
  explicit path to initialize the missing root subtype row. Do not silently
  create business rows in a data migration.
- New root subtype creation must not leave a Card committed without its
  required subtype row. Use one transaction.
- SG-21 and SG-22 rows inherit tenancy through Character and then Card; they
  also do not gain `novel_id`.
- Registry `table` and `route` strings are planned metadata until the selected
  slice implements and tests the corresponding structures.

## SG-20 — Character identity

```text
SLICE ID: SG-20
DEVELOPMENT PHASE: Phase 2 story-world cards
PREREQUISITES: SG-00 and SG-01

OBJECTIVE

Implement the Character subtype identity as a one-to-one extension of an
existing card whose card_type is character.

SCHEMA

Add only the characters table and its approved columns, unique card_id,
indexes, timestamps, and foreign key to cards. Reconcile column types with
current CakePHP conventions instead of copying raw SQL blindly.

STRUCTURAL DATA

- character remains the code-owned CardTypeRegistry key
- role remains free text unless SG-01 contains an approved closed or catalog
  decision
- do not add tables for roles, gender, pronouns, occupations, cultures, or
  religions without an approved decision

BEHAVIOR

- character add/view/edit through novel-scoped routes
- create the card and character rows atomically
- allow an existing character Card without a subtype row to initialize its
  character details through an explicit, authorized action
- reject a non-character card_id
- reject foreign-novel cards
- prevent more than one character row for a card

EXCLUDED

- appearance, personality, voice, traits, skills, goals
- locations, items, organizations, relationships
- scenes and plotting
```

## SG-21 — Character profile sections

```text
SLICE ID: SG-21
DEVELOPMENT PHASE: Phase 2 story-world cards
PREREQUISITE: SG-20

OBJECTIVE

Add the three optional one-to-one character profile sections consumed by the
character editor.

SCHEMA

Add only:
- character_appearances
- character_personalities
- character_voices

Each table must have one row at most per character, a unique character_id, a
foreign key with cascade delete, and timestamps.

STRUCTURAL DATA

Profile fields are descriptive data. Do not create catalogs for physical
attributes, vocabulary, education, accent, dialect, culture, religion, or
neurotype unless SG-01 explicitly approves one.

BEHAVIOR

- edit each section through the existing novel-scoped character surface
- create missing section rows only when submitted
- save the character and edited section transactionally
- reject foreign-novel character IDs

EXCLUDED

- repeatable traits, skills, goals
- any other subtype or graph/manuscript table
```

## SG-22 — Character repeatable details

```text
SLICE ID: SG-22
DEVELOPMENT PHASE: Phase 2 story-world cards
PREREQUISITE: SG-21

OBJECTIVE

Add repeatable traits, skills, and goals with ordered character-scoped CRUD.

SCHEMA

Add only:
- character_traits
- character_skills
- character_goals

Use the reference foreign keys and ordering concepts, adapted to current
conventions.

STRUCTURAL DATA

- trait_type is a code-owned machine contract using only the approved keys in
  SG-01
- goal_type and goal status require an approved complete key list before
  coding; stop if SG-01 still marks either decision required
- proficiency remains free text unless SG-01 approves a code list or catalog
- labels presented in forms come from registry metadata, not stored business
  values

BEHAVIOR

- add/edit/delete/reorder within one character
- server-side ownership checks for every row
- explicit validation of machine keys
- no cross-character or cross-novel reassignment through posted IDs

EXCLUDED

- generic taxonomy/value-set framework
- locations, items, organizations, relationships, scenes
```

## SG-23 — Locations

```text
SLICE ID: SG-23
DEVELOPMENT PHASE: Phase 2 story-world cards
PREREQUISITE: SG-20

OBJECTIVE

Implement Location as a card subtype with optional same-novel hierarchy.

SCHEMA

Add only locations with a unique card_id, nullable parent_location_id,
approved descriptive/geographic fields, indexes, foreign keys, and timestamps.

STRUCTURAL DATA

location_type must follow the SG-01 decision. Stop before editing if its
ownership or allowed values remain undecided.

BEHAVIOR

- novel-scoped location add/view/edit
- atomic card plus location creation
- explicit initialization path for an existing location Card
- parent selection limited to the same novel
- reject self-parenting and hierarchy cycles
- reject non-location cards and foreign-novel IDs

EXCLUDED

- maps, geocoding providers, external APIs
- items, organizations, scene location selection
```

## SG-24 — Items

```text
SLICE ID: SG-24
DEVELOPMENT PHASE: Phase 2 story-world cards
PREREQUISITES: SG-20 and SG-23

OBJECTIVE

Implement Item as a card subtype with optional character owner and current
location.

SCHEMA

Add only items with unique card_id, nullable owner_character_id, nullable
current_location_id, approved descriptive fields, indexes, foreign keys, and
timestamps.

STRUCTURAL DATA

item_type must follow the approved SG-01 decision. is_unique is boolean
business data, not a value set.

BEHAVIOR

- novel-scoped item add/view/edit
- atomic card plus item creation
- explicit initialization path for an existing item Card
- owner and location choices limited to the same novel
- reject non-item cards and cross-novel associations

EXCLUDED

- inventories, quantities, transfers, audit history
- scene participation
```

## SG-25 — Organizations and membership

```text
SLICE ID: SG-25
DEVELOPMENT PHASE: Phase 2 story-world cards
PREREQUISITES: SG-20 and SG-23

OBJECTIVE

Implement Organization as a card subtype and character membership as a rich
junction.

SCHEMA

Add only:
- organizations
- character_organizations

Keep headquarters nullable and same-novel. Enforce one membership row per
character/organization pair.

STRUCTURAL DATA

- organization_type must follow SG-01
- membership status requires an approved complete list
- membership role remains descriptive unless explicitly approved otherwise
- stop before editing if required values remain undecided

BEHAVIOR

- novel-scoped organization CRUD
- atomic card plus organization creation and an explicit initialization path
  for an existing organization Card
- same-novel headquarters selection
- add/edit/end/remove character memberships
- reject all cross-novel associations

EXCLUDED

- organization hierarchy, permissions, teams, scenes, relationship graph
```

## SG-30 — Relationship graph

```text
SLICE ID: SG-30
DEVELOPMENT PHASE: Phase 3 relationship graph
PREREQUISITE: SG-25

OBJECTIVE

Implement typed relationships between two cards in the same novel.

SCHEMA

Add relationships with direct novel_id, source_card_id, and target_card_id as
shown by the reference design, adapted to current migration conventions. Add a
relationship-type catalog table only if SG-01 explicitly approved database
ownership; otherwise use an approved code registry and persist its stable key.

STRUCTURAL DATA

The complete relationship_type list and ownership must be approved before
coding. Do not invent family, social, conflict, or organizational types.

BEHAVIOR

- create/edit/archive or delete according to an approved lifecycle
- both endpoints must belong to CurrentNovel
- reject self-relationships unless explicitly approved
- validate strength/trust/hostility ranges
- define and test reciprocal semantics without auto-creating a reverse row
  unless explicitly approved
- keep secret relationships out of unauthorized views

EXCLUDED

- visualization libraries, graph databases, inferred relationships
- chapters, scenes, plotting
```

## SG-40 — Chapters and scenes

```text
SLICE ID: SG-40
DEVELOPMENT PHASE: Phase 4 manuscript structure
PREREQUISITES: SG-20 and SG-23

OBJECTIVE

Implement ordered chapters and scenes, including optional POV character and
location links.

Chapters and scenes are direct novel-owned structures, not Cards. Preserve
ADR-002 and do not add card_id or CardTypeRegistry entries for them.

SCHEMA

Add only:
- chapters
- scenes

Keep chapter_id, pov_character_id, and location_id nullable. Add only indexes
needed by novel/chapter ordering and approved filters.

STRUCTURAL DATA

Complete chapter status, scene status, and scene_type decisions must be
approved in SG-01 before coding. Word count is derived numeric data, not a
catalog value.

BEHAVIOR

- novel-scoped chapter and scene CRUD
- deterministic ordering and reorder operations
- same-novel POV character and location validation
- word-count update through one tested service path
- no posted novel_id trust

EXCLUDED

- scene participants beyond POV/location
- rich-text editor packages
- story threads, plot points, import/export
```

## SG-41 — Scene participation

```text
SLICE ID: SG-41
DEVELOPMENT PHASE: Phase 4 manuscript structure
PREREQUISITES: SG-24, SG-25, and SG-40

OBJECTIVE

Add character, item, and organization participation to scenes.

SCHEMA

Add only:
- characters_scenes
- items_scenes
- organizations_scenes

STRUCTURAL DATA

appearance_role requires an approved complete machine-key list. Stop if SG-01
contains only the reference default and no complete list.

BEHAVIOR

- attach/detach/reorder participants where ordering exists
- prevent duplicate pairs
- validate that scene and participant belong to CurrentNovel
- never accept cross-novel posted IDs

EXCLUDED

- automatic participant extraction from manuscript text
- story threads and plot points
```

## SG-50 — Story threads

```text
SLICE ID: SG-50
DEVELOPMENT PHASE: Phase 5 plotting
PREREQUISITE: SG-40

OBJECTIVE

Implement story threads and ordered links between threads and scenes.

SCHEMA

Add only:
- story_threads
- scenes_story_threads

STRUCTURAL DATA

Complete thread_type and thread status sets must be approved before coding.
The defaults subplot and planned do not constitute complete allowed lists.

BEHAVIOR

- novel-scoped story-thread CRUD
- optional start/end scenes limited to the same novel
- attach/detach/reorder scenes within a thread
- validate start/end ordering according to the approved rule

EXCLUDED

- plot points
- automatic thread detection
- timeline visualization dependencies
```

## SG-51 — Plot points

```text
SLICE ID: SG-51
DEVELOPMENT PHASE: Phase 5 plotting
PREREQUISITES: SG-20, SG-40, and SG-50

OBJECTIVE

Implement ordered plot points and links to scenes, story threads, and
characters.

SCHEMA

Add only:
- plot_points
- plot_points_story_threads
- characters_plot_points

STRUCTURAL DATA

plot_type requires an approved complete set or approved database catalog.
The default custom alone is not a complete list.

BEHAVIOR

- novel-scoped plot-point CRUD and ordering
- same-novel introduced/resolved scene validation
- same-novel story-thread and character attachment
- consistent is_resolved and resolved_scene_id rules

EXCLUDED

- beat-sheet generators, AI plot suggestions, inferred links
```

## SG-60 — Contextual notes

```text
SLICE ID: SG-60
DEVELOPMENT PHASE: Phase 6 supporting content
PREREQUISITES: SG-20 and SG-40

OBJECTIVE

Implement notes attachable to a novel and optionally one approved context.

SCHEMA

Add only notes. Keep card_id, scene_id, and chapter_id nullable.

STRUCTURAL DATA

note_type requires an approved complete list or explicit free-text decision.
The default general alone is not a complete list.

BEHAVIOR

- novel-scoped note CRUD
- all optional targets must belong to CurrentNovel
- enforce the approved target cardinality rule; do not guess whether a note
  may target multiple contexts simultaneously
- explicit mass-assignment boundaries

EXCLUDED

- comments, collaboration, mentions, notifications, version history
```

## SG-61 — Assets

```text
SLICE ID: SG-61
DEVELOPMENT PHASE: Phase 6 supporting content
PREREQUISITE: SG-20

OBJECTIVE

Implement novel-owned asset metadata and card attachments.

This slice concerns uploaded-file domain records. Do not modify or migrate
CakePHP's static `webroot` assets as part of this slice.

SCHEMA

Add only:
- assets
- assets_cards

STRUCTURAL DATA

attachment purpose requires an approved complete list. The default reference
alone is not a complete list. MIME/media type is file metadata, not a
user-facing category catalog.

BEHAVIOR

- the current Phase 0/1 repository has no uploaded-file storage abstraction,
  provider configuration, or storage service; stop for an approved storage
  ADR/decision unless one has been added by an earlier approved change
- novel-scoped upload metadata and deletion rules
- same-novel card attachment
- validate size, media type, path handling, and authorization
- do not expose storage paths directly

EXCLUDED

- cloud-provider dependencies without approval
- image transformation pipeline, galleries, CDN, virus-scanner integration
```

## SG-70 — Import/export architecture gate

```text
SLICE ID: SG-70
DEVELOPMENT PHASE: Phase 7 interchange
PREREQUISITE: SG-61

OBJECTIVE

Produce an ADR and implementation-ready schema proposal for import/export.
This slice is design-only because the current reference schema defines no
import job, mapping, provenance, retry, or error tables.

REQUIRED DESIGN DECISIONS

- supported source formats and first supported Markdown structure
- dry-run behavior
- transaction boundary
- idempotency key and duplicate handling
- source record provenance
- stable external identifiers
- field mapping ownership
- validation-error storage
- partial failure policy
- resume/retry policy
- cancellation policy
- ownership and CurrentNovel derivation
- whether uploaded source files reuse assets
- export scope and deterministic ordering
- machine job states and user-visible labels
- retention and deletion policy

DELIVERABLE

Add one ADR containing:
- proposed tables and columns
- dependency diagram
- code-owned job state keys
- any database-owned catalogs
- state transition table
- security constraints
- phased implementation plan for SG-71 and SG-72
- alternatives rejected

FORBIDDEN

- No migrations.
- No models.
- No commands, queues, controllers, routes, templates, or placeholders.
- No dependency additions.
- No implementation until the ADR is reviewed and approved.
```

## SG-71 — Import job engine

```text
SLICE ID: SG-71
DEVELOPMENT PHASE: Phase 7 interchange
PREREQUISITE: Approved SG-70 ADR

OBJECTIVE

Implement only the approved import job, source record, mapping, provenance,
and error infrastructure required for a non-interactive dry run.

SCHEMA

Use exactly the approved SG-70 table and column names. Do not add convenience
tables or fields not present in the approved ADR.

STRUCTURAL DATA

- job states and operation keys are code-owned machine contracts
- source formats and mapping profiles follow the ADR ownership decision
- no user-facing labels are used for state transitions

BEHAVIOR

- novel-scoped import job creation
- parse and validate without mutating domain records in dry-run mode
- deterministic idempotency and provenance
- bounded error recording without storing secrets or unsafe raw content
- tested state transitions and retry rules

EXCLUDED

- interactive field-mapping UI
- background queue provider unless approved by SG-70
- Markdown domain writes
- export
```

## SG-72 — Markdown import and manuscript export

```text
SLICE ID: SG-72
DEVELOPMENT PHASE: Phase 7 interchange
PREREQUISITE: SG-71

OBJECTIVE

Implement the first approved Markdown import path and deterministic
Markdown/plain-text manuscript export.

SCHEMA

Add no schema unless the approved SG-70 ADR explicitly schedules it for this
slice. If new schema appears necessary, stop and amend the ADR first.

STRUCTURAL DATA

Parser tokens and operation states are machine contracts. User field mappings
follow the approved ownership model. Do not infer new categories or types from
unrecognized source text without an explicit mapping.

BEHAVIOR

- dry run before commit
- explicit user confirmation before domain mutation
- transactional writes at the ADR-defined boundary
- preserve source provenance and report rejected records
- enforce CurrentNovel ownership on every created association
- deterministic chapter/scene export order
- escape and encode output safely

EXCLUDED

- additional file formats
- AI-assisted parsing
- automatic conflict resolution
- cloud publishing integrations
```

## Completion rules

For a schema-changing feature slice, the database is considered aligned only
when:

1. all migrations through that slice are applied;
2. required structural machine keys and catalog rows are installed;
3. the first consuming behavior exists and is tested;
4. the phase schema test requires the slice's tables;
5. every later-slice table remains forbidden; and
6. a clean install and an upgrade from the previous slice converge on the same
   schema and required structural data.

For documentation/design gates such as SG-01 and SG-70, completion instead
requires the named document, every listed decision or explicit blocker, review
approval, and no migration or runtime implementation.

SG-00 is complete only when migration-only setup and the exhaustive Phase 0/1
boundary test are present. A platform-incompatible dependency lock blocks its
test-verification claim but does not authorize an unrelated dependency change
inside SG-00.

Schema without a consumer is premature. A consumer without migration-tracked
schema and structural data is incomplete.
