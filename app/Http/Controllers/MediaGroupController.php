<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\Conceptable;
use App\Http\Resources\MediaGroupResource;
use App\Models\MediaGroup;
use App\Models\MediaGroupFile;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class MediaGroupController extends Controller
{
    use Conceptable;

    public function index()
    {
        return Inertia::render('MediaGroup/Index', [
            'media' => MediaGroupResource::collection(
                MediaGroup::where('channel_id', session('channel.id'))
                    ->orderBy('created_at', 'DESC')
                    ->paginate()
            )
        ]);
    }

    public function create()
    {
        return Inertia::render('MediaGroup/Edit', [
            'title' => 'Create',
            'toRoute' => 'media.store',
            'cancelRoute' => 'media.index',
            'group' => MediaGroupResource::make(new MediaGroup),
        ]);
    }

    public function store(Request $request)
    {
        
        $data = $this->validateRequest($request);
        $type = $this->getRequestType($request);

        $concept = $data['concept'] ?? false;

        $media = MediaGroup::make($data);
        $media->type = $type;
        $media->user()->associate($request->user());
        $media->channel()->associate(session('channel.id'));
        $media->save();

        foreach($data['filenames'] as $index=>$filename) {
            Storage::move(
                'public/tmp/' . $filename, 
                'public/media/' . session('channel.id') . '/' . $filename
            );

            $file = new MediaGroupFile;
            $file->filename = $filename;
            $file->order = $index;
            $file->type = str_ends_with($filename, '.mp4') ? 'video' : 'photo';
            $file->media_group_id = $media->id;
            $file->save();
        }

        if ($concept) {
            $this->publishConcept($media);

            return to_route('media.edit', $media->id)
                        ->with('success', 'The Media Group was created and tested');
        }

        return to_route('media.index')->with('success', 'The Media Group was created');
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

    public function edit(MediaGroup $media)
    {
        return Inertia::render('MediaGroup/Edit', [
            'title' => 'Edit',
            'group' => MediaGroupResource::make($media),
            'toRoute' => 'media.update',
            'cancelRoute' => 'media.index',
        ]);
    }

    // TODO: take out MediaGroupFile
    public function update(Request $request, MediaGroup $media)
    {
        $data = $this->validateRequest($request);
        $type = $this->getRequestType($request);

        $data['type'] = $type;
        $concept = $data['concept'] ?? false;

        $media->update(collect($data)->except('filenames')->toArray());
        
        $filesBefore = $media->filenames->pluck('filename');
        $filesAfter = collect($data['filenames']);

        // remove missing files (disk and DB)
        if ($filesDeleted = $filesBefore->diff($filesAfter)) {
            MediaGroupFile::whereIn('filename', $filesDeleted)
                ->where('media_group_id', $media->id)
                ->delete();

            Storage::delete($filesDeleted->map(fn ($filename) => 'public/media/' . session('channel.id') . '/' . $filename)->toArray());
        }

        // move new files (disk, and add into DB)
        $orders = $filesAfter->flip();
        $filesAfter->diff($filesBefore)->each(function($filename) use ($media, $orders) {
            $file = new MediaGroupFile();
            $file->filename = $filename;
            $file->type = str_ends_with($filename, '.mp4') ? 'video' : 'photo';
            $file->media_group_id = $media->id;
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
            ->each(function($order, $filename) use ($media) {
                MediaGroupFile::where('filename', $filename)
                    ->where('media_group_id', $media->id)
                    ->update(['order' => $order]);
            });

        if ($concept) {
            $this->publishConcept(MediaGroup::find($media->id));

            return back()->with('success', 'The Media Group was updated and tested');
        }

        return to_route('media.index')->with('success', 'The media group was updated');
    }

    public function destroy(MediaGroup $media)
    {
        $files = $media->filenames->pluck('filename');

        $media->delete();

        Storage::delete($files->map(fn ($filename) => 'public/media/' . session('channel.id') . '/' . $filename)->toArray());

        return to_route('media.index')->with('success', 'The media group was deleted');
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
            'source' => ['max:190'],
            'concept' => ['boolean'],
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
