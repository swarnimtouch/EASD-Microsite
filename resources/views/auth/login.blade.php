@extends('layouts.auth')
@section('content')
    <main class="medihub-login">
        <div class="login-shell">
            <div class="login-brand">
                <div class="login-brand-mark"><i class="bi bi-activity"></i></div>
                <div class="login-brand-name">MediHub<span>Admin</span></div>
                <p>Healthcare Microsite Management System</p>
            </div>
            <div class="login-card">
                <form class="form w-100" novalidate="novalidate" id="kt_sign_in_form">
                    @csrf
                    <div class="mb-8">
                        <h1>Welcome Back</h1>
                        <p>Please enter your credentials to access the portal</p>
                        <div class="alert alert-danger d-none" id="kt_sign_in_errors">
                            <div id="kt_sign_in_error_message"></div>
                        </div>
                    </div>
                    <div class="fv-row mb-5">
                        <label class="form-label">Email Address</label>
                        <div class="login-input-wrap">
                            <i class="bi bi-envelope"></i>
                            <input class="form-control" type="email" name="email" autocomplete="email" placeholder="admin@medihub.com"/>
                        </div>
                    </div>
                    <div class="fv-row mb-5">
                        <div class="d-flex flex-stack mb-2">
                            <label class="form-label mb-0">Password</label>
                        </div>
                        <div class="login-input-wrap">
                            <i class="bi bi-lock"></i>
                            <input class="form-control" id="login-password" type="password" name="password" autocomplete="current-password" placeholder="••••••••"/>
                            <button class="password-toggle" type="button" aria-label="Show password" onclick="const input=document.getElementById('login-password');input.type=input.type==='password'?'text':'password';this.querySelector('i').classList.toggle('bi-eye-slash')"><i class="bi bi-eye"></i></button>
                        </div>
                    </div>
                    <label class="login-remember mb-6"><input type="checkbox" name="remember" value="1"> Remember me</label>
                    <button type="submit" id="kt_sign_in_submit" class="mh-btn-primary w-full py-3.5">
                            <span class="indicator-label">Sign In to Dashboard <i class="bi bi-arrow-right ms-2"></i></span>
                            <span class="indicator-progress">Please wait...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                    </button>
                </form>
                <div class="login-security">Authorized personnel only. Unauthorized access is strictly prohibited and monitored.</div>
            </div>
            <div class="login-footer">Secure healthcare administration portal</div>
        </div>
    </main>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/custom/authentication/sign-in/general.js') }}?v={{ filemtime(public_path('assets/js/custom/authentication/sign-in/general.js')) }}"></script>
@endpush
