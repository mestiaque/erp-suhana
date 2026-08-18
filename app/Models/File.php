<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

class File extends Model
{
    protected $table = 'files';

    protected $guarded = [];

    protected $casts = [
        'meta' => 'array',
    ];

    public function fileable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * [legacy src_type][legacy use_Of_file] => [fileable class, use_case]
     * Shared by the uploadFile() helper and the media->files sync command
     * so both agree on the same mapping.
     */
    public static function legacySrcTypeMap(): array
    {
        return [
            3 => [ // Attribute
                1 => [Attribute::class, 'image'],
                2 => [Attribute::class, 'banner'],
                3 => [Attribute::class, 'gallery'],
            ],
            6 => [ // Users
                1 => [User::class, 'profile'],
                2 => [User::class, 'banner'],
                3 => [User::class, 'gallery'],
            ],
            8 => [ // HR Employees
                1 => ['ME\\Hr\\Models\\HrEmployee', 'profile'],
            ],
        ];
    }

    public static function resolveFileable(int $srcType, int $fileUse): array
    {
        return static::legacySrcTypeMap()[$srcType][$fileUse] ?? [null, 'general'];
    }

    /**
     * Central per-module storage folder taxonomy (on the 'public' disk), so
     * new uploads land under a predictable module path instead of a flat
     * 'medies/<month>' bucket. Existing files already on disk keep their
     * original path — this only governs where new uploads are written.
     */
    public static function storageFolderFor(?string $fileableType, string $useCase, $fileableId = null): string
    {
        return match ($fileableType) {
            User::class => 'user/'.$useCase,
            General::class => 'general/'.$useCase,
            'ME\\Hr\\Models\\HrEmployee' => $useCase === 'documents'
                ? 'hr/document'.($fileableId ? '/'.$fileableId : '')
                : 'hr/employee',
            'ME\\Hr\\Models\\HrEmployeeNominee' => 'hr/nominee',
            'ME\\Hr\\Models\\HrFactory' => 'hr/factory',
            'ME\\AccSfl\\Models\\AcExpense' => 'accounts/expense',
            'ME\\AccSfl\\Models\\AcExpenseIou' => 'accounts/iou',
            default => 'general/other',
        };
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'addedby_id');
    }

    public function scopeUseCase($query, string $useCase)
    {
        return $query->where('use_case', $useCase);
    }

    /**
     * Absolute filesystem path to the file, resolved from its disk.
     */
    public function absolutePath(?string $path = null): ?string
    {
        $path = $path ?? $this->file_path;

        if (! $path) {
            return null;
        }

        if ($this->disk && $this->disk !== 'legacy_public' && Storage::disk($this->disk)->exists($path)) {
            return Storage::disk($this->disk)->path($path);
        }

        return public_path($path);
    }

    // ---- Legacy Media-model compatible accessors ----
    // These let existing code (e.g. User::image(), HrEmployee::image()) keep
    // reading the same attribute names it always has, without changes.

    public function getFileUrlAttribute()
    {
        if ($this->file_path) {
            return $this->urlFor($this->file_path);
        }

        return $this->file_full_path;
    }

    public function getFileUrlSmAttribute()
    {
        return $this->file_path_sm ? $this->urlFor($this->file_path_sm) : $this->getFileUrlAttribute();
    }

    public function getFileUrlMdAttribute()
    {
        return $this->file_path_md ? $this->urlFor($this->file_path_md) : $this->getFileUrlAttribute();
    }

    public function getFileUrlLgAttribute()
    {
        return $this->file_path_lg ? $this->urlFor($this->file_path_lg) : $this->getFileUrlAttribute();
    }

    public function getFileRenameAttribute()
    {
        return $this->file_name;
    }

    protected function urlFor(string $path): string
    {
        // Storage::disk()->url() bakes in the static APP_URL config, which can
        // drift from the host actually serving the request (e.g. local `artisan serve`
        // on a non-standard port). asset() resolves against the real request root instead.
        if ($this->disk === 'public') {
            return asset('storage/'.$path);
        }

        return asset($path);
    }
}
