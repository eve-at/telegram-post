<?php

namespace App\Http\Controllers;

use App\Http\Resources\VideoResource;
use App\Models\Channel;
use App\Models\Video;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class VideoController extends Controller
{
    public function index()
    {
        return Inertia::render('Video/Index', [
            'videos' => VideoResource::collection(Video::orderBy('created_at', 'DESC')->paginate())
        ]);
    }

    public function create()
    {
        return Inertia::render('Video/Edit', [
            'title' => 'Create',
            'toRoute' => 'videos.store',
            'video' => Video::make([
                'title' => '',
                'filename' => '',
                'body' => '',
                'source' => '',
            ])
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'max:190'],
            'filename' => ['required', 'max:190'],
            'body' => ['max:1024'],
            'source' => ['max:190'],
        ]);

        Storage::move('public/tmp/' . $data['filename'], 'public/medias/' . $data['filename']);

        $video = Video::make($data);
        $video->user()->associate($request->user());
        $video->channel()->associate(Channel::first());

        $video->save();

        return to_route('videos.index')->with('success', 'The video was created');
    }

    public function upload(Request $request)
    {
        if (!$request->hasFile('filename')) {
            return response()->json(['error' => 'There is no video present.'], 400);
        }

        // There is no explicit rules for a video 
        // @see: https://core.telegram.org/bots/api#sendvideo
        $request->validate([
            'filename' => ['required', 'file', 'mimes:mp4', 'max:102400'], //100MB
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
            return response()->json(['error', 'Error while removing temporary file.']);
        }
    }

    public function edit(Video $video)
    {
        $pr = VideoResource::make($video);
        return Inertia::render('Video/Edit', [
            'title' => 'Edit',
            'video' => $pr,
            'filename' => $pr->filename,
            'filepaths' => $pr->filepaths,
            'toRoute' => 'videos.update',
        ]);
    }

    public function update(Request $request, Video $video)
    {
        $data = $request->validate([
            'title' => ['required', 'max:190'],
            'filename' => ['required', 'max:190'],
            'body' => ['required', 'max:1024'],
            'source' => ['max:190'],
        ]);

        $oldFilename = $video->filename;
        $video->update($data);

        if ($data['filename'] !== $oldFilename) {
            Storage::delete('public/medias/' . $oldFilename);
            Storage::move('public/tmp/' . $data['filename'], 'public/medias/' . $data['filename']);
        }

        return to_route('videos.index')->with('success', 'The video was updated');
    }

    public function destroy(Video $video)
    {
        Storage::delete('public/medias/' . $video->filename);
        $video->delete();

        return to_route('videos.index')->with('success', 'The video was deleted');
    }
}
