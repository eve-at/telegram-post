<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Http\Services\TelegramPost;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class PostController extends Controller
{
    public function index()
    {
        return Inertia::render('Post/Index', [
            'posts' => PostResource::collection(
                Post::where('channel_id', session('channel.id'))
                    ->orderBy('created_at', 'DESC')
                    ->paginate()
            )
        ]);
    }

    public function create()
    {
        return Inertia::render('Post/Edit', [
            'title' => 'Create',
            'toRoute' => 'posts.store',
            'cancelRoute' => 'posts.index',
            'post' => PostResource::make(new Post),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'unique:posts', 'max:190'],
            'show_title' => ['boolean'],
            'body' => ['required', 'max:4096'],
            'show_signature' => ['boolean'],
            'source' => ['max:190'],
            'concept' => ['boolean'],
        ]);

        $concept = $data['concept'] ?? false;

        $post = Post::make($data);

        $post->user()->associate($request->user());
        $post->channel()->associate(session('channel.id'));

        $post->save();

        if ($concept) {
            TelegramPost::make($post, concept: true)->publish();
            return to_route('posts.edit', $post->id)
                        ->with('success', 'The post was created and tested');
        }

        return to_route('posts.index')->with('success', 'The post was created');
    }

    public function edit(Post $post)
    {
        return Inertia::render('Post/Edit', [
            'post' => PostResource::make($post),
            'title' => 'Edit',
            'toRoute' => 'posts.update',
            'cancelRoute' => 'posts.index',
        ]);
    }

    public function update(Request $request, Post $post)
    {
        $data = $request->validate([
            'title' => ['required', 'max:190', Rule::unique('posts')->ignore($post->id)],
            'show_title' => ['boolean'],
            'body' => ['required', 'max:4096'],
            'show_signature' => ['boolean'],
            'source' => ['max:190'],
            'concept' => ['boolean'],
        ]);

        $concept = $data['concept'] ?? false;

        $post->update($data);

        if ($concept) {
            TelegramPost::make($post, concept: true)->publish();
            return back()->with('success', 'The post was updated and tested');
        }

        return to_route('posts.index')->with('success', 'The post was updated');
    }

    public function destroy(Post $post)
    {
        $post->delete();

        return to_route('posts.index')->with('success', 'The post was deleted');
    }
}
