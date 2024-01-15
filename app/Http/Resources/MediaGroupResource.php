<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MediaGroupResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $filenames = $this->filenames->pluck('filename');
        return [
            'id' => $this->id,
            'title' => $this->title ?? '',
            'body' => $this->body ?? '',
            'source' => $this->source ?? '',
            'show_title' => $this->show_title ?? true,
            'show_signature' => $this->show_signature ?? true,
            'created_at' => $this->created_at ?? now(),
            'user' => UserResource::make($this->user),

            //names of files
            'filenames' => $filenames, 

            //URI of files for PQINA file plugin
            'filepaths' => $filenames->map(function ($filename) {
                return [
                    'source' => $filename,
                    'options' => ['type' => 'local'], //local => existing server file
                ];          
            }),        
        ];
    }
}
