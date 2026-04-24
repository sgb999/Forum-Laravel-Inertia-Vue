<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\Chat;
use App\Models\Comment;
use App\Models\Message;
use App\Models\Post;
use App\Http\Requests\{ImagePostRequest, PostFilterRequest, UserEditRequest, UserLoginRequest, UserStoreRequest};
use App\Models\TemporaryFile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Inertia\{Inertia, Response, ResponseFactory};
use LibDNS\Records\Resource;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Throwable;

class UserController extends Controller
{

    /**
     * @param User $user
     * @param PostFilterRequest $request
     * @return Response|ResponseFactory
     * @throws Throwable
     */
    public function index(User $user, PostFilterRequest $request) : Response|ResponseFactory
    {
        return inertia('User/Index', [
            'user' => Inertia::once(function () use ($user) {
                unset($user->email, $user->created_at, $user->updated_at);
                $user->load('getAllMedia');
                $user->setRelation('profilePicture', $user->getAllMedia->firstWhere('collection_name', 'avatar'));
                $user->setRelation('bannerPicture', $user->getAllMedia->firstWhere('collection_name', 'banner'));

                return new UserResource($user);
            }),
            'posts' => PostController::getFilteredPosts($request->merge(['user_id' => $user->id]))
        ]);
    }

    public function login(UserLoginRequest $request) : RedirectResponse
    {
        $credentials = $request->validated();
        if (auth()->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('index');
        }

        return redirect()->back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function register(UserStoreRequest $request) : RedirectResponse
    {
        $validated = $request->validated();
        DB::transaction(function () use ($validated) {
            $user = User::create([
                'name'     => $validated['name'],
                'username' => $validated['username'],
                'email'    => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            if (!empty($validated['avatar'])) {
                $tempFile = TemporaryFile::where('folder', $validated['avatar'])->first();
                $user->clearMediaCollection('avatar');
                $user->addMediaFromDisk(
                    sprintf('avatar/tmp/%s/%s', $validated['avatar'], $tempFile->filename), 'public')
                    ->toMediaCollection('avatar');
                $tempFile->delete();
            }
        });
        if (
            auth()->attempt([
                'email'    => $validated['email'],
                'password' => $validated['password'],
            ])
        ) {
            return redirect()->to(route('index'));
        }

        return back();
    }

    /**
     * @return Response|ResponseFactory
     */
    public function updateProfilePage() : Response|ResponseFactory
    {
        $user = User::where('id', auth()->id())
            ->with('media')
            ->select('id', 'name', 'username', 'email')
            ->first();
        abort_unless($user->id === auth()->id(), 403);

        return inertia('UpdateProfile', ['user' => $user]);
    }

    public function updateProfile(User $user, UserEditRequest $request) : RedirectResponse
    {
        abort_unless($user->id === auth()->id(), 403);
        $validated = $request->validated();
        array_filter($validated);
        if (array_key_exists('banner', $validated) || array_key_exists('avatar', $validated)) {
            foreach ($validated as $key => $value) {
                $tempFile = TemporaryFile::where('folder', $value)->first();
                $user->clearMediaCollection($key);
                $user->addMedia(storage_path('app/public/' . $key . '/tmp/' . $value . '/' . $tempFile->filename))
                    ->toMediaCollection($key);
                rmdir(storage_path('app/public/' . $key . '/tmp/' . $value));
                $tempFile->delete();
            }
            return back();
        }
        $user->update($validated);
        $user->save();
        return back();
    }

    /**
     * @throws Throwable
     */
    public function destroy(User $user) : RedirectResponse
    {
        abort_unless($user->id === auth()->id(), 403);
        DB::transaction(function () use ($user) {
            Comment::where('user_id', $user->id)
                ->orWhere('post_id', $user->id)
                ->delete();
            Post::where('user_id', $user->id)->delete();
            Message::where('chat_id', 'in', function () use ($user) {
                return Chat::where('user_id_1', $user->id)->orWhere('user_id_2', $user->id)->toArray();
            })->delete();
            Chat::where('user_id_1', $user->id)->orWhere('user_id_2', $user->id)->delete();

            $user->delete();
        });

        return back();
    }

    public function logOutMethod(Request $request) : RedirectResponse
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return back();
    }

    public function storeImage(ImagePostRequest $request) : string
    {
        $folder = '';
        foreach (array_filter($request->validated()) as $key => $value) {
            $file     = $request->file($key);
            $filename = $file->getClientOriginalName();
            $folder   = sprintf('%s-%s', uniqid(), now()->timestamp);
            $file->storeAs('/public/' . $key . '/tmp/' . $folder, $filename);

            TemporaryFile::create([
                'folder'   => $folder,
                'filename' => $filename,
            ]);
        }
        return $folder;
    }
}
