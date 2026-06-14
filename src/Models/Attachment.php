<?php

namespace OiLab\OiLaravelAttachments\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;
use OiLab\OiLaravelAttachments\Concerns\HasCreatorAndUpdater;
use OiLab\OiLaravelAttachments\Concerns\HasSortable;
use OiLab\OiLaravelAttachments\Database\Factories\AttachmentFactory;
use OiLab\OiLaravelAttachments\Observers\AttachmentObserver;
use OiLab\OiLaravelAttachments\OiLaravelAttachments;

/**
 * Attachment Model
 *
 * Polymorphic pivot model linking files to any attachable model.
 * Supports multiple files per model with ordering and collection categorization.
 *
 * @property int $id Primary key
 * @property int $file_id Foreign key to the file
 * @property string $attachable_type Type of the parent model (polymorphic)
 * @property int $attachable_id ID of the parent model (polymorphic)
 * @property string $collection Collection name for categorization (default: 'default')
 * @property int $sort Display order (0-based)
 * @property int|null $created_by Foreign key to the user who created this record
 * @property int|null $updated_by Foreign key to the user who last updated this record
 * @property Carbon $created_at Creation timestamp
 * @property Carbon $updated_at Last update timestamp
 * @property-read File $file The attached file
 * @property-read Model $attachable The parent model this file is attached to
 */
#[ObservedBy(AttachmentObserver::class)]
class Attachment extends Model
{
    /** @use HasCreatorAndUpdater<Attachment> */
    use HasCreatorAndUpdater;

    /** @use HasFactory<AttachmentFactory> */
    use HasFactory;

    /** @use HasSortable<Attachment> */
    use HasSortable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'uuid',
        'file_id',
        'attachable_type',
        'attachable_id',
        'collection',
        'sort',
    ];

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return AttachmentFactory::new();
    }

    /**
     * Get the file associated with this attachment.
     *
     * @return BelongsTo<File, $this>
     */
    public function file(): BelongsTo
    {
        return $this->belongsTo(OiLaravelAttachments::fileModel());
    }

    /**
     * Get the parent attachable model.
     *
     * @return MorphTo<Model, $this>
     */
    public function attachable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'file_id' => 'integer',
            'attachable_id' => 'integer',
            'sort' => 'integer',
            ...$this->creatorAndUpdaterCasts(),
        ];
    }
}
