<?php

namespace App\Http\Services;

use App\Models\Message;

class Scheduler 
{
    /**
     * Is this message's publish date in conflict with other messages.
     * Period between `publish_at` and `top_until` of a message can't intersect with other messages.
     */
    public static function inConflict(Message $message): bool
    {
        if ($message->ad) {
            $count = Message::query()
                ->where('channel_id', $message->channel_id)
                ->where('id', '<>', $message->id)
                ->where(function ($query) use ($message) {
                    $query->where(function ($query) use ($message) {
                        $query->whereBetween('published_at', [$message->published_at, $message->ad_top_until]);
                    })
                    ->orWhere(function ($query) use ($message) {
                        $query->where('ad', 1)
                            ->whereBetween('ad_top_until', [$message->published_at, $message->ad_top_until]);
                    })
                    ->orWhere(function ($query) use ($message) {
                        $query->where('ad', 1)
                            ->where('published_at', '<=', $message->published_at)
                            ->where('ad_top_until', '>=', $message->ad_top_until);
                    });
                })->count();

            return $count > 0;
        } 
        
        $count = Message::query()
            ->where('channel_id', $message->channel_id)
            ->where('ad', 1)
            ->where('id', '<>', $message->id)
            ->where(function ($query) use ($message) {
                $query->where('published_at', '<=', $message->published_at)
                    ->where('ad_top_until', '>=', $message->published_at);
            })->count();
            
        return $count > 0;
    }       
}