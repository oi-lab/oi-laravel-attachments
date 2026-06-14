<?php

use OiLab\OiLaravelAttachments\Models\File;
use OiLab\OiLaravelAttachments\Tests\Fixtures\User;

it('records the authenticated user as creator on creation', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $file = File::factory()->create(['created_by' => null]);

    expect($file->created_by)->toBe($user->id)
        ->and($file->createdByUser->id)->toBe($user->id);
});

it('records the authenticated user as updater on update', function () {
    $author = User::factory()->create();
    $editor = User::factory()->create();

    $file = File::factory()->create(['created_by' => $author->id]);

    $this->actingAs($editor);
    $file->update(['title' => 'Renamed']);

    expect($file->fresh()->updated_by)->toBe($editor->id)
        ->and($file->fresh()->updatedByUser->id)->toBe($editor->id);
});

it('leaves audit columns null when no user is authenticated', function () {
    $file = File::factory()->create(['created_by' => null]);

    expect($file->created_by)->toBeNull()
        ->and($file->updated_by)->toBeNull();
});
