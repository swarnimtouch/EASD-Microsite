@extends('layouts.website', ['title' => 'Delegate Dashboard', 'bodyClass' => 'bg-white font-sans antialiased text-slate-800'])

@section('content')
<main class="dot-pattern relative overflow-hidden px-5 py-8 md:px-8">
    <div class="relative z-10 mx-auto max-w-[1440px]">
        <div class="mb-5 flex items-center justify-between gap-4">
            <div><p class="text-xs font-semibold text-slate-500">Welcome, <strong class="text-escBlue">{{ $doctor->name }}</strong></p></div>
            <div class="flex flex-wrap items-center justify-end gap-2">
                @include('partials.website.timezone_switcher')
                <a href="{{ route('logout') }}" class="inline-flex items-center gap-2 rounded-lg border border-escRed px-4 py-2 text-xs font-extrabold text-escRed transition hover:bg-escRed hover:text-white"><i class="ph ph-sign-out"></i>Logout</a>
            </div>
        </div>

        @if(session('success') || session('comment_success'))
            <div class="mb-5 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-xs font-bold text-green-700">{{ session('success') ?: session('comment_success') }}</div>
        @endif
        @if(session('comment_error'))
            <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-xs font-bold text-escRed">{{ session('comment_error') }}</div>
        @endif

        <div class="grid gap-7 xl:grid-cols-[1.42fr_.98fr]">
            <div class="space-y-4">
                <section id="webinar-screen">
                    <div class="mb-2 flex items-center justify-between">
                        <h1 class="text-lg font-black uppercase text-escBlue flex items-center gap-2">
                            <span class="h-2.5 w-2.5 rounded-full {{ $liveWebinar ? 'bg-red-500 animate-pulse' : 'bg-slate-400' }}"></span>
                            Webinar Screen
                        </h1>
                        @if($hasLiveStream)
                            <div class="inline-flex rounded-lg border border-slate-200 bg-slate-100 p-1 text-xs font-bold shadow-sm">
                                <button type="button" id="btn-show-stream" onclick="switchDashboardScreen('stream')" class="rounded-md px-3 py-1 text-white bg-escRed transition">Live Stream</button>
                                <button type="button" id="btn-show-welcome" onclick="switchDashboardScreen('welcome')" class="rounded-md px-3 py-1 text-slate-600 hover:text-slate-900 transition">Welcome View</button>
                            </div>
                        @endif
                    </div>

                    {{-- Live Stream Screen (When stream or video available) --}}
                    <div id="screen-stream-container" class="{{ $hasLiveStream ? '' : 'hidden' }} relative aspect-video overflow-hidden rounded-xl border border-slate-300 bg-black shadow-lg">
                        @if($youtubeEmbedUrl)
                            <iframe class="absolute inset-0 h-full w-full"
                                    src="{{ $youtubeEmbedUrl }}{{ $liveWebinar ? '&autoplay=1&mute=1' : '' }}"
                                    title="{{ $featuredWebinar?->title ?? 'Live Webinar Stream' }}"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                    referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                        @elseif($isVideoFile)
                            <video class="h-full w-full object-cover" controls {{ $liveWebinar ? 'autoplay muted' : '' }} playsinline src="{{ $playbackUrl }}"></video>
                        @elseif($playbackUrl && !str_contains($playbackUrl, 'example.com'))
                            <div class="absolute inset-0 flex flex-col items-center justify-center p-8 text-center text-white bg-gradient-to-br from-[#020c31] via-[#06205c] to-[#061746]">
                                <span class="h-16 w-16 rounded-full bg-escRed/20 text-escRed flex items-center justify-center text-3xl mb-4 animate-pulse">
                                    <i class="ph-fill ph-broadcast"></i>
                                </span>
                                <span class="rounded-full bg-red-600 px-3 py-1 text-[11px] font-black uppercase tracking-widest text-white mb-3">Live Broadcast Active</span>
                                <h3 class="text-xl font-bold">{{ $featuredWebinar?->title }}</h3>
                                <p class="mt-2 text-xs text-slate-300 max-w-md">The live session is ready. Click below to launch the video stream.</p>
                                <a href="{{ $playbackUrl }}" target="_blank" rel="noopener" class="mt-5 inline-flex items-center gap-2 rounded-xl bg-escRed px-6 py-2.5 text-xs font-black text-white hover:bg-red-700 shadow-lg transition">
                                    <i class="ph-fill ph-play-circle text-lg"></i> Launch Live Stream
                                </a>
                            </div>
                        @endif
                    </div>

                    {{-- Welcome Screen Graphic (Shown when no stream or switched to Welcome view) --}}
                    <div id="screen-welcome-container" class="{{ $hasLiveStream ? 'hidden' : '' }} relative aspect-video overflow-hidden rounded-xl border border-slate-300 bg-gradient-to-br from-[#020c31] via-[#06205c] to-[#061746] shadow-lg">
                        <div class="absolute left-5 top-4 flex items-center gap-2 text-sm font-bold text-white">
                            <span class="h-3 w-3 rounded-full {{ $liveWebinar ? 'animate-pulse bg-red-500' : 'bg-slate-400' }}"></span>
                            {{ $liveWebinar ? 'LIVE' : 'UPCOMING' }}
                        </div>
                        <div class="absolute inset-0 flex flex-col items-center justify-center px-8 text-center text-white">
                            <img src="{{ asset('assets/images/branding/pulce-logo.png') }}" alt="PULCE Connect 2026" class="mb-7 w-44 brightness-0 invert opacity-30">
                            <strong class="text-4xl font-black tracking-wide md:text-6xl">WELCOME</strong>
                            @if($featuredWebinar)
                                <p class="mt-4 max-w-xl text-xs font-semibold text-white/60">{{ $featuredWebinar->title }}</p>
                            @endif
                        </div>
                        <div class="absolute inset-x-0 bottom-0 flex items-center justify-between bg-black/75 px-5 py-3 text-white">
                            <div class="flex items-center gap-4">
                                <i class="ph-fill {{ $liveWebinar ? 'ph-pause' : 'ph-play' }} text-xl"></i>
                                <span class="flex items-center gap-2 text-xs font-bold">
                                    <span class="h-2.5 w-2.5 rounded-full {{ $liveWebinar ? 'bg-red-500' : 'bg-slate-400' }}"></span>
                                    {{ $liveWebinar ? 'LIVE' : 'WAITING' }}
                                </span>
                            </div>
                            <div class="flex items-center gap-4 text-xl">
                                <i class="ph ph-speaker-high"></i>
                                <i class="ph ph-gear"></i>
                                <i class="ph ph-arrows-out"></i>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                    <h2 class="text-base font-black uppercase text-escRed">Live Comments</h2><div class="mt-1 h-0.5 w-10 bg-escRed"></div>
                    @if($liveWebinar)
                        <form method="POST" action="{{ route('webinar.comments.store', $liveWebinar) }}" class="mt-4 flex items-center gap-3">
                            @csrf
                            <input type="hidden" name="redirect_to" value="dashboard">
                            <img src="{{ $doctor->profile_image }}" alt="{{ $doctor->name }}" class="h-12 w-12 shrink-0 rounded-full border border-slate-200 object-cover">
                            <input type="text" name="body" maxlength="1000" required placeholder="Type your question here..." class="min-w-0 flex-1 rounded-lg border border-slate-200 px-4 py-3 text-sm outline-none focus:border-escRed">
                            <button class="inline-flex items-center gap-2 rounded-lg bg-escRed px-6 py-3 text-sm font-extrabold text-white"><i class="ph-fill ph-paper-plane-tilt"></i>Send</button>
                        </form>
                        <p class="ml-16 mt-2 text-[10px] text-slate-500"><i class="ph ph-info mr-1"></i>Your question will be visible to the moderator.</p>
                    @else
                        <div class="mt-4 rounded-lg bg-slate-50 p-4 text-xs text-slate-500">Comments open when a webinar goes live.</div>
                    @endif
                </section>

                <div class="grid gap-4 sm:grid-cols-2">
                    @if($featuredWebinar?->pre_read_url)
                        <a href="{{ $featuredWebinar->pre_read_url }}" target="_blank" rel="noopener" class="flex items-center justify-between rounded-xl bg-red-50 p-5 text-escRed transition hover:bg-red-100/70 hover:shadow-sm"><span class="flex items-center gap-4"><i class="ph ph-file-arrow-down text-3xl"></i><strong class="text-sm">View &amp; Download<br>Pre-read Material</strong></span><i class="ph ph-download-simple text-2xl"></i></a>
                    @else
                        <div class="flex items-center justify-between rounded-xl bg-red-50 p-5 text-escRed opacity-60"><span class="flex items-center gap-4"><i class="ph ph-file-arrow-down text-3xl"></i><strong class="text-sm">View &amp; Download<br>Pre-read Material</strong></span><i class="ph ph-download-simple text-2xl"></i></div>
                    @endif
                    @if($featuredWebinar)
                        <a href="{{ route('webinar.post_read.download', $featuredWebinar) }}" class="flex items-center justify-between rounded-xl bg-blue-50 p-5 text-escBlue transition hover:bg-blue-100/70 hover:shadow-sm"><span class="flex items-center gap-4"><i class="ph ph-file-text text-3xl"></i><strong class="text-sm">Download Session<br>Post-read Material (PDF)</strong></span><i class="ph ph-download-simple text-2xl"></i></a>
                    @else
                        <div class="flex items-center justify-between rounded-xl bg-blue-50 p-5 text-escBlue opacity-60"><span class="flex items-center gap-4"><i class="ph ph-file-text text-3xl"></i><strong class="text-sm">Access<br>Post-read Material</strong></span><i class="ph ph-arrow-right text-2xl"></i></div>
                    @endif
                </div>
            </div>

            <aside class="space-y-5">
                <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-center text-base font-black uppercase text-escRed">Next Live Scientific Session Begins In</h2>
                    <div id="dashboard-countdown" @if($countdownWebinar?->scheduled_at) data-starts-at="{{ $countdownWebinar->scheduled_at->toIso8601String() }}" @endif class="mt-5 grid grid-cols-4 gap-3 text-center">
                        @foreach([['days','Days'],['hours','Hours'],['minutes','Minutes'],['seconds','Seconds']] as [$key,$label])
                            <div class="rounded-xl bg-red-50 px-2 py-4"><strong data-countdown="{{ $key }}" class="block text-3xl font-black text-escRed">00</strong><span class="mt-1 block text-[9px] font-semibold uppercase text-slate-700">{{ $label }}</span></div>
                        @endforeach
                    </div>
                    <div class="mt-5 grid grid-cols-2 divide-x border-t border-slate-200 pt-4 text-sm font-bold">
                        <div @if($countdownWebinar?->scheduled_at) data-event-datetime="{{ $countdownWebinar->scheduled_at->toIso8601String() }}" @endif class="flex items-center justify-center gap-3 text-escRed"><i class="ph ph-calendar-blank text-2xl"></i><span data-date-part="datetime">{{ $countdownWebinar?->scheduled_at?->format('d F Y, h:i A') ?? 'Date TBA' }}</span></div>
                        <div class="flex items-center justify-center gap-3 text-slate-800"><i class="ph ph-globe text-2xl text-escRed"></i><span data-selected-timezone-label>GMT+8 SGT/PHT</span></div>
                    </div>
                    @if($countdownWebinar?->timezone_label)<p class="mt-3 text-center text-[10px] font-semibold text-slate-400">Host timezone: {{ $countdownWebinar->timezone_label }}</p>@endif
                </section>

                <section>
                    <h2 class="text-base font-black uppercase text-escRed">Quick Access</h2><div class="mt-1 h-0.5 w-10 bg-escRed"></div>
                    <div class="mt-3 grid grid-cols-2 gap-3 sm:grid-cols-4">
                        <a href="{{ route('home') }}#agenda" class="flex min-h-56 flex-col items-center rounded-xl border border-red-100 bg-white p-4 text-center shadow-sm transition hover:-translate-y-1 hover:border-escRed">
                            <span class="flex h-12 w-12 items-center justify-center rounded-full bg-red-50 text-2xl text-escRed"><i class="ph ph-calendar-blank"></i></span>
                            <strong class="mt-4 text-xs text-escRed">Scientific Agenda</strong>
                            <p class="mt-3 text-[10px] leading-5 text-slate-600">View the detailed 3-day agenda across Southeast Asia.</p>
                            <i class="ph-fill ph-arrow-circle-right mt-auto text-2xl text-escRed"></i>
                        </a>

                        <a href="{{ route('home') }}#faculty" class="flex min-h-56 flex-col items-center rounded-xl border border-red-100 bg-white p-4 text-center shadow-sm transition hover:-translate-y-1 hover:border-escRed">
                            <span class="flex h-12 w-12 items-center justify-center rounded-full bg-red-50 text-2xl text-escRed"><i class="ph ph-users-three"></i></span>
                            <strong class="mt-4 text-xs text-escRed">Faculty Profiles</strong>
                            <p class="mt-3 text-[10px] leading-5 text-slate-600">Explore our distinguished regional and international faculty.</p>
                            <i class="ph-fill ph-arrow-circle-right mt-auto text-2xl text-escRed"></i>
                        </a>

                        <a href="#webinar-screen" onclick="switchDashboardScreen('stream')" class="flex min-h-56 flex-col items-center rounded-xl border border-red-100 bg-white p-4 text-center shadow-sm transition hover:-translate-y-1 hover:border-escRed">
                            <span class="flex h-12 w-12 items-center justify-center rounded-full bg-red-50 text-2xl text-escRed"><i class="ph-fill ph-play-circle"></i></span>
                            <strong class="mt-4 text-xs text-escRed">Join Live Stream</strong>
                            <p class="mt-3 text-[10px] leading-5 text-slate-600">Join live sessions, watch presentations and interact with experts.</p>
                            <i class="ph-fill ph-arrow-circle-right mt-auto text-2xl text-escRed"></i>
                        </a>

                        @if($certificateWebinar)
                            <a href="{{ route('certificate.page', $certificateWebinar) }}" class="flex min-h-56 flex-col items-center rounded-xl border {{ $hasCertificateComment ? 'border-emerald-200 hover:border-emerald-400' : 'border-red-100 hover:border-escRed' }} bg-white p-4 text-center shadow-sm transition hover:-translate-y-1">
                                <span class="flex h-12 w-12 items-center justify-center rounded-full {{ $hasCertificateComment ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-escRed' }} text-2xl">
                                    <i class="ph ph-certificate"></i>
                                </span>
                                <strong class="mt-4 text-xs {{ $hasCertificateComment ? 'text-emerald-700' : 'text-escRed' }}">Certificate Request</strong>
                                <p class="mt-3 text-[10px] leading-5 text-slate-600">
                                    {{ $hasCertificateComment ? 'Your certificate is unlocked. Click to view & download.' : 'Request and download your certificate after submitting a comment.' }}
                                </p>
                                <span class="mt-auto inline-flex items-center gap-1 text-[11px] font-extrabold {{ $hasCertificateComment ? 'text-emerald-600' : 'text-slate-400' }}">
                                    <i class="ph-fill {{ $hasCertificateComment ? 'ph-check-circle text-emerald-500' : 'ph-lock-key' }} text-base"></i>
                                    {{ $hasCertificateComment ? 'Unlocked' : 'Requires Comment' }}
                                </span>
                            </a>
                        @else
                            <div class="flex min-h-56 flex-col items-center rounded-xl border border-red-100 bg-white p-4 text-center opacity-60 shadow-sm">
                                <span class="flex h-12 w-12 items-center justify-center rounded-full bg-red-50 text-2xl text-escRed"><i class="ph ph-certificate"></i></span>
                                <strong class="mt-4 text-xs text-escRed">Certificate Request</strong>
                                <p class="mt-3 text-[10px] leading-5 text-slate-600">Admin has not uploaded a certificate yet.</p>
                                <i class="ph-fill ph-lock-key mt-auto text-2xl text-slate-400"></i>
                            </div>
                        @endif
                    </div>
                </section>

                <a href="{{ route('snippets') }}" class="flex items-center justify-between rounded-xl border border-red-100 bg-white p-5 shadow-sm transition hover:border-escRed hover:shadow-md">
                    <span class="flex items-center gap-4">
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-red-50 text-2xl text-escRed">
                            <i class="ph ph-clapperboard"></i>
                        </span>
                        <span>
                            <strong class="block text-sm text-escRed">Snippet Videos</strong>
                            <small class="mt-0.5 block text-xs text-slate-600">Watch key highlights and expert insights.</small>
                        </span>
                    </span>
                    <i class="ph-fill ph-arrow-circle-right text-2xl text-escRed"></i>
                </a>
            </aside>
        </div>
    </div>
    <div class="asia-wave"></div>
