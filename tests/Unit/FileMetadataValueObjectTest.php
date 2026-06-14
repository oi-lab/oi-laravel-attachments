<?php

use OiLab\OiLaravelAttachments\ValueObjects\ExifValueObject;
use OiLab\OiLaravelAttachments\ValueObjects\FileMetadataValueObject;
use OiLab\OiLaravelAttachments\ValueObjects\IptcValueObject;
use OiLab\OiLaravelAttachments\ValueObjects\ResolutionValueObject;

it('builds an empty value object from null', function () {
    $meta = FileMetadataValueObject::fromArray(null);

    expect($meta->width)->toBeNull()
        ->and($meta->height)->toBeNull()
        ->and($meta->resolution)->toBeNull()
        ->and($meta->exif)->toBeNull()
        ->and($meta->iptc)->toBeNull();
});

it('builds an empty value object from an empty array', function () {
    $meta = FileMetadataValueObject::fromArray([]);

    expect($meta)->toBeInstanceOf(FileMetadataValueObject::class)
        ->and($meta->width)->toBeNull();
});

it('hydrates scalar properties from an array', function () {
    $meta = FileMetadataValueObject::fromArray([
        'width' => 1920,
        'height' => 1080,
        'aspect_ratio' => 1.777,
        'color_space' => 'sRGB',
        'color_profile' => 'Display P3',
        'bit_depth' => 8,
    ]);

    expect($meta->width)->toBe(1920)
        ->and($meta->height)->toBe(1080)
        ->and($meta->aspect_ratio)->toBe(1.777)
        ->and($meta->color_space)->toBe('sRGB')
        ->and($meta->color_profile)->toBe('Display P3')
        ->and($meta->bit_depth)->toBe(8);
});

it('hydrates nested value objects when their data is present', function () {
    $meta = FileMetadataValueObject::fromArray([
        'resolution' => ['x' => 72, 'y' => 72],
        'exif' => ['make' => 'Canon'],
        'iptc' => ['caption' => 'A photo'],
    ]);

    expect($meta->resolution)->toBeInstanceOf(ResolutionValueObject::class)
        ->and($meta->exif)->toBeInstanceOf(ExifValueObject::class)
        ->and($meta->iptc)->toBeInstanceOf(IptcValueObject::class);
});

it('is json serializable', function () {
    $meta = FileMetadataValueObject::fromArray([
        'width' => 800,
        'height' => 600,
    ]);

    $json = json_encode($meta);

    expect($json)->toBeJson()
        ->and(json_decode($json, true))->toHaveKeys(['width', 'height']);
});
