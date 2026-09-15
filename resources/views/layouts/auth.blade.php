<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} | {{ site_name }}</title>
    <link rel="shortcut icon" href="{{ Favicon }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/css/app.css'])
    @stack('styles')
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-900 antialiased">
    @yield('content')
    <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
    @stack('scripts')
</body>
</html>
