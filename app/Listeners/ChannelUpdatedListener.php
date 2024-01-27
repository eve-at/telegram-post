<?php

namespace App\Listeners;

use App\Events\ChannelUpdated;
use App\Models\Channel;

class ChannelUpdatedListener
{
   public function handle(ChannelUpdated $event): void
    {
        if (session('channel.id') !== $event->channel->id) {
            return;
        }

        session(['channel.id' => $event->channel->id]);
        session(['channel.name' => $event->channel->name]);
        session(['channel.timezone' => $event->channel->timezone]);
        session(['channel.hours' => $event->channel->hours]);
    }
}
