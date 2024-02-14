<?php

namespace App\Observers;

use App\Events\MessageScheduled;
use App\Models\Message;

class MessageObserver
{
    /**
     * Handle the Message "created" event.
     */
    public function created(Message $message): void
    {
        //MessageScheduled::dispatch($message);
        broadcast(new MessageScheduled($message))->toOthers();
    }

    /**
     * Handle the Message "updated" event.
     */
    public function updated(Message $message): void
    {
        //MessageScheduled::dispatch($message);
        broadcast(new MessageScheduled($message))->toOthers();
    }

    /**
     * Handle the Message "deleted" event.
     */
    public function deleted(Message $message): void
    {
        //MessageScheduled::dispatch($message);
        broadcast(new MessageScheduled($message))->toOthers();
    }
}
