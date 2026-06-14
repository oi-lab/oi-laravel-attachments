<?php

namespace OiLab\OiLaravelAttachments\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use OiLab\OiLaravelAttachments\OiLaravelAttachments;

class AttachUploadedFiles
{
    /**
     * Store uploaded files on disk and attach them to the model.
     *
     * @param  array<int, UploadedFile>  $files
     */
    public static function handle(Model $attachable, array $files, string $collection = 'default'): void
    {
        if (empty($files)) {
            return;
        }

        $disk = OiLaravelAttachments::disk();

        foreach ($files as $uploaded) {
            $stored = StoreUploadedFile::handle($uploaded, $disk, OiLaravelAttachments::directory());
            $attachable->attachFile($stored, $collection);
        }
    }
}
