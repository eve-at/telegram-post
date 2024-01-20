<?php

namespace App\Http\Controllers;

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
