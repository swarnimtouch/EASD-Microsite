<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} | {{ (defined('site_name') && site_name !== 'name' && !empty(site_name)) ? site_name : 'PULCE Connect 2026' }}</title>
    <link rel="shortcut icon" href="{{ defined('Favicon') ? Favicon : asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="h-screen overflow-hidden bg-slate-50 font-sans text-slate-900 antialiased">
    <div class="flex h-full flex-col">
        @include('partials.header')
        @yield('content')
        @include('partials.footer')
    </div>

    <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('web/js/local-time.js') }}"></script>
    <script>
        window.KTUtil = window.KTUtil || { onDOMContentLoaded: function (callback) { document.addEventListener('DOMContentLoaded', callback); } };
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.alert').forEach(function (alert) {
                const message = alert.innerText.replace('×', '').trim();
                const icon = alert.classList.contains('alert-danger') ? 'error' : 'success';
                alert.remove();
                if (message && window.Swal) {
                    Swal.fire({toast:true,position:'top-end',icon:icon,title:message,showConfirmButton:false,timer:3500,timerProgressBar:true});
                }
            });

            function syncMasterCheckbox(master) {
                const selector = master.dataset.ktCheckTarget;
                if (!selector) return;
                const items = Array.from(document.querySelectorAll(selector)).filter(function (item) {
                    return !item.disabled;
                });
                const checkedCount = items.filter(function (item) { return item.checked; }).length;
                master.checked = items.length > 0 && checkedCount === items.length;
                master.indeterminate = checkedCount > 0 && checkedCount < items.length;
            }

            document.addEventListener('change', function (event) {
                const checkbox = event.target.closest('input[type="checkbox"]');
                if (!checkbox) return;

                if (checkbox.matches('[data-kt-check="true"][data-kt-check-target]')) {
                    const shouldCheck = checkbox.checked;
                    document.querySelectorAll(checkbox.dataset.ktCheckTarget).forEach(function (item) {
                        if (!item.disabled) {
                            item.checked = shouldCheck;
                            item.dispatchEvent(new Event('change', {bubbles: true}));
                        }
                    });
                    checkbox.checked = shouldCheck;
                    checkbox.indeterminate = false;
                    return;
                }

                document.querySelectorAll('[data-kt-check="true"][data-kt-check-target]').forEach(function (master) {
                    try {
                        if (checkbox.matches(master.dataset.ktCheckTarget)) syncMasterCheckbox(master);
                    } catch (error) {
                        console.warn('Invalid checkbox target:', master.dataset.ktCheckTarget);
                    }
                });
            });

            document.querySelectorAll('[data-kt-check="true"][data-kt-check-target]').forEach(syncMasterCheckbox);
        });
    </script>
    @stack('scripts')
</body>
</html>
