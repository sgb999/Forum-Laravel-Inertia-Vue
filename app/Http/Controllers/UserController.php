<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ImagePostRequest;
use App\Http\Requests\PostFilterRequest;
use App\Http\Requests\UserEditRequest;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserStoreRequest;
use App\Models\TemporaryFile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Inertia\Response;
use Inertia\ResponseFactory;
use Throwable;

class UserController extends Controller
{
    public function login(UserLoginRequest $request) : RedirectResponse
    {
        $credentials = $request->validated();
        if (auth()->attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->route('home');
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

            if ($validated['avatar'] !== null) {
                $tempFile = TemporaryFile::where('folder', $validated['avatar'])->first();
                $user->addMedia(
                    storage_path('app/public/avatar/tmp/' . $validated['avatar'] . '/' . $tempFile->filename)
                )->toMediaCollection('avatar');
                rmdir(storage_path('app/public/avatar/tmp/' . $validated['avatar']));
                $tempFile->delete();
            }
        });
        if (
            auth()->attempt([
                'email'    => $validated['email'],
                'password' => $validated['password'],
            ])
        ) {
            return redirect()->to(route('home'));
        }

        return back();
    }

    /**
     * @param string $username
     * @param PostFilterRequest $request
     *
     * @return Response|ResponseFactory
     *
     * @throws Throwable
     */
    public function profile(string $username, PostFilterRequest $request) : Response|ResponseFactory
    {
        $user = User::where('username', $username)
            ->with('media')
            ->select('id', 'username')
            ->firstOrFail();
        return inertia('profile', [
                'user' => $user,
                'posts' => PostController::getFilteredPosts($request->merge(['user_id' => $user->id]))
            ]
        );
    }

    /**
     * @return Response|ResponseFactory
     */
    public function updateProfilePage() : Response|ResponseFactory
    {
        $user = User::with('media')
            ->select('id', 'name', 'username', 'email')
            ->findOrFail(auth()->id());

        abort_unless($user->id === auth()->id(), 403);

        return inertia('update-profile', ['user' => $user]);
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

    public function destroy(User $user) : RedirectResponse
    {
        abort_unless($user->id === auth()->id(), 403);
        $user->delete();

        return back();
    }

    public function logOutMethod(Request $request) : RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect(route('home'));
    }

    public function storeImage(ImagePostRequest $request) : string
    {
        $folder = '';
        foreach (array_filter($request->validated()) as $key => $value) {
            $file     = $request->file($key);
            $filename = $file->getClientOriginalName();
            $folder   = uniqid() . '-' . now()->timestamp;
            $file->storeAs('/public/' . $key . '/tmp/' . $folder, $filename);

            TemporaryFile::create([
                'folder'   => $folder,
                'filename' => $filename,
            ]);
        }

        return $folder;
    }
}
