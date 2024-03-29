<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChannelResource extends JsonResource
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
            'name' => $this->name ?? '',
            'chat_id' => $this->chat_id ?? '',
            'signature' => $this->signature ?? '',
            'timezone' => $this->timezone ?? 'Europe/London',
            'hours' => $this->hours ?? [8, 18],
            'created_at' => $this->created_at ?? now(),            
            'post_loop' => $this->post_loop ?? true,            
        ];
    }
}
