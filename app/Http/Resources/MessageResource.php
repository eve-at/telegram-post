<?php

namespace App\Http\Resources;

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
        return [
            'id' => $this->id,
            'channel_id' => $this->channel_id,
            'messagable_type' => $this->messagable_type,
            'messagable_id' => $this->messagable_id,
            'body' => $this->body,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'published_at' => $this->published_at,
        ];
    }
}
