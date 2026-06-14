<?php

namespace OiLab\OiLaravelAttachments\Events;

use OiLab\OiLaravelAttachments\Models\File;

/**
 * Dispatched after a file has been moved to a different folder.
 *
 * A null id means the file is not in any folder (root level).
 */
class FileMoved
{
    public function __construct(
        public readonly File $file,
        public readonly ?int $fromFolderId,
        public readonly ?int $toFolderId,
    ) {}
}
