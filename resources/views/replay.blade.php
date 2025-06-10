<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Replay Session</title>
    <!-- Include rrweb-player library -->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/rrweb-player@latest/dist/style.css"
    />
    <script src="https://cdn.jsdelivr.net/npm/rrweb-player@latest/dist/index.js"></script>
</head>
<body>
    <div id="replayer-container"></div>

    <script>
        async function replaySession(sessionId) {
            try {
                const response = await fetch(`/api/session/replay/${sessionId}`);
                if (!response.ok) {
                    throw new Error('Failed to fetch session data');
                }
                const { events } = await response.json();

                if (!events || events.length === 0) {
                    console.error('No events found in the session');
                    return;
                }

                // Filter out invalid events
                const filteredEvents = events.filter(event => {
                    // Ensure the event is an object and contains required properties
                    return event && typeof event === 'object' && Object.keys(event).length > 0;
                });

                if (filteredEvents.length === 0) {
                    console.error('No valid events found in the session');
                    return;
                }

                // Initialize the rrweb-player
                new rrwebPlayer({
                    target: document.getElementById('replayer-container'),
                    props: {
                        events: filteredEvents,
                        showController: true, // Show playback controls
                    },
                });
            } catch (error) {
                console.error('Error replaying session:', error);
            }
        }

        // Get session id from the controller
        const sessionId = "{{ $session_id }}";
        if (sessionId) {
            replaySession(sessionId);
        } else {
            console.error('No session ID provided');
        }
    </script>
</body>
</html>