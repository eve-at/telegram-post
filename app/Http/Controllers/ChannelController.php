<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class ChannelController extends Controller
{
    public function edit() 
    {
        return Inertia::render('Channel/Edit');    
    }
    
}
