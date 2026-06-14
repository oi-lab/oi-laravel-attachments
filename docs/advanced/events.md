---
title: Events
description: Hook into attachment and upload actions with Laravel events
section: advanced
order: 4
---

# Events

The package dispatches an event for each meaningful action, so you can react to attachments and uploads without overriding the package internals. All events live in the `OiLab\OiLaravelAttachments\Events` namespace and are plain, immutable data objects (readonly promoted properties).

## Attachment & upload events

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

## Attachment events

Lower-level events dispatched from the `AttachmentObserver` for the pivot model's lifecycle. The `Attachment` model is not soft-deletable, so there is no restored event.

| Event | Dispatched when | Payload |
|-------|-----------------|---------|
| `AttachmentCreated` | An attachment record is created | `Attachment $attachment` |
| `AttachmentUpdated` | An attachment record is updated (model-level) | `Attachment $attachment` |
| `AttachmentDeleted` | An attachment record is deleted (model-level) | `Attachment $attachment` |

### Notes on dispatch behaviour

- **`AttachmentCreated`** fires for *every* attachment creation, so `attachFile()` fires both `AttachmentCreated` (model) and `FileAttached` (semantic).
- The trait's **bulk operations bypass model events** because they use query builder updates/deletes:
  - `detachFile()` and `syncAttachments()` remove rows with a query delete — they emit `FileDetached` / `AttachmentsSynced`, **not** `AttachmentDeleted`.
  - `reorderAttachments()` updates `sort` with a query update — it emits `AttachmentsReordered`, **not** `AttachmentUpdated`.
- `AttachmentUpdated` / `AttachmentDeleted` therefore fire for model-level operations: `$attachment->save()`, `$attachment->delete()`, and the `HasSortable` helpers (`moveToPosition`, `moveUp`, `moveDown`, `swapWith`).

## File events

File events are dispatched from the `FileObserver` as the model goes through its lifecycle. These are distinct from `FileStored`, which fires only for uploads handled by the `StoreUploadedFile` action.

| Event | Dispatched when | Payload |
|-------|-----------------|---------|
| `FileCreated` | A file record is created | `File $file` |
| `FileUpdated` | A file record is updated | `File $file` |
| `FileMoved` | A file's `folder_id` changes | `File $file`, `?int $fromFolderId`, `?int $toFolderId` |
| `FileDeleted` | A file is (soft) deleted | `File $file` |
| `FileRestored` | A soft-deleted file is restored | `File $file` |

### Notes on dispatch behaviour

- **`FileCreated`** fires for *every* File creation. Storing an upload through `StoreUploadedFile` therefore fires both `FileCreated` and `FileStored`; creating a File directly (`File::create(...)`) fires only `FileCreated`.
- **`FileMoved`** fires *in addition to* `FileUpdated` when an update changes the folder. A `null` id means the file is at the root (no folder).
- **`FileRestored`** fires alongside `FileUpdated` (restoring re-saves the model); soft-deleting fires `FileDeleted` only.

## Folder events

Folder events are dispatched from the `FolderObserver` as the model goes through its lifecycle.

| Event | Dispatched when | Payload |
|-------|-----------------|---------|
| `FolderCreated` | A folder is created | `Folder $folder` |
| `FolderUpdated` | A folder is updated | `Folder $folder` |
| `FolderMoved` | A folder's `parent_id` changes | `Folder $folder`, `?int $fromParentId`, `?int $toParentId` |
| `FolderDeleted` | A folder is (soft) deleted | `Folder $folder` |
| `FolderRestored` | A soft-deleted folder is restored | `Folder $folder` |

### Notes on dispatch behaviour

- **`FolderMoved`** fires *in addition to* `FolderUpdated` when an update changes the parent. A `null` id means the root level (no parent).
- **`FolderRestored`** fires alongside `FolderUpdated`, because restoring re-saves the model.
- Soft-deleting fires `FolderDeleted` only (the soft delete is a query update, not a model save, so it does not fire `FolderUpdated`).

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
