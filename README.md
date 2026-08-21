# CakePHP 5 Codespaces Template

A practical starter layout for **CakePHP 5** projects that are developed in **GitHub Codespaces** with:

- PHP 8.3
- MySQL 8.4
- Xdebug
- PHPUnit
- VS Code tasks and launch config
- AI-friendly repo guidance for Copilot and Codex

## What this template is for

Use this repository as a **GitHub template repository** for new CakePHP 5 apps.

The intended workflow is:

1. Create a new repository from this template.
2. Open the new repository in GitHub Codespaces.
3. Let the devcontainer build.
4. Run migrations and tests.
5. Start building your app.

## What should already be in the repo

This template works best when the repo already contains the **real CakePHP app skeleton** as the application base.

That means the generated project repo should include things like:

- `bin/`
- `src/`
- `templates/`
- `webroot/`
- `config/bootstrap.php`
- `config/routes.php`
- `composer.json`

If your repository only contains scaffold files and docs, merge the official CakePHP app skeleton into it **before** turning it into a long-term template.

## Included template support files

This pack focuses on the parts that make a template pleasant to use repeatedly:

- `.devcontainer/` for Codespaces
- `.vscode/` tasks and debugger config
- `config/app_local.example.php` for local MySQL defaults
- `docs/` for setup, AI workflow, and maintenance guidance
- `.github/copilot-instructions.md` for repo-grounded AI coding help

## Suggested project flow

### In GitHub

1. Mark the repo as a **Template repository**.
2. Create a new app repo from it.
3. Open the new repo in Codespaces.

### In the Codespace

```bash
composer install
bin/cake migrations migrate
bin/cake migrations migrate --connection test
vendor/bin/phpunit
bin/cake server -H 0.0.0.0 -p 8765
```

## Database defaults used by this template

- App database: `cakephp_app`
- Test database: `cakephp_test`
- Dev user: `cake`
- Dev password: `cake`

Change these for any environment beyond disposable local or Codespaces development.

## AI usage guidance

This template is designed to work well with:

- GitHub Copilot for inline suggestions and chat
- Codex in a VS Code-compatible editor for larger coding and refactor tasks

See:

- `docs/AI_WORKFLOW.md`
- `.github/copilot-instructions.md`

## Files to review before publishing this as your main template

- `.devcontainer/devcontainer.json`
- `.devcontainer/Dockerfile`
- `.devcontainer/compose.yaml`
- `config/app_local.example.php`
- `.vscode/tasks.json`
- `.github/copilot-instructions.md`
- `docs/TEMPLATE_MAINTENANCE.md`

## Recommended next improvement

Once your current repo is fully working, copy these files into that repo and commit them as a template-improvement pass.
