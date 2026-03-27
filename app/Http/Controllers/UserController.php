<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ImagePostRequest;
use App\Http\Requests\UserEditRequest;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserStoreRequest;
use App\Models\TemporaryFile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Inertia\Response;
use Inertia\ResponseFactory;

class UserController extends Controller
{
    public function login(UserLoginRequest $request) : RedirectResponse
    {
        $credentials = $request->validated();
        if (auth()->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('home');
        } else {
            return redirect()->back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ]);
        }
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
                )
                    ->toMediaCollection('avatar');
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

    public function profile(string $username) : Response|ResponseFactory
    {
        return inertia('profile', [
            'user' => User::where('username', $username)
            ->with('media')
            ->select('id', 'username')
            ->first()
        ]);
    }

    /**public function index(string $username) : JsonResponse
    {
        return response()->json(
            User::where('username', $username)
                ->with('media')
                ->select('id')
                ->first()
        );
    }*/

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
            $folder   = uniqid() . '-' . now()->timestamp;
            $file->storeAs('/public/' . $key . '/tmp/' . $folder, $filename);

            TemporaryFile::create([
                'folder'   => $folder,
                'filename' => $filename,
            ]);
        }
        return $folder;
    }

    /**
     * can be used on profile page, is quicker to execute but would require a new vue component
    public function getUserPosts(int $id)
    {
    return response()->json(
    User::whereId($id)
    ->with(['post' => function ($query){
    $query->with('category:id,name')
    ->select('id', 'title', 'user_id', 'category_id', 'created_at')
    ->orderBy('created_at', 'desc');
    }])->select('id', 'username')
    ->paginate(10)
    );
    }*/
}
