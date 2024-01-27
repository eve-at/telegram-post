<?php

namespace App\Listeners;

use App\Models\Channel;

class ChannelSessionListListener
{
    public function handle(): void
    {
        session([
            'channel.list' => Channel::select(['id', 'name', 'timezone'])
                ->orderBy('name')
                ->get()
                ->toJson()
        ]);
    }
}
