---
title: Overview
description: How to attach, upload, and organize files
section: usage
order: 1
---

# Usage

The package centers on the `HasAttachments` trait. Add it to any model and you gain a full attachment API; pair it with the upload actions to store files, and the `Folder` model to organize them.

## The HasAttachments trait

```php
use Illuminate\Database\Eloquent\Model;
use OiLab\OiLaravelAttachments\Concerns\HasAttachments;

class Product extends Model
{
    use HasAttachments;
}
```

This trait adds:

| Method | Purpose |
|--------|---------|
| `attachFile($file, $collection, $sort)` | Attach a single file |
| `detachFile($file, $collection)` | Remove a file from the model |
| `syncAttachments($files, $collection)` | Replace a whole collection |
| `syncAttachmentsIfChanged($files, $collection)` | Replace only when ids/order differ |
| `reorderAttachments($order, $collection)` | Update sort positions |
| `attachments($collection)` | `MorphMany` relation, ordered by sort |
| `singleAttachment($collection)` | `MorphOne` relation for single-file collections |
| `attached_files` | Accessor returning a `Collection<File>` |

## In this section

- [Attachments](attachments.md) — attach, detach, sync, and reorder files
- [Uploads](uploads.md) — store uploaded files with the action classes
- [Files & Folders](files-and-folders.md) — work with the `File` and `Folder` models
