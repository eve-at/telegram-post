<?php

namespace App\Http\Controllers\Traits;

use App\Http\Services\TelegramService;

trait Conceptable 
{
    protected function publishConcept(mixed $media): array
    {
        return TelegramService::make($media, concept: true)->publish();    
    }
}