<?php

namespace App\Listeners;

use App\Events\ChannelSessionChanged;
use App\Events\ChannelDeleted;
use App\Models\Channel;

class ChannelDeletedListener
{
   public function handle(ChannelDeleted $event): void
    {
        if (session('channel.id') !== $event->channel->id) {
            return;            
        }

        if ($channel = Channel::first()) {
            ChannelSessionChanged::dispatch($channel);
        } else {
            session(['channel.id' => null]);
            session(['channel.name' => null]);
            session(['channel.timezone' => null]);
            session(['channel.hours' => []]);
            session(['channel.list' => []]);
        }
    }
}
