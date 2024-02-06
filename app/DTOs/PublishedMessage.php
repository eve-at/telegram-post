<?php

namespace App\DTOs;

use App\Models\Message;
use Telegram\Bot\Objects\Message as TelegramMessage;

class PublishedMessage
{
    public function __construct(
        readonly public Message $message, 
        readonly public TelegramMessage $response
    ) {}
    
}