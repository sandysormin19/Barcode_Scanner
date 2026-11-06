<?php

namespace App\Console\Commands;

use App\Services\EshopeSyncService;
use Illuminate\Console\Command;

class TestEshopeSyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'eshope:test-connection';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test connection to Eshope webhook receiver';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Testing Eshope connection...');

        $service = new EshopeSyncService();
        $isConnected = $service->testConnection();

        if ($isConnected) {
            $this->info('✓ Eshope connection is healthy');
            return self::SUCCESS;
        } else {
            $this->error('✗ Failed to connect to Eshope');
            $this->line('Make sure Eshope is running and the webhook URL is correct.');
            $this->line('Webhook URL: ' . config('app.eshope_webhook_url'));
            return self::FAILURE;
        }
    }
}
