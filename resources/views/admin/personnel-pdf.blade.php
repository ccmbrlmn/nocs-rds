<!DOCTYPE html>
<html>
<head>
    <title>Personnel Report</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #333;
            padding: 6px;
            text-align: center;
        }

        th {
            background-color: #f3f3f3;
        }

        .status-Active {
            background-color: #CCE5FF;
            color: #004085;
        }

        .status-Deleted {
            background-color: #F8D7DA;
            color: #721c24;
        }
    </style>
</head>

<body>

    <h2>Personnel Report</h2>

    <p>
        <strong>Status:</strong> {{ $statusLabel ?? 'All' }} &nbsp;|&nbsp;
        <strong>Date:</strong> {{ $dateLabel ?? 'All Time' }} &nbsp;|&nbsp;
        <strong>Sort:</strong> {{ $sortLabel ?? 'Newest First' }}
    </p>

    <p>
        <em>Exported on: {{ $exportedAt ?? now()->format('M d, Y - h:i A') }}</em>
    </p>

    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Office</th>
                <th>Department</th>
                <th>Created At</th>
                <th>Status</th>
            </tr>
        </thead>

        <tbody>
            @foreach($personnel as $p)
                <tr>
                    <td>{{ $p->name }}</td>
                    <td>{{ $p->email }}</td>
                    <td>{{ $p->office ?? '-' }}</td>
                    <td>{{ $p->department ?? '-' }}</td>
                    <td>{{ \Carbon\Carbon::parse($p->created_at)->format('M d, Y') }}</td>
                    <td class="status-{{ $p->deleted_at ? 'Deleted' : 'Active' }}">
                        {{ $p->deleted_at ? 'Deleted' : 'Active' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
