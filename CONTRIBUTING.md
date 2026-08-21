# Contributing

## Goal of this template

Keep this repository focused on being a **reliable starting point** for new CakePHP 5 applications in GitHub Codespaces.

## Good changes

- Improve Codespaces reliability
- Improve first-run success rate
- Improve docs for setup, testing, and AI workflows
- Keep defaults simple and understandable
- Prefer reproducible commands over clever shortcuts

## Avoid

- Project-specific business logic
- Hard-coding organization-specific plugins into the generic template
- Adding heavyweight services unless they are broadly useful
- Making the initial experience depend on hidden manual steps

## Before committing changes

Check these:

```bash
composer install
vendor/bin/phpunit
bin/cake
```

If working in Codespaces, also check:

```bash
php -v
composer --version
mysql -h db -u cake -pcake -e 'SHOW DATABASES;'
```

## Recommended commit style

Use small commits with clear messages such as:

- `Improve Codespaces docs`
- `Add MySQL-friendly app_local example`
- `Add AI workflow documentation`
- `Fix devcontainer PHP path ordering`
