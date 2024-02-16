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
            'theChannel' => ChannelResource::make(new Channel()),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'max:190'],
            'signature' => ['max:190'],
            'chat_id' => ['required', 'numeric', 'starts_with:-100'],
            'hours' => ['max:12'],
            'hours.*' => ['integer', 'min:0', 'max:23'],
            'timezone' => ['timezone'],
            'post_loop' => ['boolean'],
            'comeback' => ['boolean'],
        ]);

        $channel = Channel::create($data);

        if ($data['comeback'] ?? false) {
            return to_route('channels.edit', $channel->id)
                        ->with('success', 'The channel was created');
        }

        return to_route('channels.index')->with('success', 'The channel was updated');
    }

    public function edit(Channel $channel)
    {
        return Inertia::render('Channel/Edit', [
            'title' => 'Settings',
            'theChannel' => ChannelResource::make($channel),
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
            'hours' => ['max:12'],
            'hours.*' => ['integer', 'min:0', 'max:23'],
            'timezone' => ['timezone'],
            'post_loop' => ['boolean'],
            'comeback' => ['boolean'],
        ]);

        $channel->update($data);
        
        if ($data['comeback'] ?? false) {
            return to_route('channels.edit', $channel->id)
                        ->with('success', 'The channel was updated');
        }

        return to_route('channels.index')->with('success', 'The channel was updated');
    }

    public function destroy(Channel $channel)
    {
        $channel->delete();

        return to_route('channels.index')->with('success', 'The channel was deleted');
    }

    public function setTemplate(Request $request, Channel $channel)
    {
        $data = $request->validate([
            'template' => ['required', 'string', 'max:4096'],
        ]);

        $channel->template = $data['template'];
        $channel->save();

        return ['ok'];
    }

    public function getTemplate(Channel $channel)
    {
        return $channel->template;
    }
}
