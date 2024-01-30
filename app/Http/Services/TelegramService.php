<?php

namespace App\Http\Services;

use App\Http\Contracts\TelegramPublishable;
use App\Models\Message;

class TelegramService 
{

    public static function make(Message $message, Bool $concept = false): TelegramPublishable
    {
        $serviceClass = match($message->type) {
            // Poll
            'quiz', 'regular' => TelegramPoll::class,

            // MediaGroup
            'photo' => TelegramPhoto::class,
            'video' => TelegramVideo::class,
            'media_group' => TelegramMediaGroup::class,
            'message' => TelegramMessage::class,
            
            default => TelegramMessage::class,
        };

        return $serviceClass::make($message->messagable()->first(), $concept);
    }   
    
}