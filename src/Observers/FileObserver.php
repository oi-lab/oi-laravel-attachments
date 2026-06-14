<?php

namespace OiLab\OiLaravelAttachments\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class FileObserver
{
    /**
     * Handle the File "creating" event.
     */
    public function creating(Model $file): void
    {
        if (! $file->uuid) {
            $file->uuid = (string) Str::uuid();
        }
    }
}
