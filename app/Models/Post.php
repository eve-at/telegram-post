<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    public $guarded = ['id'];
    //public $with = ['filenames'];
    
    protected $casts = [
        'show_title' => 'boolean',
        'show_signature' => 'boolean',
        'ad' => 'boolean',
    ];

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
        return $this->hasMany(PostFile::class)->orderBy('order');    
    }

    public function messages()
    {
        return $this->morphMany(Message::class, 'messagable')->orderBy('publish_at');    
    }
}
