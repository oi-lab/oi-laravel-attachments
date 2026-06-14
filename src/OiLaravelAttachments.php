<?php

namespace OiLab\OiLaravelAttachments;

/**
 * OiLaravelAttachments
 *
 * Central resolver for the configurable model classes used across the package.
 * All package internals (models, traits, actions, factories) resolve their
 * collaborators through these helpers so host applications can swap in their
 * own model classes via config.
 */
class OiLaravelAttachments
{
    /**
     * Resolve the configured File model class.
     *
     * @return class-string
     */
    public static function fileModel(): string
    {
        return config('oi-laravel-attachments.models.file', Models\File::class);
    }

    /**
     * Resolve the configured Folder model class.
     *
     * @return class-string
     */
    public static function folderModel(): string
    {
        return config('oi-laravel-attachments.models.folder', Models\Folder::class);
    }

    /**
     * Resolve the configured Attachment model class.
     *
     * @return class-string
     */
    public static function attachmentModel(): string
    {
        return config('oi-laravel-attachments.models.attachment', Models\Attachment::class);
    }

    /**
     * Resolve the configured User model class.
     *
     * @return class-string
     */
    public static function userModel(): string
    {
        return config('oi-laravel-attachments.user_model', 'App\Models\User');
    }

    /**
     * Resolve the storage disk used for uploaded files.
     */
    public static function disk(): string
    {
        return config('oi-laravel-attachments.disk') ?? config('filesystems.default');
    }

    /**
     * Resolve the directory uploaded files are stored under.
     */
    public static function directory(): string
    {
        return config('oi-laravel-attachments.directory', 'uploads');
    }
}
