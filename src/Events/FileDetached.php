<?php

namespace OiLab\OiLaravelAttachments\Events;

use Illuminate\Database\Eloquent\Model;

/**
 * Dispatched after one or more attachments have been removed from a model.
 *
 * A null collection means the file was detached from every collection.
 */
class FileDetached
{
    public function __construct(
        public readonly Model $attachable,
        public readonly int $fileId,
        public readonly ?string $collection,
        public readonly int $count,
    ) {}
}
