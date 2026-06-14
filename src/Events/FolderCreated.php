<?php

namespace OiLab\OiLaravelAttachments\Events;

use OiLab\OiLaravelAttachments\Models\Folder;

/**
 * Dispatched after a folder has been created.
 */
class FolderCreated
{
    public function __construct(
        public readonly Folder $folder,
    ) {}
}
