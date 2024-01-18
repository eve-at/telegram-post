<?php

namespace App\Http\Controllers;

use App\Http\Resources\ChannelResource;
use App\Models\Channel;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Telegram\Bot\Objects\Chat;

class ChannelController extends Controller
{
    public function index() 
    {
        return Inertia::render('Channel/Index', [
            'channels' => ChannelResource::collection(Channel::orderBy('name')->paginate())
        ]);
    }
    
    public function create()
    {
        return Inertia::render('Channel/Edit', [
            'title' => 'Create',
            'toRoute' => 'channels.store',
            'cancelRoute' => 'channels.index',
            'channel' => ChannelResource::make(new Channel),
        ]);
    }

    public function store(Request $request)
    {
        Channel::create($request->validate([
            'name' => ['required', 'max:190'],
            'signature' => ['max:190'],
            'chat_id' => [
                'required', 
                'numeric', 
                'starts_with:-100'
            ],
        ]));

        return to_route('channels.index')->with('success', 'The channel was updated');
    }

    public function edit(Channel $channel) 
    {
        return Inertia::render('Channel/Edit', [
            'title' => 'Settings',
            'channel' => ChannelResource::make($channel),
            'toRoute' => 'channels.update',
            'cancelRoute' => 'channels.index',
        ]);    
    }
    
    public function update(Request $request, Channel $channel)
    {
        $data = $request->validate([
            'name' => ['required', 'max:190'],
            'signature' => ['max:190'],
            'chat_id' => [
                'required', 
                'numeric', 
                'starts_with:-100', 
                Rule::unique('channels')->ignore($channel->id)
            ],
        ]);

        $channel->update($data);

        return to_route('channels.index')->with('success', 'The channel was updated');
    }

    public function destroy(Channel $channel)
    {
        $channel->delete();

        return to_route('channels.index')->with('success', 'The channel was deleted');
    }
}
