<?php

namespace OiLab\OiLaravelAttachments\Observers;

use Illuminate\Support\Str;
use OiLab\OiLaravelAttachments\Events\AttachmentCreated;
use OiLab\OiLaravelAttachments\Events\AttachmentDeleted;
use OiLab\OiLaravelAttachments\Events\AttachmentUpdated;
use OiLab\OiLaravelAttachments\Models\Attachment;

class AttachmentObserver
{
    /**
     * Handle the Attachment "creating" event.
     */
    public function creating(Attachment $attachment): void
    {
        if (! $attachment->uuid) {
            $attachment->uuid = (string) Str::uuid();
        }
    }

    /**
     * Handle the Attachment "created" event.
     */
    public function created(Attachment $attachment): void
    {
        event(new AttachmentCreated($attachment));
    }

    /**
     * Handle the Attachment "updated" event.
     */
    public function updated(Attachment $attachment): void
    {
        event(new AttachmentUpdated($attachment));
    }

    /**
     * Handle the Attachment "deleted" event.
     */
    public function deleted(Attachment $attachment): void
    {
        event(new AttachmentDeleted($attachment));
    }
}
