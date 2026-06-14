<?php

namespace OiLab\OiLaravelAttachments\Events;

use Illuminate\Database\Eloquent\Model;

/**
 * Dispatched after a collection has been synced (its attachments replaced).
 *
 * @property-read array<int, int> $fileIds The file IDs now attached, in order
 */
class AttachmentsSynced
{
    /**
     * @param  array<int, int>  $fileIds  The file IDs now attached, in order
     */
    public function __construct(
        public readonly Model $attachable,
        public readonly string $collection,
        public readonly array $fileIds,
    ) {}
}
