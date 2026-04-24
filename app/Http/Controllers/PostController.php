<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\CommentResource;
use App\Models\Comment;
use Inertia\Inertia;
use App\Http\Requests\{
    PostFilterRequest,
    PostStoreRequest
};
use App\Http\Resources\PostResource;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Inertia\ResponseFactory;

class PostController extends Controller
{

    /**
     * @param Post $post
     *
     * @return Response|ResponseFactory
     */
    public function index(Post $post): Response|ResponseFactory
    {
        return inertia('Post/Index', [
            'post' => Inertia::once(function () use ($post) {
                $post->load([
                    'user' => fn ($query) => $query->with('profilePicture')->select(['id', 'username'])
                ]);
                unset($post->updated_at);

                return new PostResource($post);
            }),
            'comments' => Inertia::defer(function () use ($post) {
                return Comment::with('user:id,username')
                    ->select('id', 'comment', 'user_id', 'created_at')
                    ->where('post_id', '=', $post->id)
                    ->orderBy('created_at', 'ASC')
                    ->paginate(10)
                    ->toResourceCollection();
            })
        ]);
    }

    /**
     * @param PostFilterRequest $request
     *
     * @return Response|ResponseFactory
     *
     * @throws Throwable
     */
    public function show(PostFilterRequest $request)
    {
        return inertia('Post/LoadTitles', ['posts' => $this->getFilteredPosts($request)]);
    }

    /**
     * @param PostFilterRequest $request
     *
     * @return \Illuminate\Http\Resources\Json\ResourceCollection
     *
     * @throws \Throwable
     */
    public static function getFilteredPosts(PostFilterRequest $request): \Illuminate\Http\Resources\Json\ResourceCollection
    {
        $query = Post::with('user:id,username', 'category:id,name')
            ->select('id', 'title', 'user_id', 'category_id', 'created_at')
            ->orderByDesc('created_at');

        foreach ($request->allowedFilters() as $param => $scope) {
            if ($param === 'limit') continue;
            $query->when($request->$param, fn ($q) => $scope($q, $request->$param));
        }

        return $query->paginate($request->allowedFilters()['limit'] ?? 20)
            ->appends($request->query())
            ->toResourceCollection();
    }

    public function createPostPage(?Post $post) : Response|ResponseFactory
    {
        return inertia('MakePost', [
            'post'       => $post ? new PostResource($post?->loadMissing('category:id')) : null,
            'categories' => Category::select(['id', 'name'])->get()->toResourceCollection()]
        );
    }

    public function upsert(?Post $post, PostStoreRequest $request) : RedirectResponse
    {
        $validated  = $request->validated();
        $post       = Post::updateOrCreate(['id' => $post?->id], array_merge($validated, ['user_id' => auth()->id()]));

        return redirect()->route('post.show', $post->id);
    }

    /**
     * Delete a post and all its comments
     *
     * @param Post $post
     *
     * @return Response|ResponseFactory
     */
    public function destroy(Post $post) : Response|ResponseFactory
    {
        abort_unless($post->user_id === auth()->id(), 403);
        DB::transaction(function () use ($post) {
            Comment::where('post_id', $post->id)->delete();

            $post->delete();
        });

        return $this->viewTopics($post->id);
    }
}
