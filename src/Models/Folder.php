<?php

namespace OiLab\OiLaravelAttachments\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OiLab\OiLaravelAttachments\Concerns\HasCreatorAndUpdater;
use OiLab\OiLaravelAttachments\Data\FolderData;
use OiLab\OiLaravelAttachments\Database\Factories\FolderFactory;
use OiLab\OiLaravelAttachments\Observers\FolderObserver;
use OiLab\OiLaravelAttachments\OiLaravelAttachments;

#[ObservedBy(FolderObserver::class)]
class Folder extends Model
{
    /** @use HasCreatorAndUpdater<Folder> */
    use HasCreatorAndUpdater;

    /** @use HasFactory<FolderFactory> */
    use HasFactory;

    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'uuid',
        'name',
        'parent_id',
        'props',
    ];

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return FolderFactory::new();
    }

    /**
     * Get the parent folder.
     *
     * @return BelongsTo<Folder, $this>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(OiLaravelAttachments::folderModel(), 'parent_id');
    }

    /**
     * Get the child folders.
     *
     * @return HasMany<Folder, $this>
     */
    public function children(): HasMany
    {
        return $this->hasMany(OiLaravelAttachments::folderModel(), 'parent_id');
    }

    /**
     * Get the files contained in this folder.
     *
     * @return HasMany<File, $this>
     */
    public function files(): HasMany
    {
        return $this->hasMany(OiLaravelAttachments::fileModel());
    }

    /**
     * Get a data transfer object representing this folder.
     */
    public function toData(): FolderData
    {
        return FolderData::from($this->toArray());
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'parent_id' => 'integer',
            'props' => 'array',
            ...$this->creatorAndUpdaterCasts(),
        ];
    }
}
