<?php

namespace App\Http\Contracts;

use Telegram\Bot\Objects\Message;

interface TelegramPublishable
{ 
    // Type declaration for interfaces is supported from PHP 8.3
    // @see https://www.php.net/manual/en/language.types.declarations.php#language.types.declarations.union
    // public static function make(Post|Poll $object): TelegramMessagable;    
    public function publish(): Message;
}