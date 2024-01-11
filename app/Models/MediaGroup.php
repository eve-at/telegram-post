<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class MediaGroup extends Model
{
    use HasFactory;

    public $guarded = ['id'];
    public $with = ['filenames'];
    
    public function user()
    {
        return $this->belongsTo(User::class);    
    }
        
    public function channel()
    {
        return $this->belongsTo(Channel::class);    
    }
        
    public function filenames()
    {
        return $this->hasMany(MediaGroupFile::class)->orderBy('order');    
    }
}
