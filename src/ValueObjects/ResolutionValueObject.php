<?php

namespace OiLab\OiLaravelAttachments\ValueObjects;

use JsonSerializable;

/**
 * Resolution Value Object
 *
 * Represents image resolution in DPI (dots per inch).
 */
class ResolutionValueObject implements JsonSerializable
{
    /**
     * Create a new Resolution Value Object instance.
     *
     * @param  float|null  $x  Horizontal resolution in DPI
     * @param  float|null  $y  Vertical resolution in DPI
     */
    public function __construct(
        public ?float $x = null,
        public ?float $y = null
    ) {}

    /**
     * Create a Resolution Value Object from an array.
     *
     * @param  array<string, mixed>|null  $data  The data array from database
     */
    public static function fromArray(?array $data): self
    {
        if ($data === null || empty($data)) {
            return new self;
        }

        return new self(
            x: isset($data['x']) ? (float) $data['x'] : null,
            y: isset($data['y']) ? (float) $data['y'] : null
        );
    }

    /**
     * Convert the Resolution Value Object to an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return array_filter([
            'x' => $this->x,
            'y' => $this->y,
        ], fn ($value) => $value !== null);
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
