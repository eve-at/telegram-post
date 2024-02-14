<?php

namespace App\Events;

use App\Models\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\Channel as BroadcastChannel;

class ScheduledInChannel implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public Channel $channel)
    {
        //
    }

    public function broadcastOn(): BroadcastChannel
    {
        return new PrivateChannel('schedule.' . $this->channel->id);
    }

    // public function broadcastWhen(): bool
    // {
    //     return session('channel.id') !== $this->channel->channel_id;
    // }
}
