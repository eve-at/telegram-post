<?php

namespace App\Http\Controllers;

use App\Http\Resources\PhotoResource;
use App\Models\Channel;
use App\Models\Photo;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;
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
                'body' => '',
                'source' => '',
            ])
        ]);
    }

    public function store(Request $request, Photo $photo) 
    {
        $data = $request->validate([
            'title' => ['required', 'max:190'],
            'body' => ['required', 'max:1024'],
            'source' => ['max:190'],
        ]);

        $photo = Photo::make($data);

        $photo->user()->associate($request->user());
        $photo->channel()->associate(Channel::first());

        $photo->save();

        return to_route('photos.index')->with('success', 'The photo was created');
    }

    public function edit(Photo $photo) 
    {
        return Inertia::render('Photo/Edit', [
            'photo' => PhotoResource::make($photo),
            'title' => 'Edit',
            'toRoute' => 'photos.update',
        ]);
    }

    public function update(Request $request, Photo $photo) 
    {
        $data = $request->validate([
            'title' => ['required', 'max:190'],
            'body' => ['required', 'max:1024'],
            'source' => ['max:190'],
        ]);

        $photo->update($data);

        return to_route('photos.index')->with('success', 'The photo was updated');
    }

    public function destroy(Request $request, Photo $photo) 
    {
        $photo->delete();

        return to_route('photos.index')->with('success', 'The photo was deleted');
    }
}
