<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class PostController extends Controller
{
    public function index() 
    {
        return Inertia::render('Post/Index');    
    }
    
}
