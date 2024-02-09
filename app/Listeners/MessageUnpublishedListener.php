<?php

namespace App\Listeners;

use App\Events\MessageUnpublished;
use Illuminate\Contracts\Queue\ShouldQueue;

class MessageUnpublishedListener implements ShouldQueue
{
    public function handle(MessageUnpublished $event): void
    {
        $message = $event->message;
        
        $message->ad_deleted_at = now();
        $message->save();        
    }
}
