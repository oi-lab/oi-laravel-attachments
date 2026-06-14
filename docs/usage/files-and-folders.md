---
title: Files & Folders
description: Work with the File and Folder models
section: usage
order: 4
---

# Files & Folders

## The File model

`File` represents a stored file and its metadata. Beyond the database columns it offers helpers for type detection, storage access, and search.

### Type helpers

```php
$file->isImage(); // mimetype starts with image/
$file->isVideo(); // mimetype starts with video/
$file->isAudio(); // mimetype starts with audio/
```

### Reading the file

```php
// Absolute filesystem path — local disks only, throws on remote disks
$path = $file->getFullPath();

// Stream resource — works with local and remote disks (S3, etc.)
$stream = $file->getStream();
```

### Searching

The `search` scope matches against `filename_disk`, `filename_download`, `title`, and `description`:

```php
File::search('invoice')->get();
```

### Relationships

```php
$file->folder;      // BelongsTo<Folder>
$file->attachments; // HasMany<Attachment>, ordered by sort
```

## File metadata

The `metadata` column is cast to a `FileMetadataValueObject`, so you read structured data instead of decoding JSON:

```php
$file->metadata->width;
$file->metadata->height;
$file->metadata->aspect_ratio;
$file->metadata->color_space;
$file->metadata->color_profile;
$file->metadata->bit_depth;

$file->metadata->resolution; // ResolutionValueObject|null (DPI)
$file->metadata->exif;       // ExifValueObject|null
$file->metadata->iptc;       // IptcValueObject|null
```

See [File Metadata](../advanced/file-metadata.md) for the full structure.

## The Folder model

`Folder` is an optional self-nesting tree for organizing files:

```php
use OiLab\OiLaravelAttachments\Models\Folder;

$root = Folder::create(['name' => 'Invoices']);
$year = Folder::create(['name' => '2026', 'parent_id' => $root->id]);
```

### Relationships

```php
$folder->parent;   // BelongsTo<Folder>
$folder->children; // HasMany<Folder>
$folder->files;    // HasMany<File>
```

A file is placed in a folder by setting its `folder_id`:

```php
$file->update(['folder_id' => $year->id]);
```

## Soft deletes

Both `File` and `Folder` use soft deletes, so deleting a record keeps the row (and its history) while hiding it from queries. The `attachments` pivot rows are removed via the database `cascadeOnDelete` on `file_id` when a file is force-deleted.
