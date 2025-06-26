    // Initialize rrweb recording
    let events = [];
    rrweb.record({
        emit(event) {
            events.push(event);
        },
    });

    // Periodically send recorded events to the server
    setInterval(() => {
        if (events.length > 0) {
            fetch('https://tomato.test/api/session/record', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({ events }),
            }).then(() => {
                events = []; // Clear events after sending
            });
        }
    }, 5000); // Send every 5 seconds