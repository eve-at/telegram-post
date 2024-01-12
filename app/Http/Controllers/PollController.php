<?php

namespace App\Http\Controllers;

use App\Http\Resources\PollResource;
use App\Models\Poll;
use Illuminate\Http\Request;
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
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
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
