<?php

namespace App\Http\Controllers\Traits;

use App\Http\Services\TelegramService;
use App\Models\Message;
use App\Notifications\MessagePublished;
use Illuminate\Support\Facades\Notification;

trait Conceptable
{
    protected function publishConcept(mixed $media): void
    {
        TelegramService::make($media, concept: true)->publish();

        if (auth()->check() && auth()->user()->send_notifications) {
            $message = Message::make([
                'channel_id' => session('channel.id'),
                'ad' => false,
                'publish_at' => now(),
            ]);

            $message = $message->messagable()->associate($media);

            Notification::send(auth()->user(), new MessagePublished($message));
        }
    }
}
