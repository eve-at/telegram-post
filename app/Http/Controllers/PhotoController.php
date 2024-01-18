<?php

namespace App\Http\Controllers;

use App\Http\Resources\PhotoResource;
use App\Http\Services\TelegramPhoto;
use App\Models\Photo;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class PhotoController extends Controller
{
    public function index()
    {
        return Inertia::render('Photo/Index', [
            'photos' => PhotoResource::collection(
                Photo::where('channel_id', session('channel.id'))
                    ->orderBy('created_at', 'DESC')
                    ->paginate()
            )
        ]);
    }

    public function create()
    {
        return Inertia::render('Photo/Edit', [
            'title' => 'Create',
            'toRoute' => 'photos.store',
            'cancelRoute' => 'photos.index',
            'photo' => PhotoResource::make(new Photo),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'max:190'],
            'filename' => ['required', 'max:190'],
            'show_title' => ['boolean'],
            'show_signature' => ['boolean'],
            'body' => ['max:1024'],
            'source' => ['max:190'],
            'concept' => ['boolean'],
        ]);

        $concept = $data['concept'] ?? false;

        Storage::move(
            'public/tmp/' . $data['filename'], 
            'public/medias/' . session('channel.id') . '/' . $data['filename']
        );

        $photo = Photo::make($data);
        $photo->user()->associate($request->user());
        $photo->channel()->associate(session('channel.id'));
        
        $photo->save();

        if ($concept) {
            TelegramPhoto::make($photo, concept: true)->publish();
            return to_route('photos.edit', $photo->id)
                        ->with('success', 'The photo was created and tested');
        }

        return to_route('photos.index')->with('success', 'The photo was created');
    }

    public function upload(Request $request)
    {
        if (!$request->hasFile('filename')) {
            return response()->json(['error' => 'There is no image present.'], 400);
        }

        // TODO:
        //  - The photo must be at most 10 MB in size.
        //  - The photo's width and height must not exceed 10000 in total.
        //  - Width and height ratio must be at most 20.
        // @see: https://core.telegram.org/bots/api#sendphoto
        $request->validate([
            'filename' => ['required', 'image', 'mimes:jpeg,jpg', 'max:4096'],
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

    public function edit(Photo $photo)
    {
        return Inertia::render('Photo/Edit', [
            'title' => 'Edit',
            'photo' => PhotoResource::make($photo),
            'toRoute' => 'photos.update',
            'cancelRoute' => 'photos.index',
        ]);
    }

    public function update(Request $request, Photo $photo)
    {
        $data = $request->validate([
            'title' => ['required', 'max:190'],
            'filename' => ['required', 'max:190'],
            'show_title' => ['boolean'],
            'show_signature' => ['boolean'],
            'body' => ['required', 'max:1024'],
            'source' => ['max:190'],
            'concept' => ['boolean'],
        ]);

        $concept = $data['concept'] ?? false;

        $oldFilename = $photo->filename;

        if ($data['filename'] !== $oldFilename) {
            $data['file_id'] = null;
            $data['file_unique_id'] = null;
        }

        $photo->update($data);

        if ($data['filename'] !== $oldFilename) {
            Storage::delete('public/medias/' . session('channel.id') . '/' . $oldFilename);
            Storage::move(
                'public/tmp/' . $data['filename'], 
                'public/medias/' . session('channel.id') . '/' . $data['filename']
            );
        }

        if ($concept) {
            TelegramPhoto::make($photo, concept: true)->publish();
            return back()->with('success', 'The photo was updated and tested');
        }

        return to_route('photos.index')->with('success', 'The photo was updated');
    }

    public function destroy(Photo $photo)
    {
        Storage::delete('public/medias/' . session('channel.id') . '/' . $photo->filename);
        $photo->delete();

        return to_route('photos.index')->with('success', 'The photo was deleted');
    }
}
