<!DOCTYPE html>
<html>
<head>
    <title>Admins Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #333; padding: 6px; text-align: center; }
        th { background-color: #f3f3f3; }
        .status-Active { background-color: #CCE5FF; color: #004085; }
        .status-Deleted { background-color: #F8D7DA; color: #721c24; }
    </style>
</head>
<body>
    <h2>Admins Report</h2>
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
                <th>Name</th>
                <th>Email</th>
                <th>Created At</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($admins as $admin)
                <tr>
                    <td>{{ $admin->name }}</td>
                    <td>{{ $admin->email }}</td>
                    <td>{{ \Carbon\Carbon::parse($admin->created_at)->format('M d, Y') }}</td>
                    <td class="status-{{ $admin->deleted_at ? 'Deleted' : 'Active' }}">
                        {{ $admin->deleted_at ? 'Deleted' : 'Active' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
