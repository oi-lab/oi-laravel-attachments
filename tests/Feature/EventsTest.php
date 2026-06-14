<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use OiLab\OiLaravelAttachments\Actions\AttachUploadedFiles;
use OiLab\OiLaravelAttachments\Actions\StoreUploadedFile;
use OiLab\OiLaravelAttachments\Events\AttachmentsReordered;
use OiLab\OiLaravelAttachments\Events\AttachmentsSynced;
use OiLab\OiLaravelAttachments\Events\FileAttached;
use OiLab\OiLaravelAttachments\Events\FileDetached;
use OiLab\OiLaravelAttachments\Events\FileStored;
use OiLab\OiLaravelAttachments\Models\File;
use OiLab\OiLaravelAttachments\Tests\Fixtures\User;

// Fake only the package events so Eloquent model events (and the UUID observers) still fire.
const PACKAGE_EVENTS = [
    FileStored::class,
    FileAttached::class,
    FileDetached::class,
    AttachmentsSynced::class,
    AttachmentsReordered::class,
];

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('dispatches FileAttached when a file is attached', function () {
    Event::fake(PACKAGE_EVENTS);
    $file = File::factory()->create();

    $this->user->attachFile($file, 'gallery');

    Event::assertDispatched(FileAttached::class, function (FileAttached $event) use ($file) {
        return $event->attachable->is($this->user)
            && $event->attachment->file_id === $file->id
            && $event->attachment->collection === 'gallery';
    });
});

it('dispatches FileDetached when an attachment is removed', function () {
    $file = File::factory()->create();
    $this->user->attachFile($file, 'gallery');

    Event::fake(PACKAGE_EVENTS);

    $this->user->detachFile($file, 'gallery');

    Event::assertDispatched(FileDetached::class, function (FileDetached $event) use ($file) {
        return $event->attachable->is($this->user)
            && $event->fileId === $file->id
            && $event->collection === 'gallery'
            && $event->count === 1;
    });
});

it('does not dispatch FileDetached when nothing is removed', function () {
    $file = File::factory()->create();

    Event::fake(PACKAGE_EVENTS);

    $this->user->detachFile($file, 'gallery');

    Event::assertNotDispatched(FileDetached::class);
});

it('dispatches AttachmentsSynced when a collection is synced', function () {
    $files = File::factory()->count(2)->create();

    Event::fake(PACKAGE_EVENTS);

    $this->user->syncAttachments($files->pluck('id')->all(), 'gallery');

    Event::assertDispatched(AttachmentsSynced::class, function (AttachmentsSynced $event) use ($files) {
        return $event->attachable->is($this->user)
            && $event->collection === 'gallery'
            && $event->fileIds === $files->pluck('id')->all();
    });

    // Syncing also attaches each file individually.
    Event::assertDispatchedTimes(FileAttached::class, 2);
});

it('dispatches AttachmentsReordered when attachments are reordered', function () {
    $files = File::factory()->count(2)->create();
    foreach ($files as $file) {
        $this->user->attachFile($file, 'gallery');
    }

    Event::fake(PACKAGE_EVENTS);

    $order = [$files[0]->id => 1, $files[1]->id => 0];
    $this->user->reorderAttachments($order, 'gallery');

    Event::assertDispatched(AttachmentsReordered::class, function (AttachmentsReordered $event) use ($order) {
        return $event->attachable->is($this->user)
            && $event->collection === 'gallery'
            && $event->order === $order;
    });
});

it('dispatches FileStored when an upload is stored', function () {
    Storage::fake('local');
    Event::fake(PACKAGE_EVENTS);

    $stored = StoreUploadedFile::handle(UploadedFile::fake()->create('doc.pdf', 4, 'application/pdf'));

    Event::assertDispatched(FileStored::class, function (FileStored $event) use ($stored) {
        return $event->file->is($stored);
    });
});

it('dispatches FileStored and FileAttached for each uploaded attachment', function () {
    Storage::fake('local');
    Event::fake(PACKAGE_EVENTS);

    AttachUploadedFiles::handle($this->user, [
        UploadedFile::fake()->image('one.jpg'),
        UploadedFile::fake()->image('two.jpg'),
    ], 'gallery');

    Event::assertDispatchedTimes(FileStored::class, 2);
    Event::assertDispatchedTimes(FileAttached::class, 2);
});
