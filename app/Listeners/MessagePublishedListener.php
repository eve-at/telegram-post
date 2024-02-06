<?php

namespace App\Listeners;

use App\DTOs\PublishedMessage;

class MessagePublishedListener
{
    public function handle(PublishedMessage $pm): void
    {
        // set published date time to now
        $pm->message->published_at = now();
        $pm->message->status = 1;
        $pm->message->save();

        //TODO: move here the logic of updating post's media file_id(s)
    }
}
