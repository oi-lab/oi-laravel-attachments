<?php

namespace OiLab\OiLaravelAttachments\Observers;

use Illuminate\Support\Str;
use OiLab\OiLaravelAttachments\Events\FolderCreated;
use OiLab\OiLaravelAttachments\Events\FolderDeleted;
use OiLab\OiLaravelAttachments\Events\FolderMoved;
use OiLab\OiLaravelAttachments\Events\FolderRestored;
use OiLab\OiLaravelAttachments\Events\FolderUpdated;
use OiLab\OiLaravelAttachments\Models\Folder;

class FolderObserver
{
    /**
     * Handle the Folder "creating" event.
     */
    public function creating(Folder $folder): void
    {
        if (! $folder->uuid) {
            $folder->uuid = (string) Str::uuid();
        }
    }

    /**
     * Handle the Folder "created" event.
     */
    public function created(Folder $folder): void
    {
        event(new FolderCreated($folder));
    }

    /**
     * Handle the Folder "updated" event.
     */
    public function updated(Folder $folder): void
    {
        if ($folder->wasChanged('parent_id')) {
            $original = $folder->getOriginal('parent_id');

            event(new FolderMoved(
                $folder,
                $original === null ? null : (int) $original,
                $folder->parent_id,
            ));
        }

        event(new FolderUpdated($folder));
    }

    /**
     * Handle the Folder "deleted" event.
     */
    public function deleted(Folder $folder): void
    {
        event(new FolderDeleted($folder));
    }

    /**
     * Handle the Folder "restored" event.
     */
    public function restored(Folder $folder): void
    {
        event(new FolderRestored($folder));
    }
}
