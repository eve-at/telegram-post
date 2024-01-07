<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    use HasFactory;

    protected $appends = ['type'];

    public function getTypeAttribute()
    {
        return match ($type = (new \ReflectionClass($this->postable_type))->getShortName()) {
            'MediaGroup' => 'Group',
            default => $type
        };
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
