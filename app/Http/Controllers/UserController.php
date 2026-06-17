<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\{ PostFilterRequest, UserEditRequest, UserLoginRequest, UserStoreRequest};
use App\Models\User;
use Illuminate\Http\{ RedirectResponse, Request };
use Illuminate\Support\Facades\{ Auth, DB, Hash};
use Inertia\{ Inertia, Response, ResponseFactory};
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
     * Load user data once and defer the posts
     *
     * @param User $user
     * @param PostFilterRequest $request
     *
     * @return Response|ResponseFactory
     *
     * @throws Throwable
     */
    public function profile(User $user, PostFilterRequest $request) : Response|ResponseFactory
    {
        return inertia('user/Profile', [
                'user' => Inertia::once(function () use ($user) {
                    $user->load(['profilePicture', 'bannerPicture']);
                    unset($user->name, $user->email, $user->created_at, $user->updated_at);

                    return $user->toResource();
                }),
                'posts' => Inertia::defer(function () use ($request, $user) {
                    return PostController::getFilteredPosts($request->merge(['user_id' => $user->id]));
                })
        ]);
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

    /**
     * @param User $user
     *
     * @return RedirectResponse
     */
    public function destroy(User $user) : RedirectResponse
    {
        abort_unless($user->id === auth()->id(), 403);
        $user->delete();

        return back();
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function logOutMethod(Request $request) : RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect(route('post.show'));
    }
}
