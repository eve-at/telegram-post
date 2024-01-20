<?php

namespace App\Http\Resources;

use App\Models\MediaGroup;
use App\Models\Photo;
use App\Models\Poll;
use App\Models\Post;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $messagableResource = match ($this->messagable_type) {
            Post::class => PostResource::class,
            Video::class => VideoResource::class,
            Photo::class => PhotoResource::class,
            MediaGroup::class => MediaGroupResource::class,
            Poll::class => PollResource::class,
            default => null,
        };

        return [
            'id' => $this->id,
            'channel_id' => $this->channel_id,
            'message_id' => $this->message_id,
            'body' => $this->body,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'published_at' => $this->published_at,
            'messagable_type' => $this->messagable_type,
            'messagable_id' => $this->messagable_id,
            'messagable' => $messagableResource 
                ? $messagableResource::make($this->messagable)  //$this->whenLoaded('releaseable')
                : null,
        ];
    }
}
