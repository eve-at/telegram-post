<?php

namespace App\Models;

use App\Observers\ChannelObserver;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Channel extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $guarded = ['id'];

    protected $casts = [
        'post_loop' => 'boolean',
    ];

    public function posts()
    {
        return $this->hasMany(Post::class);    
    }
    
    public function hours(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => json_decode($value),
            set: fn (array $arr) => json_encode(collect($arr)->sort()->unique()->values()),
        );
    }
}
