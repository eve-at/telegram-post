<?php

namespace App\Http\Controllers;

use App\Http\Resources\MediaGroupResource;
use App\Models\Channel;
use App\Models\MediaGroup;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class MediaGroupController extends Controller
{
    public function index()
    {
        return Inertia::render('MediaGroup/Index', [
            'medias' => MediaGroupResource::collection(MediaGroup::orderBy('created_at', 'DESC')->paginate())
        ]);
    }

    public function create()
    {
        return Inertia::render('MediaGroup/Edit', [
            'title' => 'Create',
            'toRoute' => 'medias.store',
            'group' => new MediaGroup,
            'files' => []
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

        $group = MediaGroup::make($data);
        $group->user()->associate($request->user());
        $group->channel()->associate(Channel::first());

        $group->save();

        return to_route('medias.index')->with('success', 'The group was created');
    }

    public function upload(Request $request)
    {
        if (!$request->hasFile('filename')) {
            return response()->json(['error' => 'There is no image present.'], 400);
        }

        // TODO:
        //  - The group must be at most 10 MB in size.
        //  - The group's width and height must not exceed 10000 in total.
        //  - Width and height ratio must be at most 20.
        // @see: https://core.telegram.org/bots/api#sendgroup
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

    public function edit(MediaGroup $group)
    {
        $pr = MediaGroupResource::make($group);
        return Inertia::render('MediaGroup/Edit', [
            'title' => 'Edit',
            'group' => $pr,
            'files' => $pr->files,
            'toRoute' => 'medias.update',
        ]);
    }

    public function update(Request $request, MediaGroup $group)
    {
        $data = $request->validate([
            'title' => ['required', 'max:190'],
            'filename' => ['required', 'max:190'],
            'body' => ['required', 'max:1024'],
            'source' => ['max:190'],
        ]);

        $oldFilename = $group->filename;
        $group->update($data);

        if ($data['filename'] !== $oldFilename) {
            Storage::delete('public/medias/' . $oldFilename);
            Storage::move('public/tmp/' . $data['filename'], 'public/medias/' . $data['filename']);
        }

        return to_route('medias.index')->with('success', 'The group was updated');
    }

    public function destroy(Request $request, MediaGroup $group)
    {
        $group->delete();

        return to_route('medias.index')->with('success', 'The group was deleted');
    }
}
