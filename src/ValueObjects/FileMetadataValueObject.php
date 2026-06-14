<?php

namespace OiLab\OiLaravelAttachments\ValueObjects;

use JsonSerializable;

/**
 * File Metadata Value Object
 *
 * Represents complete file metadata including resolution, EXIF, IPTC,
 * dimensions, color information, and aspect ratio.
 */
class FileMetadataValueObject implements JsonSerializable
{
    /**
     * Create a new File Metadata Value Object instance.
     *
     * @param  ResolutionValueObject|null  $resolution  Image resolution in DPI
     * @param  ExifValueObject|null  $exif  EXIF metadata from image
     * @param  IptcValueObject|null  $iptc  IPTC metadata from image
     * @param  int|null  $width  Image width in pixels
     * @param  int|null  $height  Image height in pixels
     * @param  string|null  $color_space  Color space (e.g., sRGB, Adobe RGB)
     * @param  string|null  $color_profile  ICC color profile name
     * @param  int|null  $bit_depth  Bit depth per channel
     * @param  float|null  $aspect_ratio  Aspect ratio (width/height)
     */
    public function __construct(
        public ?ResolutionValueObject $resolution = null,
        public ?ExifValueObject $exif = null,
        public ?IptcValueObject $iptc = null,
        public ?int $width = null,
        public ?int $height = null,
        public ?string $color_space = null,
        public ?string $color_profile = null,
        public ?int $bit_depth = null,
        public ?float $aspect_ratio = null
    ) {}

    /**
     * Create a File Metadata Value Object from an array.
     *
     * @param  array<string, mixed>|null  $data  The data array from database
     */
    public static function fromArray(?array $data): self
    {
        if ($data === null || empty($data)) {
            return new self;
        }

        return new self(
            resolution: isset($data['resolution']) && is_array($data['resolution'])
                ? ResolutionValueObject::fromArray($data['resolution'])
                : null,
            exif: isset($data['exif']) && is_array($data['exif'])
                ? ExifValueObject::fromArray($data['exif'])
                : null,
            iptc: isset($data['iptc']) && is_array($data['iptc'])
                ? IptcValueObject::fromArray($data['iptc'])
                : null,
            width: isset($data['width']) ? (int) $data['width'] : null,
            height: isset($data['height']) ? (int) $data['height'] : null,
            color_space: $data['color_space'] ?? null,
            color_profile: $data['color_profile'] ?? null,
            bit_depth: isset($data['bit_depth']) ? (int) $data['bit_depth'] : null,
            aspect_ratio: isset($data['aspect_ratio']) ? (float) $data['aspect_ratio'] : null
        );
    }

    /**
     * Convert the File Metadata Value Object to an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $array = [];

        if ($this->resolution !== null) {
            $resolutionArray = $this->resolution->toArray();
            if (! empty($resolutionArray)) {
                $array['resolution'] = $resolutionArray;
            }
        }

        if ($this->exif !== null) {
            $exifArray = $this->exif->toArray();
            if (! empty($exifArray)) {
                $array['exif'] = $exifArray;
            }
        }

        if ($this->iptc !== null) {
            $iptcArray = $this->iptc->toArray();
            if (! empty($iptcArray)) {
                $array['iptc'] = $iptcArray;
            }
        }

        if ($this->width !== null) {
            $array['width'] = $this->width;
        }

        if ($this->height !== null) {
            $array['height'] = $this->height;
        }

        if ($this->color_space !== null) {
            $array['color_space'] = $this->color_space;
        }

        if ($this->color_profile !== null) {
            $array['color_profile'] = $this->color_profile;
        }

        if ($this->bit_depth !== null) {
            $array['bit_depth'] = $this->bit_depth;
        }

        if ($this->aspect_ratio !== null) {
            $array['aspect_ratio'] = $this->aspect_ratio;
        }

        return $array;
    }

    /**
     * Specify data which should be serialized to JSON.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
