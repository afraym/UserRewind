<?php

namespace App\Http\Controllers;

use App\Models\UserSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserSessionController extends Controller
{

        public function record(Request $request)
    {
        try {
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

            // Get the user's device information
            $deviceInfo = $request->header('User-Agent');
            $deviceHash = md5($deviceInfo);

            // Construct session ID
            $sessionId = "{$realIp}_{$deviceHash}";

            $origin = $request->input('origin', $request->header('Origin'));
            $paths = $request->input('paths', []);

            // Filter and clean the events array
            $newEvents = array_map(function($event) {
                // Ensure each event is an array and remove any problematic spaces in keys
                if (is_array($event) || is_object($event)) {
                    return array_combine(
                        array_map('trim', array_keys((array)$event)),
                        array_values((array)$event)
                    );
                }
                return null;
            }, $request->input('events'));

            // Remove null values
            $newEvents = array_filter($newEvents);

            // Create or update session
            $session = UserSession::updateOrCreate(
                ['session_id' => $sessionId],
                [
                    'ip' => $realIp,
                    'device_hash' => $deviceHash,
                    'origin' => $origin,
                    'paths' => array_values(array_unique($paths)),
                    'events' => array_values($newEvents) // Ensure sequential array
                ]
            );

            return response()->json([
                'message' => 'Session recorded successfully',
                'session_id' => $sessionId
            ]);

        } catch (\Exception $e) {
            \Log::error('Session recording error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error recording session',
                'error' => $e->getMessage()
            ], 500);
        }
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
        // Get all sessions, you can paginate if needed
        $sessions = UserSession::all();

        // Return as JSON or pass to a view
        // Example: return as JSON
        // return response()->json(['sessions' => $sessions]);

        // Or, to pass to a Blade view:
        return view('sessions.index', ['sessions' => $sessions]);
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
        // $filePath = "sessions/{$sessionId}.json";
        // if (!Storage::exists($filePath)) {
        //     return response()->json(['message' => 'Session file not found'], 404);
        // }

        // Pass the session ID to the replay view
        return view('replay', ['session_id' => $sessionId]);
    }
}
