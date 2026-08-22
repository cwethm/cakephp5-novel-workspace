-- Novel Factory-style CakePHP 5 application
-- MySQL 8.x / MariaDB-compatible baseline
-- CakePHP conventions: plural table names, singular *_id foreign keys,
-- alphabetically named pure junction tables.

CREATE DATABASE IF NOT EXISTS novel_factory
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE novel_factory;

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- --------------------------------------------------------------------------
-- NOVELS
-- --------------------------------------------------------------------------

CREATE TABLE novels (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    subtitle VARCHAR(255) NULL,
    author_name VARCHAR(255) NULL,
    description TEXT NULL,
    status VARCHAR(32) NOT NULL DEFAULT 'planning',
    created DATETIME NOT NULL,
    modified DATETIME NOT NULL,
    PRIMARY KEY (id),
    KEY idx_novels_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------------
-- CARDS
--
-- Shared identity/metadata for story-world objects.
-- Type-specific records use card_id as a one-to-one foreign key.
-- --------------------------------------------------------------------------

CREATE TABLE cards (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    novel_id BIGINT UNSIGNED NOT NULL,
    card_type VARCHAR(32) NOT NULL,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    short_summary TEXT NULL,
    description LONGTEXT NULL,
    importance VARCHAR(32) NOT NULL DEFAULT 'supporting',
    status VARCHAR(32) NOT NULL DEFAULT 'active',
    sort_order INT NOT NULL DEFAULT 0,
    created DATETIME NOT NULL,
    modified DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_cards_novel_slug (novel_id, slug),
    KEY idx_cards_novel_type (novel_id, card_type),
    KEY idx_cards_novel_status (novel_id, status),
    KEY idx_cards_novel_sort (novel_id, sort_order),
    CONSTRAINT fk_cards_novels
        FOREIGN KEY (novel_id) REFERENCES novels (id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------------
-- CHARACTERS
-- --------------------------------------------------------------------------

CREATE TABLE characters (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    card_id BIGINT UNSIGNED NOT NULL,
    role VARCHAR(64) NULL,
    aliases TEXT NULL,
    age INT NULL,
    birth_date VARCHAR(100) NULL,
    gender VARCHAR(100) NULL,
    pronouns VARCHAR(100) NULL,
    occupation VARCHAR(255) NULL,
    education LONGTEXT NULL,
    backstory LONGTEXT NULL,
    external_motivation LONGTEXT NULL,
    internal_motivation LONGTEXT NULL,
    core_motivation LONGTEXT NULL,
    central_conflict LONGTEXT NULL,
    family_notes LONGTEXT NULL,
    friendship_notes LONGTEXT NULL,
    culture_notes LONGTEXT NULL,
    religion_notes LONGTEXT NULL,
    created DATETIME NOT NULL,
    modified DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_characters_card_id (card_id),
    KEY idx_characters_role (role),
    CONSTRAINT fk_characters_cards
        FOREIGN KEY (card_id) REFERENCES cards (id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE character_appearances (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    character_id BIGINT UNSIGNED NOT NULL,
    height VARCHAR(64) NULL,
    weight VARCHAR(64) NULL,
    build VARCHAR(128) NULL,
    hair_color VARCHAR(128) NULL,
    hair_style TEXT NULL,
    eye_color VARCHAR(128) NULL,
    skin_description TEXT NULL,
    facial_features TEXT NULL,
    scars TEXT NULL,
    clothing_style TEXT NULL,
    health TEXT NULL,
    created DATETIME NOT NULL,
    modified DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_character_appearances_character_id (character_id),
    CONSTRAINT fk_character_appearances_characters
        FOREIGN KEY (character_id) REFERENCES characters (id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE character_personalities (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    character_id BIGINT UNSIGNED NOT NULL,
    public_self LONGTEXT NULL,
    private_self LONGTEXT NULL,
    greatest_fear LONGTEXT NULL,
    greatest_desire LONGTEXT NULL,
    wants LONGTEXT NULL,
    needs LONGTEXT NULL,
    response_to_praise LONGTEXT NULL,
    response_to_conflict LONGTEXT NULL,
    competitiveness LONGTEXT NULL,
    neurotype_notes LONGTEXT NULL,
    created DATETIME NOT NULL,
    modified DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_character_personalities_character_id (character_id),
    CONSTRAINT fk_character_personalities_characters
        FOREIGN KEY (character_id) REFERENCES characters (id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE character_voices (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    character_id BIGINT UNSIGNED NOT NULL,
    vocabulary_level VARCHAR(128) NULL,
    education_level VARCHAR(255) NULL,
    accent VARCHAR(255) NULL,
    dialect VARCHAR(255) NULL,
    speaking_style LONGTEXT NULL,
    cultural_influences LONGTEXT NULL,
    religious_influences LONGTEXT NULL,
    created DATETIME NOT NULL,
    modified DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_character_voices_character_id (character_id),
    CONSTRAINT fk_character_voices_characters
        FOREIGN KEY (character_id) REFERENCES characters (id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Flexible repeatable character attributes:
-- strength, weakness, habit, bad_habit, fear, want, need, secret, personality.
CREATE TABLE character_traits (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    character_id BIGINT UNSIGNED NOT NULL,
    trait_type VARCHAR(32) NOT NULL,
    name VARCHAR(255) NULL,
    description TEXT NOT NULL,
    sort_order INT NOT NULL DEFAULT 0,
    created DATETIME NOT NULL,
    modified DATETIME NOT NULL,
    PRIMARY KEY (id),
    KEY idx_character_traits_character_type (character_id, trait_type),
    CONSTRAINT fk_character_traits_characters
        FOREIGN KEY (character_id) REFERENCES characters (id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE character_skills (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    character_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    proficiency VARCHAR(64) NULL,
    sort_order INT NOT NULL DEFAULT 0,
    created DATETIME NOT NULL,
    modified DATETIME NOT NULL,
    PRIMARY KEY (id),
    KEY idx_character_skills_character (character_id),
    CONSTRAINT fk_character_skills_characters
        FOREIGN KEY (character_id) REFERENCES characters (id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE character_goals (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    character_id BIGINT UNSIGNED NOT NULL,
    goal_type VARCHAR(32) NOT NULL DEFAULT 'external',
    description LONGTEXT NOT NULL,
    priority INT NOT NULL DEFAULT 0,
    status VARCHAR(32) NOT NULL DEFAULT 'active',
    created DATETIME NOT NULL,
    modified DATETIME NOT NULL,
    PRIMARY KEY (id),
    KEY idx_character_goals_character_status (character_id, status),
    CONSTRAINT fk_character_goals_characters
        FOREIGN KEY (character_id) REFERENCES characters (id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------------
-- LOCATIONS
-- --------------------------------------------------------------------------

CREATE TABLE locations (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    card_id BIGINT UNSIGNED NOT NULL,
    parent_location_id BIGINT UNSIGNED NULL,
    location_type VARCHAR(64) NULL,
    address TEXT NULL,
    region VARCHAR(255) NULL,
    country VARCHAR(255) NULL,
    latitude DECIMAL(10,7) NULL,
    longitude DECIMAL(10,7) NULL,
    atmosphere LONGTEXT NULL,
    appearance LONGTEXT NULL,
    climate LONGTEXT NULL,
    culture LONGTEXT NULL,
    history LONGTEXT NULL,
    created DATETIME NOT NULL,
    modified DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_locations_card_id (card_id),
    KEY idx_locations_parent (parent_location_id),
    CONSTRAINT fk_locations_cards
        FOREIGN KEY (card_id) REFERENCES cards (id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_locations_parent
        FOREIGN KEY (parent_location_id) REFERENCES locations (id)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------------
-- ITEMS
-- --------------------------------------------------------------------------

CREATE TABLE items (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    card_id BIGINT UNSIGNED NOT NULL,
    item_type VARCHAR(64) NULL,
    owner_character_id BIGINT UNSIGNED NULL,
    current_location_id BIGINT UNSIGNED NULL,
    appearance LONGTEXT NULL,
    history LONGTEXT NULL,
    significance LONGTEXT NULL,
    capabilities LONGTEXT NULL,
    is_unique TINYINT(1) NOT NULL DEFAULT 0,
    created DATETIME NOT NULL,
    modified DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_items_card_id (card_id),
    KEY idx_items_owner_character (owner_character_id),
    KEY idx_items_current_location (current_location_id),
    CONSTRAINT fk_items_cards
        FOREIGN KEY (card_id) REFERENCES cards (id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_items_characters
        FOREIGN KEY (owner_character_id) REFERENCES characters (id)
        ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_items_locations
        FOREIGN KEY (current_location_id) REFERENCES locations (id)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------------
-- ORGANIZATIONS
-- --------------------------------------------------------------------------

CREATE TABLE organizations (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    card_id BIGINT UNSIGNED NOT NULL,
    organization_type VARCHAR(64) NULL,
    purpose LONGTEXT NULL,
    ideology LONGTEXT NULL,
    headquarters_id BIGINT UNSIGNED NULL,
    influence_level INT NULL,
    created DATETIME NOT NULL,
    modified DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_organizations_card_id (card_id),
    KEY idx_organizations_headquarters (headquarters_id),
    CONSTRAINT fk_organizations_cards
        FOREIGN KEY (card_id) REFERENCES cards (id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_organizations_locations
        FOREIGN KEY (headquarters_id) REFERENCES locations (id)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Rich junction: Character membership in an organization.
CREATE TABLE character_organizations (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    character_id BIGINT UNSIGNED NOT NULL,
    organization_id BIGINT UNSIGNED NOT NULL,
    role VARCHAR(255) NULL,
    status VARCHAR(32) NOT NULL DEFAULT 'active',
    joined_at VARCHAR(100) NULL,
    left_at VARCHAR(100) NULL,
    notes LONGTEXT NULL,
    created DATETIME NOT NULL,
    modified DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_character_organizations_pair (character_id, organization_id),
    KEY idx_character_organizations_org (organization_id),
    CONSTRAINT fk_character_organizations_characters
        FOREIGN KEY (character_id) REFERENCES characters (id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_character_organizations_organizations
        FOREIGN KEY (organization_id) REFERENCES organizations (id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------------
-- RELATIONSHIPS
--
-- General graph edge. source_card_id and target_card_id allow character ->
-- character, character -> location, organization -> location, etc.
-- --------------------------------------------------------------------------

CREATE TABLE relationships (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    novel_id BIGINT UNSIGNED NOT NULL,
    source_card_id BIGINT UNSIGNED NOT NULL,
    target_card_id BIGINT UNSIGNED NOT NULL,
    relationship_type VARCHAR(64) NOT NULL,
    label VARCHAR(255) NULL,
    description LONGTEXT NULL,
    strength TINYINT UNSIGNED NULL,
    trust TINYINT UNSIGNED NULL,
    hostility TINYINT UNSIGNED NULL,
    is_reciprocal TINYINT(1) NOT NULL DEFAULT 0,
    is_secret TINYINT(1) NOT NULL DEFAULT 0,
    start_date VARCHAR(100) NULL,
    end_date VARCHAR(100) NULL,
    sort_order INT NOT NULL DEFAULT 0,
    created DATETIME NOT NULL,
    modified DATETIME NOT NULL,
    PRIMARY KEY (id),
    KEY idx_relationships_novel (novel_id),
    KEY idx_relationships_source (source_card_id),
    KEY idx_relationships_target (target_card_id),
    KEY idx_relationships_type (relationship_type),
    CONSTRAINT fk_relationships_novels
        FOREIGN KEY (novel_id) REFERENCES novels (id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_relationships_source_cards
        FOREIGN KEY (source_card_id) REFERENCES cards (id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_relationships_target_cards
        FOREIGN KEY (target_card_id) REFERENCES cards (id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------------
-- CHAPTERS
-- --------------------------------------------------------------------------

CREATE TABLE chapters (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    novel_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    chapter_number INT NULL,
    sort_order INT NOT NULL DEFAULT 0,
    summary LONGTEXT NULL,
    manuscript_text LONGTEXT NULL,
    status VARCHAR(32) NOT NULL DEFAULT 'outline',
    word_count INT UNSIGNED NOT NULL DEFAULT 0,
    created DATETIME NOT NULL,
    modified DATETIME NOT NULL,
    PRIMARY KEY (id),
    KEY idx_chapters_novel_sort (novel_id, sort_order),
    CONSTRAINT fk_chapters_novels
        FOREIGN KEY (novel_id) REFERENCES novels (id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------------
-- SCENES
-- --------------------------------------------------------------------------

CREATE TABLE scenes (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    novel_id BIGINT UNSIGNED NOT NULL,
    chapter_id BIGINT UNSIGNED NULL,
    title VARCHAR(255) NOT NULL,
    scene_number INT NULL,
    sort_order INT NOT NULL DEFAULT 0,
    pov_character_id BIGINT UNSIGNED NULL,
    location_id BIGINT UNSIGNED NULL,
    scene_type VARCHAR(64) NULL,
    story_date VARCHAR(100) NULL,
    story_time VARCHAR(100) NULL,
    summary LONGTEXT NULL,
    purpose LONGTEXT NULL,
    conflict LONGTEXT NULL,
    outcome LONGTEXT NULL,
    opening_state LONGTEXT NULL,
    closing_state LONGTEXT NULL,
    manuscript_text LONGTEXT NULL,
    status VARCHAR(32) NOT NULL DEFAULT 'idea',
    word_count INT UNSIGNED NOT NULL DEFAULT 0,
    created DATETIME NOT NULL,
    modified DATETIME NOT NULL,
    PRIMARY KEY (id),
    KEY idx_scenes_novel_sort (novel_id, sort_order),
    KEY idx_scenes_chapter_sort (chapter_id, sort_order),
    KEY idx_scenes_pov (pov_character_id),
    KEY idx_scenes_location (location_id),
    CONSTRAINT fk_scenes_novels
        FOREIGN KEY (novel_id) REFERENCES novels (id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_scenes_chapters
        FOREIGN KEY (chapter_id) REFERENCES chapters (id)
        ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_scenes_characters
        FOREIGN KEY (pov_character_id) REFERENCES characters (id)
        ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_scenes_locations
        FOREIGN KEY (location_id) REFERENCES locations (id)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Rich scene-character junction because an appearance has a role and ordering.
CREATE TABLE characters_scenes (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    character_id BIGINT UNSIGNED NOT NULL,
    scene_id BIGINT UNSIGNED NOT NULL,
    appearance_role VARCHAR(32) NOT NULL DEFAULT 'supporting',
    sort_order INT NOT NULL DEFAULT 0,
    notes TEXT NULL,
    created DATETIME NOT NULL,
    modified DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_characters_scenes_pair (character_id, scene_id),
    KEY idx_characters_scenes_scene (scene_id),
    CONSTRAINT fk_characters_scenes_characters
        FOREIGN KEY (character_id) REFERENCES characters (id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_characters_scenes_scenes
        FOREIGN KEY (scene_id) REFERENCES scenes (id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE items_scenes (
    item_id BIGINT UNSIGNED NOT NULL,
    scene_id BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (item_id, scene_id),
    KEY idx_items_scenes_scene (scene_id),
    CONSTRAINT fk_items_scenes_items
        FOREIGN KEY (item_id) REFERENCES items (id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_items_scenes_scenes
        FOREIGN KEY (scene_id) REFERENCES scenes (id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE organizations_scenes (
    organization_id BIGINT UNSIGNED NOT NULL,
    scene_id BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (organization_id, scene_id),
    KEY idx_organizations_scenes_scene (scene_id),
    CONSTRAINT fk_organizations_scenes_organizations
        FOREIGN KEY (organization_id) REFERENCES organizations (id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_organizations_scenes_scenes
        FOREIGN KEY (scene_id) REFERENCES scenes (id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------------
-- STORY THREADS
-- --------------------------------------------------------------------------

CREATE TABLE story_threads (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    novel_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    thread_type VARCHAR(64) NOT NULL DEFAULT 'subplot',
    short_summary TEXT NULL,
    description LONGTEXT NULL,
    status VARCHAR(32) NOT NULL DEFAULT 'planned',
    start_scene_id BIGINT UNSIGNED NULL,
    end_scene_id BIGINT UNSIGNED NULL,
    sort_order INT NOT NULL DEFAULT 0,
    created DATETIME NOT NULL,
    modified DATETIME NOT NULL,
    PRIMARY KEY (id),
    KEY idx_story_threads_novel_sort (novel_id, sort_order),
    KEY idx_story_threads_start_scene (start_scene_id),
    KEY idx_story_threads_end_scene (end_scene_id),
    CONSTRAINT fk_story_threads_novels
        FOREIGN KEY (novel_id) REFERENCES novels (id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_story_threads_start_scenes
        FOREIGN KEY (start_scene_id) REFERENCES scenes (id)
        ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_story_threads_end_scenes
        FOREIGN KEY (end_scene_id) REFERENCES scenes (id)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE scenes_story_threads (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    scene_id BIGINT UNSIGNED NOT NULL,
    story_thread_id BIGINT UNSIGNED NOT NULL,
    sort_order INT NOT NULL DEFAULT 0,
    notes TEXT NULL,
    created DATETIME NOT NULL,
    modified DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_scenes_story_threads_pair (scene_id, story_thread_id),
    KEY idx_scenes_story_threads_thread (story_thread_id),
    CONSTRAINT fk_scenes_story_threads_scenes
        FOREIGN KEY (scene_id) REFERENCES scenes (id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_scenes_story_threads_story_threads
        FOREIGN KEY (story_thread_id) REFERENCES story_threads (id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------------
-- PLOT POINTS
-- --------------------------------------------------------------------------

CREATE TABLE plot_points (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    novel_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    plot_type VARCHAR(64) NOT NULL DEFAULT 'custom',
    sequence_order INT NOT NULL DEFAULT 0,
    setup LONGTEXT NULL,
    event LONGTEXT NULL,
    consequence LONGTEXT NULL,
    introduced_scene_id BIGINT UNSIGNED NULL,
    resolved_scene_id BIGINT UNSIGNED NULL,
    is_resolved TINYINT(1) NOT NULL DEFAULT 0,
    created DATETIME NOT NULL,
    modified DATETIME NOT NULL,
    PRIMARY KEY (id),
    KEY idx_plot_points_novel_sequence (novel_id, sequence_order),
    KEY idx_plot_points_introduced_scene (introduced_scene_id),
    KEY idx_plot_points_resolved_scene (resolved_scene_id),
    CONSTRAINT fk_plot_points_novels
        FOREIGN KEY (novel_id) REFERENCES novels (id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_plot_points_introduced_scenes
        FOREIGN KEY (introduced_scene_id) REFERENCES scenes (id)
        ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_plot_points_resolved_scenes
        FOREIGN KEY (resolved_scene_id) REFERENCES scenes (id)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE plot_points_story_threads (
    plot_point_id BIGINT UNSIGNED NOT NULL,
    story_thread_id BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (plot_point_id, story_thread_id),
    KEY idx_plot_points_story_threads_thread (story_thread_id),
    CONSTRAINT fk_plot_points_story_threads_plot_points
        FOREIGN KEY (plot_point_id) REFERENCES plot_points (id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_plot_points_story_threads_story_threads
        FOREIGN KEY (story_thread_id) REFERENCES story_threads (id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE characters_plot_points (
    character_id BIGINT UNSIGNED NOT NULL,
    plot_point_id BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (character_id, plot_point_id),
    KEY idx_characters_plot_points_plot_point (plot_point_id),
    CONSTRAINT fk_characters_plot_points_characters
        FOREIGN KEY (character_id) REFERENCES characters (id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_characters_plot_points_plot_points
        FOREIGN KEY (plot_point_id) REFERENCES plot_points (id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------------
-- TAGS
-- --------------------------------------------------------------------------

CREATE TABLE tags (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    novel_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL,
    created DATETIME NOT NULL,
    modified DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_tags_novel_slug (novel_id, slug),
    CONSTRAINT fk_tags_novels
        FOREIGN KEY (novel_id) REFERENCES novels (id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE cards_tags (
    card_id BIGINT UNSIGNED NOT NULL,
    tag_id BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (card_id, tag_id),
    KEY idx_cards_tags_tag (tag_id),
    CONSTRAINT fk_cards_tags_cards
        FOREIGN KEY (card_id) REFERENCES cards (id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_cards_tags_tags
        FOREIGN KEY (tag_id) REFERENCES tags (id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------------
-- NOTES
-- --------------------------------------------------------------------------

CREATE TABLE notes (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    novel_id BIGINT UNSIGNED NOT NULL,
    card_id BIGINT UNSIGNED NULL,
    scene_id BIGINT UNSIGNED NULL,
    chapter_id BIGINT UNSIGNED NULL,
    title VARCHAR(255) NOT NULL,
    note_type VARCHAR(64) NOT NULL DEFAULT 'general',
    body LONGTEXT NULL,
    sort_order INT NOT NULL DEFAULT 0,
    created DATETIME NOT NULL,
    modified DATETIME NOT NULL,
    PRIMARY KEY (id),
    KEY idx_notes_novel (novel_id),
    KEY idx_notes_card (card_id),
    KEY idx_notes_scene (scene_id),
    KEY idx_notes_chapter (chapter_id),
    CONSTRAINT fk_notes_novels
        FOREIGN KEY (novel_id) REFERENCES novels (id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_notes_cards
        FOREIGN KEY (card_id) REFERENCES cards (id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_notes_scenes
        FOREIGN KEY (scene_id) REFERENCES scenes (id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_notes_chapters
        FOREIGN KEY (chapter_id) REFERENCES chapters (id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------------
-- ASSETS
-- --------------------------------------------------------------------------

CREATE TABLE assets (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    novel_id BIGINT UNSIGNED NOT NULL,
    filename VARCHAR(255) NOT NULL,
    original_filename VARCHAR(255) NULL,
    media_type VARCHAR(100) NULL,
    storage_path VARCHAR(1024) NOT NULL,
    caption TEXT NULL,
    alt_text TEXT NULL,
    file_size BIGINT UNSIGNED NULL,
    created DATETIME NOT NULL,
    modified DATETIME NOT NULL,
    PRIMARY KEY (id),
    KEY idx_assets_novel (novel_id),
    CONSTRAINT fk_assets_novels
        FOREIGN KEY (novel_id) REFERENCES novels (id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Rich junction because an attachment can have a purpose and ordering.
CREATE TABLE assets_cards (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    asset_id BIGINT UNSIGNED NOT NULL,
    card_id BIGINT UNSIGNED NOT NULL,
    purpose VARCHAR(64) NOT NULL DEFAULT 'reference',
    sort_order INT NOT NULL DEFAULT 0,
    created DATETIME NOT NULL,
    modified DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_assets_cards_pair_purpose (asset_id, card_id, purpose),
    KEY idx_assets_cards_card (card_id),
    CONSTRAINT fk_assets_cards_assets
        FOREIGN KEY (asset_id) REFERENCES assets (id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_assets_cards_cards
        FOREIGN KEY (card_id) REFERENCES cards (id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
