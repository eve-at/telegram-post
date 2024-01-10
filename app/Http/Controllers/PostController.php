<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Models\Channel;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class PostController extends Controller
{
    public function index()
    {
        return Inertia::render('Post/Index', [
            'posts' => PostResource::collection(Post::orderBy('created_at', 'DESC')->paginate())
        ]);
    }

    public function create() 
    {
        return Inertia::render('Post/Edit', [
            'title' => 'Create',
            'toRoute' => 'post.store',
            'post' => Post::make([
                'title' => '',
                'body' => '',
                'source' => '',
            ])
        ]);
    }

    public function store(Request $request, Post $post) 
    {
        $data = $request->validate([
            'title' => ['required', 'unique:posts', 'max:190'],
            'body' => ['required', 'max:4096'],
            'source' => ['max:190'],
        ]);

        $post = Post::make($data);

        $post->user()->associate($request->user());
        $post->channel()->associate(Channel::first());

        $post->save();

        return to_route('post.index')->with('success', 'The post was created');
    }

    public function edit(Post $post) 
    {
        return Inertia::render('Post/Edit', [
            'post' => PostResource::make($post),
            'title' => 'Edit',
            'toRoute' => 'post.update',
        ]);
    }

    public function update(Request $request, Post $post) 
    {
        $data = $request->validate([
            'title' => ['required', 'max:190', Rule::unique('posts')->ignore($post->id)],
            'body' => ['required', 'max:4096'],
            'source' => ['max:190'],
        ]);

        $post->update($data);

        return to_route('post.index')->with('success', 'The post was updated');
    }

    public function destroy(Request $request, Post $post) 
    {
        $post->delete();

        return to_route('post.index')->with('success', 'The post was deleted');
    }
    
}
