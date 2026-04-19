<?php

namespace App\Http\Controllers;

use App\Models\Interaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InteractionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $interaction = Interaction::create([
            'candidate_id' => $request->candidate_id,
            'employer_id' => auth('sanctum')->id(),
            'guest_name' => $request->guest_name,
            'guest_contact' => $request->guest_contact,
            'message' => $request->message,
        ]);

        // TODO: Gửi Push Notification đến A qua Firebase


        return response()->json([
            'message' => 'Yêu cầu kết nối đã được gửi thành công!',
            'data' => $interaction
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Interaction $interaction)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Interaction $interaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Interaction $interaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Interaction $interaction)
    {
        //
    }
}
