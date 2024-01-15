<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
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
            'title' => $this->title,
            'show_title' => $this->show_title,
            'body' => $this->body,
            'show_signature' => $this->show_signature,
            'source' => $this->source,
            'created_at' => $this->created_at,
            'user' => UserResource::make($this->user)
        ];
    }
}
