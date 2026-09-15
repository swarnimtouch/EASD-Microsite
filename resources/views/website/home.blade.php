@extends('layouts.website', ['title' => 'PULCE Connect 2026'])

@section('content')
<section class="hero-gradient dot-pattern relative overflow-hidden px-6 py-12 md:py-16">
    <div class="relative z-10 mx-auto grid max-w-7xl items-center gap-12 lg:grid-cols-[1.05fr_.95fr]">
        <div>
            <div class="grid gap-8 md:grid-cols-2 md:divide-x md:divide-slate-200">
                <div class="md:pr-8">
                    <div class="mb-5 flex items-center justify-center text-escBlue"><span class="flex h-16 w-16 items-center justify-center rounded-full bg-red-50 text-4xl text-escRed"><i class="ph ph-calendar-dots"></i></span></div>
                    <h2 class="text-center text-sm font-extrabold uppercase text-escBlue">Event Dates</h2>
                    <div class="mx-auto mt-3 h-0.5 w-12 bg-escRed"></div>
                    <div class="mt-4 flex justify-center">@include('partials.website.timezone_switcher')</div>
                    <div class="mt-6 flex justify-center divide-x divide-slate-200">
                        @foreach($agendaCountries as $countryAgenda)
                            @php
                                $eventDate = $countryAgenda['webinar']?->scheduled_at;
                            @endphp
                            @if($eventDate)
                                <time class="min-w-[85px] px-3 text-center sm:min-w-[92px] sm:px-4" data-event-datetime="{{ $eventDate->toIso8601String() }}">
                                    <strong data-date-part="day" class="block text-3xl font-black text-slate-950">{{ $eventDate->format('d') }}</strong>
                                    <span class="text-xs font-bold text-slate-700"><span data-date-part="month">{{ $eventDate->format('F') }}</span><br><span data-date-part="year">{{ $eventDate->format('Y') }}</span></span>
                                </time>
                            @else
                                <div class="min-w-[85px] px-3 text-center sm:min-w-[92px] sm:px-4"><strong class="block text-xl font-black text-slate-950">TBA</strong><span class="text-[10px] font-bold text-slate-600">{{ $countryAgenda['name'] }}<br>Schedule</span></div>
                            @endif
                        @endforeach
                    </div>
                </div>
                <div class="md:pl-8">
                    <div class="mb-5 flex items-center justify-center"><span class="flex h-16 w-16 items-center justify-center rounded-full bg-blue-50 text-4xl text-escBlue"><i class="ph ph-globe-hemisphere-east"></i></span></div>
                    <h2 class="text-center text-sm font-extrabold uppercase text-escBlue">Global Collaboration</h2>
                    <div class="mx-auto mt-3 h-0.5 w-12 bg-escBlue"></div>
                    <div class="mt-5 grid grid-cols-2 gap-x-3 gap-y-4 sm:grid-cols-4">
                        @foreach($agendaCountries as $countryAgenda)<div class="text-center"><img src="{{ asset('assets/media/flags/'.$countryAgenda['flag'].'.svg') }}" alt="{{ $countryAgenda['name'] }} flag" class="mx-auto h-6 w-10 rounded-sm border border-slate-200 object-contain shadow-sm"><small class="mt-1 block text-[9px] font-semibold text-slate-700">{{ $countryAgenda['name'] }}</small></div>@endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center lg:text-left">
            <div class="mx-auto aspect-[4.05/1] w-full max-w-[620px] overflow-hidden lg:mx-0">
                <img src="{{ asset('assets/images/branding/pulce-logo.png') }}" alt="PULCE Connect 2026" class="block h-auto w-full object-contain object-top">
            </div>
            <div class="mx-auto mt-2 max-w-[620px] rounded-sm bg-escRed px-4 py-2 text-center text-sm font-black uppercase tracking-wide text-white lg:mx-0">Cardio-Renal-Metabolic Educational Series</div>
            <p class="mx-auto mt-2 max-w-[620px] text-center text-sm font-extrabold text-escBlue lg:mx-0">Redefining Diabetes Care Beyond Glycaemic Control</p>
            <p class="mx-auto mt-3 max-w-2xl text-sm font-black leading-6 text-escBlue lg:mx-0">Live Hybrid Speaker Tour across 4 Host Countries (Malaysia, Philippines, Indonesia, Thailand) <span class="text-escRed">|</span> Broadcasting Live to Emerging Markets</p>
            @guest('web')
                <div class="mt-7 flex flex-wrap items-center justify-center gap-4 lg:justify-start"><a href="{{ route('register') }}" class="rounded-md bg-escRed px-8 py-3 text-sm font-extrabold text-white shadow-lg hover:bg-red-700">REGISTER NOW</a><a href="{{ route('login') }}" class="text-xs font-extrabold text-escBlue underline underline-offset-4">Already registered? Login</a></div>
                <p class="mt-3 text-center text-xs font-bold text-slate-500 lg:text-left"><i class="ph ph-users-three mr-1 text-escBlue"></i>{{ number_format($registeredDoctors) }} healthcare professional{{ $registeredDoctors === 1 ? '' : 's' }} registered</p>
            @endguest
        </div>
    </div>
    <div class="asia-wave"></div>
