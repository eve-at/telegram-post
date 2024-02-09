<?php

namespace App\Listeners;

use App\Events\AdPublished;
use App\Http\Services\Scheduler;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;

class AdPublishedListener implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(AdPublished $event): void
    {
        $publishedMessage = $event->message;

        Scheduler::autoScheduledMessage(
            channel: $publishedMessage->channel, 
            publishAt: Carbon::parse($publishedMessage->ad_top_until)->addMinutes(2),
        );
    }
}
