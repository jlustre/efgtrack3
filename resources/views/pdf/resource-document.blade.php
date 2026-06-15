<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #172033;
            margin: 36px 42px;
        }

        h1 {
            font-size: 22px;
            color: #0B1F3A;
            margin: 0 0 8px;
        }

        .meta {
            font-size: 11px;
            color: #64748b;
            margin-bottom: 24px;
            padding-bottom: 12px;
            border-bottom: 1px solid #e2e8f0;
        }

        .content h2,
        .content h3,
        .content h4 {
            color: #0B1F3A;
            margin-top: 18px;
            margin-bottom: 8px;
        }

        .content p {
            margin: 0 0 10px;
        }

        .content ul,
        .content ol {
            margin: 0 0 12px 18px;
            padding: 0;
        }

        .content li {
            margin-bottom: 4px;
        }

        .content a {
            color: #0B1F3A;
            text-decoration: underline;
        }

        .content img.emoji-inline {
            display: inline-block;
        }
    </style>
</head>
<body>
    <h1>{{ $title }}</h1>
    @if (filled($description))
        <div class="meta">{{ $description }}</div>
    @endif
    <div class="content">
        {!! $content !!}
    </div>
</body>
</html>
