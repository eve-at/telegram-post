<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VideoResource extends JsonResource
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
            'body' => $this->body ?? '',
            'show_title' => $this->show_title ?? true,
            'show_signature' => $this->show_signature ?? true,
            'filename' => $this->filename ?? '',
            'source' => $this->source ?? '',
            'created_at' => $this->created_at ?? now(),
            //'file' => FileResource::make($this->file),
            'user' => UserResource::make($this->user),

            //URI of files for PQINA file plugin
            'filepaths' => [[
                'source' => $this->filename,
                'options' => ['type' => 'local'], //local => existing server file
            ]],  
        ];
    }
}
