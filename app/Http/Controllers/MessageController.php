<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\MessageStoreRequest;
use App\Http\Resources\MessageResource;
use App\Models\Chat;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class MessageController extends Controller
{
    /**
     * @param int $id
     * @param string $filter
     *
     * @return MessageResource
     */
    public function show(int $id, string $filter) : MessageResource
    {
        return new MessageResource(
            Message::with('user:id,username')
                ->where('chat_id', $id)
                ->where('created_at' > $filter)
                ->get()
        );
    }

    /**
     * @param MessageStoreRequest $request
     *
     * @return RedirectResponse
     */
    public function store(MessageStoreRequest $request): RedirectResponse
    {
        $validated  = $request->validated();
        $validated += ['user_id' => auth()->id()];
        $chat       = Chat::find($validated['chat_id']);
        abort_unless($chat->user_id_1 === auth()->id() || $chat->user_id_2 === auth()->id(), 403);
        Message::create($validated);

        return back();
    }

    public function index(Chat $chat) : MessageResource
    {
        abort_unless($chat->user_id_1 === auth()->id() || $chat->user_id_2 === auth()->id(), 403);

        return new MessageResource(
            Message::where('chat_id', $chat->id)
            ->with('user:id,username')
            ->select(['message', 'user_id'])
            ->get()
        );
    }
}
