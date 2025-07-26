<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $payload = $request->validate([
            "page"=>"sometimes|integer|min:0",
            "size"=>"sometimes|integer|min:1"
        ]);

        
        if(!isset($payload["page"])){
            $payload["page"] = 0;
        } 
        if (!isset($payload["size"])){
            $payload["size"] = 10;
        }

        $posts = Post::with("user")->with("post_attachment")->skip($payload["page"] * $payload["size"])->take($payload["size"])->get();

        return response()->json([
            "page"=>$payload["page"],
            "size"=>$payload["size"],
            "posts"=>$posts
        ]);
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $payload = $request->validate([
            "caption"=>"required|string",
            "attachment"=>"required|file|image|mimes:jpg, jpeg, webp, png, gif"
        ]);

        $file = $request->file("attachment")->store("posts","public");

        $request->user()->posts()->create([
            "caption"=>$payload["caption"]
        ])->post_attachment()->create([
            "storage_path"=>$file
        ]);


        return response()->json([
            "message"=>"create post success"
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id, Request $request)
    {
        $post = Post::find($id);
        if (!$post){
            return response()->json([
                "message"=>"post not found"
            ],404);
        }

        if ($post->user_id != $request->user()->id){
            return response()->json([
                "message"=>"Forbidden Access"
            ],403);
        }

        $post->delete();

        return response()->json([
            "message"=>"data deleted"
        ], 204);
    }
}
