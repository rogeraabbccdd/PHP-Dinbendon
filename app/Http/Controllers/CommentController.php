<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;

class CommentController extends Controller
{
    public function submit(Request $request)
    {
        $validated = $request->validate([
            'store_id' => 'required|exists:stores,id',
            'rating' => 'required|integer|between:1,5',
            'content' => 'required|string|max:200',
        ]);

        Comment::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'store_id' => $validated['store_id'],
            ],
            [
                'rating' => $validated['rating'],
                'content' => $validated['content'],
            ]
        );
    }
}
