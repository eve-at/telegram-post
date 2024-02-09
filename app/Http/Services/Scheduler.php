<?php

namespace App\Http\Services;

use App\Models\Channel;
use App\Models\Message;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\MorphTo;

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
                        $query->whereBetween('publish_at', [$message->publish_at, $message->ad_top_until]);
                    })
                    ->orWhere(function ($query) use ($message) {
                        $query->where('ad', 1)
                            ->whereBetween('ad_top_until', [$message->publish_at, $message->ad_top_until]);
                    })
                    ->orWhere(function ($query) use ($message) {
                        $query->where('ad', 1)
                            ->where('publish_at', '<=', $message->publish_at)
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
                $query->where('publish_at', '<=', $message->publish_at)
                    ->where('ad_top_until', '>=', $message->publish_at);
            })->count();
            
        return $count > 0;
    }   
    
    public static function autoScheduledMessage(Channel $channel, Carbon $publishAt): bool
    {
        // skip auto-scheduling if another post already scheduled in current hour
        $scheduled = Message::query()
            ->where('channel_id', $channel->id)
            ->where('status', 0)
            ->whereBetween('publish_at', [$publishAt->clone()->startOfHour(), $publishAt->clone()->addHour()])
            ->count();

        if ($scheduled > 0) {
            return false;
        }

        $message = Message::make([
            'channel_id' => $channel->id,
            'publish_at' => $publishAt,
            'ad' => 0,
        ]);

        //skip if in conflict with already published ad
        if (self::inConflict($message)) {
            return false;
        }

        $post = Post::select(['posts.id'])
            ->leftJoin('messages', function ($join) {
                $join->on('messages.messagable_id', '=', 'posts.id')
                ->where('messages.messagable_type', '=', 'App\Models\Post');
            })
            ->where('posts.channel_id', $channel->id)
            ->orderBy('messages.publish_at')
            ->first();

        if (! $post) {
            return false;
        }

        $post->fresh();
        
        $message = $message->messagable()->associate($post);

        return $message->save();
    }
}