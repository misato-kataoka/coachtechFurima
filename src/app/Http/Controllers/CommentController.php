<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CommentRequest;
use App\Http\Requests\ExhibitionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Condition;
use App\Models\Category;
use App\Models\Like;
use App\Models\Comment;
use App\Models\UserItemList;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    public function store(CommentRequest $request)
    {
        if (!auth()->check()) {
            return redirect()->back()->withErrors(['user' => 'ログインが必要です。']);
        }

        Comment::create([
            'user_id' => auth()->id(),
            'item_id' => $request->item_id,
            'content' => $request->content,
        ]);

        return redirect()->back()->with('success', 'コメントが送信されました。');
    }
}