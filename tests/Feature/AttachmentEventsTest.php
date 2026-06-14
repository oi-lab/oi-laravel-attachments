<?php

use Illuminate\Support\Facades\Event;
use OiLab\OiLaravelAttachments\Events\AttachmentCreated;
use OiLab\OiLaravelAttachments\Events\AttachmentDeleted;
use OiLab\OiLaravelAttachments\Events\AttachmentUpdated;
use OiLab\OiLaravelAttachments\Events\FileDetached;
use OiLab\OiLaravelAttachments\Models\File;
use OiLab\OiLaravelAttachments\Tests\Fixtures\User;

// Fake only the attachment lifecycle events so the UUID observer still fires.
const ATTACHMENT_EVENTS = [
    AttachmentCreated::class,
    AttachmentUpdated::class,
    AttachmentDeleted::class,
];

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->file = File::factory()->create();
});

it('dispatches AttachmentCreated when an attachment is created', function () {
    Event::fake(ATTACHMENT_EVENTS);

    $attachment = $this->user->attachFile($this->file, 'gallery');

    Event::assertDispatched(AttachmentCreated::class, function (AttachmentCreated $event) use ($attachment) {
        return $event->attachment->is($attachment)
            && $event->attachment->collection === 'gallery';
    });
});

it('dispatches AttachmentUpdated when an attachment is updated', function () {
    $attachment = $this->user->attachFile($this->file, 'gallery');

    Event::fake(ATTACHMENT_EVENTS);

    $attachment->moveToPosition(5);

    Event::assertDispatched(AttachmentUpdated::class, function (AttachmentUpdated $event) use ($attachment) {
        return $event->attachment->is($attachment)
            && $event->attachment->sort === 5;
    });
});

it('dispatches AttachmentDeleted when an attachment model is deleted', function () {
    $attachment = $this->user->attachFile($this->file, 'gallery');

    Event::fake(ATTACHMENT_EVENTS);

    $attachment->delete();

    Event::assertDispatched(AttachmentDeleted::class, fn (AttachmentDeleted $event) => $event->attachment->is($attachment));
});

it('does not dispatch AttachmentDeleted for a bulk detach (emits FileDetached instead)', function () {
    $this->user->attachFile($this->file, 'gallery');

    Event::fake([AttachmentDeleted::class, FileDetached::class]);

    $this->user->detachFile($this->file, 'gallery');

    Event::assertNotDispatched(AttachmentDeleted::class);
    Event::assertDispatched(FileDetached::class);
});
