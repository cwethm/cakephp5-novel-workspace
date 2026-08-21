# First Hour Checklist

## After creating a repo from the template

- [ ] Open the repo in Codespaces.
- [ ] Wait for the devcontainer build to finish.
- [ ] Run `php -v`.
- [ ] Run `composer --version`.
- [ ] Run `mysql -h db -u cake -pcake -e 'SHOW DATABASES;'`.
- [ ] Run `composer install`.
- [ ] Copy `config/app_local.example.php` to `config/app_local.php` if needed.
- [ ] Run `bin/cake migrations migrate`.
- [ ] Run `bin/cake migrations migrate --connection test`.
- [ ] Run `vendor/bin/phpunit`.
- [ ] Run `bin/cake server -H 0.0.0.0 -p 8765`.

## If the app does not boot

Check these first:

```bash
which php
php -v
which composer
composer --version
bin/cake
```

## If DebugKit warns about SQLite

This template installs `pdo_sqlite` in the devcontainer so DebugKit can work without extra steps.

## If DebugKit warns about safeTld

Make sure `config/app_local.php` includes a `DebugKit.safeTld` entry for the hostname/TLD you are using.
