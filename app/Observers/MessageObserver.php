<?php

namespace App\Observers;

use App\Events\ScheduledInChannel;
use App\Models\Message;

class MessageObserver
{
    /**
     * Handle the Message "created" event.
     */
    public function created(Message $message): void
    {
        //ScheduledInChannel::dispatch($message->channel);
        broadcast(new ScheduledInChannel($message->channel))->toOthers();
    }

    /**
     * Handle the Message "updated" event.
     */
    public function updated(Message $message): void
    {
        //ScheduledInChannel::dispatch($message->channel);
        broadcast(new ScheduledInChannel($message->channel))->toOthers();
    }

    /**
     * Handle the Message "deleted" event.
     */
    public function deleted(Message $message): void
    {
        //ScheduledInChannel::dispatch($message->channel);
        //broadcast(new ScheduledInChannel($message))->toOthers();
        broadcast(new ScheduledInChannel($message->channel))->toOthers();
    }
}
