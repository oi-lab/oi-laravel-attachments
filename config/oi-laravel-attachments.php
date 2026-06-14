<?php

use OiLab\OiLaravelAttachments\Models\Attachment;
use OiLab\OiLaravelAttachments\Models\File;
use OiLab\OiLaravelAttachments\Models\Folder;

return [
    /*
    |--------------------------------------------------------------------------
    | User Model
    |--------------------------------------------------------------------------
    |
    | The model used for the created_by / updated_by audit relationships.
    |
    */
    'user_model' => 'App\Models\User',

    /*
    |--------------------------------------------------------------------------
    | Models
    |--------------------------------------------------------------------------
    |
    | The model classes used by the package. Override these with your own
    | classes (extending the package base models) to customize behavior.
    |
    */
    'models' => [
        'file' => File::class,
        'folder' => Folder::class,
        'attachment' => Attachment::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Storage
    |--------------------------------------------------------------------------
    |
    | The disk used to store uploaded files. Defaults to ATTACHMENTS_DISK, then
    | falls back to the application's FILESYSTEM_DISK. The directory is the
    | folder uploaded files are stored under on that disk.
    |
    */
    'disk' => env('ATTACHMENTS_DISK', env('FILESYSTEM_DISK', 'local')),

    'directory' => 'uploads',
];
