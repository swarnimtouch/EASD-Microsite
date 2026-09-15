@extends('layouts.website', ['title' => 'Doctor Login', 'bodyClass' => 'min-h-screen bg-slate-50 font-sans text-slate-800'])
@push('styles')
    <style>
        .form-error {
            display: block;
            margin: .4rem 0 0 .25rem;
            color: #be1e2d;
            font-size: .75rem;
            font-weight: 700
        }
    </style>
@endpush
@section('content')
    <main class="dot-pattern flex min-h-[650px] items-center px-6 py-14">
        <div
            class="mx-auto grid w-full max-w-5xl overflow-hidden rounded-[36px] border border-slate-100 bg-white shadow-2xl shadow-slate-200 lg:grid-cols-2">
            <section
                class="relative hidden overflow-hidden bg-escBlue p-12 text-white lg:flex lg:flex-col lg:justify-between">
                <div class="absolute -right-24 -top-24 h-72 w-72 rounded-full bg-white/5"></div>
                <div class="absolute -bottom-28 -left-20 h-80 w-80 rounded-full bg-escRed/20"></div>
                <div class="relative"><span
                        class="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-2 text-[10px] font-extrabold uppercase tracking-[.2em]"><i
                            class="ph-fill ph-heartbeat"></i> Cardio-Renal-Metabolic Series</span>
                    <h1 class="mt-8 text-4xl font-black leading-tight">Redefining Diabetes Care Beyond Glycaemic
                        Control</h1>
                    <p class="mt-5 max-w-sm text-sm leading-7 text-white/70">Access your webinars, scientific resources,
                        expert faculty, and upcoming EASD sessions from one dashboard.</p></div>
                <div class="relative flex items-center gap-3 text-sm font-bold"><i
                        class="ph ph-shield-check text-2xl text-red-300"></i>Secure access for registered HCPs
                </div>
            </section>
            <section class="p-8 md:p-12">
                <div>
                    <p class="text-xs font-extrabold uppercase tracking-[.25em] text-escRed">Doctor access</p>
                    <h2 class="mt-3 text-3xl font-black text-escBlue">Welcome back</h2>
                    <p class="mt-2 text-sm text-slate-500">Enter your registered email address. No password is
                        required.</p>
                </div>
                @if(session('error'))
                    <div
                        class="mt-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm font-semibold text-escRed">
                        {{ session('error') }}
                    </div>
                @endif
                @if($errors->any())
                    <div
                        class="mt-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm font-semibold text-escRed">
                        {{ $errors->first() }}
                    </div>
                @endif
                <form id="doctorLoginForm" method="POST" action="{{ route('login.post') }}" class="mt-8"
                      novalidate>
                    @csrf
                    <div>
                        <label for="login-email"
                               class="ml-1 text-xs font-extrabold uppercase tracking-wider text-slate-700">Email
                            address <span class="text-escRed">*</span>
                        </label>
                        <div class="relative mt-2">
                            <i class="ph ph-envelope-simple absolute left-5 top-1/2 -translate-y-1/2 text-xl text-slate-400"></i>
                            <input id="login-email" type="email" name="email" value="{{ old('email') }}"
                                   placeholder="doctor@example.com" autocomplete="email"
                                   class="w-full rounded-2xl border border-slate-200 bg-slate-50 py-4 pl-14 pr-5 outline-none transition focus:border-escRed focus:ring-4 focus:ring-red-50">
                        </div>
                    </div>
                    <button type="submit"
                            class="mt-7 flex w-full items-center justify-center gap-3 rounded-2xl bg-escRed py-4 font-black text-white shadow-xl shadow-red-100 transition hover:bg-red-700">
                        LOGIN TO DASHBOARD <i class="ph-bold ph-arrow-circle-right text-xl"></i>
                    </button>
                </form>
                <div class="mt-8 border-t border-slate-100 pt-6 text-center text-sm text-slate-500">
                    Not registered yet?
                    <a href="{{ route('register') }}" class="font-extrabold text-escBlue hover:text-escRed">Create your
                        HCP account</a>
                </div>
            </section>
        </div>
    </main>
@endsection
@push('scripts')
    <script>
        $(function () {
            $('#doctorLoginForm').validate({
                errorClass: 'form-error',
                errorElement: 'span',
                rules: {email: {required: true, email: true}},
                messages: {email: {required: 'Email address is required', email: 'Enter a valid email address'}},
                highlight: function (el) {
                    $(el).addClass('border-escRed').removeClass('border-slate-200')
                },
                unhighlight: function (el) {
                    $(el).removeClass('border-escRed').addClass('border-slate-200')
                }
            })
        })
    </script>
@endpush
