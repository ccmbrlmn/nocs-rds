<!DOCTYPE html>
<html>
<head>
    <title>Users Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #333; padding: 6px; text-align: center; }
        th { background-color: #f3f3f3; }

        .status-Active { 
            background-color: #D4EDDA; 
            color: #155724; 
        }

        .status-Deleted { 
            background-color: #F8D7DA; 
            color: #721c24; 
        }
    </style>
</head>
<body>

    <h2>Users Report</h2>

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
            @foreach($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ \Carbon\Carbon::parse($user->created_at)->format('M d, Y') }}</td>

                    <td class="status-{{ $user->deleted_at ? 'Deleted' : 'Active' }}">
                        {{ $user->deleted_at ? 'Deleted' : 'Active' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
