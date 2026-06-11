<?php

declare(strict_types=1);

use App\Models\Chat;
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

Broadcast::channel('chat.{chatId}', function ($user, int $chatId) {
    $chat = Chat::find($chatId);

    if (! $chat) {
        return false;
    }

    return (int) $chat->user_id_1 === (int) $user->id
        || (int) $chat->user_id_2 === (int) $user->id;
});

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
