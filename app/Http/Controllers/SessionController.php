<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SessionController extends Controller
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
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Session $session)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Session $session)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Session $session)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Session $session)
    {
        //
    }

    public function record(Request $request)
    {
        // Validate the incoming data
        $request->validate([
            'events' => 'required|array',
        ]);

        // Save the events to a file
        $sessionId = session()->getId();
        $filePath = "sessions/{$sessionId}.json";

        $existingData = Storage::exists($filePath) ? json_decode(Storage::get($filePath), true) : [];
        $mergedData = array_merge($existingData, $request->input('events'));

        Storage::put($filePath, json_encode($mergedData));

        return response()->json(['message' => 'Session recorded successfully']);
    }

    public function replay($sessionId)
    {
        $filePath = "sessions/{$sessionId}.json";

        if (!Storage::exists($filePath)) {
            return response()->json(['message' => 'Session not found'], 404);
        }

        $events = json_decode(Storage::get($filePath), true);

        return response()->json(['events' => $events]);
    }
}
