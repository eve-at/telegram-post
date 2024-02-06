<?php

namespace App\Listeners;

use App\Events\MessagePublished;

class MessagePublishedListener
{
    public function handle(MessagePublished $event): void
    {

        $message = $event->publishedMessage->message;
        $response = $event->publishedMessage->response;

        // set published date time to now
        $message->published_at = now();
        $message->status = 1;

        // in case of MediaGroup responce contain an array of messages
        // otherwise there is an object with `message_id` property
        // TODO: refactor database structure to store all the message ids
        $message->message_id = $response->message_id ?? $response[0]->message_id ?? null;
        
        $message->save();

        //TODO: move here the logic of updating post's media file_id(s)
        
    }
}
