<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    use HasFactory;

    public $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);    
    }

    public function file()
    {
        return $this->belongsTo(File::class);    
    }

    public function channel()
    {
        return $this->belongsTo(Channel::class);    
    }
}
