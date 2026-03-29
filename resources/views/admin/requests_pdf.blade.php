<!DOCTYPE html>
<html>
<head>
    <title>User Requests Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #333; padding: 6px; text-align: center; }
        th { background-color: #f3f3f3; }
        .status-Open { background-color: #FFF3CD; color: #856404; }
        .status-Active { background-color: #CCE5FF; color: #004085; }
        .status-Closed { background-color: #D4EDDA; color: #155724; }
        .status-Declined { background-color: #F8D7DA; color: #721c24; }
    </style>
</head>
<body>
    <h2>User Requests Report</h2>
    <p>
        <strong>Status:</strong> {{ $statusLabel }} &nbsp;|&nbsp;
        <strong>Date:</strong> {{ $dateLabel }} &nbsp;|&nbsp;
        <strong>Sort:</strong> {{ $sortLabel }}
    </p>
    
    
    <p>
        <em>Exported on: {{ $exportedAt }}</em>
    </p>
    
    <table>
        <thead>
            <tr>
                <th>Requester</th>
                <th>Event</th>
                <th>Date</th>
                <th>Purpose</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($requests as $request)
                <tr>
                    <td>{{ $request->user->name ?? '—' }}</td>
                    <td>{{ $request->event_name ?? '—' }}</td>
                    <td>{{ \Carbon\Carbon::parse($request->created_at)->format('M d, Y') }}</td>
                    <td>{{ $request->purpose }}</td>
                    <td class="status-{{ $request->computed_status }}">{{ $request->computed_status }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
