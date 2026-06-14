# OI Laravel Attachments — AI Context

This package provides polymorphic file attachments, plus `File` and `Folder` models, for Laravel applications. Any Eloquent model can have files attached to it, organized into named collections with ordering.

## Core Concepts

- **File** — a stored file with metadata (mimetype, filesize, dimensions, EXIF/IPTC, MD5). Lives in the `files` table, can belong to a `Folder`.
- **Folder** — a self-nesting container for files (`parent_id` tree).
- **Attachment** — the polymorphic pivot linking a `File` to any model (`attachable`), with a `collection` name and a `sort` order.
- **HasAttachments** — the trait host models use to gain attachment methods.

## Attaching Files to a Model

Add the trait to any model:

```php
use OiLab\OiLaravelAttachments\Concerns\HasAttachments;

class Product extends Model
{
    use HasAttachments;
}
```

Then use its methods:

```php
$product->attachFile($file, collection: 'gallery');       // attach one File (model or id)
$product->syncAttachments([$id1, $id2], 'gallery');       // replace a collection
$product->syncAttachmentsIfChanged([$id1, $id2], 'gallery'); // sync only if order/ids differ
$product->detachFile($file, 'gallery');                   // remove
$product->reorderAttachments([$fileId => 0, ...], 'gallery');

$product->attachments;                  // all attachments (ordered by sort)
$product->attachments('gallery')->get(); // one collection
$product->singleAttachment('cover');    // MorphOne for single-file collections
$product->attached_files;               // Collection of File models
```

## Uploading Files

Use the action classes rather than building File records by hand:

```php
use OiLab\OiLaravelAttachments\Actions\StoreUploadedFile;
use OiLab\OiLaravelAttachments\Actions\AttachUploadedFiles;

// Store an UploadedFile and get back a File model
$file = StoreUploadedFile::handle($request->file('document'));

// Store many uploads and attach them to a model in one call
AttachUploadedFiles::handle($product, $request->file('images'), 'gallery');
```

## Configuration

Publish the config and migrations:

```bash
php artisan vendor:publish --tag=oi-laravel-attachments-config
php artisan vendor:publish --tag=oi-laravel-attachments-migrations
php artisan migrate
```

`config/oi-laravel-attachments.php` exposes these options:

| Key | Default | Description |
|-----|---------|-------------|
| `user_model` | `App\Models\User` | Model used for `created_by` / `updated_by` audit relationships |
| `models.file` | `File::class` | Override with your own File subclass |
| `models.folder` | `Folder::class` | Override with your own Folder subclass |
| `models.attachment` | `Attachment::class` | Override with your own Attachment subclass |
| `disk` | `env('ATTACHMENTS_DISK', env('FILESYSTEM_DISK', 'local'))` | Storage disk for uploads (defaults to `ATTACHMENTS_DISK`, then `FILESYSTEM_DISK`) |
| `directory` | `uploads` | Directory uploaded files are stored under |

Always resolve model classes through `OiLaravelAttachments` (e.g. `OiLaravelAttachments::fileModel()`), never reference `File::class` directly, so host-app overrides keep working.

## Events

Each action dispatches an event in `OiLab\OiLaravelAttachments\Events`. Listen to these instead of overriding package internals:

| Event | Dispatched when | Key payload |
|-------|-----------------|-------------|
| `FileStored` | `StoreUploadedFile` persists an upload | `$file` |
| `FileAttached` | `attachFile()` attaches a file | `$attachable`, `$attachment` |
| `FileDetached` | `detachFile()` removes attachments (count > 0) | `$attachable`, `$fileId`, `$collection`, `$count` |
| `AttachmentsSynced` | `syncAttachments()` replaces a collection | `$attachable`, `$collection`, `$fileIds` |
| `AttachmentsReordered` | `reorderAttachments()` updates sort | `$attachable`, `$collection`, `$order` |
| `AttachmentCreated` / `AttachmentUpdated` / `AttachmentDeleted` | Model-level Attachment lifecycle (via `AttachmentObserver`) | `$attachment` |
| `FileCreated` / `FileUpdated` / `FileDeleted` / `FileRestored` | File lifecycle (via `FileObserver`) | `$file` |
| `FileMoved` | A file's `folder_id` changes | `$file`, `$fromFolderId`, `$toFolderId` |
| `FolderCreated` / `FolderUpdated` / `FolderDeleted` / `FolderRestored` | Folder lifecycle (via `FolderObserver`) | `$folder` |
| `FolderMoved` | A folder's `parent_id` changes | `$folder`, `$fromParentId`, `$toParentId` |

Note: a sync of N files also fires N `FileAttached` events; `AttachUploadedFiles` fires one `FileStored` and one `FileAttached` per file; storing an upload fires both `FileCreated` and `FileStored`; `FileMoved`/`FolderMoved` fire alongside `FileUpdated`/`FolderUpdated`. The trait's bulk operations use query builder writes, so `detachFile`/`syncAttachments`/`reorderAttachments` do NOT fire the model-level `AttachmentDeleted`/`AttachmentUpdated` events (they emit `FileDetached`/`AttachmentsSynced`/`AttachmentsReordered` instead). In tests, fake only the package events (`Event::fake([FileAttached::class, ...])`) so the UUID observers keep running.

## Conventions

- Files, folders, and attachments all carry a `uuid`, `created_by`/`updated_by` (via `HasCreatorAndUpdater`), and a JSON `props` bag.
- `File` and `Folder` use `SoftDeletes`.
- `File::metadata` is cast to a `FileMetadataValueObject` (resolution, EXIF, IPTC, color info) — read it as an object, not raw JSON.
- The `sort` column is managed via the `HasSortable` trait; use `attachFile`/`reorderAttachments` to change ordering.

## Updating the AI Skill

After updating this package, re-sync the skill files:

```bash
composer sync-ai-skills
```
