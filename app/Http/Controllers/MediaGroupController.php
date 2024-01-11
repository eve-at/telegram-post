<?php

namespace App\Http\Controllers;

use App\Http\Resources\MediaGroupResource;
use App\Models\Channel;
use App\Models\MediaGroup;
use App\Models\MediaGroupFile;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use PhpParser\Node\Expr\FuncCall;

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
            'group' => MediaGroup::make([
                'title' => '',
                'filenames' => [],
                'body' => '',
                'source' => '',
            ])
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'max:190'],
            'filenames' => ['required', 'min:2', 'max:10'], // 2-10 files
            'filenames.*' => ['max:190'], // 190 max filename length
            'body' => ['max:1024'],
            'source' => ['max:190'],
        ]);

        $group = MediaGroup::make($data);
        $group->user()->associate($request->user());
        $group->channel()->associate(Channel::first());
        $group->save();

        foreach($data['filenames'] as $index=>$filename) {
            Storage::move('public/tmp/' . $filename, 'public/medias/' . $filename);

            $file = new MediaGroupFile;
            $file->filename = $filename;
            $file->order = $index;
            $file->type = str_ends_with($filename, '.mp4') ? 'video' : 'photo';
            $file->media_group_id = $group->id;
            $file->save();
        }

        return to_route('medias.index')->with('success', 'The media group was created');
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
            return response()->json(['error', 'Error while removing temporary file.']);
        }
    }

    public function edit(MediaGroup $media)
    {
        return Inertia::render('MediaGroup/Edit', [
            'title' => 'Edit',
            'group' => MediaGroupResource::make($media),
            'toRoute' => 'medias.update',
        ]);
    }

    // TODO: take out MediaGroupFile
    public function update(Request $request, MediaGroup $media)
    {
        $data = $request->validate([
            'title' => ['required', 'max:190'],
            'filenames' => ['required', 'min:2', 'max:10'], // 2-10 files
            'filenames.*' => ['max:190'], // 190 max filename length
            'body' => ['max:1024'],
            'source' => ['max:190'],
        ]);

        $media->update(collect($data)->except('filenames')->toArray());

        $filesBefore = $media->filenames->pluck('filename');
        $filesAfter = collect($data['filenames']);

        // remove missing files (disk and DB)
        if ($filesDeleted = $filesBefore->diff($filesAfter)) {
            MediaGroupFile::whereIn('filename', $filesDeleted)
                ->where('media_group_id', $media->id)
                ->delete();

            Storage::delete($filesDeleted->map(fn ($filename) => 'public/medias/' . $filename)->toArray());
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
            Storage::move('public/tmp/' . $filename, 'public/medias/' . $filename);
        });

        // reorder old files in DB
        $filesAfter->flip()
            ->intersectByKeys($filesBefore->flip())
            ->each(function($order, $filename) use ($media) {
                MediaGroupFile::where('filename', $filename)
                    ->where('media_group_id', $media->id)
                    ->update(['order' => $order]);
            });

        return to_route('medias.index')->with('success', 'The media group was updated');
    }

    public function destroy(Request $request, MediaGroup $media)
    {
        $media->delete();

        return to_route('medias.index')->with('success', 'The group was deleted');
    }
}
