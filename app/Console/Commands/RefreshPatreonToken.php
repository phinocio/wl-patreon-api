<?php

namespace App\Console\Commands;

use App\Services\TokenService;
use Illuminate\Console\Command;

class RefreshPatreonToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'patreon:refresh-token 
							{--force : Force token refresh regardless of expiration}
							{--config : Use config values instead of refreshing via API}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh the Patreon access token if expired';

    /**
     * Execute the console command.
     */
    public function handle(TokenService $tokenService)
    {
        try {
            if ($this->option('config')) {
                $tokenService->initializeToken();
                $this->info('Token has been initialized from config values!');

                return 0;
            }

            [, $refreshed] = $tokenService->refreshToken(null, (bool) $this->option('force'));

            if ($refreshed) {
                $this->info('Token has been refreshed!');
            } else {
                $this->info('Token is still valid!');
            }

            return 0;
        } catch (\Exception $e) {
            $this->error('Failed to refresh token: '.$e->getMessage());

            return 1;
        }
    }
}
