<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\Channel as BroadcastChannel;
use Illuminate\Support\Facades\Log;

class MessageScheduled implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public Message $message)
    {
        Log::info('dispatched');
        //
    }

    public function broadcastOn(): BroadcastChannel
    {
        return new PrivateChannel('schedule.' . $this->message->channel_id);
    }

    // public function broadcastWhen(): bool
    // {
    //     return session('channel.id') !== $this->message->channel_id;
    // }
}
