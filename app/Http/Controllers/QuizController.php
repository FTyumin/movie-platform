<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use App\Models\Genre;
use App\Models\User;
class QuizController extends Controller
{
    public function show() {
        $genres = Genre::all();
        return view('quiz.show', compact('genres'));
    }

    public function store(Request $request) {
        $id = Auth::id();

        $user = User::find($id);
        
        //avoid duplicates
        $user->favoriteGenres()->detach();

        $user->quiz_completed = true;
        $user->save();
        $input = $request->all();

        $user->favoriteGenres()->attach($input["genres"]);

        Cache::forget("user:{$user->id}:recs");
        
        return redirect()->route('home')->with('success', 'Quiz completed!');
    }
}
