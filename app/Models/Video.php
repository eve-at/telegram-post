<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    public $guarded = ['id'];

    protected $casts = [
        'show_title' => 'boolean',
        'show_signature' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);    
    }

    public function channel()
    {
        return $this->belongsTo(Channel::class);    
    }
}