</section>

<section id="about" class="relative overflow-hidden px-6 py-14">
    <div class="mx-auto max-w-7xl">
        <h2 class="text-xl font-black uppercase text-escRed">About the Program</h2><div class="mt-2 h-0.5 w-16 bg-escRed"></div>
        <h3 class="mt-5 text-lg font-extrabold text-escBlue">Redefining Diabetes Care Beyond Glycaemic Control</h3>
        <p class="mt-2 max-w-4xl text-sm leading-6 text-slate-700">PULCE Connect brings leading cardiology experts together to translate guideline-directed medical therapy (GDMT) into everyday clinical practice. The programme explores optimal sequencing of SGLT2 inhibitors and ARNI therapy, emphasizing early intervention across the cardio-renal-metabolic continuum.</p>
        <div class="mt-8">
        <div class="mt-10" id="faculty">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h4 class="text-base font-black uppercase text-escBlue">Speakers &amp; Moderators</h4>
                    <p class="text-xs text-slate-500">Faculty profiles published from active webinars.</p>
                </div>
            </div>

            <div class="grid gap-6 md:grid-cols-3">
                @forelse($facultyList as $fac)
                    @php
                        $isModerator = $fac->role === 'moderator';
                        $flagPath = $fac->country ? 'assets/media/flags/'.\Illuminate\Support\Str::slug($fac->country).'.svg' : null;
                    @endphp
                    <article class="reference-panel p-6 flex flex-col justify-between transition-all hover:shadow-lg hover:-translate-y-1">
                        <div>
                            <div class="flex items-center gap-3 mb-4">
                                <img src="{{ $fac->image_url }}" alt="{{ $fac->name }}" class="h-14 w-14 shrink-0 rounded-full bg-slate-100 border border-slate-200 object-cover">
                                <div>
                                    <h5 class="text-base font-extrabold text-escRed leading-snug">{{ $fac->name }}</h5>
                                    <span class="inline-block rounded px-2.5 py-0.5 text-[10px] font-bold uppercase {{ $isModerator ? 'bg-blue-50 text-escBlue' : 'bg-red-50 text-escRed' }}">{{ ucfirst($fac->role) }}</span>
                                </div>
                            </div>
                            <div class="space-y-2 border-t border-slate-100 pt-3 text-xs leading-5 text-slate-700">
                                @if($fac->qualifications)<p><strong class="block text-[10px] uppercase text-slate-400">Qualifications</strong>{{ $fac->qualifications }}</p>@endif
                                @if($fac->current_position || $fac->designation || $fac->institution)<p><strong class="block text-[10px] uppercase text-slate-400">Current Position</strong>{{ $fac->current_position ?: $fac->designation }}@if($fac->institution)<br><span class="text-slate-500">{{ $fac->institution }}</span>@endif</p>@endif
                                @if($fac->country)<p class="flex items-center gap-2 pt-1">@if($flagPath && file_exists(public_path($flagPath)))<img src="{{ asset($flagPath) }}" alt="{{ $fac->country }} flag" class="h-4 w-6 rounded-sm object-cover border border-slate-200">@endif<span>{{ $fac->country }}</span></p>@endif
                            </div>
                            @if($fac->bio)<div class="border-t border-slate-100 mt-4 pt-3 text-xs leading-5 text-slate-600"><p>{{ $fac->bio }}</p></div>@endif
                        </div>
                    </article>
                @empty
                    <div class="md:col-span-3 rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-6 py-10 text-center text-sm text-slate-500">
                        Speaker and moderator profiles will appear here after they are added to an active webinar.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</section>

@include('website.partials.agenda-cards')
@endsection
