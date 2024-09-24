<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Http\Livewire\LeaveBalanceComponent;
use App\Services\TelegramService;

class SyncLeaveBalances extends Command
{
    protected $signature = 'sync:leave-balances';
    protected $description = 'Sync leave balances for all users';

    protected $telegramService;

    public function __construct(TelegramService $telegramService)
    {
        parent::__construct();
        $this->telegramService = $telegramService;
    }

    public function handle()
    {
        try {
            // Get all users
            $users = User::all();
            $totalUsers = $users->count(); // Get the total number of users
            $leaveBalanceComponent = new LeaveBalanceComponent();

            $this->info("Starting to sync leave balances for {$totalUsers} users.");

            // Send initial progress message and save message ID for editing
            $messageId = $this->telegramService->sendMessage("🚀 Syncing leave balances for {$totalUsers} users...");

            if (!$messageId) {
                $this->error('Failed to send the initial message to Telegram');
                return;
            }

            // Initialize variables for tracking progress
            $processedUsers = 0;
            $lastProgressSent = 0; // To track progress percentage

            // Sync leave balances for all users
            foreach ($users as $user) {
                $leaveBalanceComponent->syncLeaveBalances($user->id);
                $processedUsers++;

                // Calculate progress
                $progress = round(($processedUsers / $totalUsers) * 100);

                // Only update for every 10% progress
                if ($progress % 10 === 0 && $progress !== $lastProgressSent) {
                    $this->info("LeaveBalanceSync Progress: {$progress}% ({$processedUsers}/{$totalUsers} users synced)");

                    // Edit the Telegram message to show progress
                    $this->telegramService->editMessage($messageId, "📊 Progress: {$progress}% ({$processedUsers}/{$totalUsers} users synced)");

                    $lastProgressSent = $progress;
                }
            }

            // Log success message
            $this->info('Leave balances synced for all users.');

            // Send final success message to Telegram by editing the last message
            $this->telegramService->editMessage($messageId, "✅ Leave balances synced successfully for all {$totalUsers} users.");
        } catch (\Exception $e) {
            // Log failure message
            $this->error('Sync failed: ' . $e->getMessage());

            // Send failure message to Telegram (edit the progress message to reflect failure)
            $this->telegramService->editMessage($messageId, '❌ Sync failed: ' . $e->getMessage());
        }
    }
}
