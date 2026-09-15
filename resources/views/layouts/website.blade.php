<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'PULCE Connect 2026' }} — EASD Diabetes Series</title>
    <link rel="icon" href="{{ defined('Favicon') ? Favicon : asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
          rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif']
                    },
                    colors: {
                        escRed: '#BE1E2D',
                        escBlue: '#1D438A',
                        escLight: '#F8F9FA'
                    }
                }
            }
        }
    </script>
    <style>
        :root { --esc-red:#d20714; --esc-blue:#083b8f; --esc-pale:#eef5ff; }
        html { scroll-behavior:smooth; }
        body { background:#fff; }
        .hero-gradient { background:linear-gradient(135deg,#fff 0%,#f8fbff 100%); }
        .dot-pattern { background-image:radial-gradient(#b7d8fb 1.35px,transparent 1.35px); background-size:14px 14px; }
        .pulce-wordmark { display:flex; align-items:center; color:var(--esc-red); font-weight:900; font-style:italic; letter-spacing:-.08em; line-height:.8; }
        .pulce-wordmark .pulse-disc { display:inline-flex; width:.88em; height:.88em; margin:0 -.04em; align-items:center; justify-content:center; border-radius:999px; background:linear-gradient(135deg,#1c65ba,#75a0d5); color:#fff; font-size:.82em; letter-spacing:0; }
        .pulce-wordmark .connect-ring { margin-left:.22em; padding:.18em .32em; border-right:3px solid #2456a0; border-radius:50%; font-style:normal; letter-spacing:-.04em; line-height:.72; }
        .asia-wave { position:absolute; inset:auto 0 0; height:120px; overflow:hidden; pointer-events:none; opacity:.55; }
        .asia-wave:before,.asia-wave:after { content:""; position:absolute; left:-5%; width:110%; height:115px; border-radius:50%; border-top:1px solid #9bc9fb; transform:rotate(-2deg); }
        .asia-wave:before { top:42px; box-shadow:0 -7px 0 -6px #9bc9fb,0 -14px 0 -13px #9bc9fb,0 -21px 0 -20px #9bc9fb,0 -28px 0 -27px #9bc9fb,0 -35px 0 -34px #9bc9fb; }
        .asia-wave:after { top:67px; left:20%; transform:rotate(2deg); }
        .reference-panel { border:1px solid #e5e7eb; border-radius:16px; background:rgba(255,255,255,.96); box-shadow:0 8px 30px rgba(15,23,42,.06); }
        @media (max-width:767px) { .asia-wave{height:70px}.pulce-wordmark .connect-ring{display:none} }
    </style>
    @stack('styles')
</head>
<body class="{{ $bodyClass ?? 'bg-white font-sans antialiased text-slate-800' }}">
@include('partials.website.header')
@yield('content')
@include('partials.website.footer')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
<script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
@vite('resources/js/app.js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const switchers = document.querySelectorAll('[data-timezone-switcher]');
    if (!switchers.length) return;

    const savedTimezone = sessionStorage.getItem('pulce-timezone') || 'Asia/Singapore';
    const timezoneLabels = {
        'Asia/Bangkok': 'GMT+7 ICT',
        'Asia/Singapore': 'GMT+8 SGT/PHT',
        'Asia/Kolkata': 'GMT+5:30 IST',
        'Asia/Dubai': 'GMT+4 GST',
    };
    const valueFor = (date, timezone, options) => new Intl.DateTimeFormat('en-GB', {timeZone: timezone, ...options}).format(date);
    const renderTimes = timezone => {
        document.querySelectorAll('[data-event-datetime]').forEach(element => {
            const date = new Date(element.dataset.eventDatetime);
            if (Number.isNaN(date.getTime())) return;
            element.querySelectorAll('[data-date-part]').forEach(part => {
                const type = part.dataset.datePart;
                const formats = {
                    day: {day: '2-digit'},
                    month: {month: 'long'},
                    year: {year: 'numeric'},
                    date: {day: '2-digit', month: 'long', year: 'numeric'},
                    datetime: {day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit'},
                    time: {hour: '2-digit', minute: '2-digit'},
                };
                part.textContent = valueFor(date, timezone, formats[type] || formats.datetime);
            });
        });
        document.querySelectorAll('[data-selected-timezone-label]').forEach(element => {
            element.textContent = timezoneLabels[timezone] || timezone;
        });
        switchers.forEach(select => select.value = timezone);
    };

    switchers.forEach(select => select.addEventListener('change', event => {
        sessionStorage.setItem('pulce-timezone', event.target.value);
        renderTimes(event.target.value);
    }));
    renderTimes(savedTimezone);
});
</script>
@stack('scripts')
</body>
</html>
