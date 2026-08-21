# ADR-001 Card + subtype architecture
Cards are stored in a single `cards` table with a `card_type` discriminator validated by `CardTypeRegistry`. Subtype-specific tables are deferred to later phases.
