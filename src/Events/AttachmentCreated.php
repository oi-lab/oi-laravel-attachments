<?php

namespace OiLab\OiLaravelAttachments\Events;

use OiLab\OiLaravelAttachments\Models\Attachment;

/**
 * Dispatched after an attachment record has been created.
 *
 * Fires for every Attachment creation. When the attachment was created through
 * the HasAttachments trait, the higher-level FileAttached event is also dispatched.
 */
class AttachmentCreated
{
    public function __construct(
        public readonly Attachment $attachment,
    ) {}
}
