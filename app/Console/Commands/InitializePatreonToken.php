<?php

namespace App\Console\Commands;

use App\Services\TokenService;
use Illuminate\Console\Command;

class InitializePatreonToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'patreon:init-token';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize Patreon token from environment variables';

    /**
     * Execute the console command.
     */
    public function handle(TokenService $tokenService)
    {
        try {
            $tokenService->initializeToken();
            $this->info('Patreon token has been initialized successfully');

            return 0;
        } catch (\Exception $e) {
            $this->error($e->getMessage());

            return 1;
        }
    }
}
