---
title: Events
description: Hook into attachment and upload actions with Laravel events
section: advanced
order: 4
---

# Events

The package dispatches an event for each meaningful action, so you can react to attachments and uploads without overriding the package internals. All events live in the `OiLab\OiLaravelAttachments\Events` namespace and are plain, immutable data objects (readonly promoted properties).

## Available events

| Event | Dispatched when | Payload |
|-------|-----------------|---------|
| `FileStored` | `StoreUploadedFile` persists an uploaded file | `File $file` |
| `FileAttached` | `attachFile()` attaches a file to a model | `Model $attachable`, `Attachment $attachment` |
| `FileDetached` | `detachFile()` removes one or more attachments | `Model $attachable`, `int $fileId`, `?string $collection`, `int $count` |
| `AttachmentsSynced` | `syncAttachments()` replaces a collection | `Model $attachable`, `string $collection`, `int[] $fileIds` |
| `AttachmentsReordered` | `reorderAttachments()` updates sort positions | `Model $attachable`, `?string $collection`, `array $order` |

### Notes on dispatch behaviour

- **`FileDetached`** fires only when at least one attachment was actually removed (`count > 0`). A `null` collection means the file was detached from every collection.
- **`AttachmentsSynced`** fires once per sync. Because syncing attaches each file individually, a sync of *N* files also fires *N* `FileAttached` events. Clearing a collection (syncing an empty array) fires `AttachmentsSynced` with an empty `fileIds`.
- **`AttachUploadedFiles`** stores then attaches each upload, so it fires one `FileStored` **and** one `FileAttached` per file.

## Listening to an event

Register a listener the usual way — for example in a service provider:

```php
use Illuminate\Support\Facades\Event;
use OiLab\OiLaravelAttachments\Events\FileAttached;

Event::listen(function (FileAttached $event): void {
    logger()->info('File attached', [
        'attachable' => $event->attachable->getMorphClass(),
        'file_id' => $event->attachment->file_id,
        'collection' => $event->attachment->collection,
    ]);
});
```

Or with a dedicated listener class:

```php
namespace App\Listeners;

use OiLab\OiLaravelAttachments\Events\FileStored;

class OptimizeStoredImage
{
    public function handle(FileStored $event): void
    {
        if ($event->file->isImage()) {
            // dispatch an optimization job, generate thumbnails, ...
        }
    }
}
```

```php
// In a service provider
Event::listen(FileStored::class, OptimizeStoredImage::class);
```

## Reading the payload

Each event exposes its data as readonly properties:

```php
use OiLab\OiLaravelAttachments\Events\AttachmentsReordered;

Event::listen(function (AttachmentsReordered $event): void {
    // $event->attachable — the model whose attachments were reordered
    // $event->collection — the collection that was reordered (or null)
    // $event->order      — [file_id => sort] map that was applied
});
```

## Testing with faked events

Because these are standard Laravel events, you can assert them with `Event::fake()`. Fake only the package events so the models' UUID observers keep running:

```php
use Illuminate\Support\Facades\Event;
use OiLab\OiLaravelAttachments\Events\FileAttached;

Event::fake([FileAttached::class]);

$product->attachFile($file, 'gallery');

Event::assertDispatched(FileAttached::class);
```
