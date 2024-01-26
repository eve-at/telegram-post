<?php

namespace App\Http\Controllers;

use App\Http\Resources\MessageResource;
use App\Http\Services\Scheduler;
use App\Models\Message;
use App\Models\Poll;
use App\Models\Post;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

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
            'message' => MessageResource::make(new Message()),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'schedulable_type' => ['required', Rule::in(['post', 'poll'])],
            'schedulable_id' => ['required', 'integer'],
            'published_at' => ['required', 'date'],
        ]);

        if ($data['schedulable_type'] === 'poll') {
            $messagable = Poll::find($data['schedulable_id']);
        } else {
            $messagable = Post::find($data['schedulable_id']);
        }

        // ReDo validation in case of an Ad
        if ($messagable?->ad) {
            $dataAd = $request->validate([
                'ad_hours_on_top' => ['integer', 'min:1'],
                'ad_remove_after_hours' => ['integer', 'min:0'],
            ]);

            $data = [...$data, ...$dataAd];
        }

        $publishedAt = Carbon::make($data['published_at']);

        $message = Message::make([
            'channel_id' => session('channel.id'),
            'ad' => $messagable->ad ?? false,
            'published_at' => $publishedAt,
        ]);

        // store polymorphic relation
        $message = $message->messagable()->associate($messagable);

        if ($message->ad) {
            $message->ad_hours_on_top = $data['ad_hours_on_top'];
            $message->ad_remove_after_hours = $data['ad_remove_after_hours'];
            $message->ad_top_until = $publishedAt->clone()->addHours($data['ad_hours_on_top']);
            $message->ad_removed_at = $publishedAt->clone()->addDays($data['ad_remove_after_hours']);
        }

        if (Scheduler::inConflict($message)) {
            return response()->json([
                'status' => false,
                'message' => 'Publish Date Conflict',
                'errors' => [['Publish Date Conflict']],
            ], 422);
        }

        $message->save();

        return JsonResource::make(['status' => 'ok']);
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

    public function date(String $date)
    {
        if (empty(session('channel.timezone'))) {
            throw new Exception('Channel timezone is missing');
        }

        try {
            $date = Carbon::parse($date, session('channel.timezone'));
        } catch(InvalidFormatException) {
            return [];
        }

        $start = $date->clone()->startOfDay()->setTimezone('UTC');
        $end = $date->clone()->endOfDay()->setTimezone('UTC');

        return Message::select([
            'id',
            'channel_id',
            'messagable_type',
            'messagable_id',
            'status',
            'published_at',
            'ad',
            'ad_hours_on_top',
            'ad_remove_after_hours',
            'ad_top_until',
            'ad_removed_at',
        ])
            ->with(['messagable:id,title'])
            ->where('channel_id', session('channel.id'))
            //->whereDate('published_at', $date)
            ->whereBetween('published_at', [$start, $end])
            ->orderBy('published_at')
            ->get()
            ->map(function ($message) {
                $message['published_at'] = Carbon::createFromFormat('Y-m-d H:i:s', $message['published_at'])->setTimezone(session('channel.timezone'))->toDateTimeString();
                $message['ad_top_until'] = $message['ad_top_until'] 
                    ? Carbon::createFromFormat('Y-m-d H:i:s', $message['ad_top_until'])->setTimezone(session('channel.timezone'))->toDateTimeString()
                    : null;
                $message['ad_removed_at'] = $message['ad_removed_at'] 
                    ? Carbon::createFromFormat('Y-m-d H:i:s', $message['ad_removed_at'])->setTimezone(session('channel.timezone'))->toDateTimeString()
                    : null;
                return $message;
            });        
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

    public function destroy(Message $message)
    {
        $message->delete();

        return ['ok'];
    }
}
