<?php

declare(strict_types=1);

namespace OiLab\OiLaravelAttachments\Concerns;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use OiLab\OiLaravelAttachments\OiLaravelAttachments;

/**
 * Trait HasCreatorAndUpdater
 *
 * Provides automatic tracking of creator and updater users.
 * Adds created_by and updated_by fields with their relationships.
 *
 * @property int|null $created_by
 * @property int|null $updated_by
 */
trait HasCreatorAndUpdater
{
    /**
     * Boot the trait.
     */
    protected static function bootHasCreatorAndUpdater(): void
    {
        static::creating(function ($model): void {
            if (Auth::check() && $model->created_by === null) {
                $model->created_by = Auth::id();
            }
        });

        static::updating(function ($model): void {
            if (Auth::check()) {
                $model->updated_by = Auth::id();
            }
        });
    }

    /**
     * Initialize the trait.
     */
    public function initializeHasCreatorAndUpdater(): void
    {
        $this->fillable = array_merge($this->fillable, [
            'created_by',
            'updated_by',
        ]);
    }

    /**
     * Get the user who created this record.
     */
    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(OiLaravelAttachments::userModel(), 'created_by');
    }

    /**
     * Get the user who last updated this record.
     */
    public function updatedByUser(): BelongsTo
    {
        return $this->belongsTo(OiLaravelAttachments::userModel(), 'updated_by');
    }

    /**
     * Get the casts array for the trait.
     *
     * @return array<string, string>
     */
    protected function creatorAndUpdaterCasts(): array
    {
        return [
            'created_by' => 'integer',
            'updated_by' => 'integer',
        ];
    }
}
