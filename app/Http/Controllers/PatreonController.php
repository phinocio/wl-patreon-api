<?php

namespace App\Http\Controllers;

use App\Models\Patron;
use App\Models\Post;

class PatreonController extends Controller
{
    public function index()
    {
        return response()->json([
            'patrons' => Patron::first()->patrons,
            'posts' => Post::first()->content,
        ]);
    }

    public function update()
    {
        try {
            \Artisan::call('patreon:update-data');

            return response()->json([
                'message' => 'Patron and Post caches updated successfully',
                'status' => 200,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Error: '.$th->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    public function lastUpdated()
    {
        // There shouldn't be more than one row, but just in case.
        $lastPatronsUpdated = Patron::latest()->first()->updated_at;
        $lastPostsUpdated = Post::latest()->first()->updated_at;

        return [
            'last_updated' => max($lastPostsUpdated->timestamp, $lastPatronsUpdated->timestamp),
        ];
    }
}
