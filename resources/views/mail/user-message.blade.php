<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $subject ?? 'Message' }}</title>
    <style>
        body {
            font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, "Apple Color Emoji", "Segoe UI Emoji";
            line-height: 1.5;
            color: #0f172a;
            background: #f8fafc;
        }

        .container {
            max-width: 640px;
            margin: 0 auto;
            padding: 24px;
        }

        .card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 24px;
        }

        .footer {
            margin-top: 24px;
            color: #64748b;
            font-size: 12px;
        }

        a {
            color: #2563eb;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <p style="margin-top: 0;">{{ ($name ?? '') !== '' ? 'Hello ' . $name . ',' : 'Hello,' }}</p>

        <div>{!! str($html ?? '')->sanitizeHtml() !!}</div>

        <p>Regards,<br>{{ $appName ?? config('app.name') }}</p>
    </div>
    <div class="footer">
        <p>This message was sent by {{ $appName ?? config('app.name') }}.</p>
    </div>
</div>
</body>
</html>
