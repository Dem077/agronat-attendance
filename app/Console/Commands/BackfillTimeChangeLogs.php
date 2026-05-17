<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use App\Models\TimeChangeLog;
use App\Models\TimeSheet;
use App\Models\User;
use Illuminate\Console\Command;

class BackfillTimeChangeLogs extends Command
{
    protected $signature = 'timesheet:backfill-change-logs {--dry-run : Preview without creating logs}';

    protected $description = 'Backfill INSERT time_change_logs for existing timesheets where logged_by is not 0';

    public function handle()
    {
        $dryRun = (bool) $this->option('dry-run');

        $query = TimeSheet::query()
            ->select(['id', 'user_id', 'punch', 'logged_by'])
            ->where('logged_by', '!=', 0)
            ->orderBy('id');

        $total = (clone $query)->count();
        if ($total === 0) {
            $this->info('No timesheet rows found with logged_by != 0.');
            return Command::SUCCESS;
        }

        $this->info(($dryRun ? '[DRY RUN] ' : '') . "Scanning {$total} timesheet rows...");

        $created = 0;
        $skippedExisting = 0;
        $skippedAttendance = 0;

        $query->chunkById(1000, function ($timeSheets) use (&$created, &$skippedExisting, &$skippedAttendance, $dryRun) {
            $timeSheetIds = $timeSheets->pluck('id')->all();
            $existingInsertLogIds = TimeChangeLog::whereIn('time_sheet_id', $timeSheetIds)
                ->where('type', 'INSERT')
                ->pluck('time_sheet_id')
                ->flip();

            $changedByNames = User::whereIn('id', $timeSheets->pluck('logged_by')->unique()->values())
                ->pluck('name', 'id');

            $attendanceKeys = [];
            $userIds = [];
            $dates = [];
            foreach ($timeSheets as $timeSheet) {
                if (isset($existingInsertLogIds[$timeSheet->id])) {
                    continue;
                }

                $date = substr((string) $timeSheet->punch, 0, 10);
                $key = $timeSheet->user_id . '|' . $date;
                $attendanceKeys[$timeSheet->id] = $key;
                $userIds[] = $timeSheet->user_id;
                $dates[] = $date;
            }

            $attendanceMap = Attendance::query()
                ->select(['id', 'user_id', 'ck_date'])
                ->whereIn('user_id', array_values(array_unique($userIds)))
                ->whereIn('ck_date', array_values(array_unique($dates)))
                ->get()
                ->keyBy(fn ($attendance) => $attendance->user_id . '|' . $attendance->ck_date);

            $insertRows = [];
            $now = now();
            foreach ($timeSheets as $timeSheet) {
                if (isset($existingInsertLogIds[$timeSheet->id])) {
                    $skippedExisting++;
                    continue;
                }

                $attendance = $attendanceMap[$attendanceKeys[$timeSheet->id]] ?? null;

                if (!$attendance) {
                    $skippedAttendance++;
                    continue;
                }

                $changedBy = $changedByNames[$timeSheet->logged_by] ?? "User #{$timeSheet->logged_by}";

                if (!$dryRun) {
                    $insertRows[] = [
                        'attendances_id' => $attendance->id,
                        'time_sheet_id' => $timeSheet->id,
                        'changed_by' => $changedBy,
                        'reason' => 'Backfilled for existing imported/manual timesheet record',
                        'type' => 'INSERT',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                $created++;
            }

            if (!$dryRun && $insertRows) {
                TimeChangeLog::insert($insertRows);
            }
        });

        $this->line('Backfill complete.');
        $this->line("Created: {$created}");
        $this->line("Skipped (existing INSERT log): {$skippedExisting}");
        $this->line("Skipped (attendance not found): {$skippedAttendance}");

        return Command::SUCCESS;
    }
}
