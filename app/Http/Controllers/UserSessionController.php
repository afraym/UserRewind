<?php

namespace App\Http\Controllers;

use App\Models\UserSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserSessionController extends Controller
{

        public function record(Request $request)
    {
        // Validate the incoming data
        $request->validate([
            'events' => 'required|array',
        ]);

        // Filter out null or empty values from the events array
        $newEvents = array_filter($request->input('events'), function ($event) {
            return !is_null($event) && $event !== '';
        });

        // Get the user's IP address
        $realIp = $request->header('CF-Connecting-IP')
        ?? $request->header('X-Forwarded-For')
        ?? $request->ip();

        // Get the user's device information (User-Agent)
        $deviceInfo = $request->header('User-Agent');
        $deviceHash = md5($deviceInfo); // Hash the device info to make it file-system safe

        // Construct the file name using IP address and device hash
        $filePath = "sessions/{$realIp}_{$deviceHash}.json";

        // Retrieve existing data if the file exists
        $existingData = Storage::exists($filePath) ? json_decode(Storage::get($filePath), true) : [];

        // Ensure both are arrays and merge without duplicating events
        if (!is_array($existingData)) {
            $existingData = [];
        }

        // Optionally, prevent duplicates by using array_unique with serialization
        $mergedData = array_merge($existingData, $newEvents);
        $mergedData = array_map("unserialize", array_unique(array_map("serialize", $mergedData)));

        // Save the merged data back to the file
        Storage::put($filePath, json_encode($mergedData));

        return response()->json(['message' => 'Session recorded successfully']);
    }

    public function replay($sessionId)
    {
        $filePath = "sessions/{$sessionId}.json";

        // Check if the session file exists
        if (!Storage::exists($filePath)) {
            return response()->json(['message' => 'Session not found'], 404);
        }

        // Retrieve the session events
        $events = json_decode(Storage::get($filePath), true);

        // Filter out invalid events
        $events = array_filter($events, function ($event) {
            // Ensure the event is an array or object and contains required keys
            return is_array($event) || is_object($event);
        });

        // Check if the session file is empty or invalid
        if (empty($events)) {
            return response()->json(['message' => 'No valid events found in the session'], 404);
        }

        // Return the events as JSON
        return response()->json(['events' => $events], 200, ['Content-Type' => 'application/json']);
    }
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
        return view('record');
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
    public function show(UserSession $userSession)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UserSession $userSession)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, UserSession $userSession)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserSession $userSession)
    {
        //
    }
    public function viewReplay(Request $request)
    {
        // Generate or retrieve the session ID
        $sessionId = $request->sessionId;
        if (empty($sessionId)) {
            return response()->json(['message' => 'Session ID is required'], 400);
        }

        // Check if the session file exists
        $filePath = "sessions/{$sessionId}.json";
        if (!Storage::exists($filePath)) {
            return response()->json(['message' => 'Session file not found'], 404);
        }

        // Pass the session ID to the replay view
        return view('replay', ['session_id' => $sessionId]);
    }
}
