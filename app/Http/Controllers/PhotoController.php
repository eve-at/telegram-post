<?php

namespace App\Http\Controllers;

use App\Http\Resources\PhotoResource;
use App\Models\Channel;
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
            'photos' => PhotoResource::collection(Photo::orderBy('created_at', 'DESC')->paginate())
        ]);
    }

    public function create()
    {
        return Inertia::render('Photo/Edit', [
            'title' => 'Create',
            'toRoute' => 'photos.store',
            'photo' => Photo::make([
                'title' => '',
                'filename' => '',
                'filepaths' => [],
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

        $photo = Photo::make($data);
        $photo->user()->associate($request->user());
        $photo->channel()->associate(Channel::first());

        $photo->save();

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
        $pr = PhotoResource::make($photo);
        return Inertia::render('Photo/Edit', [
            'title' => 'Edit',
            'photo' => $pr,
            'filename' => $pr->filename,
            'filepaths' => $pr->filepaths,
            'toRoute' => 'photos.update',
        ]);
    }

    public function update(Request $request, Photo $photo)
    {
        $data = $request->validate([
            'title' => ['required', 'max:190'],
            'filename' => ['required', 'max:190'],
            'body' => ['required', 'max:1024'],
            'source' => ['max:190'],
        ]);

        $oldFilename = $photo->filename;
        $photo->update($data);

        if ($data['filename'] !== $oldFilename) {
            Storage::delete('public/medias/' . $oldFilename);
            Storage::move('public/tmp/' . $data['filename'], 'public/medias/' . $data['filename']);
        }

        return to_route('photos.index')->with('success', 'The photo was updated');
    }

    public function destroy(Request $request, Photo $photo)
    {
        Storage::delete('public/medias/' . $photo->filename);
        $photo->delete();

        return to_route('photos.index')->with('success', 'The photo was deleted');
    }
}
