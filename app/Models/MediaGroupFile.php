<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class MediaGroupFile extends Model
{
    use HasFactory;

    public $guarded = ['id'];
}
