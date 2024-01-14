<?php

namespace App\Http\Controllers;

use App\Http\Services\TelegramService;
use App\Models\Post;
use Illuminate\Http\Request;

class ConceptController extends Controller
{
    public function store(Request $request)
    {   
        //validate
        /*$data = $request->validate([
            'messagable_type' => ['required'],
            'messagable_id' => ['required'],
        ]);*/

        // detect post type ['post', 'photo', 'video', 'group', 'poll']
        $postable = Post::find(2);

        // publish post on telegram
        return TelegramService::publish($postable, concept: true);
    }    
}
