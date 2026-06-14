<?php

namespace OiLab\OiLaravelAttachments\Events;

use OiLab\OiLaravelAttachments\Models\Attachment;

/**
 * Dispatched after an attachment record has been deleted.
 *
 * Fires for model-level deletes (`$attachment->delete()`). Bulk removals via
 * detachFile() / syncAttachments() use a query delete and emit
 * FileDetached / AttachmentsSynced instead.
 */
class AttachmentDeleted
{
    public function __construct(
        public readonly Attachment $attachment,
    ) {}
}
