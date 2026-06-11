<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    <style>
        :root {
            color-scheme: light dark;
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        body {
            min-height: 100vh;
            margin: 0;
            display: grid;
            place-items: center;
            background: #f8fafc;
            color: #111827;
        }

        main {
            width: min(42rem, calc(100vw - 2rem));
            text-align: center;
        }

        h1 {
            margin: 0 0 1rem;
            font-size: clamp(2rem, 6vw, 4rem);
            line-height: 1;
        }

        p {
            margin: 0;
            color: #4b5563;
            font-size: 1.125rem;
            line-height: 1.7;
        }

        @media (prefers-color-scheme: dark) {
            body {
                background: #030712;
                color: #f9fafb;
            }

            p {
                color: #d1d5db;
            }
        }
    </style>
</head>
<body>
    <main>
        <h1>{{ $title }}</h1>
        <p>{{ $message }}</p>
    </main>
</body>
</html>
