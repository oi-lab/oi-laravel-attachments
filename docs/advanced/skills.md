---
title: AI Assistant Skills
description: Give AI coding assistants context about this package
section: advanced
order: 5
---

# AI Assistant Skills

When you work with an AI coding assistant (Claude Code, JetBrains Junie) in a project that uses oi-laravel-attachments, the assistant benefits from knowing how the package is meant to be used — that models opt in with `HasAttachments`, that uploads go through the action classes, and that model classes should be resolved via `OiLaravelAttachments`.

The package ships a canonical skill file that communicates this context, plus a script that syncs it into the assistant skill directories.

## Syncing the skill

Run the Composer script once after adding the package:

```bash
composer sync-ai-skills
```

This copies the canonical stub at `resources/stubs/ai-skill.md` to:

- `.claude/skills/oilab-laravel-attachments/skill.md` (Claude Code)
- `.junie/skills/oilab-laravel-attachments/skill.md` (JetBrains Junie)

The sync also runs automatically on `post-autoload-dump`, so the skill files stay in step with the installed package version after every `composer install` / `composer update`.

## What the skill tells the assistant

The skill file instructs the assistant to:

- Add the `HasAttachments` trait to make a model attachable, and use its attach / detach / sync / reorder API.
- Use `StoreUploadedFile` and `AttachUploadedFiles` for uploads rather than building `File` records by hand.
- Resolve model classes through `OiLaravelAttachments` (`fileModel()`, `folderModel()`, …) so host-app overrides keep working.
- Read `File::metadata` as a `FileMetadataValueObject` instead of raw JSON.

## Customizing the skill

The source of truth is `resources/stubs/ai-skill.md`. Edit it and re-run `composer sync-ai-skills` to propagate changes to both assistant directories. The generated `skill.md` files are overwritten on each sync, so make your edits in the stub, not the targets.
