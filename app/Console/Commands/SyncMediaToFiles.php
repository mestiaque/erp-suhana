<?php

namespace App\Console\Commands;

use App\Models\File;
use App\Models\Media;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use ME\Hr\Models\HrEmployee;
use ME\Hr\Models\HrEmployeeDocument;

/**
 * Non-destructive, idempotent sync from the legacy `media` table (and other
 * ad-hoc file columns/tables across the project) into the new central
 * `files` table.
 *
 * - Never modifies or deletes rows in `media`, `users`, or `hr_employee_documents`.
 * - Never touches any physical file on disk.
 * - Safe to re-run any time: existing synced rows are matched by
 *   (source_table, source_id) and updated in place instead of duplicated.
 */
class SyncMediaToFiles extends Command
{
    protected $signature = 'media:sync-to-files';

    protected $description = 'Sync media, user photo/signature, and HR employee documents into the central files table';

    public function handle(): int
    {
        $this->syncMediaTable();
        $this->syncUserColumn('photo', 'photo');
        $this->syncUserColumn('signature', 'signature');
        $this->syncHrEmployeeDocuments();
        $this->syncModelFileColumn(\ME\Hr\Models\HrFactory::class, 'hr_factories', 'hr_seal', 'hr_seal');
        $this->syncModelFileColumn(\ME\Hr\Models\HrFactory::class, 'hr_factories', 'hr_signature', 'hr_signature');

        $this->info('Sync complete.');

        return self::SUCCESS;
    }

    protected function syncMediaTable(): void
    {
        if (! Schema::hasTable('media')) {
            $this->info('media table not found (already retired), skipping.');

            return;
        }

        $count = 0;

        Media::query()->orderBy('id')->chunk(200, function ($medias) use (&$count) {
            foreach ($medias as $media) {
                [$fileableType, $useCase] = File::resolveFileable((int) $media->src_type, (int) $media->use_Of_file);

                // media.file_url holds the full relative path + filename;
                // media.file_path historically holds only the directory.
                $filePath = $this->stripPublicPrefix($media->file_url ?: $media->file_path);

                $file = File::firstOrNew([
                    'source_table' => 'media',
                    'source_id' => $media->id,
                ]);

                if (! $file->exists) {
                    $file->file_name = (string) Str::uuid();
                }

                $file->fileable_type = $fileableType;
                $file->fileable_id = $media->src_id;
                $file->use_case = $useCase;
                $file->original_name = $media->file_name;
                $file->file_path = $filePath;
                $file->file_full_path = $filePath ? asset($filePath) : null;
                $file->file_path_sm = $this->stripPublicPrefix($media->file_url_sm);
                $file->file_path_md = $this->stripPublicPrefix($media->file_url_md);
                $file->file_path_lg = $this->stripPublicPrefix($media->file_url_lg);
                $file->disk = 'legacy_public';
                $file->alt_text = $media->alt_text;
                $file->caption = $media->caption;
                $file->description = $media->description;
                $file->file_type = $this->guessMime($filePath);
                $file->extension = $filePath ? pathinfo($filePath, PATHINFO_EXTENSION) : null;
                $file->size = $media->file_size;
                $file->sort_order = $media->drag ?? 0;
                $file->meta = [
                    'legacy_src_type' => $media->src_type,
                    'legacy_use_of_file' => $media->use_Of_file,
                    'legacy_file_type_code' => $media->file_type,
                    'legacy_file_rename' => $media->file_rename,
                    'unmapped' => $fileableType === null,
                ];
                $file->addedby_id = $media->addedby_id;
                $file->editedby_id = $media->editedby_id;
                $file->save();

                $count++;
            }
        });

        $this->info("media -> files: {$count} row(s) synced.");
    }

    protected function syncUserColumn(string $column, string $useCase): void
    {
        $count = 0;

        User::query()->whereNotNull($column)->where($column, '<>', '')->orderBy('id')
            ->chunk(200, function ($users) use ($column, $useCase, &$count) {
                foreach ($users as $user) {
                    $rawPath = $user->{$column};
                    $relativePath = $this->stripStoragePrefix($rawPath);

                    $file = File::firstOrNew([
                        'source_table' => "users.{$column}",
                        'source_id' => $user->id,
                    ]);

                    if (! $file->exists) {
                        $file->file_name = (string) Str::uuid();
                    }

                    $file->fileable_type = User::class;
                    $file->fileable_id = $user->id;
                    $file->use_case = $useCase;
                    $file->original_name = basename($rawPath);
                    $file->file_path = $relativePath;
                    $file->file_full_path = asset($rawPath);
                    $file->disk = 'public';
                    $file->file_type = $this->guessMime($relativePath);
                    $file->extension = pathinfo($relativePath, PATHINFO_EXTENSION);
                    $file->meta = ['legacy_column' => $column];
                    $file->save();

                    $count++;
                }
            });

        $this->info("users.{$column} -> files: {$count} row(s) synced.");
    }

