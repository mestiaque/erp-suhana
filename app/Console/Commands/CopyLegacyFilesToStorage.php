<?php

namespace App\Console\Commands;

use App\Models\File;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

/**
 * Copies (never moves) physical files that still live under public/ (disk =
 * 'legacy_public', from the original pre-files-table media uploads) into the
 * storage/app/public disk, and repoints the File row at the copy.
 *
 * - The original file under public/ is left in place untouched.
 * - Safe to re-run: rows already on the 'public' disk are skipped.
 */
class CopyLegacyFilesToStorage extends Command
{
    protected $signature = 'files:copy-legacy-to-storage {--dry-run : Report what would happen without copying or writing anything}';

    protected $description = 'Copy legacy public/ files into storage/app/public and repoint their files-table row (originals are kept in place)';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $query = File::where('disk', 'legacy_public')->whereNotNull('file_path');
        $total = $query->count();
        $this->info("{$total} legacy file row(s) to process.".($dryRun ? ' (dry run)' : ''));

        $copied = 0;
        $missing = 0;
        $alreadyThere = 0;

        // chunkById(), not chunk(): this loop mutates the very `disk` column the WHERE
        // clause filters on, which would shift chunk()'s LIMIT/OFFSET window mid-iteration
        // and silently skip rows. chunkById() re-queries by primary key instead, immune to that.
        $query->chunkById(100, function ($files) use (&$copied, &$missing, &$alreadyThere, $dryRun) {
            foreach ($files as $file) {
                $sourcePath = public_path($file->file_path);

                if (! is_file($sourcePath)) {
                    $this->warn("Missing on disk, skipped: {$file->file_path} (file id {$file->id})");
                    $missing++;

                    continue;
                }

                if (Storage::disk('public')->exists($file->file_path)) {
                    $alreadyThere++;
                } elseif (! $dryRun) {
                    Storage::disk('public')->put($file->file_path, file_get_contents($sourcePath));
                }

                if (! $dryRun) {
                    $file->disk = 'public';
                    $file->file_full_path = asset('storage/'.$file->file_path);
                    $file->save();
                }

                $copied++;
            }
        });

        $this->info("Copied/repointed: {$copied}, already present: {$alreadyThere}, missing source file: {$missing}.");

        return self::SUCCESS;
    }
}
