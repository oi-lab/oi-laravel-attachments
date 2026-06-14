<?php

namespace OiLab\OiLaravelAttachments\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Collection;
use OiLab\OiLaravelAttachments\Events\AttachmentsReordered;
use OiLab\OiLaravelAttachments\Events\AttachmentsSynced;
use OiLab\OiLaravelAttachments\Events\FileAttached;
use OiLab\OiLaravelAttachments\Events\FileDetached;
use OiLab\OiLaravelAttachments\OiLaravelAttachments;

/**
 * HasAttachments Trait
 *
 * Provides polymorphic attachment functionality to models with collection support.
 * Allows models to have multiple files attached with ordering and categorization.
 */
trait HasAttachments
{
    /**
     * Get all attached files (eager loaded through attachments).
     *
     * @param  string|null  $collection  Optional collection name to filter by
     * @return Collection<int, Model>
     */
    public function getAttachedFilesAttribute(?string $collection = null): Collection
    {
        $query = $this->attachments;

        if ($collection !== null) {
            $query = $query->where('collection', $collection);
        }

        return $query->pluck('file');
    }

    /**
     * Detach a file from this model.
     *
     * @param  Model|int  $file  The file to detach (Model instance or ID)
     * @param  string|null  $collection  Optional collection name to filter by
     * @return int Number of attachments deleted
     */
    public function detachFile(Model|int $file, ?string $collection = null): int
    {
        $fileId = $file instanceof Model ? $file->id : $file;

        $query = $this->attachments()->where('file_id', $fileId);

        if ($collection !== null) {
            $query->where('collection', $collection);
        }

        $count = $query->delete();

        if ($count > 0) {
            event(new FileDetached($this, $fileId, $collection, $count));
        }

        return $count;
    }

    /**
     * Get all attachments for this model.
     *
     * @param  string|null  $collection  Optional collection name to filter by
     * @return MorphMany<Model, $this>
     */
    public function attachments(?string $collection = null): MorphMany
    {
        $query = $this->morphMany(OiLaravelAttachments::attachmentModel(), 'attachable')->orderBy('sort');

        if ($collection !== null) {
            $query->where('collection', $collection);
        }

        return $query;
    }

    /**
     * Get a single attachment for this model.
     *
     * @param  string  $collection  Collection name
     * @return MorphOne<Model, $this>
     */
    public function singleAttachment(string $collection): MorphOne
    {
        return $this->morphOne(OiLaravelAttachments::attachmentModel(), 'attachable')
            ->where('collection', $collection)
            ->orderBy('sort');
    }

    /**
     * Reorder attachments for this model.
     *
     * @param  array<int, int>  $fileIdOrder  Array mapping file IDs to their new order [file_id => sort]
     * @param  string|null  $collection  Optional collection name to filter by
     */
    public function reorderAttachments(array $fileIdOrder, ?string $collection = null): void
    {
        foreach ($fileIdOrder as $fileId => $sort) {
            $query = $this->attachments()
                ->where('file_id', $fileId);

            if ($collection !== null) {
                $query->where('collection', $collection);
            }

            $query->update(['sort' => $sort]);
        }

        event(new AttachmentsReordered($this, $collection, $fileIdOrder));
    }

    /**
     * Sync attachments for this model only if they have changed.
     * Compares existing attachments with new ones (order matters).
     * More efficient than syncAttachments() as it avoids unnecessary database operations.
     *
     * @param  array<int, int|Model>|null  $files  Array of file IDs or Model instances to attach (null or empty array clears attachments)
     * @param  string  $collection  Collection name (default: 'default')
     * @return bool True if attachments were synced, false if no changes detected
     */
    public function syncAttachmentsIfChanged(?array $files, string $collection = 'default'): bool
    {
        // Normalize files to array of IDs
        $newFileIds = $this->normalizeFileIds($files ?? []);

        // Get existing attachments for this collection (ordered by sort)
        $existingFileIds = $this->attachments($collection)
            ->orderBy('sort')
            ->pluck('file_id')
            ->toArray();

        // Compare: if identical (same IDs in same order), no sync needed
        if ($existingFileIds === $newFileIds) {
            return false;
        }

        // Otherwise, perform sync
        $this->syncAttachments($files ?? [], $collection);

        return true;
    }

    /**
     * Sync attachments for this model (replaces all existing attachments).
     *
     * @param  array<int, int|Model>  $files  Array of file IDs or Model instances to attach
     * @param  string  $collection  Collection name (default: 'default')
     */
    public function syncAttachments(array $files, string $collection = 'default'): void
    {
        $this->attachments($collection)->delete();

        foreach ($files as $index => $file) {
            $this->attachFile($file, $collection, $index);
        }

        event(new AttachmentsSynced($this, $collection, $this->normalizeFileIds($files)));
    }

    /**
     * Normalize file input to array of file IDs.
     * Handles both Model instances and integer IDs.
     *
     * @param  array<int, int|Model>  $files
     * @return array<int, int>
     */
    protected function normalizeFileIds(array $files): array
    {
        return collect($files)
            ->map(fn ($file) => $file instanceof Model ? $file->id : $file)
            ->values()
            ->toArray();
    }

    /**
     * Attach a file to this model.
     *
     * @param  Model|int  $file  The file to attach (Model instance or ID)
     * @param  string  $collection  Collection name (default: 'default')
     * @param  int|null  $sort  The display sort (null = append to end)
     * @return Model The created attachment
     */
    public function attachFile(Model|int $file, string $collection = 'default', ?int $sort = null): Model
    {
        $fileId = $file instanceof Model ? $file->id : $file;

        if ($sort === null) {
            $maxSort = $this->attachments($collection)->max('sort');
            $sort = $maxSort !== null ? $maxSort + 1 : 0;
        }

        $attachment = $this->attachments()->create([
            'file_id' => $fileId,
            'collection' => $collection,
            'sort' => $sort,
        ]);

        event(new FileAttached($this, $attachment));

        return $attachment;
    }
}
