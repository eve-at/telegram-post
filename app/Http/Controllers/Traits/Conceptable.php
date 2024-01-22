<?php

namespace App\Http\Controllers\Traits;

use App\Http\Services\TelegramMediaGroup;
use App\Http\Services\TelegramPhoto;
use App\Http\Services\TelegramPoll;
use App\Http\Services\TelegramMessage;
use App\Http\Services\TelegramVideo;

trait Conceptable 
{
    protected function publishConcept(mixed $media): array
    {
        $telegramService = match($media->type) {
            // Poll
            'quiz', 'regular' => TelegramPoll::class,

            // MediaGroup
            'photo' => TelegramPhoto::class,
            'video' => TelegramVideo::class,
            'media_group' => TelegramMediaGroup::class,
            'message' => TelegramMessage::class,
            
            default => TelegramMessage::class,
        };

        return $telegramService::make($media, concept: true)->publish();    
    }
}