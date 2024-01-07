<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Models\Post;
use Inertia\Inertia;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::paginate();

        return Inertia::render('Post/Index', [
            //'posts' => PostResource::collection($posts)
            'posts' => $posts
        ]);
    }
}
