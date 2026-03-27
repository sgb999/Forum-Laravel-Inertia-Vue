<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\MessageStoreRequest;
use App\Models\Chat;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class MessageController extends Controller
{
    public function show(int $id, string $filter) : JsonResponse
    {
        return response()->json(
            Message::with('user:id,username')
                ->where('chat_id', $id)
                ->where('created_at' > $filter)
                ->get()
        );
    }

    public function store(MessageStoreRequest $request)
    {
        $validated  = $request->validated();
        $validated += ['user_id' => auth()->id()];
        $chat       = Chat::find($validated['chat_id']);
        abort_unless($chat->user_id_1 === auth()->id() || $chat->user_id_2 === auth()->id(), 403);
        Message::create($validated);

        return back();
    }

    public function index(Chat $chat) : JsonResponse
    {
        abort_unless($chat->user_id_1 === auth()->id() || $chat->user_id_2 === auth()->id(), 403);

        return response()->json(
            Message::where('chat_id', $chat->id)
            ->with('user:id,username')
            ->select('message', 'user_id')
            ->get()
        );
    }
}
