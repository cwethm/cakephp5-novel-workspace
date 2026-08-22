# Novel Workspace (CakePHP 5)

Production-minded Phase 0/1 foundation for a Novel Factory-style writing workspace.

## Requirements

- PHP 8.3+
- MySQL 8
- Composer

## Install

```bash
composer install
cp config/app_local.example.php config/app_local.php
```

## Database setup

Default local databases:

- `cakephp_app` (development)
- `cakephp_test` (test)

Configure credentials in `config/app_local.php` (or env overrides used by that file).

## Migrations

```bash
composer migrate
composer migrate:test
```

## Run app

```bash
bin/cake server -H 0.0.0.0 -p 8765
```

## Testing

```bash
composer test
```

## Static analysis

```bash
composer stan
```

## Coding standards

```bash
composer cs-check
composer cs-fix
```

## Current scope

Implemented foundation:

- Authentication (login/logout)
- User-owned Novels with ownership scoping (404 on foreign access)
- CurrentNovel server-side context
- Cards + Tags + `cards_tags`
- Card type registry
- Slug and card workflow services
- Migrations, tests, and CI pipeline

Later-phase features (subtype tables, relationships, chapters/scenes, plot, import/export) are intentionally deferred.
