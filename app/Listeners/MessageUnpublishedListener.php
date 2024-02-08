<?php

namespace App\Listeners;

use App\Events\MessageUnpublished;

class MessageUnpublishedListener
{
    public function handle(MessageUnpublished $event): void
    {
        $message = $event->message;
        
        $message->ad_deleted_at = now();
        $message->save();        
    }
}
