<!DOCTYPE html>
<html>
<head>
    <title>Assets Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }

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

        .status-Available {
            background-color: #D4EDDA;
            color: #155724;
        }

        .status-InUse {
            background-color: #CCE5FF;
            color: #004085;
        }

        .status-Maintenance {
            background-color: #F8D7DA;
            color: #721c24;
        }
    </style>
</head>

<body>

    <h2>Assets Report</h2>

    <p>
        <em>Exported on: {{ $exportedAt }}</em>
    </p>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Asset Name</th>
                <th>Tag</th>
                <th>Serial</th>
                <th>Model</th>
                <th>Category</th>
                <th>Status</th>
                <th>Date Added</th>
            </tr>
        </thead>

        <tbody>
            @foreach($assets as $asset)
                <tr>
                    <td>{{ $asset->id }}</td>
                    <td>{{ $asset->asset_name ?? '—' }}</td>
                    <td>{{ $asset->asset_tag ?? '—' }}</td>
                    <td>{{ $asset->asset_serial ?? '—' }}</td>
                    <td>{{ $asset->asset_model ?? '—' }}</td>
                    <td>{{ $asset->asset_category ?? '—' }}</td>

                    <td class="status-{{ str_replace(' ', '', $asset->asset_status) }}">
                        {{ $asset->asset_status }}
                    </td>

                    <td>
                        {{ \Carbon\Carbon::parse($asset->created_at)->format('M d, Y') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
