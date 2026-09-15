@extends('layouts.admin')

@section('content')
    <main id="kt_content" class="content flex-1 p-5 sm:p-7 lg:p-9">
        <div class="mx-auto max-w-4xl">
            <div class="mb-7">
                <p class="mb-1 text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Account / Security</p>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Change Password</h1>
                <p class="mt-1 text-sm text-slate-500">Choose a strong password to keep your administrator account secure.</p>
            </div>

            @if(session('success'))
                <div class="alert alert-success mb-6" role="alert">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger mb-6" role="alert">
                    <ul class="m-0 list-disc space-y-1 pl-5">
                        @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.password.update') }}" id="kt_password_update_form">
                @csrf
                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-200 px-6 py-5 sm:px-8">
                        <div class="flex items-start gap-4">
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-cyan-50 text-cyan-700">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <rect width="18" height="11" x="3" y="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-slate-900">Password security</h2>
                                <p class="mt-1 text-sm text-slate-500">Your new password must contain at least 6 characters.</p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6 p-6 sm:p-8">
                        @foreach ([
                            ['current_password', 'Current password', 'Enter current password', 'current-password'],
                            ['password', 'New password', 'Enter new password', 'new-password'],
                            ['password_confirmation', 'Confirm new password', 'Repeat new password', 'new-password']
                        ] as [$field, $label, $placeholder, $autocomplete])
                            <div class="fv-row">
                                <label for="{{ $field }}" class="mb-2 block text-sm font-semibold text-slate-700">
                                    {{ $label }} <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="password" name="{{ $field }}" id="{{ $field }}" class="form-control pr-12"
                                           placeholder="{{ $placeholder }}" autocomplete="{{ $autocomplete }}">
                                    <button type="button" data-password-toggle="{{ $field }}"
                                            class="absolute inset-y-0 right-0 flex w-12 items-center justify-center text-slate-400 transition hover:text-cyan-700"
                                            aria-label="Show {{ strtolower($label) }}" aria-pressed="false">
                                        <svg class="password-eye-open h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <path d="M2.1 12a10.7 10.7 0 0 1 19.8 0 10.7 10.7 0 0 1-19.8 0Z"/><circle cx="12" cy="12" r="3"/>
                                        </svg>
                                        <svg class="password-eye-closed hidden h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <path d="m2 2 20 20M6.7 6.7A10.8 10.8 0 0 0 2.1 12a10.7 10.7 0 0 0 16.1 3.7M10.7 4.1A10.7 10.7 0 0 1 21.9 12a10.8 10.8 0 0 1-1.3 2.2M14.1 14.1A3 3 0 0 1 9.9 9.9"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="flex flex-col-reverse gap-3 border-t border-slate-200 bg-slate-50/70 px-6 py-4 sm:flex-row sm:justify-end sm:px-8">
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-light justify-center">Cancel</a>
                        <button type="submit" class="btn btn-primary justify-center" id="kt_password_update_submit">
                            <span class="indicator-label">Update password</span>
                            <span class="indicator-progress">Updating… <span class="spinner-border spinner-border-sm ms-2"></span></span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </main>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('[data-password-toggle]').forEach(function (button) {
                button.addEventListener('click', function () {
                    const input = document.getElementById(button.dataset.passwordToggle);
                    const showing = input.type === 'text';
                    input.type = showing ? 'password' : 'text';
                    button.setAttribute('aria-pressed', String(!showing));
                    button.setAttribute('aria-label', (showing ? 'Show ' : 'Hide ') + input.name.replaceAll('_', ' '));
                    button.querySelector('.password-eye-open').classList.toggle('hidden', !showing);
                    button.querySelector('.password-eye-closed').classList.toggle('hidden', showing);
                });
            });

            const form = document.getElementById('kt_password_update_form');
            const submitButton = document.getElementById('kt_password_update_submit');
            if (!form || !submitButton || typeof FormValidation === 'undefined') return;

            const validator = FormValidation.formValidation(form, {
                fields: {
                    current_password: {validators: {notEmpty: {message: 'Current password is required'}}},
                    password: {validators: {notEmpty: {message: 'New password is required'}, stringLength: {min: 6, message: 'Password must be at least 6 characters'}}},
                    password_confirmation: {validators: {
                        notEmpty: {message: 'Password confirmation is required'},
                        identical: {compare: function () { return form.querySelector('[name="password"]').value; }, message: 'Passwords do not match'}
                    }}
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap: new FormValidation.plugins.Bootstrap5({rowSelector: '.fv-row', eleInvalidClass: '', eleValidClass: ''})
                }
            });

            submitButton.addEventListener('click', function (event) {
                event.preventDefault();
                validator.validate().then(function (status) {
                    if (status !== 'Valid') return;
                    submitButton.setAttribute('data-kt-indicator', 'on');
                    submitButton.disabled = true;
                    form.submit();
                });
            });
        });
    </script>
@endpush
