@php
    $agendaItems = [
        ['ph-podium', 'Welcome & Opening Remarks'],
        ['ph-target', 'Keynote Session'],
        ['ph-users-three', 'Expert Session'],
        ['ph-stethoscope', 'NCL Session'],
        ['ph-users', 'Panel Discussion'],
        ['ph-chat-centered-text', 'Live Q&A'],
        ['ph-globe-hemisphere-east', 'Webinar Q&A'],
        ['ph-certificate', 'Closing Remarks & Certification'],
    ];
@endphp

<section id="agenda" class="dot-pattern relative overflow-hidden bg-slate-50 px-4 py-14 sm:px-6">
    <div class="relative z-10 mx-auto max-w-7xl">
        <div>
            <h2 class="text-xl font-black uppercase text-escRed">Scientific Agenda</h2>
            <div class="mt-2 h-0.5 w-16 bg-escRed"></div>
            <p class="mt-3 text-sm text-slate-600">Detailed program agenda for each country.</p>
        </div>

        <div class="mt-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach($agendaCountries as $countryAgenda)
                @php
                    $webinar = $countryAgenda['webinar'];
                    $isRed = $countryAgenda['name'] === 'Indonesia';
                    $accentText = $isRed ? 'text-escRed' : 'text-escBlue';
                    $accentBorder = $isRed ? 'border-escRed' : 'border-escBlue';
                    $accentBg = $isRed ? 'bg-red-50' : 'bg-blue-50';
                    $speakerCount = $webinar?->people->where('role', 'speaker')->count() ?? 0;
                    $moderatorCount = $webinar?->people->where('role', 'moderator')->count() ?? 0;
                @endphp

                <article class="flex min-h-full flex-col rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                    <header class="flex items-center gap-3">
                        <img src="{{ asset('assets/media/flags/'.$countryAgenda['flag'].'.svg') }}"
                             alt="{{ $countryAgenda['name'] }} flag"
                             class="h-11 w-11 rounded-full border border-slate-200 object-cover shadow-sm">
                        <div class="min-w-0">
                            <h3 class="text-sm font-black uppercase {{ $accentText }}">{{ $countryAgenda['name'] }}</h3>
                            <p class="text-[10px] font-bold uppercase tracking-wide text-slate-700">Event Details</p>
                        </div>
                    </header>

                    <div class="mt-3 flex items-center">
                        <span class="h-0.5 flex-1 {{ $isRed ? 'bg-red-200' : 'bg-blue-200' }}"></span>
                        <span class="h-1.5 w-1.5 rounded-full {{ $isRed ? 'bg-escRed' : 'bg-escBlue' }}"></span>
                    </div>

                    <div class="mt-4 min-h-[112px] rounded-xl {{ $accentBg }} p-3">
                        @if($webinar)
                            <h4 class="line-clamp-2 text-xs font-black leading-5 text-slate-900">{{ $webinar->title }}</h4>
                            <div class="mt-2 space-y-1 text-[10px] font-semibold text-slate-600">
                                <p><i class="ph ph-stethoscope mr-1 {{ $accentText }}"></i>{{ $webinar->speciality?->name ?? 'Speciality to be announced' }}</p>
                                <p><i class="ph ph-calendar-blank mr-1 {{ $accentText }}"></i>{{ $webinar->scheduled_at?->format('d M Y, h:i A') ?? 'Schedule to be announced' }}</p>
                                <p><i class="ph ph-users-three mr-1 {{ $accentText }}"></i>{{ $speakerCount }} speaker{{ $speakerCount === 1 ? '' : 's' }} · {{ $moderatorCount }} moderator{{ $moderatorCount === 1 ? '' : 's' }}</p>
                            </div>
                        @else
                            <div class="flex h-full min-h-[88px] items-center justify-center text-center text-xs font-bold text-slate-500">
                                Event details coming soon
                            </div>
                        @endif
                    </div>

                    <div class="mt-4 grid grid-cols-4 gap-x-1.5 gap-y-5">
                        @foreach($agendaItems as [$icon, $label])
                            <div class="flex flex-col items-center text-center">
                                <span class="flex h-9 w-9 items-center justify-center rounded-full border {{ $accentBorder }} {{ $accentBg }} text-lg {{ $accentText }}">
                                    <i class="ph {{ $icon }}"></i>
                                </span>
                                <span class="mt-1.5 text-[8px] font-bold leading-[1.25] text-slate-700">{{ $label }}</span>
                            </div>
                        @endforeach
                    </div>
                </article>
            @endforeach
        </div>
    </div>
    <div class="asia-wave"></div>
</section>
