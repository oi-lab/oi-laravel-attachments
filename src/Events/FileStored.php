<?php

namespace OiLab\OiLaravelAttachments\Events;

use OiLab\OiLaravelAttachments\Models\File;

/**
 * Dispatched after an uploaded file has been stored on disk and persisted
 * as a File record by the StoreUploadedFile action.
 */
class FileStored
{
    public function __construct(
        public readonly File $file,
    ) {}
}
