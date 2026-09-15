@extends('layouts.website', ['title' => 'Session Highlights & Post-read Materials', 'bodyClass' => 'bg-slate-50 font-sans antialiased text-slate-800', 'hideWebsiteNav' => true])

@section('content')
<main class="dot-pattern relative min-h-[calc(100vh-140px)] overflow-hidden px-4 py-8 sm:px-8 md:py-10">
    <div class="relative z-10 mx-auto max-w-[1440px]">
        <!-- Top Navigation -->
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3 border-b border-slate-200/80 pb-4">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-sm font-black text-escBlue transition hover:text-blue-700">
                <i class="ph-bold ph-arrow-left text-base"></i> Back to Dashboard
            </a>
            <div class="flex items-center gap-4">
                <span class="text-xs font-semibold text-slate-600">Logged in as: <strong class="text-escBlue font-bold">{{ $doctor->name }}</strong></span>
                <a href="{{ route('logout') }}" class="inline-flex items-center gap-1.5 rounded-xl border border-escRed/40 bg-white px-4 py-2 text-xs font-extrabold text-escRed shadow-sm transition hover:bg-escRed hover:text-white">
                    <i class="ph ph-sign-out"></i> Logout
                </a>
            </div>
        </div>

        <!-- Hero Heading -->
        <div class="mb-10">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-black uppercase tracking-wide text-escBlue sm:text-3xl">Session Resources &amp; Highlights</h1>
                    <div class="mt-2.5 h-1.5 w-16 rounded-full bg-escRed"></div>
                    <p class="mt-2.5 text-sm text-slate-600">Watch expert clinical snippet highlights and download official session presentations &amp; summary materials.</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-600 shadow-sm">
                        <i class="ph ph-calendar-blank text-escRed text-base"></i> Series: PULCE Connect 2026
                    </span>
                </div>
            </div>
        </div>

        <!-- Two Column Rich Section Grid -->
        <div class="grid gap-10 lg:grid-cols-12">
            <!-- LEFT COLUMN: SNIPPET VIDEO PLAYERS -->
            <section class="lg:col-span-8">
                <div class="mb-6 flex items-center justify-between border-b border-slate-200 pb-4">
                    <div>
                        <div class="flex items-center gap-2.5">
                            <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-escBlue text-white text-base">
                                <i class="ph ph-video-camera"></i>
                            </span>
                            <h2 class="text-lg font-black uppercase tracking-wide text-escBlue">Snippet Videos</h2>
                        </div>
                        <p class="mt-1 text-xs text-slate-500">Key clinical insights and highlights from our expert faculty.</p>
                    </div>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-3 py-1 text-[11px] font-extrabold uppercase tracking-wider text-slate-600 border border-slate-200">
                        <i class="ph ph-play-circle text-xs"></i> Play Video
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-3 xl:grid-cols-4">
                    @forelse($snippetWebinars as $webinar)
                        @php
                            $embedUrl = \App\Models\Webinar::youtubeEmbedUrl($webinar->recording_url);
                            $videoUrl = $embedUrl ?: $webinar->recording_url;
                        @endphp
                        <button type="button"
                                class="snippet-player-trigger group flex flex-col overflow-hidden rounded-2xl border border-slate-200/90 bg-white text-left shadow-sm transition hover:-translate-y-0.5 hover:border-escBlue/30 hover:shadow-md"
                                data-video-url="{{ $videoUrl }}"
                                data-video-type="{{ $embedUrl ? 'iframe' : 'video' }}"
                                data-video-title="{{ $webinar->title }}">
                            <!-- Large Video Thumbnail Showcase -->
                            <div class="relative aspect-[16/10] w-full overflow-hidden bg-gradient-to-br from-[#020b29] via-[#06205c] to-[#0a338c] p-5 text-center">
                                <!-- Watermark Logo -->
                                <img src="{{ asset('assets/images/branding/pulce-logo.png') }}" alt="" class="pointer-events-none absolute inset-0 m-auto w-3/4 opacity-25 brightness-0 invert">

                                <!-- Top Badges -->
                                <div class="relative z-10 flex items-center justify-between">
                                    <span class="inline-flex items-center gap-1.5 rounded-lg bg-black/60 px-2.5 py-1 text-[10px] font-extrabold uppercase tracking-wider text-white backdrop-blur-md">
                                        <i class="ph-fill ph-film-strip text-escRed"></i> Snippet
                                    </span>
                                    <span class="inline-flex items-center gap-1 rounded-lg bg-black/60 px-2.5 py-1 text-[10px] font-bold text-white/90 backdrop-blur-md">
                                        <i class="ph ph-clock text-xs"></i> {{ sprintf('%02d:%02d', intdiv($webinar->duration_minutes, 60), $webinar->duration_minutes % 60) }}
                                    </span>
                                </div>

                                <!-- Center Video Play Badge -->
                                <div class="relative z-10 flex h-full items-center justify-center -mt-4">
                                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-white/20 text-white shadow-lg backdrop-blur-md border border-white/40">
                                        <i class="ph-fill ph-play text-2xl ml-1"></i>
                                    </div>
                                </div>

                                <!-- Bottom Overlay Info -->
                                <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/80 to-transparent p-3 text-left">
                                    <div class="flex items-center justify-between gap-2 text-[10px] font-bold uppercase tracking-wider">
                                        <span class="text-red-300">{{ $webinar->speciality?->name ?: 'Speciality TBA' }}</span>
                                        <span class="inline-flex items-center gap-1 text-blue-200"><i class="ph-fill ph-map-pin"></i>{{ $webinar->country ?: 'Country TBA' }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Card Body -->
                            <div class="flex flex-1 flex-col p-5">
                                <h3 class="text-sm font-black leading-snug text-slate-900 line-clamp-2 min-h-10">
                                    {{ $webinar->title }}
                                </h3>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    <span class="inline-flex items-center gap-1 rounded-lg bg-blue-50 px-2.5 py-1 text-[10px] font-bold text-escBlue"><i class="ph-fill ph-map-pin"></i>{{ $webinar->country ?: 'Country TBA' }}</span>
                                    <span class="inline-flex items-center gap-1 rounded-lg bg-red-50 px-2.5 py-1 text-[10px] font-bold text-escRed"><i class="ph ph-stethoscope"></i>{{ $webinar->speciality?->name ?: 'Speciality TBA' }}</span>
                                </div>
                                <div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-3.5 text-xs text-slate-500">
                                    <span class="inline-flex items-center gap-1.5 font-medium">
                                        <i class="ph ph-calendar text-slate-400"></i>
                                        {{ $webinar->scheduled_at?->format('d M Y') }}
                                    </span>
                                    <span class="inline-flex items-center gap-1 rounded-md bg-slate-100 px-2 py-0.5 text-[10px] font-bold text-slate-600">
                                        Highlights
                                    </span>
                                </div>
                            </div>
                        </button>
                    @empty
                        <div class="col-span-full rounded-3xl border border-dashed border-slate-300 bg-white/60 p-10 text-center text-sm text-slate-500">
                            <i class="ph ph-film-slate text-4xl text-slate-400 mb-3 block"></i>
                            Snippet videos will appear here once published by the session organizers.
                        </div>
                    @endforelse
                </div>
            </section>

            <!-- RIGHT COLUMN: POST-READ MATERIAL -->
            <section id="post-read-material" class="lg:col-span-4">
                <div class="mb-4 border-b border-slate-200 pb-4">
                    <div>
                        <div class="flex items-center gap-2.5">
                            <h2 class="text-lg font-black uppercase tracking-wide text-escBlue">Post-read Material</h2>
                        </div>
                        <div class="mt-2 h-0.5 w-12 bg-escBlue"></div>
                        <p class="mt-2 text-xs text-slate-500">Access presentations and resources from completed webinars.</p>
                    </div>
                </div>

                <div class="space-y-3">
                    @forelse($materialWebinars as $webinar)
                        @php
                            $materialSize = 'On demand';
                            if ($webinar->post_read_file_path && \Storage::disk('public')->exists($webinar->post_read_file_path)) {
                                $bytes = \Storage::disk('public')->size($webinar->post_read_file_path);
                                $materialSize = number_format($bytes / 1048576, 1).' MB';
                            }
                        @endphp
                        <article class="flex items-center gap-3 rounded-xl border border-slate-200 bg-white p-3 shadow-sm transition hover:border-red-200 hover:shadow-md sm:p-4">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full border border-slate-200 bg-slate-50 text-2xl text-escRed">
                                <i class="ph ph-file-pdf"></i>
                                <span class="sr-only">PDF document</span>
                            </span>

                            <div class="min-w-0 flex-1">
                                <h3 class="truncate text-xs font-extrabold text-slate-800" title="{{ $webinar->title }}">{{ $webinar->title }}</h3>
                                <p class="mt-1 text-[10px] font-semibold text-slate-500">
                                    PDF <span class="mx-1 text-slate-300">•</span> {{ $materialSize }}
                                </p>
                                <p class="mt-0.5 truncate text-[9px] font-bold text-escBlue">{{ $webinar->country }} · {{ $webinar->scheduled_at?->format('d M Y') }}</p>
                            </div>

                            <a href="{{ route('webinar.post_read.download', $webinar) }}"
                               class="inline-flex shrink-0 items-center justify-center gap-1.5 rounded-md border border-escRed bg-white px-3 py-2 text-[9px] font-black uppercase tracking-wide text-escRed transition hover:bg-escRed hover:text-white"
                               aria-label="Download {{ $webinar->title }} PDF">
                                <i class="ph-bold ph-download-simple text-sm"></i>
                                <span class="hidden sm:inline">Download</span>
                            </a>
                        </article>
                    @empty
                        <div class="rounded-xl border border-dashed border-slate-300 bg-white/60 p-8 text-center text-sm text-slate-500">
                            <i class="ph ph-file-pdf text-4xl text-slate-400 mb-3 block"></i>
                            Post-read presentations will be available here after each session concludes.
                        </div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
    <div class="asia-wave"></div>
</main>

<div id="snippet-player-modal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-950/85 p-4" role="dialog" aria-modal="true" aria-labelledby="snippet-player-title">
    <div class="w-full max-w-5xl overflow-hidden rounded-2xl bg-white shadow-2xl">
        <div class="flex items-center justify-between gap-4 border-b border-slate-200 px-5 py-4">
            <h2 id="snippet-player-title" class="truncate text-sm font-black text-escBlue">Session Video</h2>
            <button type="button" id="snippet-player-close" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-slate-100 text-xl text-slate-700 hover:bg-red-50 hover:text-escRed" aria-label="Close video player"><i class="ph ph-x"></i></button>
        </div>
        <div class="aspect-video bg-black">
            <iframe id="snippet-player-iframe" class="hidden h-full w-full" title="Session video player" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
            <video id="snippet-player-video" class="hidden h-full w-full" controls playsinline></video>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('snippet-player-modal');
    const title = document.getElementById('snippet-player-title');
    const frame = document.getElementById('snippet-player-iframe');
    const video = document.getElementById('snippet-player-video');

    const closePlayer = () => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        frame.src = '';
        video.pause();
        video.removeAttribute('src');
        frame.classList.add('hidden');
        video.classList.add('hidden');
    };

    document.querySelectorAll('.snippet-player-trigger').forEach(button => {
        button.addEventListener('click', () => {
            title.textContent = button.dataset.videoTitle || 'Session Video';
            modal.classList.remove('hidden');
            modal.classList.add('flex');

            if (button.dataset.videoType === 'iframe') {
                frame.src = button.dataset.videoUrl + (button.dataset.videoUrl.includes('?') ? '&autoplay=1' : '?autoplay=1');
                frame.classList.remove('hidden');
            } else {
                video.src = button.dataset.videoUrl;
                video.classList.remove('hidden');
                video.play().catch(() => {});
            }
        });
    });

    document.getElementById('snippet-player-close').addEventListener('click', closePlayer);
    modal.addEventListener('click', event => { if (event.target === modal) closePlayer(); });
    document.addEventListener('keydown', event => { if (event.key === 'Escape') closePlayer(); });
});
</script>
@endpush
@endsection