    /**
     * Generic backfill for any model that stores an uploaded file's relative
     * (public-disk) path directly on one of its own columns — e.g.
     * hr_factories.hr_seal / hr_signature.
     */
    protected function syncModelFileColumn(string $modelClass, string $table, string $column, string $useCase): void
    {
        if (! Schema::hasTable($table) || ! Schema::hasColumn($table, $column)) {
            $this->info("{$table}.{$column} -> files: table/column not found, skipping.");

            return;
        }

        $count = 0;

        $modelClass::query()->whereNotNull($column)->where($column, '<>', '')->orderBy('id')
            ->chunk(200, function ($items) use ($modelClass, $table, $column, $useCase, &$count) {
                foreach ($items as $item) {
                    $relativePath = $item->{$column};

                    $file = File::firstOrNew([
                        'source_table' => "{$table}.{$column}",
                        'source_id' => $item->id,
                    ]);

                    if (! $file->exists) {
                        $file->file_name = (string) Str::uuid();
                    }

                    $file->fileable_type = $modelClass;
                    $file->fileable_id = $item->id;
                    $file->use_case = $useCase;
                    $file->original_name = basename($relativePath);
                    $file->file_path = $relativePath;
                    $file->file_full_path = asset('storage/'.$relativePath);
                    $file->disk = 'public';
                    $file->file_type = $this->guessMime($relativePath);
                    $file->extension = pathinfo($relativePath, PATHINFO_EXTENSION);
                    $file->meta = ['legacy_column' => $column];
                    $file->save();

                    $count++;
                }
            });

        $this->info("{$table}.{$column} -> files: {$count} row(s) synced.");
    }

    protected function syncHrEmployeeDocuments(): void
    {
        if (! Schema::hasTable('hr_employee_documents')) {
            $this->info('hr_employee_documents table not found, skipping.');

            return;
        }

        $count = 0;

        HrEmployeeDocument::query()->orderBy('id')->chunk(200, function ($docs) use (&$count) {
            foreach ($docs as $doc) {
                $file = File::firstOrNew([
                    'source_table' => 'hr_employee_documents',
                    'source_id' => $doc->id,
                ]);

                if (! $file->exists) {
                    $file->file_name = (string) Str::uuid();
                }

                $file->fileable_type = HrEmployee::class;
                $file->fileable_id = $doc->employee_id;
                $file->use_case = 'documents';
                $file->original_name = $doc->file_name;
                $file->file_path = $doc->file_path;
                $file->file_full_path = $doc->file_path ? asset('storage/'.$doc->file_path) : null;
                $file->disk = 'public';
                $file->caption = $doc->title;
                $file->file_type = $this->guessMime($doc->file_path) ?: $doc->file_type;
                $file->extension = $doc->file_path ? pathinfo($doc->file_path, PATHINFO_EXTENSION) : $doc->file_type;
                $file->size = $doc->file_size;
                $file->meta = ['legacy_document_id' => $doc->id];
                $file->save();

                $count++;
            }
        });

        $this->info("hr_employee_documents -> files: {$count} row(s) synced.");
    }

    protected function stripPublicPrefix(?string $path): ?string
    {
        if (! $path) {
            return $path;
        }

        return str_starts_with($path, 'public/') ? substr($path, 7) : $path;
    }

    protected function stripStoragePrefix(?string $path): ?string
    {
        if (! $path) {
            return $path;
        }

        return str_starts_with($path, 'storage/') ? substr($path, 8) : $path;
    }

    protected function guessMime(?string $relativePath): ?string
    {
        if (! $relativePath) {
            return null;
        }

        $absolute = public_path($relativePath);

        if (is_file($absolute)) {
            $mime = @mime_content_type($absolute);
            if ($mime) {
                return $mime;
            }
        }

        $ext = strtolower(pathinfo($relativePath, PATHINFO_EXTENSION));

        return match ($ext) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            'webp' => 'image/webp',
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'zip' => 'application/zip',
            'rar' => 'application/vnd.rar',
            'mp4' => 'video/mp4',
            'webm' => 'video/webm',
            'mov' => 'video/quicktime',
            'mp3' => 'audio/mpeg',
            default => null,
        };
    }
}
