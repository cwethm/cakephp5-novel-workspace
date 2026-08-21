# Copilot and AI coding instructions for this repository

This repository is a **CakePHP 5 application template** intended for use with GitHub Codespaces.

## Main goals

- Keep the template reusable across many CakePHP apps.
- Prefer simple, dependable defaults over project-specific customization.
- Preserve a clean separation between:
  - application code
  - plugin code
  - environment setup
  - testing setup
  - documentation

## Environment assumptions

- PHP 8.3
- MySQL 8.4 service in Codespaces
- CakePHP 5.x app skeleton already present in the repo
- Xdebug available but off by default
- PHPUnit available for tests

## When making changes

Prefer:

- updating docs alongside config changes
- making setup steps reproducible in terminal commands
- keeping `config/app_local.example.php` aligned with `.devcontainer/devcontainer.json`
- preserving a clean first-run path in Codespaces

Avoid:

- rewriting the template around organization-specific app logic
- assuming plugins like LemonTools or Workbench are always present
- adding hidden environment dependencies not documented in `README.codespaces.md`

## Coding preferences

- Use CakePHP conventions where possible.
- Keep scripts readable and explicit.
- Prefer MySQL defaults for both `default` and `test` datasource examples in this template.
- Do not silently switch tests to SQLite unless the docs and config both say so.

## AI task guidance

Good tasks for AI help in this repo:

- improving setup reliability
- writing or refining README instructions
- adding VS Code tasks
- improving test bootstrapping
- reviewing devcontainer config
- proposing plugin integration patterns without hard-coding them into the generic template
