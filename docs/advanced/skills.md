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

Run the Artisan command once after adding the package:

```bash
php artisan oi:install-ai-skill
```

This copies the canonical stub at `resources/stubs/ai-skill.md` to:

- `.claude/skills/oilab-laravel-attachments/SKILL.md` (Claude Code)
- `.junie/skills/oilab-laravel-attachments/SKILL.md` (JetBrains Junie)

It also adds an `=== oi-lab/oi-laravel-attachments rules ===` section to your project's `CLAUDE.md` (creating the file if needed). Re-running the command refreshes that section in place rather than duplicating it.

### Keeping it up to date automatically

To refresh the skill whenever the package is updated, add the command to your application's `composer.json`:

```json
"scripts": {
    "post-autoload-dump": [
        "@php artisan oi:install-ai-skill --quiet"
    ]
}
```

### Publishing only the skill file

If you only want the skill file without touching `CLAUDE.md`, publish it via the standard vendor publish mechanism:

```bash
php artisan vendor:publish --tag=oi-laravel-attachments-skill
```

> Note: `composer sync-ai-skills` is a **package-development** helper that syncs the stub into this package's own `.claude` / `.junie` directories. It must be run from inside the package repository — consuming applications should use `php artisan oi:install-ai-skill` instead.

## What the skill tells the assistant

The skill file instructs the assistant to:

- Add the `HasAttachments` trait to make a model attachable, and use its attach / detach / sync / reorder API.
- Use `StoreUploadedFile` and `AttachUploadedFiles` for uploads rather than building `File` records by hand.
- Resolve model classes through `OiLaravelAttachments` (`fileModel()`, `folderModel()`, …) so host-app overrides keep working.
- Read `File::metadata` as a `FileMetadataValueObject` instead of raw JSON.

## Customizing the skill

The source of truth is `resources/stubs/ai-skill.md`. Edit it and re-run `php artisan oi:install-ai-skill` to propagate changes to both assistant directories. The generated `SKILL.md` files are overwritten on each run, so make your edits in the stub, not the targets.
