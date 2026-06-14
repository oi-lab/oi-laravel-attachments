<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use OiLab\OiLaravelAttachments\Actions\AttachUploadedFiles;
use OiLab\OiLaravelAttachments\Actions\StoreUploadedFile;
use OiLab\OiLaravelAttachments\Models\File;
use OiLab\OiLaravelAttachments\Tests\Fixtures\User;

beforeEach(function () {
    Storage::fake('local');
});

it('stores an uploaded file and records its metadata', function () {
    $upload = UploadedFile::fake()->create('invoice.pdf', 12, 'application/pdf');

    $file = StoreUploadedFile::handle($upload, 'local', 'uploads');

    expect($file)->toBeInstanceOf(File::class)
        ->and($file->filename_download)->toBe('invoice.pdf')
        ->and($file->mimetype)->toBe('application/pdf')
        ->and($file->storage)->toBe('local')
        ->and($file->md5)->not->toBeNull();

    Storage::disk('local')->assertExists($file->filename_disk);
});

it('captures image dimensions for image uploads', function () {
    $upload = UploadedFile::fake()->image('photo.jpg', 320, 240);

    $file = StoreUploadedFile::handle($upload);

    expect($file->width)->toBe(320)
        ->and($file->height)->toBe(240);
});

it('stores a unique disk filename per upload', function () {
    $a = StoreUploadedFile::handle(UploadedFile::fake()->create('a.txt', 1, 'text/plain'));
    $b = StoreUploadedFile::handle(UploadedFile::fake()->create('a.txt', 1, 'text/plain'));

    expect($a->filename_disk)->not->toBe($b->filename_disk);
});

it('stores multiple uploads and attaches them to a model', function () {
    $user = User::factory()->create();

    AttachUploadedFiles::handle($user, [
        UploadedFile::fake()->image('one.jpg'),
        UploadedFile::fake()->image('two.jpg'),
    ], 'gallery');

    expect($user->attachments('gallery')->get())->toHaveCount(2)
        ->and(File::count())->toBe(2);
});

it('does nothing when given no files', function () {
    $user = User::factory()->create();

    AttachUploadedFiles::handle($user, [], 'gallery');

    expect($user->attachments()->count())->toBe(0)
        ->and(File::count())->toBe(0);
});
