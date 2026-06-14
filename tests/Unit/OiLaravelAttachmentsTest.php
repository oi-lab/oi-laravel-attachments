<?php

use OiLab\OiLaravelAttachments\Models\Attachment;
use OiLab\OiLaravelAttachments\Models\File;
use OiLab\OiLaravelAttachments\Models\Folder;
use OiLab\OiLaravelAttachments\OiLaravelAttachments;
use OiLab\OiLaravelAttachments\Tests\Fixtures\User;

it('resolves the default model classes', function () {
    expect(OiLaravelAttachments::fileModel())->toBe(File::class)
        ->and(OiLaravelAttachments::folderModel())->toBe(Folder::class)
        ->and(OiLaravelAttachments::attachmentModel())->toBe(Attachment::class);
});

it('resolves the configured user model', function () {
    expect(OiLaravelAttachments::userModel())->toBe(User::class);
});

it('resolves overridden model classes from config', function () {
    config()->set('oi-laravel-attachments.models.file', 'App\\Models\\CustomFile');

    expect(OiLaravelAttachments::fileModel())->toBe('App\\Models\\CustomFile');
});

it('falls back to the default filesystem disk when none is configured', function () {
    config()->set('oi-laravel-attachments.disk', null);
    config()->set('filesystems.default', 'local');

    expect(OiLaravelAttachments::disk())->toBe('local');
});

it('uses the configured disk when set', function () {
    config()->set('oi-laravel-attachments.disk', 's3');

    expect(OiLaravelAttachments::disk())->toBe('s3');
});

it('returns the configured upload directory', function () {
    expect(OiLaravelAttachments::directory())->toBe('uploads');

    config()->set('oi-laravel-attachments.directory', 'media');

    expect(OiLaravelAttachments::directory())->toBe('media');
});
