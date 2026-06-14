<?php

namespace OiLab\OiLaravelAttachments\Events;

use OiLab\OiLaravelAttachments\Models\Folder;

/**
 * Dispatched after a folder has been updated.
 *
 * When the update changed the parent folder, a more specific FolderMoved
 * event is also dispatched.
 */
class FolderUpdated
{
    public function __construct(
        public readonly Folder $folder,
    ) {}
}
