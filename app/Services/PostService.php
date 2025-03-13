<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PostService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public static function generatePosts($url, $access_token)
    {
        $allPosts = [];
        $resp = Http::withToken($access_token)->get($url);
        $resp = $resp->json();

        foreach ($resp['data'] as $post) {
            if ($post['attributes']['is_public'] != false) {
                $post = [
                    'title' => $post['attributes']['title'],
                    'content' => $post['attributes']['content'],
                    'published' => $post['attributes']['published_at'],
                    'url' => $post['attributes']['url'],
                ];
                array_push($allPosts, $post);
            }
        }

        if (array_key_exists('links', $resp)) {
            $nextLink = $resp['links']['next'];
        } else {
            $nextLink = false;
        }

        while ($nextLink != false) {
            $resp = Http::withToken($access_token)->get($nextLink);

            foreach ($resp['data'] as $post) {
                if ($post['attributes']['is_public'] != false) {
                    $post = [
                        'title' => $post['attributes']['title'],
                        'content' => $post['attributes']['content'],
                        'published' => $post['attributes']['published_at'],
                        'url' => $post['attributes']['url'],
                    ];
                    array_push($allPosts, $post);
                }
            }

            if (isset($resp['links']['next'])) {
                $nextLink = $resp['links']['next'];
            } else {
                $nextLink = false;
            }
        }
        $cache = \App\Models\PostCache::first();

        if (! $cache) {
            $cache = new \App\Models\PostCache;
        }
        $cache->posts = json_encode(array_reverse($allPosts));
        $cache->save();

        return $cache;
    }
}
