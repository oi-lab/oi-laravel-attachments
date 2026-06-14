<?php

namespace OiLab\OiLaravelAttachments\Actions;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use OiLab\OiLaravelAttachments\Events\FileStored;
use OiLab\OiLaravelAttachments\Models\File;
use OiLab\OiLaravelAttachments\OiLaravelAttachments;

class StoreUploadedFile
{
    public static function handle(UploadedFile $file, string $disk = 'local', string $directory = 'uploads'): File
    {
        $filename = Str::uuid().'.'.$file->getClientOriginalExtension();

        Storage::disk($disk)->putFileAs($directory, $file, $filename);

        $width = null;
        $height = null;
        if (str_starts_with($file->getMimeType() ?? '', 'image/')) {
            [$width, $height] = @getimagesize($file->getRealPath()) ?: [null, null];
        }

        $model = OiLaravelAttachments::fileModel();

        $stored = $model::create([
            'filename_disk' => $directory.'/'.$filename,
            'filename_download' => $file->getClientOriginalName(),
            'mimetype' => $file->getMimeType(),
            'filesize' => $file->getSize(),
            'width' => $width,
            'height' => $height,
            'storage' => $disk,
            'md5' => md5_file($file->getRealPath()),
        ]);

        event(new FileStored($stored));

        return $stored;
    }
}
