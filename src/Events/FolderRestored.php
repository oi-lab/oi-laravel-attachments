<?php

namespace OiLab\OiLaravelAttachments\Events;

use OiLab\OiLaravelAttachments\Models\Folder;

/**
 * Dispatched after a soft-deleted folder has been restored.
 */
class FolderRestored
{
    public function __construct(
        public readonly Folder $folder,
    ) {}
}
