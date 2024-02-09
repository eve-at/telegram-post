<?php

namespace App\Http\Services;

use App\Http\Contracts\TelegramPublishable;
use App\Models\Message;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramService
{
    public static function make(mixed $messagable, Bool $concept = false): TelegramPublishable
    {
        $serviceClass = match ($messagable->type) {
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

    // https://core.telegram.org/bots/api#deletemessage
    public static function deleteMessage(Message $message): bool
    {
        // Must return True on success, but returns void
        Telegram::deleteMessage([
            'chat_id' => $message->channel->chat_id, 
            'message_id' => $message->message_id
        ]);

        return true;
    }
}
