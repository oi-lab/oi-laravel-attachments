<?php

namespace OiLab\OiLaravelAttachments\Data;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

/**
 * Data transfer object describing the polymorphic link between a file and an attachable model.
 */
class AttachmentData extends Data
{
    public function __construct(
        #[Required]
        public int $file_id,
        public ?int $id = null,
        #[Nullable]
        public ?string $uuid = null,
        #[Nullable, Max(255)]
        public ?string $attachable_type = null,
        #[Nullable]
        public ?int $attachable_id = null,
        #[Max(255)]
        public string $collection = 'default',
        public int $sort = 0,
        #[Nullable]
        public ?int $created_by = null,
        #[Nullable]
        public ?int $updated_by = null,
        public ?FileData $file = null,
    ) {}
}
