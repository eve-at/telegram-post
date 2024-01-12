<?php

namespace App\Http\Controllers;

use App\Http\Resources\PollResource;
use App\Models\Poll;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class PollController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Poll/Index', [
            'polls' => PollResource::collection(Poll::orderBy('created_at', 'DESC')->paginate())
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Poll/Edit', [
            'toRoute' => 'polls.store',
            'title' => '',
            'type' => 'quiz',
            'options' => [],
            'answer' => 0,
            'explanation' => '',

            'types' => [
                'quiz' => 'Quiz', 
                'regular' => 'Regular',
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return $request->all();
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Poll $poll)
    {
        return Inertia::render('Poll/Edit', [
            'toRoute' => 'polls.update',
            'id' => $poll->id,
            'title' => $poll->title,
            'type' => $poll->type,
            'options' => $poll->options,
            'answer' => $poll->correct_option_id,
            'explanation' => $poll->explanation ?? '',

            'types' => [
                'quiz' => 'Quiz', 
                'regular' => 'Regular',
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
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
        ]);

        $data['correct_option_id'] = $data['answer'];
        unset($data['answer']);
        $poll->update($data);

        return to_route('polls.index')->with('success', 'The poll was updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Poll $poll)
    {
        $poll->delete();

        return to_route('polls.index')->with('success', 'The poll was deleted');
    }
}
