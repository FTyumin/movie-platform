<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function create(Request $request) {

        if (Auth::check()) {
            $userId = Auth::user()->id;
        } else {
            return back()->with('warning', 'You must be logged in to write a comment.');
        }

        $request->validate([ 'comment' => 'required|string|min:5|max:300']);

        Comment::create([
            'user_id' => $userId,
            'review_id' => $request->review_id,
            'description' => $request->comment,
        ]);

        return back();
    }

    public function update(Request $request,Comment $comment) {
        $request->validate(['comment' => 'required|string|max:1000']);
        $comment->update(['description' => $request->comment]);
        return redirect()->route('reviews.show', $comment->review_id);
    }

    public function destroy(Comment $comment) {
        $comment->delete();
        return back();
    }
}
