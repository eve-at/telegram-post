<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    public $guarded = ['id'];
    public $with = ['messagable'];

    protected $casts = [
        'status' => 'boolean',
        'ad' => 'boolean',
    ];

    public function messagable()
    {
        return $this->morphTo();
    }
    
}
