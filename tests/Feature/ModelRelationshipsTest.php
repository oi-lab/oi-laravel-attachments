<?php

use OiLab\OiLaravelAttachments\Models\Attachment;
use OiLab\OiLaravelAttachments\Models\File;
use OiLab\OiLaravelAttachments\Models\Folder;
use OiLab\OiLaravelAttachments\Tests\Fixtures\User;

it('generates a uuid for every model on creation', function () {
    $file = File::factory()->create();
    $folder = Folder::factory()->create();
    $user = User::factory()->create();
    $attachment = $user->attachFile($file);

    expect($file->uuid)->not->toBeNull()
        ->and($folder->uuid)->not->toBeNull()
        ->and($attachment->uuid)->not->toBeNull();
});

it('does not overwrite a provided uuid', function () {
    $file = File::factory()->create(['uuid' => 'fixed-uuid-value']);

    expect($file->uuid)->toBe('fixed-uuid-value');
});

it('relates a file to its folder', function () {
    $folder = Folder::factory()->create();
    $file = File::factory()->create(['folder_id' => $folder->id]);

    expect($file->folder->id)->toBe($folder->id)
        ->and($folder->files->pluck('id')->all())->toBe([$file->id]);
});

it('builds a nested folder tree', function () {
    $parent = Folder::factory()->create();
    $child = Folder::factory()->create(['parent_id' => $parent->id]);

    expect($child->parent->id)->toBe($parent->id)
        ->and($parent->children->pluck('id')->all())->toBe([$child->id]);
});

it('relates an attachment to its file and attachable', function () {
    $user = User::factory()->create();
    $file = File::factory()->create();
    $attachment = $user->attachFile($file);

    expect($attachment->file->id)->toBe($file->id)
        ->and($attachment->attachable)->toBeInstanceOf(User::class)
        ->and($attachment->attachable->id)->toBe($user->id);
});

it('lists the attachments of a file', function () {
    $user = User::factory()->create();
    $file = File::factory()->create();
    $user->attachFile($file);

    expect($file->attachments)->toHaveCount(1);
});

it('soft deletes files and folders', function () {
    $file = File::factory()->create();
    $folder = Folder::factory()->create();

    $file->delete();
    $folder->delete();

    expect(File::count())->toBe(0)
        ->and(File::withTrashed()->count())->toBe(1)
        ->and(Folder::withTrashed()->count())->toBe(1);
});

it('cascades attachment deletion when a file is force deleted', function () {
    $user = User::factory()->create();
    $file = File::factory()->create();
    $user->attachFile($file);

    $file->forceDelete();

    expect(Attachment::count())->toBe(0);
});
