<?php

namespace OiLab\OiLaravelAttachments\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use OiLab\OiLaravelAttachments\ValueObjects\FileMetadataValueObject;

/**
 * File Metadata Cast
 *
 * Casts the metadata JSON column to a FileMetadataValueObject for object-oriented access.
 * Used on the File model to provide structured access to image metadata including
 * resolution, EXIF, IPTC, dimensions, color information, and aspect ratio.
 *
 * @implements CastsAttributes<FileMetadataValueObject, array<string, mixed>>
 */
class FileMetadataCast implements CastsAttributes
{
    /**
     * Cast the given value from the database to a FileMetadataValueObject.
     *
     * @param  Model  $model  The model instance
     * @param  string  $key  The attribute key
     * @param  mixed  $value  The raw value from database (JSON string or null)
     * @param  array<string, mixed>  $attributes  All model attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): FileMetadataValueObject
    {
        if ($value === null || $value === '') {
            return FileMetadataValueObject::fromArray(null);
        }

        $decoded = is_string($value) ? json_decode($value, true) : $value;

        return FileMetadataValueObject::fromArray($decoded);
    }

    /**
     * Prepare the given value for storage in the database.
     *
     * @param  Model  $model  The model instance
     * @param  string  $key  The attribute key
     * @param  mixed  $value  The FileMetadataValueObject or array
     * @param  array<string, mixed>  $attributes  All model attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof FileMetadataValueObject) {
            $array = $value->toArray();

            return empty($array) ? null : json_encode($array);
        }

        if (is_array($value)) {
            return empty($value) ? null : json_encode($value);
        }

        return null;
    }
}
