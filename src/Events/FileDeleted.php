<?php

namespace OiLab\OiLaravelAttachments\Events;

use OiLab\OiLaravelAttachments\Models\File;

/**
 * Dispatched after a file has been deleted (soft delete).
 */
class FileDeleted
{
    public function __construct(
        public readonly File $file,
    ) {}
}
