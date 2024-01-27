<?php

namespace App\Listeners;

use App\Events\ChannelSessionChanged;
use App\Events\ChannelDelited;
use App\Models\Channel;

class ChannelDelitedListener
{
   public function handle(ChannelDelited $event): void
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
