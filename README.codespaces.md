# Codespaces Quick Start

## First launch

After creating a repository from the template and opening it in Codespaces:

1. Wait for the container build to finish.
2. Confirm PHP and Composer:

```bash
which php
php -v
which composer
composer --version
```

3. Confirm MySQL is reachable:

```bash
mysql -h db -u cake -pcake -e 'SHOW DATABASES;'
```

4. Install dependencies if needed:

```bash
composer install
```

5. Run migrations:

```bash
bin/cake migrations migrate
bin/cake migrations migrate --connection test
```

6. Run tests:

```bash
vendor/bin/phpunit
```

7. Start the dev server:

```bash
bin/cake server -H 0.0.0.0 -p 8765
```

## Ports

- `8765` — CakePHP app
- `3306` — MySQL

## Debugging

To enable Xdebug in the Codespace:

1. Edit `.devcontainer/devcontainer.json`
2. Change:

```json
"XDEBUG_MODE": "off"
```

To:

```json
"XDEBUG_MODE": "debug,develop"
```

3. Rebuild the container.
4. Start the VS Code `Listen for Xdebug` configuration.

## If PHP is wrong in PATH

This template forces the container-managed PHP path to the front of `PATH` using `remoteEnv` in `.devcontainer/devcontainer.json`.

If you still get an unexpected PHP binary, rebuild the Codespace after confirming the devcontainer files are present.
