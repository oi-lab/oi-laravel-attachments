<?php

namespace OiLab\OiLaravelAttachments\Events;

use OiLab\OiLaravelAttachments\Models\Folder;

/**
 * Dispatched after a folder has been deleted (soft delete).
 */
class FolderDeleted
{
    public function __construct(
        public readonly Folder $folder,
    ) {}
}
