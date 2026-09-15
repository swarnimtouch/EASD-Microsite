@php
    $doctorName = $doctor->name ?? 'Doctor';
    $doctorRole = $doctor->hospital ?: 'Reproductive Lead';
    $avatarName = urlencode($doctorName);
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} | {{ site_name }}</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ Favicon }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('web/css/style.css') }}">
</head>
<body>
    <nav class="navbar navbar-expand-lg bg-white border-bottom d-lg-none sticky-top py-3">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('dashboard') }}">
                <div class="logo-box d-flex justify-content-center align-items-center rounded me-2">R</div>
                <span class="brand-text fw-bold">ReproMed <span class="brand-text-purple">Series</span></span>
            </a>
            <button class="navbar-toggler border-0 shadow-none ms-auto" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-label="Toggle sidebar">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </nav>

    <header class="top-header sticky-top d-flex justify-content-between align-items-stretch ps-0 pe-4 pe-lg-5 border-bottom bg-white">
        <div class="sidebar-brand-wrapper d-none d-lg-flex align-items-center border-end py-3 pe-3 flex-shrink-0">
            <div class="logo-box flex-shrink-0 d-flex justify-content-center align-items-center rounded me-2">R</div>
            <span class="brand-text fw-bold text-wrap text-break lh-sm">ReproMed <span class="brand-text-purple">Series</span></span>
        </div>

        <div class="d-flex align-items-center flex-grow-1 ps-4 ps-lg-5 me-3">
            @hasSection('dashboard_header_center')
                @yield('dashboard_header_center')
            @else
{{--                <div class="search-box position-relative w-100">--}}
{{--                    <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>--}}
{{--                    <input type="text" id="dashboardSearch" class="form-control custom-search-input ps-5 py-2 rounded-pill bg-light border-0" placeholder="Search episodes, doctors...">--}}
{{--                </div>--}}
            @endif
        </div>

        <div class="user-profile d-flex align-items-center py-3">
            <div class="profile-info position-relative d-flex align-items-center cursor-pointer">
                <img src="{{ $doctor->getRawOriginal('profile_image') ? $doctor->profile_image : 'https://ui-avatars.com/api/?name=' . $avatarName . '&background=random' }}" alt="Profile" class="header-profile-avatar rounded-circle me-2 border shadow-sm">
                <div class="user-details me-3 d-none d-md-block text-start">
                    <div class="fw-bold fs-6 text-dark-green lh-1">Dr. {{ $doctorName }}</div>
                    <div class="text-muted small sidebar-role-text mt-1">{{ $doctorRole }}</div>
                </div>
                <i class="bi bi-chevron-down text-muted small profile-chevron"></i>

                <div class="profile-dropdown shadow-lg rounded-3 bg-white">
                    <ul class="list-unstyled mb-0 py-2">
                        <li>
                            <a href="{{ route('profile.edit') }}" class="dropdown-item d-flex align-items-center text-dark px-3 py-2">
                                <i class="bi bi-person me-3 text-primary-green"></i> My Profile
                            </a>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <a href="{{ route('logout') }}" class="dropdown-item d-flex align-items-center text-danger px-3 py-2">
                                <i class="bi bi-box-arrow-right me-3"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </header>

    <div class="d-flex flex-column flex-lg-row min-vh-100">
        <div class="offcanvas-lg offcanvas-end custom-sidebar bg-white border-end d-flex flex-column flex-shrink-0" tabindex="-1" id="sidebarMenu" aria-labelledby="sidebarMenuLabel">
            <div class="offcanvas-header border-bottom d-lg-none">
                <h5 class="offcanvas-title fw-bold text-dark-green" id="sidebarMenuLabel">Menu</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="offcanvas" data-bs-target="#sidebarMenu" aria-label="Close"></button>
            </div>

            <div class="offcanvas-body flex-column p-0 flex-grow-1 d-flex overflow-hidden">
                <ul class="nav flex-column w-100 sidebar-nav-list flex-grow-1 overflow-y-auto pt-0">
                    <li class="nav-item">
                        <a class="nav-link sidebar-link {{ request()->routeIs('course.dashboard') ? 'active' : '' }} d-flex align-items-center" href="{{ route('course.dashboard') }}">
                            <i class="bi bi-house me-3 fs-5"></i> Home
                        </a>
                    </li>
                    <li class="nav-item mb-1 mt-0">
                        <a class="nav-link sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }} d-flex align-items-center" href="{{ route('dashboard') }}">
                            <i class="bi bi-graph-up me-3 fs-5"></i> Dashboard
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link sidebar-link {{ request()->routeIs('modules*') ? 'active' : '' }} d-flex align-items-center" href="{{ route('modules') }}">
                            <i class="bi bi-book me-3 fs-5"></i> Episodes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link sidebar-link {{ request()->routeIs('profile.*') ? 'active' : '' }} d-flex align-items-center" href="{{ route('profile.edit') }}">
                            <i class="bi bi-person me-3 fs-5"></i> My Profile
                        </a>
                    </li>
{{--                    @foreach($modules ?? collect() as $module)--}}
{{--                        <li class="nav-item">--}}
{{--                            <a class="nav-link sidebar-link d-flex align-items-center ps-5" href="{{ $module->isAvailable() ? route('modules.show', $module->slug) : '#' }}">--}}
{{--                                Episode {{ $loop->iteration }}--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                    @endforeach--}}
                </ul>

                <div class="p-4 bg-white border-top mt-auto">
                    <a href="{{ route('logout') }}" class="text-danger text-decoration-none fw-medium sidebar-logout-link d-flex align-items-center">
                        <i class="bi bi-box-arrow-right me-3 fs-5"></i> Logout
                    </a>
                </div>
            </div>
        </div>

        <div class="main-content flex-grow-1">
            <div class="container-fluid p-4 p-lg-5">
                @yield('content')
            </div>

            <footer class="custom-footer py-4 mt-auto">
                <div class="container-fluid px-4 px-lg-5">
                    <div class="row align-items-center">
                        <div class="col-lg-3 col-md-12 mb-4 mb-lg-0 d-flex justify-content-center justify-content-lg-start">
                            <a class="navbar-brand d-flex align-items-center" href="{{ route('dashboard') }}">
                                <div class="logo-box d-flex justify-content-center align-items-center rounded me-2">R</div>
                                <span class="footer-brand-text">ReproMed Series</span>
                            </a>
                        </div>
                        <div class="col-lg-auto col-md-12 ms-auto text-center text-lg-end mt-3 mt-lg-0">
                            <span class="footer-text">&copy; {{ date('Y') }} ReproMed Series. Clinical Education Portal.</span>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ asset('web/js/local-time.js') }}"></script>
    @stack('dashboard_scripts')
    <script src="{{ asset('web/js/dashboard.js') }}"></script>
    <script type="application/javascript">
        $(function () {
            toastr.options = {
                closeButton: true,
                progressBar: true,
                positionClass: "toast-top-right",
                timeOut: 5000,
            };

            @if(session('success'))
                toastr.success(@json(session('success')));
            @endif

            @if(session('error'))
                toastr.error(@json(session('error')));
            @endif
        });
    </script>
</body>
</html>
