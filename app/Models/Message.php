<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;

    public $guarded = ['id'];
    public $with = ['messagable'];

    public function messagable()
    {
        return $this->morphTo();
    }
    
}
