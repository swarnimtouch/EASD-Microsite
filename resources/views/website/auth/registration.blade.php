@extends('layouts.website', ['title' => 'Registration — PULCE Connect 2026', 'bodyClass' => 'bg-white font-sans antialiased text-slate-800'])

@section('content')
<main class="dot-pattern relative min-h-screen overflow-hidden px-5 py-12 md:px-8">
    <div class="relative z-10 mx-auto max-w-4xl">
        <div class="rounded-3xl border border-slate-200 bg-white shadow-xl shadow-slate-200/50 overflow-hidden">
            <div class="p-6 sm:p-10 md:p-12 space-y-8">
                <div class="text-center space-y-2">
                    <span class="inline-block rounded bg-red-50 px-3 py-1 text-[10px] font-bold uppercase tracking-widest text-escRed">One-Time Registration</span>
                    <h1 class="text-2xl sm:text-3xl font-black text-escBlue">Join the Cardio-Renal-Metabolic Educational Series</h1>
                    <p class="text-xs sm:text-sm text-slate-500 font-medium max-w-xl mx-auto">Please enter your professional credentials to register and access upcoming live scientific sessions, CME accredited modules, and materials.</p>
                </div>

                @if ($errors->any())
                    <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                        <ul class="list-disc space-y-1 pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form class="space-y-8" id="easd-registration-form" method="POST" action="{{ route('register.store') }}"
                      novalidate>
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-700 uppercase tracking-wider ml-1">Full Name
                                *</label>
                            <input type="text" name="name" value="{{ old('name') }}" placeholder="Enter your full name"
                                   class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 bg-slate-50 focus:outline-none focus:ring-4 focus:ring-escRed/5 focus:border-escRed transition-all">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-700 uppercase tracking-wider ml-1">Email Address
                                *</label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                   placeholder="Enter your email address"
                                   class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 bg-slate-50 focus:outline-none focus:ring-4 focus:ring-escRed/5 focus:border-escRed transition-all">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-700 uppercase tracking-wider ml-1">Mobile Number
                                *</label>
                            <input type="tel" name="mobile" value="{{ old('mobile') }}"
                                   placeholder="Enter your mobile number"
                                   class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 bg-slate-50 focus:outline-none focus:ring-4 focus:ring-escRed/5 focus:border-escRed transition-all">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-700 uppercase tracking-wider ml-1">Country
                                *</label>
                            <select name="country"
                                    class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 bg-slate-50 focus:outline-none focus:ring-4 focus:ring-escRed/5 focus:border-escRed transition-all appearance-none font-medium">
                                <option value="">Select your country</option>
                                @foreach ($country as $countryName)
                                    <option
                                        value="{{ $countryName }}" @selected(old('country') === $countryName)>{{ $countryName }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-700 uppercase tracking-wider ml-1">Speciality
                                *</label>
                            <select name="speciality"
                                    class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 bg-slate-50 focus:outline-none focus:ring-4 focus:ring-escRed/5 focus:border-escRed transition-all appearance-none font-medium">
                                <option value="">Select your speciality</option>
                                @foreach ($specialities as $speciality)
                                    <option
                                        value="{{ $speciality->name }}" @selected(old('speciality') === $speciality->name)>{{ $speciality->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-700 uppercase tracking-wider ml-1">Hospital /
                                Institution *</label>
                            <input type="text" name="hospital" value="{{ old('hospital') }}"
                                   placeholder="Enter hospital name"
                                   class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 bg-slate-50 focus:outline-none focus:ring-4 focus:ring-escRed/5 focus:border-escRed transition-all">
                        </div>
                        <div class="md:col-span-2 space-y-2">
                            <label class="text-xs font-bold text-slate-700 uppercase tracking-wider ml-1">Medical
                                License / Registration Number *</label>
                            <input type="text" name="medical_registration_number"
                                   value="{{ old('medical_registration_number') }}"
                                   placeholder="Enter your medical license number"
                                   class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 bg-slate-50 focus:outline-none focus:ring-4 focus:ring-escRed/5 focus:border-escRed transition-all">
                        </div>
                    </div>

                    <div class="space-y-4 pt-4">
                        <div class="checkbox-field">
                            <label class="flex items-start gap-3 cursor-pointer group">
                                <input type="checkbox" name="hcp_confirmation" value="1"
                                       @checked(old('hcp_confirmation')) class="mt-1 w-5 h-5 shrink-0 rounded-md border-slate-300 text-escRed focus:ring-escRed accent-escRed">
                                <span class="text-sm font-medium text-slate-600 leading-tight">I confirm that I am a Healthcare Professional. <span
                                        class="text-escRed">(Mandatory HCP Verification T&C from countries)</span></span>
                            </label>
                        </div>
                        <div class="checkbox-field">
                            <label class="flex items-start gap-3 cursor-pointer group">
                                <input type="checkbox" name="terms" value="1"
                                       @checked(old('terms')) class="mt-1 w-5 h-5 shrink-0 rounded-md border-slate-300 text-escRed focus:ring-escRed accent-escRed">
                                <span class="text-sm font-medium text-slate-600 leading-tight">I have read and agree to the <a
                                        href="#" class="text-escBlue font-bold underline">Privacy Policy</a> and <a
                                        href="#" class="text-escBlue font-bold underline">Terms & Conditions</a>.</span>
                            </label>
                        </div>
                    </div>

                    <div class="pt-6">
                        <button type="submit"
                                class="w-full bg-escRed hover:bg-red-700 text-white py-4 sm:py-5 rounded-2xl font-black text-base sm:text-lg shadow-xl shadow-red-100 transition-all flex items-center justify-center gap-3">
                            <span>Submit &amp; Access Program</span>
                            <i class="ph-bold ph-arrow-circle-right text-xl"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="asia-wave"></div>
</main>

    <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('easd-registration-form');
            const validator = FormValidation.formValidation(form, {
                fields: {
                    name: {validators: {notEmpty: {message: 'Full name is required'}}},
                    email: {
                        validators: {
                            notEmpty: {message: 'Email address is required'},
                            emailAddress: {message: 'Enter a valid email address'}
                        }
                    },
                    mobile: {
                        validators: {
                            notEmpty: {message: 'Mobile number is required'},
                            regexp: {regexp: /^\+?[0-9\s\-()]{7,25}$/, message: 'Enter a valid mobile number'}
                        }
                    },
                    country: {validators: {notEmpty: {message: 'Country is required'}}},
                    speciality: {validators: {notEmpty: {message: 'Speciality is required'}}},
                    hospital: {validators: {notEmpty: {message: 'Hospital or institution is required'}}},
                    medical_registration_number: {validators: {notEmpty: {message: 'Medical registration number is required'}}},
                    hcp_confirmation: {validators: {notEmpty: {message: 'Please confirm that you are a healthcare professional'}}},
                    terms: {validators: {notEmpty: {message: 'Please accept the Privacy Policy and Terms & Conditions'}}}
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap: new FormValidation.plugins.Bootstrap5({
                        rowSelector: function (field) {
                            return field === 'hcp_confirmation' || field === 'terms'
                                ? '.checkbox-field'
                                : '.space-y-2';
                        }
                    })
                }
            });
            form.addEventListener('submit', function (event) {
                event.preventDefault();
                validator.validate().then(function (status) {
                    if (status === 'Valid') form.submit();
                });
            });
        });
    </script>
    <style>
        .fv-plugins-message-container {
            margin-top: .4rem;
            color: #be1e2d;
            font-size: .75rem;
            font-weight: 600;
        }

        .checkbox-field .fv-plugins-message-container {
            margin-left: 2rem;
        }

        .checkbox-field.fv-plugins-bootstrap5-row-invalid label {
            color: #be1e2d;
        }
    </style>

@endsection
