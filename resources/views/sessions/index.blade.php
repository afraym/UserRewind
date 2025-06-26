{{-- filepath: resources/views/sessions/index.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>WebsiteCCTV - Sessions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .session-card {
            transition: all 0.3s ease;
            margin-bottom: 1rem;
        }
        .session-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .stats {
            font-size: 0.9rem;
            color: #666;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-4">
        <h1 class="mb-4">Recorded Sessions</h1>
        
        <div class="row">
            @forelse($sessions as $session)
                <div class="col-md-6 col-lg-4">
                    <div class="card session-card">
                        <div class="card-body">
                            <h5 class="card-title">Session #{{ $session->id }}</h5>
                            <div class="stats mb-3">
                                <div><i class="bi bi-globe"></i> {{ $session->origin ?: 'Unknown Origin' }}</div>
                                <div><i class="bi bi-clock"></i> {{ $session->created_at->diffForHumans() }}</div>
                                <div><i class="bi bi-camera"></i> {{ is_array($session->events) ? count($session->events) : 0 }} events</div>
                                <div><i class="bi bi-geo"></i> {{ $session->ip }}</div>
                            </div>
                            
                            @if(is_array($session->paths) && count($session->paths) > 0)
                                <div class="paths small text-muted mb-3">
                                    <strong>Paths:</strong> {{ implode(', ', array_slice($session->paths, 0, 3)) }}
                                    @if(count($session->paths) > 3)
                                        and {{ count($session->paths) - 3 }} more...
                                    @endif
                                </div>
                            @endif
                            
                            <a href="{{ url('/session/replay/' . $session->session_id) }}" 
                               class="btn btn-primary btn-sm">
                                <i class="bi bi-play-fill"></i> Replay Session
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info">
                        No sessions recorded yet.
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>