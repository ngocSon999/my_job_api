<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Services\Post\PostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

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

    public function myPosts(Request $request): JsonResponse
    {
        $data = $this->postService->getMyPosts($request->all());

        return response()->json(
            PostResource::collection($data)->response()->getData(true)
        );
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
    public function show($id): PostResource
    {
        $post = $this->postService->find($id);

        $cacheKey = 'post_viewed_' . $id . '_' . request()->ip();

        if (!Cache::has($cacheKey)) {
            $post->increment('views');
            Cache::forever($cacheKey, true);
        }

        return new PostResource($post);
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
