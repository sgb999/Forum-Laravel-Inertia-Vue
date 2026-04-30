<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\{CommentStoreRequest, CommentEditRequest};
use App\Models\{Comment, Post};
use Illuminate\Http\{JsonResponse, RedirectResponse};

class CommentController extends Controller
{
    /**
     * Add a comment to an existing post
     *
     * @param Post $post
     * @param CommentStoreRequest $request
     *
     * @return JsonResponse
     */
    public function store(Post $post, CommentStoreRequest $request) : JsonResponse
    {
        $validated = $request->validated();
        $comment = Comment::create([
            'comment' => $validated['comment'],
            'post_id' => $post->id,
            'user_id' => auth()->id(),
        ]);

        return response()->json(
            $comment->load(['user' => function ($query) {
                    $query->select('id', 'username');
            }])->toResource(), 201);
    }

    /**
     * Update a comment.
     * If the user making the request does not match the original comment user id respond with a 403 error
     * If the comment has not been edited, send an HTTP status code of 304.
     *
     * @param Comment $comment
     * @param CommentEditRequest $request
     *
     * @return JsonResponse
     */
    public function edit(Comment $comment, CommentEditRequest $request) : JsonResponse
    {
        $comment->comment = $request->validated()['comment'];
        $comment->save();

        return response()->json($comment->load('user:id,username')->toResource());
    }

    /**
     * Delete a comment.
     * If the user making the request does not match the original comment user id respond with a 403 error
     *
     * @param Comment $comment
     *
     * @return RedirectResponse
     */
    public function destroy(Comment $comment) : RedirectResponse
    {
        abort_if($comment->user_id !== auth()->id(), 403);
        $comment->delete();

        return back();
    }
}
