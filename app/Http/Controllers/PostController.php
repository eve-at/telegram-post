<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\Conceptable;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\PostFile;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class PostController extends Controller
{
    use Conceptable;

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
        
        $data = $this->validateRequest($request);
        $type = $this->getRequestType($request);

        $concept = $data['concept'] ?? false;
        $comeback = $data['comeback'] ?? false;

        $post = Post::make($data);
        $post->type = $type;
        $post->user()->associate($request->user());
        $post->channel()->associate(session('channel.id'));
        $post->save();

        foreach($data['filenames'] as $index=>$filename) {
            Storage::move(
                'public/tmp/' . $filename, 
                'public/media/' . session('channel.id') . '/' . $filename
            );

            $file = new PostFile;
            $file->filename = $filename;
            $file->order = $index;
            $file->type = str_ends_with($filename, '.mp4') ? 'video' : 'photo';
            $file->post_id = $post->id;
            $file->save();
        }

        if ($concept) {
            $this->publishConcept($post);

            return to_route('posts.edit', $post->id)
                        ->with('success', 'The Post was created and tested');
        }

        if ($comeback) {;
            return to_route('posts.edit', $post->id)
                        ->with('success', 'The Post was created');
        }

        return to_route('posts.index')->with('success', 'The Post was created');
    }

    public function upload(Request $request)
    {
        if (!$request->hasFile('filename')) {
            return response()->json(['error' => 'There is no image present.'], 400);
        }

        // TODO:
        //  - The image must be at most 10 MB in size.
        //  - The image's width and height must not exceed 10000 in total.
        //  - Width and height ratio must be at most 20.
        // @see: https://core.telegram.org/bots/api#sendgroup
        //  - There is no explicit rules for a video 
        // @see: https://core.telegram.org/bots/api#sendgroup
        $rules = ['required', 'image', 'mimes:jpeg,jpg', 'max:4096'];
        if ($request->file('filename')->getClientMimeType() === 'video/mp4') {
            $rules = ['required', 'file', 'mimes:mp4', 'max:102400'];
        }
       
        $request->validate([
            'filename' => $rules,
        ]);

        if (!$request->file('filename')->store('public/tmp')) {
            return response()->json(['error', 'The file could no be saved.'], 500);
        }

        return $request->file('filename')->hashName();
    }

    public function uploadUndo(Request $request)
    {
        try {
            Storage::delete('public/tmp/' . $request->getContent());
        } catch(Exception $e) {
            return response()->json(['error', 'Error while removing temporary file.'], 500);
        }
    }

    public function edit(Post $post)
    {
        return Inertia::render('Post/Edit', [
            'title' => 'Edit',
            'post' => PostResource::make($post),
            'toRoute' => 'posts.update',
            'cancelRoute' => 'posts.index',
        ]);
    }

    // TODO: take out PostFile
    public function update(Request $request, Post $post)
    {
        $data = $this->validateRequest($request);
        $type = $this->getRequestType($request);

        $data['type'] = $type;
        $concept = $data['concept'] ?? false;
        $comeback = $data['comeback'] ?? false;

        $post->update(collect($data)->except('filenames')->toArray());
        
        $filesBefore = $post->filenames->pluck('filename');
        $filesAfter = collect($data['filenames']);

        // remove missing files (disk and DB)
        if ($filesDeleted = $filesBefore->diff($filesAfter)) {
            PostFile::whereIn('filename', $filesDeleted)
                ->where('post_id', $post->id)
                ->delete();

            Storage::delete($filesDeleted->map(fn ($filename) => 'public/media/' . session('channel.id') . '/' . $filename)->toArray());
        }

        // move new files (disk, and add into DB)
        $orders = $filesAfter->flip();
        $filesAfter->diff($filesBefore)->each(function($filename) use ($post, $orders) {
            $file = new PostFile();
            $file->filename = $filename;
            $file->type = str_ends_with($filename, '.mp4') ? 'video' : 'photo';
            $file->post_id = $post->id;
            $file->order = $orders->get($filename);
            $file->save();

            // TODO: queue it
            Storage::move(
                'public/tmp/' . $filename, 
                'public/media/' . session('channel.id') . '/' . $filename
            );
        });

        // reorder old files in DB
        $filesAfter->flip()
            ->intersectByKeys($filesBefore->flip())
            ->each(function($order, $filename) use ($post) {
                PostFile::where('filename', $filename)
                    ->where('post_id', $post->id)
                    ->update(['order' => $order]);
            });

        if ($concept) {;
            $this->publishConcept($post->fresh());

            return back()->with('success', 'The Post was updated and tested');
        }

        if ($comeback) {
            return to_route('posts.edit', $post->id)
                        ->with('success', 'The Post was updated');
        }

        return to_route('posts.index')->with('success', 'The Post was updated');
    }

    public function destroy(Post $post)
    {
        $files = $post->filenames->pluck('filename');

        $post->delete();

        Storage::delete($files->map(fn ($filename) => 'public/media/' . session('channel.id') . '/' . $filename)->toArray());

        return to_route('posts.index')->with('success', 'The Post was deleted');
    }

    protected function getRequestType(Request $request) 
    {
        if ($request->filenames) {
            if (count($request->filenames) > 1) {
                return 'media_group';
            } 
            
            if (str_ends_with($request->filenames[0], '.mp4')) {
                return 'video';
            } 
            
            //if (str_ends_with($request->filenames[0], '.jpg')) {
                return 'photo';
            //}
        }

        return 'message';
    }

    protected function validateRequest(Request $request) 
    {
        // No files => message => `text` 4096 chars
        // 1 photo => photo => `caption` 1024 chars
        // 1 video => video => `caption` 1024 chars
        // 2+ photo/video => media_group => `caption` 1024 chars

        $validationRules = [
            'title' => ['required', 'max:190'],
            'body' => ['required', 'max:4096'],
            'show_title' => ['boolean'],
            'show_signature' => ['boolean'],
            'ad' => ['boolean'],
            'comment' => ['max:4096'],
            'source' => ['max:190'],
            'concept' => ['boolean'],
            'comeback' => ['boolean'],
            'filenames' => ['max:10'],
        ];

        $type = $this->getRequestType($request);
        
        if ($type !== 'message') {
            $validationRules['body'] = ['max:1024']; // not required
            $validationRules['filenames.*'] = ['max:190']; // 190 max filename length

            if ($type === 'media_group') {
                $validationRules['filenames.*'][] = 'ends_with:.mp4,.jpg';
            } else if ($type === 'video') {
                $validationRules['filenames.*'][] = 'ends_with:.mp4';
            } else if ($type === 'photo') {
                $validationRules['filenames.*'][] = 'ends_with:.jpg';
            }
        }

        return $request->validate($validationRules);    
    }        
}
