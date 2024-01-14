<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    public $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);    
    }

    public function channel()
    {
        return $this->belongsTo(Channel::class);    
    }

    public function show_title(): Attribute
    {
        return Attribute::make(
            get: fn (int $value) => (bool)$value,
            set: fn (bool $value) => $value ? 1 : 0,
        );
    }
}
