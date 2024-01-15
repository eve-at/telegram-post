<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PollResource extends JsonResource
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
            'title' => $this->title ?? '',
            'type' => $this->type ?? 'quiz',
            'options' => $this->options, // ?? ''
            'explanation' => $this->explanation ?? '',
            'answer' => $this->correct_option_id ?? 0,
            'show_signature' => $this->show_signature ?? false,
            'created_at' => $this->created_at ?? now(),
            'user' => UserResource::make($this->user),
        ];
    }
}
