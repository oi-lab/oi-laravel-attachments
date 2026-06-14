<?php

namespace OiLab\OiLaravelAttachments\ValueObjects;

use JsonSerializable;

/**
 * EXIF Value Object
 *
 * Represents EXIF metadata extracted from image files.
 */
class ExifValueObject implements JsonSerializable
{
    /**
     * Create a new EXIF Value Object instance.
     *
     * @param  string|null  $camera_make  Camera manufacturer
     * @param  string|null  $camera_model  Camera model
     * @param  string|null  $exposure_bias  Exposure bias value
     * @param  int|null  $exposure_program  Exposure program mode
     * @param  string|null  $exposure_time  Exposure time (shutter speed)
     * @param  string|null  $f_number  F-number (aperture)
     * @param  int|null  $flash  Flash mode
     * @param  string|null  $focal_length  Focal length in mm
     * @param  float|null  $gps_latitude  GPS latitude coordinate
     * @param  float|null  $gps_longitude  GPS longitude coordinate
     * @param  float|null  $gps_altitude  GPS altitude in meters
     * @param  int|null  $iso  ISO sensitivity
     * @param  int|null  $metering_mode  Light metering mode
     * @param  int|null  $orientation  Image orientation
     * @param  string|null  $software  Software used to process the image
     * @param  string|null  $taken_at  Date and time when photo was taken
     */
    public function __construct(
        public ?string $camera_make = null,
        public ?string $camera_model = null,
        public ?string $exposure_bias = null,
        public ?int $exposure_program = null,
        public ?string $exposure_time = null,
        public ?string $f_number = null,
        public ?int $flash = null,
        public ?string $focal_length = null,
        public ?float $gps_latitude = null,
        public ?float $gps_longitude = null,
        public ?float $gps_altitude = null,
        public ?int $iso = null,
        public ?int $metering_mode = null,
        public ?int $orientation = null,
        public ?string $software = null,
        public ?string $taken_at = null
    ) {}

    /**
     * Create an EXIF Value Object from an array.
     *
     * @param  array<string, mixed>|null  $data  The data array from database
     */
    public static function fromArray(?array $data): self
    {
        if ($data === null || empty($data)) {
            return new self;
        }

        return new self(
            camera_make: $data['camera_make'] ?? null,
            camera_model: $data['camera_model'] ?? null,
            exposure_bias: $data['exposure_bias'] ?? null,
            exposure_program: isset($data['exposure_program']) ? (int) $data['exposure_program'] : null,
            exposure_time: $data['exposure_time'] ?? null,
            f_number: $data['f_number'] ?? null,
            flash: isset($data['flash']) ? (int) $data['flash'] : null,
            focal_length: $data['focal_length'] ?? null,
            gps_latitude: isset($data['gps_latitude']) ? (float) $data['gps_latitude'] : null,
            gps_longitude: isset($data['gps_longitude']) ? (float) $data['gps_longitude'] : null,
            gps_altitude: isset($data['gps_altitude']) ? (float) $data['gps_altitude'] : null,
            iso: isset($data['iso']) ? (int) $data['iso'] : null,
            metering_mode: isset($data['metering_mode']) ? (int) $data['metering_mode'] : null,
            orientation: isset($data['orientation']) ? (int) $data['orientation'] : null,
            software: $data['software'] ?? null,
            taken_at: $data['taken_at'] ?? null
        );
    }

    /**
     * Convert the EXIF Value Object to an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return array_filter([
            'camera_make' => $this->camera_make,
            'camera_model' => $this->camera_model,
            'exposure_bias' => $this->exposure_bias,
            'exposure_program' => $this->exposure_program,
            'exposure_time' => $this->exposure_time,
            'f_number' => $this->f_number,
            'flash' => $this->flash,
            'focal_length' => $this->focal_length,
            'gps_latitude' => $this->gps_latitude,
            'gps_longitude' => $this->gps_longitude,
            'gps_altitude' => $this->gps_altitude,
            'iso' => $this->iso,
            'metering_mode' => $this->metering_mode,
            'orientation' => $this->orientation,
            'software' => $this->software,
            'taken_at' => $this->taken_at,
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
