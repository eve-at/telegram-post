<?php

namespace App\Http\Services;

use App\Http\Contracts\TelegramMessagable;
use App\Models\Channel;
use Exception;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramService 
{
    protected $channelId;

    function __construct($concept = false) 
    {
        $this->channelId = $concept ? env('TELEGRAM_CONCEPT_CHANNEL_ID') : env('TELEGRAM_CHANNEL_ID');
    }

    public function sendMessage(TelegramMessagable $messagable) 
    {
        $channel = Channel::first();
        if (! $channel) {
            throw new Exception('No channel found');
        }

        //'protect_content' => true,
        //'disable_notification' => true,
        $message = [
            'parse_mode' => 'HTML',
            'text' => '',
            ...$messagable->message(),
            'chat_id' => $this->channelId, //env('TELEGRAM_CHANNEL_ID'),
        ];
        
        if ($messagable->showSignature()) {
            $message['text'] .= (strlen($message['text']) > 0 ? PHP_EOL . PHP_EOL : '')
                             . $channel->signature;
        }
        
        return Telegram::sendMessage($message);
    }

    public static function publish(TelegramMessagable $messagable, $concept = false)
    {
        return match($messagable->type()) {
            'Post' => (new self($concept))->sendMessage($messagable),
            default => throw new Exception('Unsupported messagable class')
        };
    }
    
}