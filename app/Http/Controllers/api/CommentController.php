<?php

namespace App\Http\Controllers\api;

use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends \App\Http\Controllers\Controller
{
    /**
     * Get all comments for a post
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Post $post)
    {
        $comments = $post->comments;

        // Check if the authenticated user is owner of the comment
        foreach ($comments as $comment) {
            $comment->is_owner = auth()->user()->id === $comment->author_id;
            $comment->author = $comment->author;
        }

        return response()->json([
            'message' => 'Comments retrieved successfully',
            'data' => $comments
        ]);
    }

    /**
     * Store a newly created Comment in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Post $post)
    {
        // Validate request data
        $request->validate([
            'body' => 'required'
        ]);

        // Get authenticated user
        $user = auth()->user();

        // Create new comment
        $comment = Comment::create([
            'body' => $request->body,
            'author_id' => $user->id,
            'post_id' => $post->id
        ]);

        // User usefull response to return data
        return response()->json([
            'message' => 'Comment created successfully',
            'data' => $comment
        ]);
    }

    /**
     * Update the specified Comment in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post, Comment $comment)
    {
        // Validate request data
        $request->validate([
            'body' => 'required'
        ]);

        // Get authenticated user
        $user = auth()->user();

        // Check if the authenticated user is owner of the comment and the role is not admin
        if ($user->id !== $comment->author_id && $user->role->access_level !== 0) {
            return response()->json([
                'message' => 'You are not authorized to update this comment'
            ], 403);
        }

        // Update comment
        $comment->update([
            'body' => $request->body
        ]);

        // User usefull response to return data
        return response()->json([
            'message' => 'Comment updated successfully',
            'data' => $comment
        ]);
    }

    /**
     * Remove the specified Comment from storage.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post, Comment $comment)
    {
        // Get authenticated user
        $user = auth()->user();

        // Check if the authenticated user is owner of the comment and the role is not admin
        if ($user->id !== $comment->author_id && $user->role->access_level !== 0) {
            return response()->json([
                'message' => 'You are not authorized to delete this comment'
            ], 403);
        }


        // Delete comment
        $comment->delete();

        // User usefull response to return data
        return response()->json([
            'message' => 'Comment deleted successfully'
        ]);
    }
}
