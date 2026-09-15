@extends('layouts.portal', ['title' => 'Webinars'])
@if(false)
    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Webinars — PULCE Connect 2026</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
          rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {sans: ['Plus Jakarta Sans', 'sans-serif']},
                    colors: {escRed: '#BE1E2D', escBlue: '#1D438A'}
                }
            }
        }
    </script>
</head>
<body class="bg-[#f5f7f9] font-sans text-slate-800 antialiased">
<div class="min-h-screen lg:flex">
    <aside class="border-r border-slate-200 bg-white lg:fixed lg:inset-y-0 lg:w-72">
        <div class="flex h-20 items-center border-b border-slate-100 px-7">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <i class="ph-fill ph-heart text-2xl text-escRed"></i>
                <div>
                    <span class="text-xl font-black text-escBlue">PULCE</span>
                    <span class="ml-2 font-black text-escRed">2026</span>
                    <p class="text-[8px] font-bold uppercase tracking-widest text-slate-400">EASD Diabetes
                        Series</p>
                </div>
            </a>
        </div>
        <nav class="space-y-2 p-5">
            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-3 rounded-2xl px-4 py-3.5 text-sm font-bold text-slate-500 transition hover:bg-slate-50 hover:text-escBlue">
                <i class="ph ph-squares-four text-xl"></i> Dashboard
            </a>
            <a href="{{ route('webinars') }}"
               class="flex items-center gap-3 rounded-2xl bg-escBlue px-4 py-3.5 text-sm font-extrabold text-white">
                <i class="ph ph-video-camera text-xl"></i> Webinars
            </a>
            <a href="{{ route('home') }}#agenda"
               class="flex items-center gap-3 rounded-2xl px-4 py-3.5 text-sm font-bold text-slate-500 transition hover:bg-slate-50 hover:text-escBlue">
                <i class="ph ph-calendar-blank text-xl"></i> Scientific Agenda
            </a>
            <a href="{{ route('home') }}#about"
               class="flex items-center gap-3 rounded-2xl px-4 py-3.5 text-sm font-bold text-slate-500 transition hover:bg-slate-50 hover:text-escBlue">
                <i class="ph ph-users-three text-xl"></i> Faculty
            </a>
        </nav>
        <div class="m-5 rounded-2xl border border-red-100 bg-red-50 p-4 lg:absolute lg:bottom-0 lg:inset-x-0">
            <div class="flex items-center gap-3">
                <img src="{{ $doctor->profile_image }}" alt="{{ $doctor->name }}"
                     class="h-11 w-11 rounded-full border-2 border-white object-cover shadow">
                <div class="min-w-0">
                    <p class="truncate text-sm font-extrabold">{{ $doctor->name }}</p>
                    <p class="truncate text-[10px] font-bold text-slate-500">{{ $doctor->speciality ?: 'Healthcare Professional' }}</p>
                </div>
            </div>
            <a href="{{ route('logout') }}"
               class="mt-4 flex items-center justify-center gap-2 rounded-xl bg-white py-2.5 text-xs font-extrabold text-escRed shadow-sm">
                <i class="ph ph-sign-out"></i> Logout
            </a>
        </div>
    </aside>
    <div class="min-w-0 flex-1 lg:ml-72">
        <header
            class="sticky top-0 z-40 flex h-20 items-center justify-between border-b border-slate-200 bg-white/95 px-6 backdrop-blur md:px-10">
            <div>
                <p class="text-xs font-semibold text-slate-400">Welcome back,</p>
                <p class="font-extrabold">{{ $doctor->name }}</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('home') }}"
                   class="hidden rounded-xl border border-slate-200 px-4 py-2 text-xs font-extrabold text-slate-600 sm:block">View
                    website</a>
                <img src="{{ $doctor->profile_image }}"
                     class="h-10 w-10 rounded-full border border-slate-200 object-cover"
                     alt="{{ $doctor->name }}">
            </div>
        </header>
        @endif
        @section('content')
            <main class="mx-auto max-w-[1500px] space-y-12 p-6 md:p-10">
                <section class="flex flex-col justify-between gap-5 md:flex-row md:items-end">
                    <div>
                        <p class="text-[10px] font-extrabold uppercase tracking-[.25em] text-escRed">EASD Diabetes
                            Series</p>
                        <h1 class="mt-2 text-3xl font-black text-escBlue">Webinars</h1>
                        <p class="mt-2 text-sm text-slate-500">Live sessions, upcoming expert webinars, and previous
                            program content.</p>
                    </div>
                    <div class="flex gap-3">
                        <span
                            class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-xs font-bold text-slate-500">
                            <strong
                                class="text-escBlue">{{ $liveWebinars->count() + $upcomingWebinars->count() + $completedWebinars->count() }}</strong> total webinars
                        </span>
                    </div>
                </section>

                @if($liveWebinars->isNotEmpty())
                    <section class="space-y-5">
                        <div class="flex items-center gap-3">
                            <span
                                class="flex h-10 w-10 items-center justify-center rounded-xl bg-red-50 text-escRed"><i
                                    class="ph-fill ph-broadcast text-xl"></i></span>
                            <div>
                                <h2 class="text-xl font-black text-slate-900">Live Now</h2>
                                <p class="text-xs text-slate-500">Sessions currently in progress</p>
                            </div>
                        </div>
                        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                            @foreach($liveWebinars as $webinar)
                                @include('website.partials.webinar_card', ['webinar' => $webinar, 'state' => 'live'])
                            @endforeach
                        </div>
                    </section>
                @endif

                <section class="space-y-5">
                    <div class="flex items-center gap-3">
                        <span
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-escBlue">
                            <i
                                class="ph-fill ph-calendar-check text-xl"></i>
                        </span>
                        <div>
                            <h2 class="text-xl font-black text-slate-900">Upcoming Webinars</h2>
                            <p class="text-xs text-slate-500">Your next scheduled scientific sessions</p>
                        </div>
                    </div>
                    <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                        @forelse($upcomingWebinars as $webinar)
                            @include('website.partials.webinar_card', ['webinar' => $webinar, 'state' => 'upcoming'])
                        @empty
                            <div class="col-span-full rounded-3xl border border-slate-200 bg-white p-10 text-center">
                                <i class="ph ph-calendar-x text-5xl text-slate-300"></i>
                                <h3 class="mt-4 font-black text-escBlue">No upcoming webinars</h3>
                                <p class="mt-2 text-sm text-slate-500">New webinar dates will appear here after
                                    publication.</p>
                            </div>
                        @endforelse
                    </div>
                </section>

                @if($completedWebinars->isNotEmpty())
                    <section class="space-y-5">
                        <div class="flex items-center gap-3">
                            <span
                                class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-500">
                                <i
                                    class="ph-fill ph-play-circle text-xl"></i>
                            </span>
                            <div>
                                <h2 class="text-xl font-black text-slate-900">Previous Webinars</h2>
                                <p class="text-xs text-slate-500">Completed sessions and available recordings</p>
                            </div>
                        </div>
                        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                            @foreach($completedWebinars as $webinar)
                                @include('website.partials.webinar_card', ['webinar' => $webinar, 'state' => 'completed'])
                            @endforeach
                        </div>
                    </section>
                @endif
            </main>
@endsection
