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
use Illuminate\Support\Facades\Storage;
use Throwable;

class UserController extends Controller
{
    public function login(UserLoginRequest $request) : RedirectResponse
    {
        $credentials = $request->validated();
        if (auth()->attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->route('post.show');
        }

        return redirect()->back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function register(UserStoreRequest $request) : RedirectResponse
    {
        $validated = $request->validated();

        //dd($validated);

        DB::transaction(function () use ($validated) {
            $user = User::create([
                'name'     => $validated['name'],
                'username' => $validated['username'],
                'email'    => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            if (!empty($validated['avatar'])) $user->addMediaFromRequest('avatar')->toMediaCollection('avatar');
        });

        auth()->attempt($request->only('email', 'password'));

        return redirect()->route('post.show');
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
            ->with(['profilePicture', 'bannerPicture'])
            ->select('id', 'username')
            ->firstOrFail();

        return inertia('user/Profile', [
                'user' => $user->toResource(),
                'posts' => PostController::getFilteredPosts($request->merge(['user_id' => $user->id]))
            ]
        );
    }

    /**
     * @return Response|ResponseFactory
     */
    public function updateProfilePage() : Response|ResponseFactory
    {
        $user = User::with(['profilePicture', 'bannerPicture'])
            ->select('id', 'name', 'username', 'email')
            ->findOrFail(auth()->id());

        abort_unless($user->id === auth()->id(), 403);

        return inertia('user/Update', ['user' => $user->toResource()]);
    }

    /**
     * @param User $user
     * @param UserEditRequest $request
     *
     * @return RedirectResponse
     *
     * @throws Throwable
     */
    public function updateProfile(User $user, UserEditRequest $request) : RedirectResponse
    {
        abort_unless($user->id === auth()->id(), 403);
        $validated = array_filter($request->validated());
        DB::transaction(function () use ($validated, $user) {
            foreach (['avatar', 'banner'] as $collection) {
                if (!isset($validated[$collection])) continue;
                $user->clearMediaCollection($collection);
                $user->addMediaFromRequest($collection)->toMediaCollection($collection);
            }

            // Update remaining profile fields
            if (!empty($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            }
            if (!empty($validated)) {
                $user->update($validated);
            }
        });

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

        return redirect(route('post.show'));
    }
}
