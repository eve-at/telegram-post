<?php

namespace App\Http\Controllers;

use App\Events\ChannelSessionChanged;
use App\Models\Channel;
use Illuminate\Http\Request;

class SessionChannelController extends Controller
{
    public function update(Request $request, Channel $channel)
    {
        ChannelSessionChanged::dispatch($channel);

        return to_route('dashboard');
    }    
}
