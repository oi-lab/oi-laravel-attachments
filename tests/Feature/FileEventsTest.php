<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use OiLab\OiLaravelAttachments\Actions\StoreUploadedFile;
use OiLab\OiLaravelAttachments\Events\FileCreated;
use OiLab\OiLaravelAttachments\Events\FileDeleted;
use OiLab\OiLaravelAttachments\Events\FileMoved;
use OiLab\OiLaravelAttachments\Events\FileRestored;
use OiLab\OiLaravelAttachments\Events\FileUpdated;
use OiLab\OiLaravelAttachments\Models\File;
use OiLab\OiLaravelAttachments\Models\Folder;

// Fake only the file lifecycle events so Eloquent model events (and the UUID observer) still fire.
const FILE_EVENTS = [
    FileCreated::class,
    FileUpdated::class,
    FileDeleted::class,
    FileRestored::class,
    FileMoved::class,
];

it('dispatches FileCreated when a file is created', function () {
    Event::fake(FILE_EVENTS);

    $file = File::factory()->create();

    Event::assertDispatched(FileCreated::class, fn (FileCreated $event) => $event->file->is($file));
});

it('also dispatches FileCreated when a file is stored via the action', function () {
    Storage::fake('local');
    Event::fake(FILE_EVENTS);

    $stored = StoreUploadedFile::handle(UploadedFile::fake()->create('doc.pdf', 4, 'application/pdf'));

    Event::assertDispatched(FileCreated::class, fn (FileCreated $event) => $event->file->is($stored));
});

it('dispatches FileUpdated when a file is updated', function () {
    $file = File::factory()->create();

    Event::fake(FILE_EVENTS);

    $file->update(['title' => 'Renamed']);

    Event::assertDispatched(FileUpdated::class, fn (FileUpdated $event) => $event->file->is($file));
    Event::assertNotDispatched(FileMoved::class);
});

it('dispatches FileMoved when the folder changes', function () {
    $folder = Folder::factory()->create();
    $file = File::factory()->create();

    Event::fake(FILE_EVENTS);

    $file->update(['folder_id' => $folder->id]);

    Event::assertDispatched(FileMoved::class, function (FileMoved $event) use ($file, $folder) {
        return $event->file->is($file)
            && $event->fromFolderId === null
            && $event->toFolderId === $folder->id;
    });

    // A move is also an update.
    Event::assertDispatched(FileUpdated::class);
});

it('dispatches FileDeleted when a file is soft deleted', function () {
    $file = File::factory()->create();

    Event::fake(FILE_EVENTS);

    $file->delete();

    Event::assertDispatched(FileDeleted::class, fn (FileDeleted $event) => $event->file->is($file));
});

it('dispatches FileRestored when a file is restored', function () {
    $file = File::factory()->create();
    $file->delete();

    Event::fake(FILE_EVENTS);

    $file->restore();

    Event::assertDispatched(FileRestored::class, fn (FileRestored $event) => $event->file->is($file));
});
