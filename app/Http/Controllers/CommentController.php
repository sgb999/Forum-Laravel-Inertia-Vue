<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CommentEditRequest;
use App\Http\Requests\CommentStoreRequest;
use App\Models\Comment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class CommentController extends Controller
{
    public function store(CommentStoreRequest $request) : RedirectResponse
    {
        $validated = $request->validated();
        Comment::create([
            'comment' => $validated['comment'],
            'post_id' => $validated['post_id'],
            'user_id' => auth()->id(),
        ]);
        return back();
    }

    public function edit(Comment $comment, CommentEditRequest $request) : RedirectResponse
    {
        abort_if($comment->user_id !== auth()->id(), 403);
        $validated        = $request->validated();
        $comment->comment = $validated['comment'];
        $comment->save();
        return back();
    }

    public function destroy(Comment $comment) : RedirectResponse
    {
        abort_if($comment->user_id !== auth()->id(), 403);
        $comment->delete();
        return back();
    }
}
