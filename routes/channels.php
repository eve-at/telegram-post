<?php

use App\Models\Channel;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function (User $user, int $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel("schedule.{channel_id}", function (User $user, int $channel_id) {
    return true; // all the users are admins so far
    //return (int) $user->id === (int) $id;
});

// Channel::select('id')->each(function ($channel_id) {
//     Broadcast::channel("schedule.$channel_id", function (User $user) { //, int $channel_id
//         return true; // all the users are admins so far
//         //return (int) $user->id === (int) $id;
//     });
// });
