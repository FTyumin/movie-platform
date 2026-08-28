<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'min:5', 'max:30', Rule::unique('users', 'name')->ignore($user->id)],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        // Uploading file
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('profiles', 'public');
            $request->user()->image = $path;
            $data['image'] = $path;
        }

        $user->fill($data);

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.show', $request->user()->id)->with('status', 'profile-updated');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function show(User $user)
    {
        if (! $user) {
            abort(404);
        }

        if (auth()->check() && $user->id == auth()->id()) {
            return redirect('dashboard');
        }

        // query user data for profile page
        $reviews = $user->reviews->take(5);
        $review_count = Review::where('user_id', $user->id)->count();
        $average_review = round(Review::where('user_id', $user->id)->avg('rating'), 2) ?? 0;

        $watchList = $user->wantToWatch->take(6);
        $seen = $user->seenMovies->take(6);
        $favorites = $user->favorites->take(4);

        $lists = $user->lists()->withCount('movies')->latest()->limit(5)->get();

        return view('profile.show', compact('reviews', 'user', 'watchList', 'average_review', 'review_count', 'seen', 'favorites', 'lists'));
    }

    public function dashboard(Request $request)
    {
        $user = $request->user();
        if (! $user) {
            abort(404);
        }

        $watchList = $user->wantToWatch->take(4);
        $seen = $user->seenMovies->take(4);
        $favorites = $user->favorites->take(4);

        $reviews = Review::where('user_id', $user->id)->limit(3)->get();
        $review_count = Review::where('user_id', $user->id)->count();
        $average_review = round(Review::where('user_id', $user->id)->avg('rating'), 2) ?? 0;

        $lists = $user->lists()->withCount('movies')->latest()->limit(5)->get();

        return view('dashboard', compact('reviews', 'user', 'watchList', 'average_review', 'seen', 'favorites', 'lists'));
    }
}
