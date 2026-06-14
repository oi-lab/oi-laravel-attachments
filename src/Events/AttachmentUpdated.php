<?php

namespace OiLab\OiLaravelAttachments\Events;

use OiLab\OiLaravelAttachments\Models\Attachment;

/**
 * Dispatched after an attachment record has been updated.
 *
 * Fires for model-level updates (e.g. moveToPosition / swapWith). Bulk reorders
 * via reorderAttachments() use a query update and emit AttachmentsReordered instead.
 */
class AttachmentUpdated
{
    public function __construct(
        public readonly Attachment $attachment,
    ) {}
}
