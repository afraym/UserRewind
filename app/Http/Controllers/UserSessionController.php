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
            'origin' => 'nullable|string',
            'paths' => 'nullable|array',
        ]);

        // Get the user's IP address
        $realIp = $request->header('CF-Connecting-IP')
        ?? $request->header('X-Forwarded-For')
        ?? $request->ip();

        // Get the user's device information (User-Agent)
        $deviceInfo = $request->header('User-Agent');
        $deviceHash = md5($deviceInfo); // Hash the device info to make it file-system safe

        // Construct the session ID using IP address and device hash
        $sessionId = "{$realIp}_{$deviceHash}";

        $origin = $request->input('origin', $request->header('Origin'));
        $paths = $request->input('paths', []);

        // Filter and validate the events array
        $newEvents = array_filter($request->input('events'), function ($event) {
            return is_array($event) || is_object($event);
        });

        // Create or update the user session
        $session = UserSession::firstOrCreate(
            ['session_id' => $sessionId],
            [
                'ip' => $realIp,
                'device_hash' => $deviceHash,
                'origin' => $origin,
                'paths' => [],
                'events' => [],
            ]
        );

        // Merge and deduplicate events
        $mergedEvents = array_merge($session->events ?? [], $newEvents);
        $mergedEvents = array_map("unserialize", array_unique(array_map("serialize", $mergedEvents)));

        // Merge and deduplicate paths
        $mergedPaths = array_unique(array_merge($session->paths ?? [], $paths));

        $session->events = $mergedEvents;
        $session->origin = $origin;
        $session->paths = $mergedPaths;
        $session->save();

        return response()->json(['message' => 'Session recorded successfully']);
    }

    public function replay($sessionId)
    {
        $session = UserSession::where('session_id', $sessionId)->first();

        if (!$session || empty($session->events)) {
            return response()->json(['message' => 'Session not found'], 404);
        }

        return response()->json(['events' => $session->events]);
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
