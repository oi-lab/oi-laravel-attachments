---
title: Custom Models
description: Swap in your own File, Folder, or Attachment classes
section: advanced
order: 1
---

# Custom Models

Every model the package uses can be replaced with your own subclass. This lets you add relationships, scopes, accessors, or business logic without forking the package.

## How resolution works

The package never references its model classes directly. Instead, it resolves them through the `OiLaravelAttachments` helper:

```php
use OiLab\OiLaravelAttachments\OiLaravelAttachments;

OiLaravelAttachments::fileModel();        // config('oi-laravel-attachments.models.file')
OiLaravelAttachments::folderModel();      // config('oi-laravel-attachments.models.folder')
OiLaravelAttachments::attachmentModel();  // config('oi-laravel-attachments.models.attachment')
OiLaravelAttachments::userModel();        // config('oi-laravel-attachments.user_model')
```

Relationships, factories, traits, and actions all go through these helpers, so overriding a model in config takes effect everywhere.

## Overriding a model

1. Subclass the package model:

```php
namespace App\Models;

use OiLab\OiLaravelAttachments\Models\File as BaseFile;

class File extends BaseFile
{
    public function thumbnailUrl(): string
    {
        return Storage::disk($this->storage)->url($this->filename_disk);
    }
}
```

2. Point the config at your class:

```php
// config/oi-laravel-attachments.php
'models' => [
    'file' => App\Models\File::class,
],
```

That's it — `$product->attached_files`, the `Attachment::file()` relation, and the upload actions all return your subclass.

## When writing your own code

Always resolve through `OiLaravelAttachments` rather than hardcoding the package class, so your code respects any host-app override:

```php
// Good
$model = OiLaravelAttachments::fileModel();
$file = $model::find($id);

// Avoid — ignores config overrides
$file = \OiLab\OiLaravelAttachments\Models\File::find($id);
```
