<?php

namespace OiLab\OiLaravelAttachments\Data;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

/**
 * Data transfer object describing a stored file.
 */
class FileData extends Data
{
    /**
     * @param  array<string, mixed>|null  $metadata
     * @param  array<string, mixed>|null  $props
     */
    public function __construct(
        #[Required, Max(255)]
        public string $filename_disk,
        #[Required, Max(255)]
        public string $filename_download,
        #[Required, Max(255)]
        public string $mimetype,
        #[Required]
        public int $filesize,
        public ?int $id = null,
        #[Nullable]
        public ?string $uuid = null,
        #[Nullable, Max(255)]
        public ?string $title = null,
        #[Nullable]
        public ?string $description = null,
        #[Nullable]
        public ?int $width = null,
        #[Nullable]
        public ?int $height = null,
        #[Max(255)]
        public string $storage = 'local',
        #[Nullable, Max(32)]
        public ?string $md5 = null,
        #[Nullable]
        public ?array $metadata = null,
        #[Nullable]
        public ?int $folder_id = null,
        #[Nullable]
        public ?array $props = null,
        #[Nullable]
        public ?int $created_by = null,
        #[Nullable]
        public ?int $updated_by = null,
    ) {}
}
