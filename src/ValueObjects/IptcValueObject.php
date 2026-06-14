<?php

namespace OiLab\OiLaravelAttachments\ValueObjects;

use JsonSerializable;

/**
 * IPTC Value Object
 *
 * Represents IPTC metadata extracted from image files.
 */
class IptcValueObject implements JsonSerializable
{
    /**
     * Create a new IPTC Value Object instance.
     *
     * @param  string|null  $credit  Credit/Provider
     * @param  string|null  $date_created  Date the content was created
     * @param  string|null  $date_time_original  Original date and time
     * @param  string|null  $digital_source_description  Description of the digital source
     * @param  string|null  $digital_source_type  Type of digital source
     */
    public function __construct(
        public ?string $credit = null,
        public ?string $date_created = null,
        public ?string $date_time_original = null,
        public ?string $digital_source_description = null,
        public ?string $digital_source_type = null
    ) {}

    /**
     * Create an IPTC Value Object from an array.
     *
     * @param  array<string, mixed>|null  $data  The data array from database
     */
    public static function fromArray(?array $data): self
    {
        if ($data === null || empty($data)) {
            return new self;
        }

        return new self(
            credit: $data['credit'] ?? null,
            date_created: $data['date_created'] ?? null,
            date_time_original: $data['date_time_original'] ?? null,
            digital_source_description: $data['digital_source_description'] ?? null,
            digital_source_type: $data['digital_source_type'] ?? null
        );
    }

    /**
     * Convert the IPTC Value Object to an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return array_filter([
            'credit' => $this->credit,
            'date_created' => $this->date_created,
            'date_time_original' => $this->date_time_original,
            'digital_source_description' => $this->digital_source_description,
            'digital_source_type' => $this->digital_source_type,
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
