<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $posts = Post::all();

        return response()->json([
            'status' => 'success',
            'message' => 'List all posts',
            'data' => $posts
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $model = Post::query()->firstOrCreate($request->only(['title','description']));

        return response()->json([
            'status' => 'success',
            'message' => 'Post created',
            'data' => $model
        ]);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $post = Post::query()->find($id);
        if(!$post){
            return response()->json([
                'status' => 'error',
                'message' => 'Post not found',
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Post show',
            'data' => $post
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $post = Post::query()->find($id);
        if(!$post){
            return response()->json([
                'status' => 'error',
                'message' => 'Post not found',
            ]);
        }

        $post->fill($request->only(['title','description']));
        $post->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Post updated',
            'data' => $post
        ]);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $post = Post::query()->find($id);
        if(!$post){
            return response()->json([
                'status' => 'error',
                'message' => 'Post not found',
            ]);
        }

        $post->forceDelete();

        return response()->json([
            'status' => 'success',
            'message' => 'Post deleted',
        ]);
    }
}
