<?php

namespace App\Http\Controllers\Traits;

use App\Http\Services\TelegramService;

trait Conceptable 
{
    protected function publishConcept(mixed $media): void
    {
        TelegramService::make($media, concept: true)->publish();    
    }
}