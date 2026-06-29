<?php

namespace OiLab\OiLaravelAttachments\Data;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

/**
 * Data transfer object describing a folder.
 */
class FolderData extends Data
{
    /**
     * @param  array<string, mixed>|null  $props
     */
    public function __construct(
        #[Required, Max(255)]
        public string $name,
        public ?int $id = null,
        #[Nullable]
        public ?string $uuid = null,
        #[Nullable]
        public ?int $parent_id = null,
        #[Nullable]
        public ?array $props = null,
        #[Nullable]
        public ?int $created_by = null,
        #[Nullable]
        public ?int $updated_by = null,
    ) {}
}
