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
            'title' => $this->title,
            'body' => $this->body,
            'source' => $this->source,
            'created_at' => $this->created_at,
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
