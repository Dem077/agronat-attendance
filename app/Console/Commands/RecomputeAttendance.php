<?php

namespace App\Console\Commands;

use App\Services\AttendanceService;
use App\Services\TelegramService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Models\User;

class RecomputeAttendance extends Command
{
    // The name and signature of the console command.
    protected $signature = 'attendance:recompute';

    // The console command description.
    protected $description = 'Recompute attendance for the previous day for all users';

    protected $attendanceService;
    protected $telegramService;

    public function __construct(AttendanceService $attendanceService, TelegramService $telegramService)
    {
        parent::__construct();
        $this->attendanceService = $attendanceService;
        $this->telegramService = $telegramService;
    }

    public function handle()
    {
        // Get the previous day's date
        $from = Carbon::yesterday();
        $to = Carbon::yesterday();

        // Get all users
        $users = User::all()->where('active', 1);
        $totalUsers = $users->count();

        if ($totalUsers == 0) {
            $this->warn('No users found to process.');
            return;
        }

        // Send initial message to Telegram
        $messageId = $this->telegramService->sendMessage("🚀 Starting attendance recompute for {$totalUsers} users for the date: {$from->format('Y-m-d')}.");

        // Initialize variables for tracking progress
        $processedUsers = 0;
        $lastProgressSent = 0; // To track progress percentage

        try {
            foreach ($users as $user) {
                // Run the recompute function for each user
                $this->attendanceService->recompute($from, $to, $user->id);
                $processedUsers++;

                // Calculate progress percentage
                $progress = round(($processedUsers / $totalUsers) * 100);

                // Only send update for every 10% progress
                if ($progress % 1 === 0 && $progress !== $lastProgressSent) {
                    // Edit the Telegram message to show progress
                    $this->telegramService->editMessage($messageId, "📊Recompute Progress: {$progress}% ({$processedUsers}/{$totalUsers} users processed)");
                    $lastProgressSent = $progress;
                }
            }

            // Send success message to Telegram once done
            $this->telegramService->editMessage($messageId, "✅ Recompute task completed successfully for all {$totalUsers} users for the date: {$from->format('Y-m-d')} to {$to->format('Y-m-d')}");

        } catch (\Exception $e) {
            // Send failure message to Telegram with error
            $this->telegramService->editMessage($messageId, "❌ Recompute task failed: " . $e->getMessage());
            $this->error('An error occurred: ' . $e->getMessage());
        }

        // Output success message to the console
        $this->info('Attendance recompute task completed.');
    }
}
