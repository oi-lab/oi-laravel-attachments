<?php

use OiLab\OiLaravelAttachments\Models\Attachment;
use OiLab\OiLaravelAttachments\Models\File;
use OiLab\OiLaravelAttachments\Tests\Fixtures\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->file = File::factory()->create();
});

function makeAttachment(User $user, File $file, int $sort): Attachment
{
    return Attachment::factory()
        ->forFile($file)
        ->sort($sort)
        ->create([
            'attachable_type' => $user->getMorphClass(),
            'attachable_id' => $user->id,
        ]);
}

it('orders records with the sorted scope', function () {
    makeAttachment($this->user, $this->file, 2);
    makeAttachment($this->user, $this->file, 0);
    makeAttachment($this->user, $this->file, 1);

    expect(Attachment::sorted()->pluck('sort')->all())->toBe([0, 1, 2])
        ->and(Attachment::sorted('desc')->pluck('sort')->all())->toBe([2, 1, 0]);
});

it('moves a record to an explicit position', function () {
    $attachment = makeAttachment($this->user, $this->file, 0);

    $attachment->moveToPosition(5);

    expect($attachment->fresh()->sort)->toBe(5);
});

it('moves a record up and down, clamping at zero', function () {
    $attachment = makeAttachment($this->user, $this->file, 3);

    $attachment->moveUp();
    expect($attachment->fresh()->sort)->toBe(2);

    $attachment->moveDown(2);
    expect($attachment->fresh()->sort)->toBe(4);

    $attachment->moveUp(10);
    expect($attachment->fresh()->sort)->toBe(0);
});

it('swaps positions with another record', function () {
    $a = makeAttachment($this->user, $this->file, 0);
    $b = makeAttachment($this->user, $this->file, 1);

    $a->swapWith($b);

    expect($a->fresh()->sort)->toBe(1)
        ->and($b->fresh()->sort)->toBe(0);
});
