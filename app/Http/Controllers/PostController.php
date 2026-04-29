<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\{PostFilterRequest, PostStoreRequest };
use App\Http\Resources\PostResource;
use App\Models\{Post, Category, Comment};
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Inertia\Inertia;
use Inertia\{Response, ResponseFactory};
use Throwable;

class PostController extends Controller
{

    /**
     * @param Post $post
     *
     * @return Response|ResponseFactory
     *
     * @throws Throwable
     */
    public function index(Post $post): Response|ResponseFactory
    {
        return inertia('ViewPost', [
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
                    ->orderBy('created_at', 'DESC')
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
    public function show(PostFilterRequest $request): Response|ResponseFactory
    {
        return inertia('LoadTitles', ['posts' => $this->getFilteredPosts($request)]);
    }

    /**
     * @param PostFilterRequest $request
     *
     * @return ResourceCollection
     *
     * @throws Throwable
     */
    public static function getFilteredPosts(PostFilterRequest $request): ResourceCollection
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

    /**
     * @param Post|null $post
     *
     * @return Response|ResponseFactory
     */
    public function postPage(?Post $post) : Response|ResponseFactory
    {
        return inertia('MakePost', [
            'post'       => $post ? new PostResource($post?->loadMissing('category:id')) : null,
            'categories' => Category::select(['id', 'name'])->get()->toArray()]
        );
    }

    /**
     * @param Post|null $post
     * @param PostStoreRequest $request
     *
     * @return RedirectResponse
     */
    public function upsert(?Post $post, PostStoreRequest $request) : RedirectResponse
    {
        $validated  = $request->validated();
        $post       = Post::updateOrCreate(['id' => $post?->id], array_merge($validated, ['user_id' => auth()->id()]));

        return redirect()->route('post.show', $post->id);
    }

    /**
     * @param Post $post
     *
     * @return Response|ResponseFactory
     */
    public function destroy(Post $post) : Response|ResponseFactory
    {
        abort_unless($post->user_id === auth()->id(), 403);
        $id = $post->category_id;
        $post->delete();

        return $this->viewTopics($id);
    }
}
