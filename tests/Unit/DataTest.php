<?php

use OiLab\OiLaravelAttachments\Data\AttachmentData;
use OiLab\OiLaravelAttachments\Data\FileData;
use OiLab\OiLaravelAttachments\Data\FolderData;
use OiLab\OiLaravelAttachments\Models\Attachment;
use OiLab\OiLaravelAttachments\Models\File;
use OiLab\OiLaravelAttachments\Models\Folder;

it('builds file data from an array', function () {
    $data = FileData::from([
        'filename_disk' => 'abc.jpg',
        'filename_download' => 'photo.jpg',
        'mimetype' => 'image/jpeg',
        'filesize' => 2048,
        'width' => 640,
        'height' => 480,
    ]);

    expect($data)->toBeInstanceOf(FileData::class)
        ->and($data->filename_disk)->toBe('abc.jpg')
        ->and($data->mimetype)->toBe('image/jpeg')
        ->and($data->filesize)->toBe(2048)
        ->and($data->storage)->toBe('local')
        ->and($data->title)->toBeNull()
        ->and($data->metadata)->toBeNull();
});

it('builds file data from a model including metadata as an array', function () {
    $file = File::factory()->withMetadata(['width' => 800, 'height' => 600])->create([
        'filename_download' => 'report.pdf',
    ]);

    $data = $file->toData();

    expect($data)->toBeInstanceOf(FileData::class)
        ->and($data->id)->toBe($file->id)
        ->and($data->uuid)->toBe($file->uuid)
        ->and($data->filename_download)->toBe('report.pdf')
        ->and($data->metadata)->toBeArray()
        ->and($data->metadata['width'])->toBe(800)
        ->and($data->metadata['height'])->toBe(600);
});

it('builds folder data from a model', function () {
    $folder = Folder::factory()->create(['name' => 'Invoices']);

    $data = $folder->toData();

    expect($data)->toBeInstanceOf(FolderData::class)
        ->and($data->id)->toBe($folder->id)
        ->and($data->uuid)->toBe($folder->uuid)
        ->and($data->name)->toBe('Invoices')
        ->and($data->parent_id)->toBeNull();
});

it('builds attachment data from a model', function () {
    $attachment = Attachment::factory()->create(['collection' => 'gallery', 'sort' => 3]);

    $data = $attachment->toData();

    expect($data)->toBeInstanceOf(AttachmentData::class)
        ->and($data->id)->toBe($attachment->id)
        ->and($data->file_id)->toBe($attachment->file_id)
        ->and($data->collection)->toBe('gallery')
        ->and($data->sort)->toBe(3);
});

it('nests file data inside attachment data when the file relation is loaded', function () {
    $attachment = Attachment::factory()->create();

    $data = $attachment->load('file')->toData();

    expect($data->file)->toBeInstanceOf(FileData::class)
        ->and($data->file->id)->toBe($attachment->file_id);
});

it('defaults the collection and sort on attachment data', function () {
    $data = new AttachmentData(file_id: 1);

    expect($data->collection)->toBe('default')
        ->and($data->sort)->toBe(0)
        ->and($data->file)->toBeNull();
});
