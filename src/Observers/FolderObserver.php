<?php

namespace OiLab\OiLaravelAttachments\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class FolderObserver
{
    /**
     * Handle the Folder "creating" event.
     */
    public function creating(Model $folder): void
    {
        if (! $folder->uuid) {
            $folder->uuid = (string) Str::uuid();
        }
    }
}
