<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use Illuminate\Http\Request;

class SessionChannelController extends Controller
{
    public static function setChannel()
    {
        // TODO: get user's channels
        $channels = Channel::select(['id', 'name'])
                            ->orderBy('name')
                            ->get();

        $channel ??= $channels[0] ?? null;

        if ($channel) {
            session(['channel.id' => $channel->id]);
            session(['channel.name' => $channel->name]);
            session(['channel.list' => $channels->toJson()]);
        }
    }

    public function update(Request $request, Channel $channel) 
    {

        if ($channel) {
            session(['channel.id' => $channel->id]);
            session(['channel.name' => $channel->name]);
        } 

        return to_route('dashboard');
    }
    
}
