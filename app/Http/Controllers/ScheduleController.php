<?php

namespace App\Http\Controllers;

use App\Http\Services\Scheduler;
use App\Models\Message;
use App\Models\Poll;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Validation\Rule;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // TODO: `hours_on_top` and `remove_after_hours` must be required in case of an ad
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
                'hours_on_top' => ['integer', 'min:1'],
                'remove_after_hours' => ['integer', 'min:0'],
            ]);

            $data = [...$data, ...$dataAd];
        }

        $publishedAt = Carbon::make($data['published_at']);

        $message = Message::make([
            'channel_id' => session('channel.id'),
            'ad' => $messagable?->ad,
            'published_at' => $publishedAt,
        ]);
        
        // store polymorphic relation
        $message = $message->messagable()->associate($messagable);

        if ($message->ad) {
            $message->ad_top_until = $publishedAt->clone()->addHours($data['hours_on_top']);
            $message->ad_removed_at = $publishedAt->clone()->addDays($data['remove_after_hours']);
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

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
