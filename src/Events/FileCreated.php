<?php

namespace OiLab\OiLaravelAttachments\Events;

use OiLab\OiLaravelAttachments\Models\File;

/**
 * Dispatched after a file record has been created.
 *
 * Fires for every File creation. When the file came from the
 * StoreUploadedFile action, a FileStored event is also dispatched.
 */
class FileCreated
{
    public function __construct(
        public readonly File $file,
    ) {}
}
