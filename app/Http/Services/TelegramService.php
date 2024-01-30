<?php

namespace App\Http\Services;

use App\Http\Contracts\TelegramPublishable;

class TelegramService 
{

    public static function make(mixed $messagable, Bool $concept = false): TelegramPublishable
    {

        $serviceClass = match($messagable->type) {
            // Poll
            'quiz', 'regular' => TelegramPoll::class,

            // MediaGroup
            'photo' => TelegramPhoto::class,
            'video' => TelegramVideo::class,
            'media_group' => TelegramMediaGroup::class,
            'message' => TelegramMessage::class,
            
            default => TelegramMessage::class,
        };

        return $serviceClass::make($messagable, $concept);
    }   
    
}