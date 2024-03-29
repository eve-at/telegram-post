<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Poll extends Model
{
    use HasFactory;

    public $guarded = ['id'];
    
    protected $casts = [
        //'show_signature' => 'boolean',
        'is_anonymous' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);    
    }
        
    public function channel()
    {
        return $this->belongsTo(Channel::class);    
    }

    public function options(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => json_decode($value),
            set: fn (array $value) => json_encode($value),
        );
    }

    public function messages()
    {
        return $this->morphMany(Message::class, 'messagable')->orderBy('publish_at');      
    }
}
