<?php

namespace App\Console\Commands;

use App\Models\File;
use App\Models\User;
use Illuminate\Console\Command;
use ME\Hr\Models\HrEmployee;

/**
 * Before the User/HrEmployee id-collision fix, every HR employee photo uploaded
 * through the old code was tagged fileable_type=User (because the shared upload
 * helper only had a "Users" bucket). Whenever a User row and an HrEmployee row
 * happened to share the same numeric id, this caused genuine mix-ups and
 * overwrites (the original bug this project fixed).
 *
 * This command re-examines every files-table row currently tagged as a User
 * profile/banner/gallery image: if no User exists at that id but an HrEmployee
 * does, the row almost certainly belongs to that employee, not a user — so it
 * gets retagged. Rows are left untouched whenever both a User and an HrEmployee
 * exist at that id (genuinely ambiguous — reported for manual review) or when
 * neither exists (orphaned historical data, harmless).
 */
class FixEmployeeUserFileTagging extends Command
{
    protected $signature = 'files:fix-employee-user-tagging {--dry-run : Report what would change without writing anything}';

    protected $description = 'Retag files-table rows mistakenly attributed to a User that actually belong to an HrEmployee';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $files = File::where('fileable_type', User::class)
            ->whereIn('use_case', ['profile', 'banner', 'gallery'])
            ->get();

        $this->info("{$files->count()} User-tagged file row(s) to check.".($dryRun ? ' (dry run)' : ''));

        $retagged = 0;
        $ambiguous = [];
        $orphaned = 0;

        foreach ($files as $file) {
            $id = $file->fileable_id;
            $hasUser = User::withTrashed()->whereKey($id)->exists();
            $hasEmployee = HrEmployee::whereKey($id)->exists();

            if ($hasUser && $hasEmployee) {
                $ambiguous[] = $file->id;

                continue;
            }

            if (! $hasUser && $hasEmployee) {
                if (! $dryRun) {
                    $file->fileable_type = HrEmployee::class;
                    $file->meta = array_merge($file->meta ?? [], ['retagged_from' => User::class]);
                    $file->save();
                }
                $retagged++;

                continue;
            }

            if (! $hasUser && ! $hasEmployee) {
                $orphaned++;
            }
        }

        $this->info("Retagged User -> HrEmployee: {$retagged}.");
        $this->info("Orphaned (neither exists, left as-is): {$orphaned}.");

        if ($ambiguous) {
            $this->warn('Ambiguous (both a User and an HrEmployee exist at that id) — left untouched, review manually: file id(s) '.implode(', ', $ambiguous));
        }

        return self::SUCCESS;
    }
}
