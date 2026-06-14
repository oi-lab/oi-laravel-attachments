<?php

use Illuminate\Support\Facades\Storage;
use OiLab\OiLaravelAttachments\Models\File;

it('detects image files', function () {
    $file = File::factory()->make(['mimetype' => 'image/jpeg']);

    expect($file->isImage())->toBeTrue()
        ->and($file->isVideo())->toBeFalse()
        ->and($file->isAudio())->toBeFalse();
});

it('detects video files', function () {
    $file = File::factory()->make(['mimetype' => 'video/mp4']);

    expect($file->isVideo())->toBeTrue()
        ->and($file->isImage())->toBeFalse();
});

it('detects audio files', function () {
    $file = File::factory()->make(['mimetype' => 'audio/mpeg']);

    expect($file->isAudio())->toBeTrue()
        ->and($file->isImage())->toBeFalse();
});

it('casts metadata to a value object', function () {
    $file = File::factory()->withMetadata(['width' => 640, 'height' => 480])->create();

    expect($file->fresh()->metadata->width)->toBe(640)
        ->and($file->fresh()->metadata->height)->toBe(480);
});

it('searches files by filename, title, or description', function () {
    File::factory()->create(['title' => 'Quarterly report', 'description' => null]);
    File::factory()->create(['title' => 'Holiday photo', 'description' => null]);

    expect(File::search('report')->count())->toBe(1)
        ->and(File::search('photo')->count())->toBe(1)
        ->and(File::search('missing')->count())->toBe(0);
});

it('reads a stored file as a stream', function () {
    Storage::fake('local');
    Storage::disk('local')->put('uploads/sample.txt', 'hello world');

    $file = File::factory()->create([
        'storage' => 'local',
        'filename_disk' => 'uploads/sample.txt',
    ]);

    $stream = $file->getStream();

    expect(stream_get_contents($stream))->toBe('hello world');

    fclose($stream);
});
