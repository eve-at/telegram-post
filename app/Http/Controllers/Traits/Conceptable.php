<?php

namespace App\Http\Controllers\Traits;

use App\Http\Services\TelegramMediaGroup;
use App\Http\Services\TelegramPhoto;
use App\Http\Services\TelegramPoll;
use App\Http\Services\TelegramMessage;
use App\Http\Services\TelegramService;
use App\Http\Services\TelegramVideo;

trait Conceptable 
{
    protected function publishConcept(mixed $media): array
    {
        return TelegramService::make($media, concept: true)->publish();    
    }
}