<?php

use OiLab\OiLaravelAttachments\Models\File;
use OiLab\OiLaravelAttachments\Tests\Fixtures\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('attaches a file to a model', function () {
    $file = File::factory()->create();

    $attachment = $this->user->attachFile($file);

    expect($attachment->file_id)->toBe($file->id)
        ->and($attachment->collection)->toBe('default')
        ->and($attachment->sort)->toBe(0)
        ->and($this->user->attachments)->toHaveCount(1);
});

it('accepts a file id as well as a model', function () {
    $file = File::factory()->create();

    $this->user->attachFile($file->id);

    expect($this->user->attachments)->toHaveCount(1);
});

it('appends each new attachment with an incremented sort', function () {
    $files = File::factory()->count(3)->create();

    foreach ($files as $file) {
        $this->user->attachFile($file);
    }

    expect($this->user->attachments()->pluck('sort')->all())->toBe([0, 1, 2]);
});

it('keeps collections independent', function () {
    $gallery = File::factory()->create();
    $cover = File::factory()->create();

    $this->user->attachFile($gallery, 'gallery');
    $this->user->attachFile($cover, 'cover');

    expect($this->user->attachments('gallery')->get())->toHaveCount(1)
        ->and($this->user->attachments('cover')->get())->toHaveCount(1)
        ->and($this->user->attachments('gallery')->first()->file_id)->toBe($gallery->id);
});

it('exposes the attached files through the accessor', function () {
    $files = File::factory()->count(2)->create();

    foreach ($files as $file) {
        $this->user->attachFile($file);
    }

    $this->user->load('attachments.file');

    expect($this->user->attached_files)->toHaveCount(2)
        ->and($this->user->attached_files->first())->toBeInstanceOf(File::class);
});

it('exposes a single attachment for single-file collections', function () {
    $file = File::factory()->create();
    $this->user->attachFile($file, 'cover');

    expect($this->user->singleAttachment('cover')->first()->file_id)->toBe($file->id);
});

it('detaches a file', function () {
    $file = File::factory()->create();
    $this->user->attachFile($file);

    $deleted = $this->user->detachFile($file);

    expect($deleted)->toBe(1)
        ->and($this->user->attachments()->count())->toBe(0);
});

it('detaches only from the given collection', function () {
    $file = File::factory()->create();
    $this->user->attachFile($file, 'gallery');
    $this->user->attachFile($file, 'cover');

    $this->user->detachFile($file, 'gallery');

    expect($this->user->attachments('gallery')->count())->toBe(0)
        ->and($this->user->attachments('cover')->count())->toBe(1);
});

it('syncs a collection by replacing its attachments', function () {
    $first = File::factory()->count(2)->create();
    $this->user->syncAttachments($first->pluck('id')->all(), 'gallery');

    $second = File::factory()->count(3)->create();
    $this->user->syncAttachments($second->pluck('id')->all(), 'gallery');

    expect($this->user->attachments('gallery')->pluck('file_id')->all())
        ->toBe($second->pluck('id')->all());
});

it('clears a collection when syncing an empty array', function () {
    $files = File::factory()->count(2)->create();
    $this->user->syncAttachments($files->pluck('id')->all(), 'gallery');

    $this->user->syncAttachments([], 'gallery');

    expect($this->user->attachments('gallery')->count())->toBe(0);
});

it('only syncs when the collection actually changes', function () {
    $files = File::factory()->count(2)->create();
    $ids = $files->pluck('id')->all();

    expect($this->user->syncAttachmentsIfChanged($ids, 'gallery'))->toBeTrue()
        ->and($this->user->syncAttachmentsIfChanged($ids, 'gallery'))->toBeFalse()
        ->and($this->user->syncAttachmentsIfChanged(array_reverse($ids), 'gallery'))->toBeTrue();
});

it('reorders attachments by file id', function () {
    $files = File::factory()->count(3)->create();

    foreach ($files as $file) {
        $this->user->attachFile($file, 'gallery');
    }

    $this->user->reorderAttachments([
        $files[0]->id => 2,
        $files[1]->id => 0,
        $files[2]->id => 1,
    ], 'gallery');

    expect($this->user->attachments('gallery')->pluck('file_id')->all())
        ->toBe([$files[1]->id, $files[2]->id, $files[0]->id]);
});
