# Template Maintenance

## Keep these areas aligned

When updating the template, check consistency across:

- `.devcontainer/devcontainer.json`
- `.devcontainer/compose.yaml`
- `.devcontainer/Dockerfile`
- `config/app_local.example.php`
- `README.md`
- `README.codespaces.md`
- `.vscode/tasks.json`

## When CakePHP updates

Review:

- minimum supported PHP version
- current `cakephp/app` skeleton structure
- testing defaults
- DebugKit expectations

## When Codespaces behavior changes

Review:

- `devcontainer.json` format
- default PHP path behavior
- extension recommendations
- Docker Compose support and syntax

## Suggested release discipline

For the template repo:

1. make config/doc updates in a branch
2. create a fresh test repo from the template
3. open that test repo in Codespaces
4. verify first-run success
5. then merge changes back into the template repo

## Keep the template generic

Do not hard-wire:

- organization-specific plugins
- production secrets
- project-specific migrations
- custom business rules

Document plugin integration patterns separately instead.
