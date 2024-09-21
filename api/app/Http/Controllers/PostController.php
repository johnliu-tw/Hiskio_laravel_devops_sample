<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        return Post::all();
    }

    public function show($id)
    {
        return Post::findOrFail($id);
    }

    public function store(Request $request)
    {
        $post = Post::create($request->all());
        return response()->json($post, 201);
    }

    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);
        $post->update($request->all());
        return response()->json($post, 200);
    }

    public function destroy($id)
    {
        Post::findOrFail($id)->delete();
        return response()->json(null, 204);
    }

    public function latestReport()
    {
        $post = Post::orderBy('id', 'desc')->first();

        if (!$post) {
            return response()->json(['message' => 'No published post found'], 404);
        }

        $content = "ID: {$post->id}\n";
        $content .= "Title: {$post->title}\n";
        $content .= "Content: {$post->content}\n";

        $fileName = "latest_post_report_". time() .".txt";

        Storage::disk('s3')->put($fileName, $content);

        $fileUrl = Storage::disk('s3')->url($fileName);

        return response()->json(['file_url' => $fileUrl]);
    }
}
