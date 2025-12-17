<?php

namespace App\Console\Commands;

use App\Models\ScreenUsageLog;
use Illuminate\Console\Command;

class CloseInactiveScreenLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'screens:close-inactive-logs 
                            {--timeout=30 : Heartbeat timeout in seconds}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Close usage logs for inactive screens (screens with expired heartbeats)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $timeout = (int) $this->option('timeout');

        $this->info("Closing inactive screen logs (timeout: {$timeout} seconds)...");

        $closed = ScreenUsageLog::closeInactiveSessions($timeout);

        if ($closed > 0) {
            $this->info("Successfully closed {$closed} inactive screen log(s).");
        } else {
            $this->info('No inactive screens to close.');
        }

        return Command::SUCCESS;
    }
}
