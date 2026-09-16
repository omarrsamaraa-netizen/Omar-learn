<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Luxury Store</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500;600&family=Jost:wght@300;400;500&display=swap" rel="stylesheet">

    @vite('resources/css/app.css')
</head>
<body>
    <header class="site-header">
        <nav class="site-nav">
            <h1 class="brand"><a href="/">Luxury Store</a></h1>

            <div class="nav-links">
                <a href="/main" class="nav-link">Collection</a>
                <a href="/main/create" class="nav-link">Add piece</a>
            </div>
        </nav>
    </header>

    <main class="page">
        @if (session('status'))
            <p class="mb-8 border-l-2 border-brass pl-4 text-ink">{{ session('status') }}</p>
        @endif

        {{ $slot }}
    </main>
</body>
</html>
