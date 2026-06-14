<?php

namespace OiLab\OiLaravelAttachments\Events;

use OiLab\OiLaravelAttachments\Models\File;

/**
 * Dispatched after a file record has been updated.
 *
 * When the update changed the containing folder, a more specific FileMoved
 * event is also dispatched.
 */
class FileUpdated
{
    public function __construct(
        public readonly File $file,
    ) {}
}
