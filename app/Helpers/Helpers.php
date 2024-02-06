<?php

namespace App\Helpers;

class Helper
{
    public static function filepath(int $channelId, string $filename)
    {
        return storage_path(implode(DIRECTORY_SEPARATOR, ['app', 'public', 'media', $channelId, $filename]));
    }
}


