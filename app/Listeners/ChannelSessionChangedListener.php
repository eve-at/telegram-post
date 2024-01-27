<?php

namespace App\Listeners;

use App\Events\ChannelSessionChanged;

class ChannelSessionChangedListener
{
    public function handle(ChannelSessionChanged $event): void
    {
        session(['channel.id' => $event->channel->id]);
        session(['channel.name' => $event->channel->name]);
        session(['channel.timezone' => $event->channel->timezone]);
        session(['channel.hours' => $event->channel->hours]);
    }    
}
