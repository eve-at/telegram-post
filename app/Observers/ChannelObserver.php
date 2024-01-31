<?php

namespace App\Observers;

use App\Events\ChannelDeleted;
use App\Events\ChannelUpdated;
use App\Models\Channel;

class ChannelObserver
{
    /**
     * Handle the Channel "created" event.
     */
    public function created(Channel $channel): void
    {
        ChannelUpdated::dispatch($channel);
    }

    /**
     * Handle the Channel "updated" event.
     */
    public function updated(Channel $channel): void
    {
        ChannelUpdated::dispatch($channel);
    }

    /**
     * Handle the Channel "deleted" event.
     */
    public function deleted(Channel $channel): void
    {
        ChannelDeleted::dispatch($channel);
    }

    /**
     * Handle the Channel "restored" event.
     */
    public function restored(Channel $channel): void
    {
        ChannelUpdated::dispatch($channel);
    }

    /**
     * Handle the Channel "force deleted" event.
     */
    public function forceDeleted(Channel $channel): void
    {
        ChannelDeleted::dispatch($channel);
    }
}
