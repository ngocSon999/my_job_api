<?php

namespace App\Http\Controllers;

use App\Http\Resources\InteractionResource;
use App\Models\Interaction;
use App\Services\Interaction\InteractionService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InteractionController extends Controller
{
    protected InteractionService $interactionService;

    public function __construct(InteractionService $interactionService)
    {
        $this->interactionService = $interactionService;
    }
    /**
     * Display a listing of the resource.
     */
    // InteractionController.php
    public function index(Request $request): JsonResponse
    {
        $data = $this->interactionService->getMyNotifications($request);

        return response()->json(
            InteractionResource::collection($data)->response()->getData(true)
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $data = $request->only([
                'post_id',
                'message',
            ]);

            $interaction = $this->interactionService->createInteraction($data);

            return response()->json([
                'status'  => 'success',
                'message' => 'Yêu cầu kết nối đã được gửi thành công!',
                'data'    => $interaction
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id): InteractionResource
    {
        $interaction = $this->interactionService->find($id);

        return new InteractionResource($interaction);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            // status 'accepted' hoặc 'rejected'
            $status = $request->input('status');
            $interaction = $this->interactionService->respondInteraction($id, $status);

            $msg = $status === 'accepted' ? 'Bạn đã đồng ý kết nối!' : 'Bạn đã từ chối yêu cầu.';

            return response()->json([
                'status' => 'success',
                'message' => $msg,
                'data' => new InteractionResource($interaction)
            ]);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Interaction $interaction)
    {
        //
    }
}
