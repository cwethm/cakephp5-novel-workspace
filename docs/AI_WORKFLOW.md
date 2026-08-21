# AI Workflow for This Template

## Good use of AI in this repo

Use AI to:

- explain CakePHP conventions in unfamiliar files
- review configuration changes before committing them
- write tests for controllers, services, and behaviors
- improve setup documentation
- generate migration boilerplate and fixture scaffolds
- review repetitive refactors

## Use GitHub Copilot for

- autocomplete
- small local edits
- quick boilerplate
- inline documentation improvements

## Use Codex for

- larger multi-file changes
- implementation plans
- test generation and repair
- config review
- “explain the repo” tasks
- refactor proposals

## Good prompts

- "Review this CakePHP controller and suggest missing tests."
- "Write a CakePHP integration test for this action."
- "Explain how this datasource config interacts with Codespaces."
- "Propose a plugin integration pattern without hard-coding plugin assumptions into the generic template."

## Grounding tips

Before asking an AI agent to make changes:

- point it at the exact files involved
- tell it whether you are in the generic template repo or a real generated app repo
- tell it whether the repo already contains the full CakePHP app skeleton
- tell it whether it should preserve Codespaces compatibility

## Safety note

Do not let AI silently replace environment-specific credentials or hide setup steps. Ask it to keep docs and config aligned.
