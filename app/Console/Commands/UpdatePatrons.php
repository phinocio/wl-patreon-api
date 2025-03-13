<?php

namespace App\Console\Commands;

use App\Services\PatronService;
use Illuminate\Console\Command;

class UpdatePatrons extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'patreon:update-patrons';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update patrons from Patreon API';

    /**
     * Execute the console command.
     */
    public function handle(PatronService $patronService)
    {
        try {
            $this->info('Updating patrons from Patreon...');
            $patronService->generatePatrons();
            $this->info('Patrons updated successfully!');

            return 0;
        } catch (\Exception $e) {
            $this->error('Failed to update patrons: '.$e->getMessage());

            return 1;
        }
    }
}
