@extends('layouts.admin')

@section('content')
    @php($user = \Illuminate\Support\Facades\Auth::guard('admin')->user())

    <main id="kt_content" class="content flex-1 p-5 sm:p-7 lg:p-9">
        <div class="mx-auto max-w-6xl">
            <div class="mb-7">
                <p class="mb-1 text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Account / My profile</p>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Profile Details</h1>
                <p class="mt-1 text-sm text-slate-500">Update your administrator information and profile image.</p>
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

            <form id="kt_account_profile_details_form" method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data">
                @csrf
                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="grid lg:grid-cols-[18rem_minmax(0,1fr)]">
                        <aside class="border-b border-slate-200 bg-slate-50/70 p-6 lg:border-b-0 lg:border-r">
                            <h2 class="text-base font-semibold text-slate-900">Profile image</h2>
                            <p class="mt-1 text-sm leading-6 text-slate-500">This image appears in the admin header and account menu.</p>
                            <div class="mt-6 flex justify-center lg:justify-start">
                                <div class="relative">
                                    <div id="profile-avatar-preview" class="h-36 w-36 rounded-full border-4 border-white bg-cover bg-center bg-no-repeat shadow ring-1 ring-slate-200" style="background-image: url('{{ $user->profile_image }}')"></div>
                                    <label for="profile-avatar-input" class="absolute bottom-1 right-1 flex h-10 w-10 cursor-pointer items-center justify-center rounded-full border-4 border-white bg-cyan-600 text-white shadow transition hover:bg-cyan-700 focus-within:ring-2 focus-within:ring-cyan-500 focus-within:ring-offset-2" title="Change profile image">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <path d="M14.5 4h-5L7.8 6H5a3 3 0 0 0-3 3v8a3 3 0 0 0 3 3h14a3 3 0 0 0 3-3V9a3 3 0 0 0-3-3h-2.8l-1.7-2Z"/>
                                            <circle cx="12" cy="13" r="3.5"/>
                                        </svg>
                                        <span class="sr-only">Choose profile image</span>
                                        <input id="profile-avatar-input" class="sr-only" type="file" name="avatar" accept="image/png,image/jpeg,image/webp">
                                    </label>
                                    <button id="profile-avatar-remove" type="button" class="absolute right-0 top-0 flex h-8 w-8 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-500 shadow-sm transition hover:border-red-200 hover:bg-red-50 hover:text-red-600" title="Remove profile image">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true">
                                            <path d="m6 6 12 12M18 6 6 18"/>
                                        </svg>
                                        <span class="sr-only">Remove profile image</span>
                                    </button>
                                </div>
                            </div>
                            <p class="mt-5 text-center text-xs leading-5 text-slate-400 lg:text-left">PNG, JPG or WEBP. Maximum 5 MB.</p>
                            <input id="profile-avatar-remove-value" type="hidden" name="avatar_remove" value="0">
                        </aside>

                        <section class="p-6 sm:p-8">
                            <div class="mb-6">
                                <h2 class="text-lg font-semibold text-slate-900">Personal information</h2>
                                <p class="mt-1 text-sm text-slate-500">These details are used for your administrator account.</p>
                            </div>
                            <div class="grid gap-6 sm:grid-cols-2">
                                <div class="fv-row sm:col-span-2">
                                    <label for="profile-name" class="mb-2 block text-sm font-semibold text-slate-700">Name <span class="text-red-500">*</span></label>
                                    <input id="profile-name" type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control" placeholder="Enter your name" autocomplete="name">
                                </div>
                                <div class="fv-row">
                                    <label for="profile-username" class="mb-2 block text-sm font-semibold text-slate-700">Username <span class="text-red-500">*</span></label>
                                    <input id="profile-username" type="text" name="username" value="{{ old('username', $user->username) }}" class="form-control" placeholder="Enter username" autocomplete="username">
                                </div>
                                <div class="fv-row">
                                    <label for="profile-email" class="mb-2 block text-sm font-semibold text-slate-700">Email address <span class="text-red-500">*</span></label>
                                    <input id="profile-email" type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control" placeholder="name@example.com" autocomplete="email">
                                </div>
                            </div>
                        </section>
                    </div>
                    <div class="flex flex-col-reverse gap-3 border-t border-slate-200 bg-slate-50/70 px-6 py-4 sm:flex-row sm:justify-end sm:px-8">
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-light justify-center">Cancel</a>
                        <button type="submit" class="btn btn-primary justify-center" id="kt_account_profile_details_submit">
                            <span class="indicator-label">Save changes</span><span class="indicator-progress">Saving… <span class="spinner-border spinner-border-sm ms-2"></span></span>
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
            const form = document.getElementById('kt_account_profile_details_form');
            const submitButton = document.getElementById('kt_account_profile_details_submit');
            const fileInput = document.getElementById('profile-avatar-input');
            const preview = document.getElementById('profile-avatar-preview');
            const removeButton = document.getElementById('profile-avatar-remove');
            const removeValue = document.getElementById('profile-avatar-remove-value');
            const defaultAvatar = @json(asset('assets/media/avatars/blank.png'));

            fileInput?.addEventListener('change', function () {
                const file = this.files && this.files[0];
                if (!file) return;
                removeValue.value = '0';
                const reader = new FileReader();
                reader.addEventListener('load', event => preview.style.backgroundImage = `url('${event.target.result}')`);
                reader.readAsDataURL(file);
            });
            removeButton?.addEventListener('click', function () {
                fileInput.value = '';
                removeValue.value = '1';
                preview.style.backgroundImage = `url('${defaultAvatar}')`;
            });

            if (!form || !submitButton || typeof FormValidation === 'undefined') return;
            const validator = FormValidation.formValidation(form, {
                fields: {
                    name: {validators: {notEmpty: {message: 'Name is required'}}},
                    username: {validators: {notEmpty: {message: 'Username is required'}, regexp: {regexp: /^\S+$/, message: 'Spaces are not allowed'}}},
                    email: {validators: {
                        notEmpty: {message: 'Email is required'},
                        emailAddress: {message: 'Enter a valid email address'},
                        remote: {url: @json(route('admin.check-email-exists')), method: 'POST', headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content}, data: {id: @json($user->id)}, message: 'This email address is already in use'}
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
