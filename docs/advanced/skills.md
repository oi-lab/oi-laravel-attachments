---
title: AI Assistant Skills
description: Give AI coding assistants context about this package
section: advanced
order: 5
---

# AI Assistant Skills

When you work with an AI coding assistant (Claude Code, JetBrains Junie) in a project that uses oi-laravel-attachments, the assistant benefits from knowing how the package is meant to be used — that models opt in with `HasAttachments`, that uploads go through the action classes, and that model classes should be resolved via `OiLaravelAttachments`.

The package ships a canonical skill file that communicates this context, plus an Artisan command that installs it into your application.

## Installing the skill

The recommended way to install skills is the unified `oi:skills` command (provided by `oi-lab/oi-laravel-development`). It discovers the skills declared by every installed `oi-lab/*` package and lets you pick which ones to install through an interactive multiselect picker, with a choice of project (`.claude` + `.junie`) or global (`~/.claude`) scope:

```bash
php artisan oi:skills
```

To install only this package's skill non-interactively, pass its name and a scope:

```bash
php artisan oi:skills oilab-laravel-attachments --project
# or, for the global ~/.claude scope:
php artisan oi:skills oilab-laravel-attachments --global
```

This copies the canonical stub at `resources/stubs/ai-skill.md` to:

- `.claude/skills/oilab-laravel-attachments/SKILL.md` (Claude Code)
- `.junie/skills/oilab-laravel-attachments/SKILL.md` (JetBrains Junie)

It also adds an `=== oi-lab/oi-laravel-attachments rules ===` section to your project's `CLAUDE.md` (creating the file if needed). Re-running the command refreshes that section in place rather than duplicating it.

> A package-local command `php artisan oi-attachments:install-ai-skill` is still available for projects that don't use `oi-lab/oi-laravel-development`, but it is **deprecated** in favor of `oi:skills`.

### Keeping it up to date automatically

To refresh the skill whenever the package is updated, add the command to your application's `composer.json`:

```json
"scripts": {
    "post-autoload-dump": [
        "@php artisan oi:skills oilab-laravel-attachments --project --quiet"
    ]
}
```

> This requires the `oi-lab/oi-laravel-development` package, which provides the `oi:skills` command.

### Publishing only the skill file

If you only want the skill file without touching `CLAUDE.md`, publish it via the standard vendor publish mechanism:

```bash
php artisan vendor:publish --tag=oi-laravel-attachments-skill
```

> Note: `composer sync-ai-skills` is a **package-development** helper that syncs the stub into this package's own `.claude` / `.junie` directories. It must be run from inside the package repository — consuming applications should use `php artisan oi:skills` instead.

## What the skill tells the assistant

The skill file instructs the assistant to:

- Add the `HasAttachments` trait to make a model attachable, and use its attach / detach / sync / reorder API.
- Use `StoreUploadedFile` and `AttachUploadedFiles` for uploads rather than building `File` records by hand.
- Resolve model classes through `OiLaravelAttachments` (`fileModel()`, `folderModel()`, …) so host-app overrides keep working.
- Read `File::metadata` as a `FileMetadataValueObject` instead of raw JSON.

## Customizing the skill

The source of truth is `resources/stubs/ai-skill.md`. Edit it and re-run `php artisan oi:skills` to propagate changes to both assistant directories. The generated `SKILL.md` files are overwritten on each run, so make your edits in the stub, not the targets.
