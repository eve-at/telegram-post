<?php

namespace App\Listeners;

use App\Models\Channel;

class ChannelSessionInitListener
{
    public function handle(): void
    {
        $channel = Channel::orderBy('name')->first();

        session(['channel.id' => $channel?->id]);
        session(['channel.name' => $channel?->name]);
        session(['channel.timezone' => $channel?->timezone]);
        session(['channel.hours' => $channel?->hours]);
    }    
}
