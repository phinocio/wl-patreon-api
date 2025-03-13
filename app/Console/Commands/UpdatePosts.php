<?php

namespace App\Console\Commands;

use App\Services\PostService;
use Illuminate\Console\Command;

class UpdatePosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'patreon:update-posts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update posts from Patreon API';

    /**
     * @var PostService
     */
    private $postService;

    /**
     * Create a new command instance.
     */
    public function __construct(PostService $postService)
    {
        parent::__construct();
        $this->postService = $postService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->info('Updating posts from Patreon...');
            $this->postService->generatePosts();
            $this->info('Posts updated successfully!');

            return 0;
        } catch (\Exception $e) {
            $this->error('Failed to update posts: '.$e->getMessage());

            return 1;
        }
    }
}
