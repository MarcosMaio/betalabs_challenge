<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\CommentHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    public function index()
    {
        $comments = Comment::select('id', 'user_id', 'content', 'created_at', 'updated_at')
        ->with(['user:id,name,email,created_at,updated_at'])
        ->get();

        return response()->json($comments, 200);
    }

    public function show(Request $request)
    {
        $comment = Comment::where('user_id', $request->user()->id)->get();

        return response()->json($comment, 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required|min:3|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()
            ], 422);
        }

        $comment = $request->user()->comments()->create([
            'content' => $request->content,
        ]);

        return response()->json($comment, 201);
    }

    public function update(Request $request, $id)
    {
        $comment = Comment::findOrFail($id);

        if ($request->user()->id != $comment->user_id && !$request->user()->is_admin) {
            return response()->json(['message' => 'You do not have permission to edit this comment.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'content' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            CommentHistory::create([
                'comment_id' => $comment->id,
                'content' => $comment->content,
                'edited_at' => now(),
            ]);

            $comment->content = $request->content;
            $comment->updated_at = now();
            $comment->save();

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to update comment.'], 500);
        }

        return response()->json($comment, 201);
    }

    public function destroy(Request $request, $id)
    {
        $comment = Comment::findOrFail($id);

        if ($request->user()->id !== $comment->user_id && !$request->user()->is_admin) {
            return response()->json(['message' => 'You do not have permission to delete this comment.'], 403);
        }

        $comment->delete();

        return response()->json(['message' => 'Comment deleted successfully'], 200);
    }

    public function history($id)
    {
        $comment = Comment::findOrFail($id);

        $histories = $comment->histories;

        return response()->json($histories, 200);
    }
}
