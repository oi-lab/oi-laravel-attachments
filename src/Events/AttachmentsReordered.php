<?php

namespace OiLab\OiLaravelAttachments\Events;

use Illuminate\Database\Eloquent\Model;

/**
 * Dispatched after the attachments of a collection have been reordered.
 *
 * A null collection means the reorder was applied across every collection.
 */
class AttachmentsReordered
{
    /**
     * @param  array<int, int>  $order  Map of file ID to its new sort position
     */
    public function __construct(
        public readonly Model $attachable,
        public readonly ?string $collection,
        public readonly array $order,
    ) {}
}
