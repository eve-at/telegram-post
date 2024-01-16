<?php

namespace App\Http\Contracts;

// use App\Models\MediaGroup;
// use App\Models\Photo;
// use App\Models\Poll;
// use App\Models\Post;
// use App\Models\Video;

interface TelegramPublishable
{ 
    // Type declaration for interfaces is supported from PHP 8.3
    // @see https://www.php.net/manual/en/language.types.declarations.php#language.types.declarations.union
    // public static function make(Post|Poll|Photo|Video|MediaGroup $object): TelegramMessagable;    
    public function publish(): array;  
}