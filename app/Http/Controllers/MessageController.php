<?php

namespace App\Http\Controllers;

use App\Http\Resources\MessageResource;
use App\Models\Message;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;

class MessageController extends Controller
{
    public function index() 
    {
        $start = (Carbon::now())->startOfWeek();
        $end = (Carbon::now())->endOfWeek();

        return Inertia::render('Message/Index', [
            'messages' => static::messagesBetweenDates($start, $end)
        ]);
    }

    public function create()
    {
        return Inertia::render('Message/Edit', [
            'title' => 'Create',
            'toRoute' => 'messages.store',
            'cancelRoute' => 'messages.index',
            'message' => MessageResource::make(new Message),
        ]);
    }

    public function store(Request $request)
    {
        // $data = $request->validate([
        //     'title' => ['required', 'max:190'],
        //     'filename' => ['required', 'max:190'],
        //     'show_title' => ['boolean'],
        //     'show_signature' => ['boolean'],
        //     'body' => ['max:1024'],
        //     'source' => ['max:190'],
        //     'concept' => ['boolean'],
        // ]);

        // $concept = $data['concept'] ?? false;

        // Storage::move(
        //     'public/tmp/' . $data['filename'], 
        //     'public/media/' . session('channel.id') . '/' . $data['filename']
        // );

        // $photo = Photo::make($data);
        // $photo->user()->associate($request->user());
        // $photo->channel()->associate(session('channel.id'));
        
        // $photo->save();

        // if ($concept) {
        //     TelegramPhoto::make($photo, concept: true)->publish();
        //     return to_route('photos.edit', $photo->id)
        //                 ->with('success', 'The photo was created and tested');
        // }

        // return to_route('photos.index')->with('success', 'The photo was created');
    }

    public function edit(Message $message)
    {
        //return MessageResource::make($message);
        return Inertia::render('Message/Edit', [
            'title' => 'Edit',
            'message' => MessageResource::make($message),
            'toRoute' => 'messages.update',
            'cancelRoute' => 'messages.index',
        ]);
    }

    public function update(Request $request, Message $message)
    {
        // $data = $request->validate([
        //     'title' => ['required', 'max:190'],
        //     'filename' => ['required', 'max:190'],
        //     'show_title' => ['boolean'],
        //     'show_signature' => ['boolean'],
        //     'body' => ['required', 'max:1024'],
        //     'source' => ['max:190'],
        //     'concept' => ['boolean'],
        // ]);

        // $concept = $data['concept'] ?? false;

        // $oldFilename = $photo->filename;

        // if ($data['filename'] !== $oldFilename) {
        //     $data['file_id'] = null;
        //     $data['file_unique_id'] = null;
        // }

        // $photo->update($data);

        // if ($data['filename'] !== $oldFilename) {
        //     Storage::delete('public/media/' . session('channel.id') . '/' . $oldFilename);
        //     Storage::move(
        //         'public/tmp/' . $data['filename'], 
        //         'public/media/' . session('channel.id') . '/' . $data['filename']
        //     );
        // }

        // if ($concept) {
        //     TelegramPhoto::make($photo, concept: true)->publish();
        //     return back()->with('success', 'The photo was updated and tested');
        // }

        // return to_route('photos.index')->with('success', 'The photo was updated');
    }

    public function dates(Request $request, String $start = null, String $end = null) 
    {
        try {
            $start = Carbon::parse($start);
            $end = $end ? Carbon::parse($end) : null;
        } catch (InvalidFormatException $e) {
            echo 'Failed to parse time string';
        }

        return static::messagesBetweenDates($start, $end);
    }
    
    protected static function messagesBetweenDates(Carbon $start, Carbon $end): Collection
    {
        return Message::where('channel_id', session('channel.id'))
            ->whereBetween('published_at', [$start, $end])
            ->orderBy('created_at')
            ->get()->map(function ($message) {
                $type = str_replace('Group', '', Str::afterLast($message->messagable_type, '\\'));
                
                return [
                    'id' => $message->id,
                    'name' => $message->messagable->title,
                    'date' => Carbon::createFromTimeString($message->published_at)->jsonSerialize(),
                    'status' => $message->status,
                    'keywords' => $type,
                    'type' => Str::plural(strtolower($type)),
                    'type_id' => $message->messagable_id,
                ];
            });
    }
}
