<?php

namespace App\Listeners;

use App\Events\AdPublished;
use App\Events\MessagePublished;
use Illuminate\Contracts\Queue\ShouldQueue;

class MessagePublishedListener implements ShouldQueue
{
    public function handle(MessagePublished $event): void
    {

        $now = now();

        $message = $event->publishedMessage->message;
        $response = $event->publishedMessage->response;

        // set published date time to now
        $message->published_at = $now;
        $message->status = 1;

        // in case of MediaGroup responce contain an array of messages
        // otherwise there is an object with `message_id` property
        // TODO: refactor database structure to store all the message ids
        $message->message_id = $response->message_id ?? $response[0]->message_id ?? null;
        
        //update remove timestamp: now + reserved time + 2 minutes
        //but in case of 48h post: now + 48h - 2 minutes (A message can only be deleted if it was sent less than 48 hours ago)
        //https://core.telegram.org/bots/api#deletemessage
        if ($message->ad) {
            $message->ad_top_until = $now->clone()->addHours($message->ad_hours_on_top);

            if ($message->ad_remove_after_hours < 48) {
                $message->ad_delete_at = $now->clone()->addHours($message->ad_remove_after_hours)->addMinutes(2);
            } else {
                $message->ad_delete_at = $now->clone()->addHours(48)->subMinutes(2);
            }
        }

        $message->save();
        
        if ($message->ad && $message->id) {
            AdPublished::dispatch($message);
        }

        //TODO: move here the logic of updating post's media file_id(s)
        
    }
}
