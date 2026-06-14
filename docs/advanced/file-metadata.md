---
title: File Metadata
description: The FileMetadataValueObject and its nested value objects
section: advanced
order: 2
---

# File Metadata

The `File` model's `metadata` column is a JSON column cast to a `FileMetadataValueObject` through `FileMetadataCast`. This gives you typed, object-oriented access to image metadata instead of decoding raw JSON.

## FileMetadataValueObject

```php
$meta = $file->metadata;

$meta->width;          // ?int  — pixels
$meta->height;         // ?int  — pixels
$meta->aspect_ratio;   // ?float — width / height
$meta->color_space;    // ?string — e.g. sRGB, Adobe RGB
$meta->color_profile;  // ?string — ICC profile name
$meta->bit_depth;      // ?int — bits per channel
$meta->resolution;     // ?ResolutionValueObject
$meta->exif;           // ?ExifValueObject
$meta->iptc;           // ?IptcValueObject
```

The value object is `JsonSerializable`, so it round-trips cleanly back to the database and serializes naturally in API responses.

## Nested value objects

| Property | Type | Holds |
|----------|------|-------|
| `resolution` | `ResolutionValueObject` | Image resolution in DPI |
| `exif` | `ExifValueObject` | EXIF metadata extracted from the image |
| `iptc` | `IptcValueObject` | IPTC metadata extracted from the image |

Each nested object is `null` when the corresponding data is absent, so guard before accessing it:

```php
if ($file->metadata->exif !== null) {
    // read EXIF fields
}
```

## Building metadata

A `FileMetadataValueObject` can be created from an array (e.g. the JSON stored in the column):

```php
use OiLab\OiLaravelAttachments\ValueObjects\FileMetadataValueObject;

$meta = FileMetadataValueObject::fromArray([
    'width' => 1920,
    'height' => 1080,
    'aspect_ratio' => 1.777,
    'resolution' => ['x' => 72, 'y' => 72],
]);
```

Passing `null` or an empty array returns an empty value object with all properties set to `null`.

## Assigning metadata

Because the column is cast, you can assign a value object (or an array) and Laravel will serialize it on save:

```php
$file->metadata = FileMetadataValueObject::fromArray([
    'width' => 800,
    'height' => 600,
]);

$file->save();
```
