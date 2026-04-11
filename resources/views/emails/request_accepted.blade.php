<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Request Accepted</title>
</head>

<body style="margin:0; padding:0; background:#f4f6f9; font-family:Arial, sans-serif;">

<div style="max-width:650px; margin:30px auto; background:#ffffff; border-radius:10px; overflow:hidden; box-shadow:0 2px 10px rgba(0,0,0,0.08);">

    <!-- HEADER -->
    <div style="background:#16a34a; padding:20px; color:white; text-align:center;">
        <h2 style="margin:0;">🎉 Request Approved</h2>
        <p style="margin:5px 0 0; font-size:14px;">Good news! Your request has been accepted</p>
    </div>

    <!-- BODY -->
    <div style="padding:25px; color:#333;">

        <p style="font-size:15px;">
            Hello <strong>{{ $requestData['requested_by'] }}</strong>,
        </p>

        <p style="font-size:15px; margin-bottom:20px;">
            We are pleased to inform you that your request has been <strong style="color:#16a34a;">approved</strong>.
        </p>

        <!-- REQUEST SUMMARY -->
        <div style="background:#f8fafc; padding:15px; border-radius:8px; margin-bottom:20px;">

            <p style="margin:5px 0;">
                <strong>Event Name:</strong> {{ $requestData['event_name'] }}
            </p>

            <p style="margin:5px 0;">
                <strong>Representative:</strong> {{ $requestData['representative_name'] }}
            </p>

            <p style="margin:5px 0;">
                <strong>Location:</strong> {{ $requestData['location'] }}
            </p>

            <p style="margin:5px 0;">
                <strong>Schedule:</strong>
                {{ \Carbon\Carbon::parse($requestData['start_date'])->format('M d, Y') }}
                →
                {{ \Carbon\Carbon::parse($requestData['end_date'])->format('M d, Y') }}
            </p>

        </div>

        <!-- ITEMS -->
        <h3 style="margin-bottom:10px;">📦 Approved Items</h3>

        <div style="background:#f8fafc; padding:15px; border-radius:8px;">
            <ul style="margin:0; padding-left:20px;">
                @foreach($requestData['items'] as $item)
                    <li>
                        <strong>{{ $item['name'] }}</strong>
                        — Quantity: {{ $item['quantity'] }}
                    </li>
                @endforeach
            </ul>
        </div>

        <!-- SUCCESS BOX -->
        <div style="margin-top:25px; padding:15px; background:#ecfdf5; border-left:4px solid #16a34a; border-radius:6px;">
            <p style="margin:0; font-size:14px;">
                ✅ You may now proceed with your scheduled activity. Please ensure all requirements are followed.
            </p>
        </div>

    </div>

    <!-- FOOTER -->
    <div style="background:#f1f5f9; padding:15px; text-align:center; font-size:12px; color:#666;">
        This is an automated approval notification from your Request Management System
    </div>

</div>

</body>
</html>
