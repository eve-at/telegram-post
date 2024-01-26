<?php

namespace App\Http\Controllers;

use App\Http\Resources\ChannelResource;
use App\Models\Channel;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

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
            'timezones' => array_combine(timezone_identifiers_list(), timezone_identifiers_list()),
            'channel' => ChannelResource::make(new Channel),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'max:190'],
            'signature' => ['max:190'],
            'chat_id' => ['required', 'numeric', 'starts_with:-100'],
            'hours' => ['max:5'],
            'hours.*' => ['integer', 'min:0', 'max:23'],
            'timezone' => ['timezone'],
        ]);

        Channel::create($data);

        SessionChannelController::refreshSession();

        return to_route('channels.index')->with('success', 'The channel was updated');
    }

    public function edit(Channel $channel) 
    {
        return Inertia::render('Channel/Edit', [
            'title' => 'Settings',
            'channel' => ChannelResource::make($channel),
            'timezones' => array_combine(timezone_identifiers_list(), timezone_identifiers_list()),
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
            'hours' => ['max:5'],
            'hours.*' => ['integer', 'min:0', 'max:23'],
            'timezone' => ['timezone'],
        ]);

        $channel->update($data);

        SessionChannelController::refreshSession();

        return to_route('channels.index')->with('success', 'The channel was updated');
    }

    public function destroy(Channel $channel)
    {
        $channel->delete();

        SessionChannelController::refreshSession();

        return to_route('channels.index')->with('success', 'The channel was deleted');
    }
}
