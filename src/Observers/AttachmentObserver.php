<?php

namespace OiLab\OiLaravelAttachments\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AttachmentObserver
{
    /**
     * Handle the Attachment "creating" event.
     */
    public function creating(Model $attachment): void
    {
        if (! $attachment->uuid) {
            $attachment->uuid = (string) Str::uuid();
        }
    }
}
