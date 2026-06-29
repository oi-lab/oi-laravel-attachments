<?php

namespace OiLab\OiLaravelAttachments\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Local\LocalFilesystemAdapter;
use OiLab\OiLaravelAttachments\Casts\FileMetadataCast;
use OiLab\OiLaravelAttachments\Concerns\HasCreatorAndUpdater;
use OiLab\OiLaravelAttachments\Data\FileData;
use OiLab\OiLaravelAttachments\Database\Factories\FileFactory;
use OiLab\OiLaravelAttachments\Observers\FileObserver;
use OiLab\OiLaravelAttachments\OiLaravelAttachments;
use OiLab\OiLaravelAttachments\ValueObjects\FileMetadataValueObject;
use RuntimeException;

/**
 * File Model
 *
 * Represents a media file with metadata and storage information.
 * Files can be organized in folders and are tracked with uploader information.
 *
 * @property int $id Primary key
 * @property string $uuid Unique identifier (UUID v4)
 * @property string $filename_disk Name of the file stored on disk
 * @property string $filename_download Original filename for downloads
 * @property string|null $title Optional display title for the file
 * @property string|null $description Optional description of the file
 * @property string $mimetype MIME type of the file (e.g., image/jpeg, application/pdf)
 * @property int $filesize Size of the file in bytes
 * @property int|null $width Image width in pixels (for images only)
 * @property int|null $height Image height in pixels (for images only)
 * @property string $storage Storage location identifier (e.g., local, s3)
 * @property string|null $md5 MD5 hash of the file content for duplicate detection
 * @property FileMetadataValueObject $metadata File metadata including EXIF, IPTC, resolution, color info
 * @property int|null $folder_id Foreign key to the folder containing this file
 * @property int|null $created_by Foreign key to the user who created this record
 * @property int|null $updated_by Foreign key to the user who last updated this record
 * @property Carbon $created_at Creation timestamp
 * @property Carbon $updated_at Last update timestamp
 */
#[ObservedBy(FileObserver::class)]
class File extends Model
{
    /** @use HasCreatorAndUpdater<File> */
    use HasCreatorAndUpdater;

    /** @use HasFactory<FileFactory> */
    use HasFactory;

    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'uuid',
        'filename_disk',
        'filename_download',
        'title',
        'description',
        'mimetype',
        'filesize',
        'width',
        'height',
        'storage',
        'md5',
        'metadata',
        'folder_id',
        'props',
    ];

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return FileFactory::new();
    }

    /**
     * Get the folder that the file belongs to.
     *
     * @return BelongsTo<Folder, $this>
     */
    public function folder(): BelongsTo
    {
        return $this->belongsTo(OiLaravelAttachments::folderModel());
    }

    /**
     * Get all attachments for this file.
     *
     * @return HasMany<Attachment, $this>
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(OiLaravelAttachments::attachmentModel())->orderBy('sort');
    }

    /**
     * Check if the file is an image.
     */
    public function isImage(): bool
    {
        return str_starts_with($this->mimetype, 'image/');
    }

    /**
     * Check if the file is a video.
     */
    public function isVideo(): bool
    {
        return str_starts_with($this->mimetype, 'video/');
    }

    /**
     * Check if the file is an audio file.
     */
    public function isAudio(): bool
    {
        return str_starts_with($this->mimetype, 'audio/');
    }

    /**
     * Get the full filesystem path to the file.
     * Only works for local storage disks.
     *
     * @throws RuntimeException if the storage disk is not local
     */
    public function getFullPath(): string
    {
        $disk = Storage::disk($this->storage);

        if (! $disk->getAdapter() instanceof LocalFilesystemAdapter) {
            throw new RuntimeException(
                'getFullPath() only supports local storage. Use getStream() for remote storage.'
            );
        }

        return $disk->path($this->filename_disk);
    }

    /**
     * Get a stream resource for reading the file.
     * Works with both local and remote storage (S3, etc.).
     *
     * @return resource|null Stream resource, or null on failure
     */
    public function getStream()
    {
        return Storage::disk($this->storage)->readStream($this->filename_disk);
    }

    /**
     * Get a data transfer object representing this file.
     */
    public function toData(): FileData
    {
        return FileData::from([
            ...$this->toArray(),
            'metadata' => $this->metadata->toArray(),
        ]);
    }

    /**
     * Scope a query to search files by filename_disk, filename_download, title, or description.
     *
     * @param  Builder<File>  $query
     * @return Builder<File>
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->whereAny(
            ['filename_disk', 'filename_download', 'title', 'description'],
            'LIKE',
            "%{$search}%"
        );
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'filesize' => 'integer',
            'width' => 'integer',
            'height' => 'integer',
            'metadata' => FileMetadataCast::class,
            'folder_id' => 'integer',
            'props' => 'array',
            ...$this->creatorAndUpdaterCasts(),
        ];
    }
}
