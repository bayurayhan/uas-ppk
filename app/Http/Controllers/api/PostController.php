<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of the Posts.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::all();

        // Check if the authenticated user is owner of the post
        foreach ($posts as $post) {
            $post->is_owner = auth()->user()->id === $post->author_id;
        }

        // User usefull response to return data
        return response()->json([
            'message' => 'Posts retrieved successfully',
            'data' => $posts
        ]);
    }

    /**
     * Store a newly created Post in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate request data
        $request->validate([
            'title' => 'required',
            'body' => 'required'
        ]);

        // Get authenticated user
        $user = auth()->user();

        // Create new post
        $post = Post::create([
            'title' => $request->title,
            'body' => $request->body,
            'author_id' => $user->id
        ]);

        // User usefull response to return data
        return response()->json([
            'message' => 'Post created successfully',
            'data' => $post
        ]);
    }

    /**
     * Display the specified Post.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        // User usefull response to return data
        return response()->json([
            'message' => 'Post retrieved successfully',
            'data' => $post
        ]);
    }

    /**
     * Update the specified Post in storage.
     * 
     * This method is only for authenticated user that is the author of the post
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
        // Validate request data
        $request->validate([
            'title' => 'required',
            'body' => 'required'
        ]);

        // Get authenticated user
        $user = auth()->user();

        // Check if authenticated user is the author of the post and the role is not admin
        if ($user->id !== $post->author_id && $user->role->access_level !== 0) {
            return response()->json([
                'message' => 'You are not authorized to update this post'
            ], 403);
        }

        // Update post
        $post->update([
            'title' => $request->title,
            'body' => $request->body
        ]);

        // User usefull response to return data
        return response()->json([
            'message' => 'Post updated successfully',
            'data' => $post
        ]);
    }

    /**
     * Remove the specified Post from storage.
     * 
     * This method is only for authenticated user that is the author of the post
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        // Get authenticated user
        $user = auth()->user();

        // Check if authenticated user is the author of the post and the role is not admin
        if ($user->id !== $post->author_id && $user->role->access_level !== 0) {
            return response()->json([
                'message' => 'You are not authorized to delete this post'
            ], 403);
        }

        // Delete post
        $post->delete();

        // User usefull response to return data
        return response()->json([
            'message' => 'Post deleted successfully'
        ]);
    }

    /**
     * Get all posts of the authenticated user
     *
     * @return \Illuminate\Http\Response
     */
    public function userPosts()
    {
        // Get authenticated user
        $user = auth()->user();

        // Get all posts of the authenticated user
        $posts = $user->posts;

        // User usefull response to return data
        return response()->json([
            'message' => 'Posts retrieved successfully',
            'data' => $posts
        ]);
    }
}
