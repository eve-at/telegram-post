<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Models\Post;
use Clockwork\Request\Request;
use Inertia\Inertia;

class PostController extends Controller
{
    public function index()
    {
        return Inertia::render('Post/Index', [
            'posts' => PostResource::collection(Post::paginate())
        ]);
    }

    public function edit(Post $post) 
    {
        return Inertia::render('Post/Edit', [
            'post' => PostResource::make($post)
        ]);
    }

    public function create() 
    {
        return Inertia::render('Post/Create');
    }

    public function store(Request $request, Post $post) 
    {
        
    }
    
}
