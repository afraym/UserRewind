<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Session Replay</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/rrweb-player@latest/dist/style.css"/>
    <script src="https://cdn.jsdelivr.net/npm/rrweb-player@latest/dist/index.js"></script>
</head>
<body>
    <div id="replayer-container"></div>

    <script>
        async function replaySession(sessionId) {
            try {
                const response = await fetch(`/api/session/replay/${sessionId}`, {
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) {
                    throw new Error('Failed to fetch session data');
                }

                const { events } = await response.json();

                if (!events || events.length === 0) {
                    console.error('No events found in the session');
                    return;
                }

                // Filter out invalid events
                const filteredEvents = events.filter(event => 
                    event && 
                    typeof event === 'object' && 
                    'type' in event && 
                    'data' in event
                );

                if (filteredEvents.length === 0) {
                    console.error('No valid events found in the session');
                    return;
                }

                // Create the replayer
                const replayer = new rrwebPlayer({
                    target: document.getElementById('replayer-container'),
                    props: {
                        events: filteredEvents,
                        showController: true,
                        width: window.innerWidth * 0.9,
                        height: window.innerHeight * 0.8,
                        autoPlay: true,
                        skipInactive: true,
                        triggerFocus: true,
                        UNSAFE_replayCanvas: true
                    }
                });

                // Log success
                console.log(`Replay started with ${filteredEvents.length} events`);
            } catch (error) {
                console.error('Error replaying session:', error);
            }
        }

        // Get session id and start replay
        const sessionId = "{{ $session_id }}";
        if (sessionId) {
            replaySession(sessionId);
        } else {
            console.error('No session ID provided');
        }
    </script>
</body>
</html>