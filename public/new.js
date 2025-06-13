// Load rrweb script dynamically
const rrwebScript = document.createElement('script');
rrwebScript.src = 'https://cdn.jsdelivr.net/npm/rrweb@latest/dist/rrweb.min.js';
rrwebScript.async = true;
document.head.appendChild(rrwebScript);
// Example JavaScript to collect paths and send with events
let paths = [];
function trackPath() {
    if (!paths.includes(window.location.pathname)) {
        paths.push(window.location.pathname);
    }
}
window.addEventListener('popstate', trackPath);
window.addEventListener('pushstate', trackPath);
trackPath(); // Initial path

// When sending events:
fetch('/api/session/record', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
    },
    body: JSON.stringify({
        events,
        origin: window.location.origin,
        paths,
    }),
});