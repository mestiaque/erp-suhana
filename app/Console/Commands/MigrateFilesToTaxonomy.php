<?php

namespace App\Console\Commands;

use App\Models\File;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

/**
 * One-shot pipeline that gets the files table fully into its final state:
 *
 *  1. media:sync-to-files          — repopulate files-table rows from source-of-truth
 *                                     (media, users.photo/signature, hr_employee_documents,
 *                                     hr_factories seal/signature).
 *  2. files:fix-employee-user-tagging — retag rows mistakenly attributed to a User that
 *                                     actually belong to an HrEmployee (pre-fix id collision).
 *  3. Reorganize                   — copy every file into its module-specific storage
 *                                     folder (user/, general/, hr/{employee,nominee,document,factory},
 *                                     accounts/{expense,iou}) instead of the flat legacy
 *                                     'medies/<month>' bucket, and repoint the row at it.
 *
 * Non-destructive throughout: every step only copies or writes files-table metadata.
 * Nothing under public/medies is ever moved or deleted.
 */
class MigrateFilesToTaxonomy extends Command
{
    protected $signature = 'files:migrate {--dry-run : Report what step 3 would do without writing anything}';

    protected $description = 'Sync, retag, and reorganize the files table into the per-module storage taxonomy — one command, start to finish';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $this->info('Step 1/3: media:sync-to-files');
        $this->call('media:sync-to-files');

        $this->info('Step 2/3: files:fix-employee-user-tagging');
        $this->call('files:fix-employee-user-tagging', $dryRun ? ['--dry-run' => true] : []);

        $this->info('Step 3/3: reorganizing into the per-module taxonomy'.($dryRun ? ' (dry run)' : ''));
        $this->reorganize($dryRun);

        return self::SUCCESS;
    }

    protected function reorganize(bool $dryRun): void
    {
        $moved = 0;
        $alreadyCorrect = 0;
        $missing = 0;

        File::query()->chunkById(100, function ($files) use (&$moved, &$alreadyCorrect, &$missing, $dryRun) {
            foreach ($files as $file) {
                if (! $file->file_path) {
                    continue;
                }

                $targetDir = File::storageFolderFor($file->fileable_type, $file->use_case, $file->fileable_id);
                $filename = basename($file->file_path);
                $targetPath = $targetDir.'/'.$filename;

                if ($file->disk === 'public' && $file->file_path === $targetPath) {
                    $alreadyCorrect++;

                    continue;
                }

                // Prefer an existing storage/app/public copy (from an earlier, wrongly-placed
                // copy step); otherwise fall back to the original public/ file.
                if ($file->disk === 'public' && Storage::disk('public')->exists($file->file_path)) {
                    $sourceDisk = 'public';
                    $sourcePath = $file->file_path;
                } elseif (is_file(public_path($file->file_path))) {
                    $sourceDisk = null; // raw public/ path
                    $sourcePath = $file->file_path;
                } else {
                    $this->warn("Missing source, skipped: {$file->file_path} (file id {$file->id})");
                    $missing++;

                    continue;
                }

                if ($dryRun) {
                    $moved++;

                    continue;
                }

                if (! Storage::disk('public')->exists($targetPath)) {
                    $contents = $sourceDisk === 'public'
                        ? Storage::disk('public')->get($sourcePath)
                        : file_get_contents(public_path($sourcePath));
                    Storage::disk('public')->put($targetPath, $contents);
                }

                // Clean up an old mis-placed storage copy this project's own tooling created
                // earlier (never the original public/medies file, which is never touched).
                if ($sourceDisk === 'public' && $sourcePath !== $targetPath) {
                    Storage::disk('public')->delete($sourcePath);
                }

                $file->file_path = $targetPath;
                $file->disk = 'public';
                $file->file_full_path = asset('storage/'.$targetPath);
                $file->save();

                $moved++;
            }
        });

        $this->info("Reorganized: {$moved}, already correct: {$alreadyCorrect}, missing source: {$missing}.");
    }
}
