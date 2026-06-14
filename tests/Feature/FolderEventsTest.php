<?php

use Illuminate\Support\Facades\Event;
use OiLab\OiLaravelAttachments\Events\FolderCreated;
use OiLab\OiLaravelAttachments\Events\FolderDeleted;
use OiLab\OiLaravelAttachments\Events\FolderMoved;
use OiLab\OiLaravelAttachments\Events\FolderRestored;
use OiLab\OiLaravelAttachments\Events\FolderUpdated;
use OiLab\OiLaravelAttachments\Models\Folder;

// Fake only the folder events so Eloquent model events (and the UUID observer) still fire.
const FOLDER_EVENTS = [
    FolderCreated::class,
    FolderUpdated::class,
    FolderDeleted::class,
    FolderRestored::class,
    FolderMoved::class,
];

it('dispatches FolderCreated when a folder is created', function () {
    Event::fake(FOLDER_EVENTS);

    $folder = Folder::factory()->create();

    Event::assertDispatched(FolderCreated::class, fn (FolderCreated $event) => $event->folder->is($folder));
});

it('dispatches FolderUpdated when a folder is updated', function () {
    $folder = Folder::factory()->create();

    Event::fake(FOLDER_EVENTS);

    $folder->update(['name' => 'Renamed']);

    Event::assertDispatched(FolderUpdated::class, fn (FolderUpdated $event) => $event->folder->is($folder));
    Event::assertNotDispatched(FolderMoved::class);
});

it('dispatches FolderMoved when the parent changes', function () {
    $parent = Folder::factory()->create();
    $folder = Folder::factory()->create();

    Event::fake(FOLDER_EVENTS);

    $folder->update(['parent_id' => $parent->id]);

    Event::assertDispatched(FolderMoved::class, function (FolderMoved $event) use ($folder, $parent) {
        return $event->folder->is($folder)
            && $event->fromParentId === null
            && $event->toParentId === $parent->id;
    });

    // A move is also an update.
    Event::assertDispatched(FolderUpdated::class);
});

it('dispatches FolderDeleted when a folder is soft deleted', function () {
    $folder = Folder::factory()->create();

    Event::fake(FOLDER_EVENTS);

    $folder->delete();

    Event::assertDispatched(FolderDeleted::class, fn (FolderDeleted $event) => $event->folder->is($folder));
});

it('dispatches FolderRestored when a folder is restored', function () {
    $folder = Folder::factory()->create();
    $folder->delete();

    Event::fake(FOLDER_EVENTS);

    $folder->restore();

    Event::assertDispatched(FolderRestored::class, fn (FolderRestored $event) => $event->folder->is($folder));
});
