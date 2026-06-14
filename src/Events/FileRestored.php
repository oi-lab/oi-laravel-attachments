<?php

namespace OiLab\OiLaravelAttachments\Events;

use OiLab\OiLaravelAttachments\Models\File;

/**
 * Dispatched after a soft-deleted file has been restored.
 */
class FileRestored
{
    public function __construct(
        public readonly File $file,
    ) {}
}
