<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Channel extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $guarded = ['id'];

    public function posts()
    {
        return $this->hasMany(Post::class);    
    }
    
    public function hours(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => collect(explode(',', $value))->map(fn($i) => (int)$i)->toArray(),
            set: fn (array $arr) => collect($arr)->sort()->unique()->implode(','),
        );
    }
}
