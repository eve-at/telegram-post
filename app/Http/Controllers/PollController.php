<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\Conceptable;
use App\Http\Resources\PollResource;
use App\Models\Poll;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class PollController extends Controller
{
    use Conceptable;

    public function index()
    {
        return Inertia::render('Poll/Index', [
            'polls' => PollResource::collection(
                Poll::where('channel_id', session('channel.id'))
                    ->orderBy('created_at', 'DESC')
                    ->paginate()
            )
        ]);
    }

    public function create()
    {
        return Inertia::render('Poll/Edit', [
            'title' => 'Create',
            'toRoute' => 'polls.store',
            'cancelRoute' => 'polls.index',
            'poll' => PollResource::make(new Poll),
            'types' => [
                'quiz' => 'Quiz', 
                'regular' => 'Regular',
            ],
        ]);
    }

    public function store(Request $request)
    {
        $maxAnswerIndex = 9;
        $options = $request->input('options', []);
        if (is_array($options) && count($options) > 0) {
            $maxAnswerIndex = count($options) - 1;
        }

        $data = $request->validate([
            'title' => ['required', 'max:190'],
            'type' => ['required', Rule::in(['quiz', 'regular'])],
            'options' => ['required', 'min:2', 'max:10'], // 2-10 options
            'options.*' => ['required', 'max:50'], // max 50 symbols per option
            'answer' => ['required', 'min:0', "max:$maxAnswerIndex"],
            'explanation' => ['max:190'],
            //'show_signature' => ['boolean'],
            //'show_signature' => ['boolean'], // non-anonymous polls can't be sent to channel chats
            'concept' => ['boolean'],
        ]);

        $concept = $data['concept'] ?? false;

        $data['correct_option_id'] = $data['answer'];
        unset($data['answer']);
        
        $data['is_anonymous'] = true; //
        $poll = Poll::make($data);
        $poll->user()->associate($request->user());
        $poll->channel()->associate(session('channel.id'));
        $poll->save();

        if ($concept) {
            $this->publishConcept($poll);

            return to_route('polls.edit', $poll->id)
                        ->with('success', 'The poll was created and tested');
        }

        return to_route('polls.index')->with('success', 'The poll was created');
    }

    public function edit(Poll $poll)
    {
        return Inertia::render('Poll/Edit', [
            'title' => 'Update',
            'toRoute' => 'polls.update',
            'cancelRoute' => 'polls.index',
            'poll' => PollResource::make($poll),
            'types' => [
                'quiz' => 'Quiz', 
                'regular' => 'Regular',
            ],
        ]);
    }

    public function update(Request $request, Poll $poll)
    {
        $maxAnswerIndex = 9;
        $options = $request->input('options', []);
        if (is_array($options) && count($options) > 0) {
            $maxAnswerIndex = count($options) - 1;
        }

        $data = $request->validate([
            'title' => ['required', 'max:190'],
            'type' => ['required', Rule::in(['quiz', 'regular'])],
            'options' => ['required', 'min:2', 'max:10'], // 2-10 options
            'options.*' => ['required', 'max:50'], // max 50 symbols per option
            'answer' => ['required', 'min:0', "max:$maxAnswerIndex"],
            'explanation' => ['max:190'],
            //'show_signature' => ['boolean'], // non-anonymous polls can't be sent to channel chats
            'is_anonymous' => ['boolean'],
            'concept' => ['boolean'],
        ]);

        $concept = $data['concept'] ?? false;

        $data['correct_option_id'] = $data['answer'];
        unset($data['answer']);

        $data['is_anonymous'] = true;
        $poll->update($data);

        if ($concept) {
            $this->publishConcept($poll->fresh());

            return to_route('polls.edit', $poll->id)
                        ->with('success', 'The poll was updated and tested');
        }

        return to_route('polls.index')->with('success', 'The poll was updated');
    }

    public function destroy(Poll $poll)
    {
        $poll->delete();

        return to_route('polls.index')->with('success', 'The poll was deleted');
    }
}
