<!DOCTYPE html>
<html>
<head>
    <title>Request Updated</title>
</head>
<body>

    <h2>Request Updated</h2>
    <hr>

    <p>A request has been edited by the user.</p>

    <p><strong>Event Name:</strong> {{ $requestData['event_name'] }}</p>
    <p><strong>Requested By:</strong> {{ $requestData['requested_by'] }}</p>

    <hr>

    <h3>Changes Made:</h3>

    @if(!empty($changes))
        <ul>
            @foreach($changes as $field => $change)
                <li>
                    <strong>{{ ucfirst(str_replace('_', ' ', $field)) }}:</strong><br>
                    From: {{ is_array($change['old']) ? json_encode($change['old']) : $change['old'] }} <br>
                    To: {{ is_array($change['new']) ? json_encode($change['new']) : $change['new'] }}
                </li>
            @endforeach
        </ul>
    @else
        <p>No detailed changes available.</p>
    @endif

    <br>
    <p>Please review the updated request in the system.</p>

</body>
</html>
