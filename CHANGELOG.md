# Changelog

All notable changes to `oi-laravel-attachments` will be documented in this file.

## [1.0.0] - 2026-06-14

Initial release of OI Laravel Attachments — polymorphic file attachments, files, and folders for Laravel applications.

### Core Features
- **Polymorphic Attachments**: The `HasAttachments` trait makes any Eloquent model attachable, with a full attach / detach / sync / reorder API.
- **Named Collections**: Group attachments per model (e.g. `gallery`, `cover`) with independent ordering.
- **Ordering**: First-class `sort` support via the `HasSortable` trait (`moveUp`, `moveDown`, `moveToPosition`, `swapWith`, `sorted` scope).
- **File Model**: Stores mimetype, filesize, dimensions, MD5, and rich metadata; includes `isImage()` / `isVideo()` / `isAudio()` helpers, a `search` scope, and `getFullPath()` / `getStream()` storage access.
- **File Metadata**: The `metadata` column is cast to a `FileMetadataValueObject` exposing resolution, EXIF, IPTC, dimensions, color space/profile, bit depth, and aspect ratio as typed value objects.
- **Folders**: Optional self-nesting folder tree (`parent_id`) for organizing files.
- **Upload Actions**: `StoreUploadedFile` persists an upload and captures its metadata; `AttachUploadedFiles` stores many uploads and attaches them in one call.
- **Events**: Semantic attachment/upload events (`FileStored`, `FileAttached`, `FileDetached`, `AttachmentsSynced`, `AttachmentsReordered`), plus model lifecycle events for files (`FileCreated`, `FileUpdated`, `FileMoved`, `FileDeleted`, `FileRestored`), folders (`FolderCreated`, `FolderUpdated`, `FolderMoved`, `FolderDeleted`, `FolderRestored`), and attachments (`AttachmentCreated`, `AttachmentUpdated`, `AttachmentDeleted`) are dispatched for each action so host apps can react without overriding package internals.
- **Audit Tracking**: The `HasCreatorAndUpdater` trait records `created_by` / `updated_by` automatically from the authenticated user.
- **Configurable Models**: Every model (`File`, `Folder`, `Attachment`, and the user model) is resolved through `OiLaravelAttachments` and can be swapped via config.
- **Storage Agnostic**: Works with any Flysystem disk (local, S3, etc.); the disk and upload directory are configurable.
- **UUIDs & Soft Deletes**: Files, folders, and attachments each carry a unique `uuid` (auto-generated via observers); files and folders are soft-deletable.
- **AI Assistant Skills**: The `oi:install-ai-skill` Artisan command installs Claude Code / JetBrains Junie skill files and a `CLAUDE.md` rules section into the host application.

### Requirements
- PHP 8.2, 8.3, or 8.4
- Laravel 11.0+, 12.0+, or 13.0+

### Testing
- 75 tests covering the attachment trait, models and relationships, sorting, upload actions, file metadata, audit tracking, events, and the configurable model resolver.
