<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PhotoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $filename = $this->filename;
        return [
            'id' => $this->id,
            'title' => $this->title ?? '',
            'show_title' => $this->show_title ?? true,
            'body' => $this->body ?? '',
            'show_signature' => $this->show_signature ?? true,
            'filename' => $filename ?? '',
            'source' => $this->source ?? '',
            'created_at' => $this->created_at ?? now(),
            //'file' => FileResource::make($this->file),
            'user' => UserResource::make($this->user),

            //URI of files for PQINA file plugin
            'filepaths' => $filename 
                ? [[
                    'source' => $filename,
                    'options' => ['type' => 'local'], //local => existing server file
                ]]
                :[],
        ];
    }
}
