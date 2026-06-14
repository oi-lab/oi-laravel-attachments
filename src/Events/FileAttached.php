<?php

namespace OiLab\OiLaravelAttachments\Events;

use Illuminate\Database\Eloquent\Model;
use OiLab\OiLaravelAttachments\Models\Attachment;

/**
 * Dispatched after a file has been attached to a model.
 *
 * The collection and sort position are available on the attachment
 * (`$event->attachment->collection`, `$event->attachment->sort`).
 */
class FileAttached
{
    public function __construct(
        public readonly Model $attachable,
        public readonly Attachment $attachment,
    ) {}
}
