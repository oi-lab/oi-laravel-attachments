<?php

namespace OiLab\OiLaravelAttachments\Observers;

use Illuminate\Support\Str;
use OiLab\OiLaravelAttachments\Events\FileCreated;
use OiLab\OiLaravelAttachments\Events\FileDeleted;
use OiLab\OiLaravelAttachments\Events\FileMoved;
use OiLab\OiLaravelAttachments\Events\FileRestored;
use OiLab\OiLaravelAttachments\Events\FileUpdated;
use OiLab\OiLaravelAttachments\Models\File;

class FileObserver
{
    /**
     * Handle the File "creating" event.
     */
    public function creating(File $file): void
    {
        if (! $file->uuid) {
            $file->uuid = (string) Str::uuid();
        }
    }

    /**
     * Handle the File "created" event.
     */
    public function created(File $file): void
    {
        event(new FileCreated($file));
    }

    /**
     * Handle the File "updated" event.
     */
    public function updated(File $file): void
    {
        if ($file->wasChanged('folder_id')) {
            $original = $file->getOriginal('folder_id');

            event(new FileMoved(
                $file,
                $original === null ? null : (int) $original,
                $file->folder_id,
            ));
        }

        event(new FileUpdated($file));
    }

    /**
     * Handle the File "deleted" event.
     */
    public function deleted(File $file): void
    {
        event(new FileDeleted($file));
    }

    /**
     * Handle the File "restored" event.
     */
    public function restored(File $file): void
    {
        event(new FileRestored($file));
    }
}
