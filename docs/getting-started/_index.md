---
title: Introduction
description: Discover OI Laravel Attachments and what it can do for your project
section: getting-started
order: 1
---

# OI Laravel Attachments

OI Laravel Attachments adds polymorphic file attachments to any Eloquent model. Instead of building a bespoke `*_files` pivot for every model that needs uploads, you add a single trait and gain a complete attach / detach / sync / reorder API — backed by a rich `File` model and an optional `Folder` tree.

## Why use this package?

Most Laravel apps eventually need "this model can have files." Rolling that by hand means a pivot table, ordering logic, metadata extraction, and audit columns — repeated for every model. This package centralizes all of it:

- One `HasAttachments` trait makes **any** model attachable.
- Files are grouped into named **collections** per model (e.g. `gallery`, `cover`).
- Each attachment has a **sort** order with reorder/move/swap helpers.
- The `File` model stores **metadata** (dimensions, MIME, MD5, EXIF, IPTC, color).
- Files can optionally be organized into a nested **folder** tree.

## The three models

| Model | Role |
|-------|------|
| `File` | A stored file and its metadata, optionally inside a `Folder` |
| `Folder` | A self-nesting container for files (`parent_id` tree) |
| `Attachment` | Polymorphic pivot linking a `File` to any `attachable` model |

A host model never references `Attachment` directly — it uses the `HasAttachments` trait, which manages the pivot for you.

## What it looks like

```php
use OiLab\OiLaravelAttachments\Concerns\HasAttachments;

class Product extends Model
{
    use HasAttachments;
}

$product->attachFile($file, collection: 'gallery');
$product->attached_files; // Collection<File>
```

## Requirements

- PHP 8.2+
- Laravel 11, 12, or 13

## Next steps

Follow the [Installation](installation.md) guide to add the package to your project.
