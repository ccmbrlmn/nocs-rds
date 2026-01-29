<!DOCTYPE html>
<html>
<head>
    <title>New Request</title>
</head>
<body>
    <h2>New Request by {{ $requestData['requested_by'] }}</h2>
    <hr>
    <p>A new request has been submitted. Below are the details:</p>
    
    <p><strong>Representative Name:</strong> {{ $requestData['representative_name'] }}</p>
    <p><strong>Event Name:</strong> {{ $requestData['event_name'] }}</p>
    <p><strong>Purpose:</strong> {{ $requestData['purpose'] }}</p>

    @if(!empty($requestData['other_purpose']))
        <p><strong>Other Purpose:</strong> {{ $requestData['other_purpose'] }}</p>
    @endif

    <p><strong>Start Date:</strong> {{ \Carbon\Carbon::parse($requestData['start_date'])->format('M d, Y') }}</p>
    <p><strong>End Date:</strong> {{ \Carbon\Carbon::parse($requestData['end_date'])->format('M d, Y') }}</p>
    <p><strong>Location:</strong> {{ $requestData['location'] }}</p>

    <p><strong>Requested Items:</strong></p>
    <ul>
        @foreach($requestData['items'] as $item)
            <li>{{ $item['name'] }} — Quantity: {{ $item['quantity'] }}</li>
        @endforeach
    </ul>
    
    <br>
    <p>Kindly review the request in the system for further details and necessary action.</p>
    
</body>
</html>

