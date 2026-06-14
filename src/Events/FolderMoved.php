<?php

namespace OiLab\OiLaravelAttachments\Events;

use OiLab\OiLaravelAttachments\Models\Folder;

/**
 * Dispatched after a folder has been moved to a different parent.
 *
 * A null id means the root level (no parent).
 */
class FolderMoved
{
    public function __construct(
        public readonly Folder $folder,
        public readonly ?int $fromParentId,
        public readonly ?int $toParentId,
    ) {}
}
