<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\PostStoreRequest;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Inertia\ResponseFactory;

class PostController extends Controller
{
    /**
     * @return Response|ResponseFactory
     */
    public function index() : Response|ResponseFactory
    {
        return inertia('home', ['url' => '/view-all-topics/']);
    }

    public function viewAllTopics() : JsonResponse
    {
        return response()->json(
            Post::with('user:id,username', 'category:id,name')
                ->select('id', 'title', 'user_id', 'category_id', 'created_at')
                ->orderByDesc('created_at')
                ->paginate(20)
        );
    }

    public function viewTopics(int $id) : Response|ResponseFactory
    {
        return inertia('LoadTitles', ['url' => '/view-topics-ajax/' . $id]);
    }

    public function viewPost(int $id) : Response|ResponseFactory
    {
        return inertia('ViewPost', [
            'post' => Post::whereId($id)
            ->with([
                'user' => function ($query) {
                    $query->with('media')
                     ->select('id', 'username');
                },
            ])
            ->select('id', 'title', 'content', 'user_id', 'created_at', 'category_id')
            ->first(),
        ]);
    }

    public function viewTopicsAjax(int $id) : JsonResponse
    {
        return response()->json(
            Post::with('user:id,username', 'category:id,name')
                ->select('id', 'title', 'user_id', 'category_id', 'created_at')
                ->where('category_id', $id)
                ->orderByDesc('created_at')
                ->paginate(20)
        );
    }

    public function postPage() : Response|ResponseFactory
    {
        return inertia('MakePost', ['categories' => Category::select(['id', 'name'])->get()->toArray()]);
    }

    public function updatePostPage(Post $post) : Response|ResponseFactory
    {
        abort_if($post->user_id !== auth()->id(), 403);
        return inertia('updatePost', [
            'post'       => $post,
            'categories' => Category::all(),
        ]);
    }

    public function store(PostStoreRequest $request) : RedirectResponse
    {
        $validated  = $request->validated();
        $validated += ['user_id' => auth()->id()];
        $post       = Post::create($validated);

        return redirect()->route('post.show', $post->id);
    }

    public function edit(Post $post, PostStoreRequest $request) : Response|ResponseFactory
    {
        abort_unless($post->user_id === auth()->id(), 403);
        $validated = $request->validated();
        $post->update($validated);
        $post->save();

        return $this->viewPost($post->id);
    }

    public function getProfilePosts(int $id) : JsonResponse
    {
        return response()->json(
            Post::with('user:id,username', 'category:id,name')
                ->select('id', 'title', 'user_id', 'category_id', 'created_at')
                ->where('user_id', $id)
                ->orderByDesc('created_at')
                ->paginate(10)
        );
    }

    public function destroy(Post $post) : Response|ResponseFactory
    {
        abort_unless($post->user_id === auth()->id(), 403);
        $id = $post->category_id;
        $post->delete();
        return $this->viewTopics($id);
    }
}
