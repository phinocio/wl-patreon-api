<?php

namespace App\Console\Commands;

use App\Services\PatronService;
use App\Services\PostService;
use Illuminate\Console\Command;

class UpdatePatreonData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'patreon:update-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update both patrons and posts from Patreon API';

    /**
     * Execute the console command.
     */
    public function handle(PatronService $patronService, PostService $postService)
    {
        $this->info('Starting Patreon data update...');

        try {
            $this->info('Updating patrons...');
            $patronService->generatePatrons();
            $this->info('Patrons updated successfully!');

            $this->info('Updating posts...');
            $postService->generatePosts();
            $this->info('Posts updated successfully!');

            $this->info('All Patreon data has been updated successfully!');

            return 0;
        } catch (\Exception $e) {
            $this->error('Failed to update Patreon data: '.$e->getMessage());

            return 1;
        }
    }
}
