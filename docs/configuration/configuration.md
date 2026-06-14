---
title: Configuration
description: Configure models, storage disk, and audit user
section: configuration
order: 1
---

# Configuration

Publish the configuration file to customize the package:

```bash
php artisan vendor:publish --tag=oi-laravel-attachments-config
```

This creates `config/oi-laravel-attachments.php`:

```php
return [
    'user_model' => 'App\Models\User',

    'models' => [
        'file' => OiLab\OiLaravelAttachments\Models\File::class,
        'folder' => OiLab\OiLaravelAttachments\Models\Folder::class,
        'attachment' => OiLab\OiLaravelAttachments\Models\Attachment::class,
    ],

    'disk' => env('ATTACHMENTS_DISK', env('FILESYSTEM_DISK', 'local')),

    'directory' => 'uploads',
];
```

## Options

| Key | Default | Description |
|-----|---------|-------------|
| `user_model` | `App\Models\User` | Model used for the `created_by` / `updated_by` audit relationships |
| `models.file` | `File::class` | The File model class |
| `models.folder` | `Folder::class` | The Folder model class |
| `models.attachment` | `Attachment::class` | The Attachment pivot model class |
| `disk` | `env('ATTACHMENTS_DISK', env('FILESYSTEM_DISK', 'local'))` | Storage disk for uploads; defaults to `ATTACHMENTS_DISK`, then the app's `FILESYSTEM_DISK` |
| `directory` | `uploads` | Directory uploaded files are stored under on the disk |

## Storage Disk

Uploads are stored on the configured disk. Set `ATTACHMENTS_DISK` in your `.env` to target a specific disk; when it is not defined, the package falls back to the application's `FILESYSTEM_DISK`:

```dotenv
# Use a dedicated disk for attachments...
ATTACHMENTS_DISK=s3

# ...or leave it unset to reuse the app's default disk
FILESYSTEM_DISK=local
```

The disk is resolved at runtime through `OiLaravelAttachments::disk()`, so any Flysystem-backed disk (local, S3, etc.) works without further changes.

## Audit User

Both `File`, `Folder`, and `Attachment` automatically record the authenticated user in `created_by` on creation and `updated_by` on update, via the `HasCreatorAndUpdater` trait. The related user model is whatever you set in `user_model`. The relationships are exposed as:

```php
$file->createdByUser; // BelongsTo<User>
$file->updatedByUser; // BelongsTo<User>
```

When no user is authenticated, these columns stay `null`.

## Overriding Models

Every package model can be swapped for your own subclass — see [Custom Models](../advanced/custom-models.md).
