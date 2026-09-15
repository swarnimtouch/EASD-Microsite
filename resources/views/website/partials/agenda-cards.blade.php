@php
    $stepDetails = [
        [
            'step' => '01',
            'duration' => '30 Mins',
            'icon' => 'ph-podium',
            'title' => 'Opening & Keynote Session',
            'desc' => 'Welcome remarks by EASD faculty and clinical rationale for early cardio-renal-metabolic intervention.',
        ],
        [
            'step' => '02',
            'duration' => '60 Mins',
            'icon' => 'ph-users-three',
            'title' => 'Expert Sessions & Clinical Panel',
            'desc' => 'Guideline translation (GDMT), evidence-based sequencing of SGLT2i and ARNI, and case studies.',
        ],
        [
            'step' => '03',
            'duration' => '30 Mins',
            'icon' => 'ph-chat-centered-text',
            'title' => 'Live Q&A & Certification',
            'desc' => 'Interactive delegate Q&A with the expert panel, followed by digital EASD certification issuance.',
        ],
    ];
@endphp

<section id="agenda" class="dot-pattern relative overflow-hidden bg-slate-50 px-4 py-14 sm:px-6">
    <div class="relative z-10 mx-auto max-w-7xl">
        <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div>
                <h2 class="text-xl font-black uppercase text-escRed">Scientific Agenda</h2>
                <div class="mt-2 h-0.5 w-16 bg-escRed"></div>
                <p class="mt-3 max-w-3xl text-sm font-semibold text-slate-700">
                    Live Hybrid Speaker Tour across 4 Host Countries (Malaysia, Philippines, Indonesia, Thailand) <span class="text-escRed">|</span> Broadcasting Live to Emerging Markets
                </p>
            </div>
            <div class="shrink-0">
                @include('partials.website.timezone_switcher')
            </div>
        </div>

        {{-- Interactive Country Tabs --}}
        @php
            $doctor = Auth::guard('web')->user();
            $defaultActiveCountry = ($doctor && $doctor->country && collect($agendaCountries)->pluck('name')->contains($doctor->country))
                ? $doctor->country
                : 'Philippines';
        @endphp
        <div class="mt-8">
            <div class="flex flex-wrap gap-2 sm:gap-3" role="tablist" aria-label="Host Country Agenda Tabs">
                @foreach($agendaCountries as $index => $countryAgenda)
                    @php
                        $isActive = strcasecmp($countryAgenda['name'], $defaultActiveCountry) === 0;
                        $webinar = $countryAgenda['webinar'];
                        $slug = \Illuminate\Support\Str::slug($countryAgenda['name']);
                    @endphp
                    <button type="button"
                            role="tab"
                            aria-selected="{{ $isActive ? 'true' : 'false' }}"
                            aria-controls="agenda-panel-{{ $slug }}"
                            id="agenda-tab-{{ $slug }}"
                            data-agenda-tab="{{ $slug }}"
                            class="agenda-tab-btn flex items-center gap-2.5 rounded-xl border px-4 py-3 text-xs font-black uppercase tracking-wider transition-all {{ $isActive ? 'border-escRed bg-escRed text-white shadow-md' : 'border-slate-200 bg-white text-slate-700 hover:border-slate-300 hover:bg-slate-100' }}">
                        <img src="{{ asset('assets/media/flags/'.$countryAgenda['flag'].'.svg') }}"
                             alt="{{ $countryAgenda['name'] }} flag"
                             class="h-5 w-7 rounded-xs border border-white/40 object-cover shrink-0 shadow-xs">
                        <span>{{ $countryAgenda['name'] }}</span>
                        @if($webinar?->scheduled_at)
                            <span class="rounded px-1.5 py-0.5 text-[9px] font-extrabold {{ $isActive ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600' }}">
                                {{ $webinar->scheduled_at->format('d M') }}
                            </span>
                        @endif
                    </button>
                @endforeach
            </div>

            {{-- Tab Panels (Single Unified View per Country) --}}
            <div class="mt-6">
                @foreach($agendaCountries as $index => $countryAgenda)
                    @php
                        $isActive = strcasecmp($countryAgenda['name'], $defaultActiveCountry) === 0;
                        $slug = \Illuminate\Support\Str::slug($countryAgenda['name']);
                        $webinar = $countryAgenda['webinar'];
                        $speakerCount = $webinar?->people->where('role', 'speaker')->count() ?? 0;
                        $moderatorCount = $webinar?->people->where('role', 'moderator')->count() ?? 0;
                    @endphp

                    <div id="agenda-panel-{{ $slug }}"
                         role="tabpanel"
                         aria-labelledby="agenda-tab-{{ $slug }}"
                         class="agenda-tab-panel {{ $isActive ? '' : 'hidden' }} transition-opacity duration-300">
                        <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                            {{-- Header & Meta Information --}}
                            <div class="flex flex-col gap-6 border-b border-slate-100 pb-6 lg:flex-row lg:items-center lg:justify-between">
                                <div class="space-y-3">
                                    <div class="flex flex-wrap items-center gap-2.5">
                                        <span class="inline-flex items-center gap-2 rounded-full border border-red-200 bg-red-50 px-3 py-1 text-xs font-black uppercase tracking-wider text-escRed">
                                            <img src="{{ asset('assets/media/flags/'.$countryAgenda['flag'].'.svg') }}" alt="{{ $countryAgenda['name'] }} flag" class="h-3.5 w-5 rounded-xs object-cover">
                                            {{ $countryAgenda['name'] }} Host Hub
                                        </span>
                                        @if($webinar?->speciality)
                                            <span class="inline-flex items-center gap-1 rounded-full border border-blue-200 bg-blue-50 px-3 py-1 text-xs font-bold text-escBlue">
                                                <i class="ph ph-stethoscope"></i>{{ $webinar->speciality->name }}
                                            </span>
                                        @endif
                                        <span class="inline-flex items-center gap-1 rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-600">
                                            <i class="ph ph-broadcast text-escRed"></i>Live Hybrid Broadcast
                                        </span>
                                    </div>

                                    <h3 class="text-xl font-black text-slate-900 sm:text-2xl">
                                        {{ $webinar?->title ?? 'Session Title Announced Soon' }}
                                    </h3>

                                    <div class="flex flex-wrap items-center gap-x-6 gap-y-2 text-xs font-semibold text-slate-600">
                                        @if($webinar?->scheduled_at)
                                            <span data-event-datetime="{{ $webinar->scheduled_at->toIso8601String() }}" class="flex items-center gap-1.5 text-slate-900 font-bold">
                                                <i class="ph ph-calendar-blank text-base text-escRed"></i>
                                                <span data-date-part="datetime">{{ $webinar->scheduled_at->format('d F Y, h:i A') }}</span>
                                            </span>
                                        @endif
                                        <span class="flex items-center gap-1.5">
                                            <i class="ph ph-globe text-base text-escBlue"></i>
                                            <span data-selected-timezone-label>GMT+8 SGT/PHT</span>
                                        </span>
                                        <span class="flex items-center gap-1.5">
                                            <i class="ph ph-users-three text-base text-escBlue"></i>
                                            {{ $speakerCount }} Speaker{{ $speakerCount === 1 ? '' : 's' }} · {{ $moderatorCount }} Moderator{{ $moderatorCount === 1 ? '' : 's' }}
                                        </span>
                                    </div>
                                </div>

                                @if($webinar)
                                    <div class="flex shrink-0 flex-wrap items-center gap-3">
                                        <a href="{{ route('webinar', $webinar->id) }}"
                                           class="inline-flex items-center gap-2 rounded-xl bg-escRed px-6 py-3 text-xs font-black uppercase tracking-wider text-white shadow-md hover:bg-red-700 transition">
                                            <i class="ph-fill ph-play-circle text-base"></i>
                                            <span>Join Webinar</span>
                                        </a>
                                        <a href="#about"
                                           class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-5 py-3 text-xs font-extrabold text-slate-700 hover:bg-slate-50 transition">
                                            <span>View Faculty</span>
                                        </a>
                                    </div>
                                @endif
                            </div>

                            {{-- Simplified 3-Step Agenda Timeline --}}
                            <div class="mt-8">
                                <div class="mb-4 flex items-center justify-between">
                                    <h4 class="text-xs font-black uppercase tracking-widest text-escBlue">
                                        Simplified 3-Step Scientific Timeline
                                    </h4>
                                    <span class="text-[11px] font-bold text-slate-500">Total Duration: 120 Minutes</span>
                                </div>

                                <div class="grid gap-6 md:grid-cols-3 relative">
                                    @foreach($stepDetails as $step)
                                        <div class="relative flex flex-col justify-between rounded-xl border border-slate-200 bg-slate-50/70 p-5 transition hover:bg-white hover:shadow-sm">
                                            <div>
                                                <div class="flex items-center justify-between">
                                                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-red-100 text-lg font-black text-escRed">
                                                        <i class="ph {{ $step['icon'] }}"></i>
                                                    </span>
                                                    <span class="rounded-full bg-white border border-slate-200 px-2.5 py-0.5 text-[10px] font-black uppercase tracking-wider text-slate-600">
                                                        Step {{ $step['step'] }} · {{ $step['duration'] }}
                                                    </span>
                                                </div>
                                                <h5 class="mt-4 text-sm font-black text-slate-900 leading-snug">
                                                    {{ $step['title'] }}
                                                </h5>
                                                <p class="mt-2 text-xs leading-5 text-slate-600">
                                                    {{ $step['desc'] }}
                                                </p>
                                            </div>
                                            <div class="mt-4 border-t border-slate-200/80 pt-3 flex items-center justify-between text-[11px] font-extrabold text-escBlue">
                                                <span>Session {{ $step['step'] }}</span>
                                                <i class="ph ph-arrow-right"></i>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Active Faculty Spotlight for this Host Country --}}
                            @if($webinar && $webinar->people->count() > 0)
                                <div class="mt-8 border-t border-slate-100 pt-6">
                                    <h4 class="text-xs font-black uppercase tracking-widest text-slate-400 mb-3">Host Country Session Faculty</h4>
                                    <div class="flex flex-wrap items-center gap-4">
                                        @foreach($webinar->people as $person)
                                            <div class="flex items-center gap-2.5 rounded-full border border-slate-200 bg-slate-50 py-1 pl-1 pr-3 shadow-2xs">
                                                <img src="{{ $person->image_url }}" alt="{{ $person->name }}" class="h-8 w-8 rounded-full border border-slate-200 object-cover">
                                                <div class="text-left">
                                                    <strong class="block text-xs font-bold text-slate-800 leading-none">{{ $person->name }}</strong>
                                                    <span class="text-[9px] font-semibold text-slate-500 uppercase">{{ ucfirst($person->role) }}</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </article>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="asia-wave"></div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const tabButtons = document.querySelectorAll('.agenda-tab-btn');
    const tabPanels = document.querySelectorAll('.agenda-tab-panel');

    tabButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            const targetSlug = this.dataset.agendaTab;

            tabButtons.forEach(b => {
                b.setAttribute('aria-selected', 'false');
                b.classList.remove('border-escRed', 'bg-escRed', 'text-white', 'shadow-md');
                b.classList.add('border-slate-200', 'bg-white', 'text-slate-700');
                const badge = b.querySelector('span:last-child');
                if (badge) {
                    badge.classList.remove('bg-white/20', 'text-white');
                    badge.classList.add('bg-slate-100', 'text-slate-600');
                }
            });

            this.setAttribute('aria-selected', 'true');
            this.classList.remove('border-slate-200', 'bg-white', 'text-slate-700');
            this.classList.add('border-escRed', 'bg-escRed', 'text-white', 'shadow-md');
            const activeBadge = this.querySelector('span:last-child');
            if (activeBadge) {
                activeBadge.classList.remove('bg-slate-100', 'text-slate-600');
                activeBadge.classList.add('bg-white/20', 'text-white');
            }

            tabPanels.forEach(p => {
                p.classList.add('hidden');
            });
            const activePanel = document.getElementById('agenda-panel-' + targetSlug);
            if (activePanel) {
                activePanel.classList.remove('hidden');
            }
        });
    });
});
</script>
