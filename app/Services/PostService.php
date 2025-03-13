<?php

namespace App\Services;

use App\Models\Post;
use App\Models\Token;
use Illuminate\Support\Collection;
use Patreon\API;

class PostService
{
    /**
     * @var API
     */
    private $apiClient;

    /**
     * Create a new PostService instance.
     */
    public function __construct()
    {
        $token = Token::first();

        if (! $token) {
            throw new \RuntimeException('No Patreon token found in database. Unable to initialize API client.');
        }

        $this->apiClient = new API($token->access);
    }

    /**
     * Generate and store posts from Patreon API
     */
    public function generatePosts(): void
    {
        $postsUrl = $this->buildPostsUrl();
        $allPosts = collect();
        $nextLink = $postsUrl;

        while ($nextLink) {
            $response = $this->apiClient->get_data($nextLink);
            $posts = $this->processApiResponse($response);
            $allPosts = $allPosts->concat($posts);

            $nextLink = $response['links']['next'] ?? false;
        }

        $this->storePosts($allPosts);
    }

    private function buildPostsUrl(): string
    {
        $campaignResponse = $this->apiClient->fetch_campaigns();

        if (! isset($campaignResponse['data'][0]['id'])) {
            throw new \RuntimeException('Failed to fetch campaign ID');
        }

        return sprintf(
            'campaigns/%s/posts?fields[post]=title,content,is_public,published_at,url',
            $campaignResponse['data'][0]['id']
        );
    }

    /**
     * Process API response and extract post data
     */
    private function processApiResponse(array $response): Collection
    {
        return collect($response['data'])
            ->filter(fn ($post) => $post['attributes']['is_public'] !== false)
            ->map(fn ($post) => [
                'title' => $post['attributes']['title'],
                'content' => $post['attributes']['content'],
                'published' => $post['attributes']['published_at'],
                'url' => $post['attributes']['url'],
            ]);
    }

    /**
     * Store the posts in the database
     */
    private function storePosts(Collection $posts): void
    {
        $post = Post::first() ?? new Post;
        $post->content = $posts->reverse()->values()->all();
        $post->save();
    }
}
