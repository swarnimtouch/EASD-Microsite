<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} — PULCE Connect 2026</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
          rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script>tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {sans: ['Plus Jakarta Sans', 'sans-serif']},
                    colors: {escRed: '#BE1E2D', escBlue: '#1D438A'}
                }
            }
        }
    </script>
    @stack('styles')
</head>
<body class="bg-[#f5f7f9] font-sans text-slate-800 antialiased">
<div class="min-h-screen lg:flex">
    @include('partials.website.portal_sidebar')
    <div class="flex min-h-screen min-w-0 flex-1 flex-col lg:ml-72">
        @include('partials.website.portal_header')
        <div class="flex-1">
            @yield('content')
        </div>
        @include('partials.website.portal_footer')
    </div>
</div>
@vite('resources/js/app.js')
@stack('scripts')
</body>
</html>