</main>
@endsection

@push('scripts')
<script>
function switchDashboardScreen(mode) {
    const streamBox = document.getElementById('screen-stream-container');
    const welcomeBox = document.getElementById('screen-welcome-container');
    const btnStream = document.getElementById('btn-show-stream');
    const btnWelcome = document.getElementById('btn-show-welcome');

    if (mode === 'stream' && streamBox) {
        streamBox.classList.remove('hidden');
        welcomeBox?.classList.add('hidden');
        btnStream?.classList.add('bg-escRed', 'text-white');
        btnStream?.classList.remove('text-slate-600');
        btnWelcome?.classList.remove('bg-escRed', 'text-white');
        btnWelcome?.classList.add('text-slate-600');
        const screenEl = document.getElementById('webinar-screen');
        if (screenEl) screenEl.scrollIntoView({ behavior: 'smooth' });
    } else if (welcomeBox) {
        welcomeBox.classList.remove('hidden');
        streamBox?.classList.add('hidden');
        btnWelcome?.classList.add('bg-escRed', 'text-white');
        btnWelcome?.classList.remove('text-slate-600');
        btnStream?.classList.remove('bg-escRed', 'text-white');
        btnStream?.classList.add('text-slate-600');
    }
}

(() => {
    const timer = document.getElementById('dashboard-countdown');
    if (!timer?.dataset.startsAt) return;
    const startsAt = new Date(timer.dataset.startsAt).getTime();
    const render = () => {
        const remaining = Math.max(0, startsAt - Date.now());
        const values = {days: Math.floor(remaining / 86400000), hours: Math.floor((remaining % 86400000) / 3600000), minutes: Math.floor((remaining % 3600000) / 60000), seconds: Math.floor((remaining % 60000) / 1000)};
        Object.entries(values).forEach(([key, value]) => { const el = timer.querySelector(`[data-countdown="${key}"]`); if (el) el.textContent = String(value).padStart(2, '0'); });
    };
    render();
    window.setInterval(render, 1000);
})();
</script>
@endpush
