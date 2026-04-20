<?php

namespace App\Http\Controllers;

use App\Services\Post\PostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostController extends Controller
{
    protected PostService $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        return response()->json($this->postService->getActivePosts($request->all()));
    }

    /**
     * Store a newly created resource in storage.
     * @throws \Exception
     */
    public function store(Request $request): JsonResponse
    {
        $post = $this->postService->createPost($request->all());

        return response()->json($post, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id): JsonResponse
    {
        return response()->json($this->postService->find($id));
    }

    /**
     * Update the specified resource in storage.
     * @throws \Exception
     */
    public function update(Request $request, $id): JsonResponse
    {
        $post = $this->postService->updatePost($id, $request->all());

        return response()->json($post);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        $this->postService->delete($id);
        return response()->json(['message' => 'Xóa bài viết thành công']);
    }
}
