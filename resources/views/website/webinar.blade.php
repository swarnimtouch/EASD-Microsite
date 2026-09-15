@extends('layouts.website', ['title' => $webinar->title, 'bodyClass' => 'bg-[#F4F6F8] font-sans antialiased text-slate-800'])
@if(false)
    <!DOCTYPE html>
<html lang="en">
<head>
    <script>
        window.FontAwesomeConfig = {
            autoReplaceSvg: 'nest'
        };
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js" crossorigin="anonymous"
            referrerpolicy="no-referrer"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $webinar->title }} — PULCE Connect 2026</title>
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
                    fontFamily: {sans: ['"Plus Jakarta Sans"', 'sans-serif']},
                    colors: {
                        escRed: '#BE1E2D',
                        escBlue: '#1D438A',
                        escLight: '#F8F9FA'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-[#F4F6F8] font-sans antialiased text-slate-800">

<!-- Top Brand Bar -->
<header
    class="bg-white border-b border-slate-200 py-3 px-6 shadow-sm flex items-center justify-between sticky top-0 z-50">
    <div class="flex items-center gap-6">
        <div class="flex items-center gap-2">
            <i class="ph-fill ph-heart text-escRed text-xl"></i>
            <span class="text-lg font-black text-slate-900">EASD</span>
        </div>
        <div class="flex items-center gap-3 border-l border-slate-200 pl-6">
            <div class="text-xl font-black italic tracking-tighter flex items-center">
                <span class="text-escBlue">P</span>
                <div
                    class="w-5 h-5 rounded-full border-2 border-escRed flex items-center justify-center -mx-0.5 bg-white">
                    <i class="ph-bold ph-pulse text-escRed text-[8px]"></i>
                </div>
                <span class="text-escBlue">LCE</span>
            </div>
            <span class="text-lg font-black text-escRed">2026</span>
        </div>
    </div>
    <div class="flex items-center gap-4">
        <div
            class="flex items-center gap-2 px-3 py-1 rounded-full {{ $isLive ? 'bg-escRed/10 text-escRed animate-pulse' : 'bg-slate-100 text-slate-500' }}">
            <span class="w-2 h-2 rounded-full {{ $isLive ? 'bg-escRed' : 'bg-slate-400' }}"></span>
            <span
                class="text-[10px] font-black uppercase tracking-widest">{{ $isLive ? 'Live Session' : ($isUpcoming ? 'Upcoming Session' : 'Completed Session') }}</span>
        </div>
        <div class="h-8 w-px bg-slate-200"></div>
        <div class="flex items-center gap-2">
            <img src="{{ $doctor->profile_image }}" class="w-8 h-8 rounded-full border border-slate-200"
                 alt="{{ $doctor->name }}">
            <span class="text-xs font-bold text-slate-600">{{ $doctor->name }}</span>
        </div>
    </div>
</header>
@endif
@section('content')

    <main class="max-w-[1400px] mx-auto p-6 grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Left: Webinar Content -->
        <div class="{{ $isUpcoming ? 'lg:col-span-8' : 'lg:col-span-12' }} space-y-6">
            <section class="rounded-[32px] border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex flex-wrap items-center gap-2">
                    @if($webinar->speciality)
                        <span
                            class="rounded-full bg-escBlue/10 px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-escBlue">{{ $webinar->speciality->name }}</span>
                    @endif
                    @if($webinar->type)
                        <span
                            class="rounded-full bg-red-50 px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-escRed">{{ $webinar->type }}</span>
                    @endif
                </div>
                <h1 class="mt-4 text-2xl font-black text-slate-900 md:text-3xl">{{ $webinar->title }}</h1>
                <div class="mt-4 flex flex-wrap gap-x-6 gap-y-2 text-xs font-semibold text-slate-500">
                    @if($webinar->scheduled_at)
                        <span class="flex items-center gap-2"><i class="ph ph-calendar-blank text-lg text-escRed"></i>{{ $webinar->scheduled_at->format('d F Y, h:i A') }}</span>
                    @endif
                    @if($webinar->timezone_label)
                        <span class="flex items-center gap-2"><i class="ph ph-globe text-lg text-escRed"></i>{{ $webinar->timezone_label }}</span>
                    @endif
                    @if($webinar->duration_minutes)
                        <span class="flex items-center gap-2"><i class="ph ph-clock text-lg text-escRed"></i>{{ $webinar->duration_minutes }} minutes</span>
                    @endif
                </div>
            </section>

            @if($playbackUrl)
                <!-- Video Player -->
                <div class="bg-slate-900 rounded-[32px] overflow-hidden shadow-2xl relative aspect-video group">
                    @if($youtubeEmbedUrl)
                        <iframe class="absolute inset-0 h-full w-full" src="{{ $youtubeEmbedUrl }}"
                                title="{{ $webinar->title }}"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                    @else
                        <img class="w-full h-full object-cover opacity-50" src="{{ $webinar->cover_image }}"
                             alt="{{ $webinar->title }}"/>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="text-center text-white space-y-4">
                                <h2 class="text-4xl font-black tracking-tight">WELCOME</h2>
                                <p class="text-white/60 font-medium">{{ $webinar->title }}</p>
                                <div class="flex items-center justify-center gap-3 pt-4">
                                    <a href="{{ $playbackUrl ?: '#' }}" @if($playbackUrl) target="_blank" rel="noopener"
                                       @endif class="w-16 h-16 rounded-full bg-white text-escBlue flex items-center justify-center hover:scale-110 transition-transform">
                                        <i class="ph-fill ph-play text-2xl ml-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            @if($webinar->description)
                <section class="rounded-[32px] border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="flex items-center gap-2 text-sm font-bold uppercase tracking-widest text-escBlue"><i
                            class="ph ph-info"></i> About this Webinar</h3>
                    <p class="mt-4 whitespace-pre-line text-sm leading-7 text-slate-600">{{ $webinar->description }}</p>
                </section>
            @endif

            @if($webinar->people->isNotEmpty())
                <section class="bg-white p-6 rounded-[32px] border border-slate-200 shadow-sm">
                    <div class="mb-5 flex items-center justify-between"><h3
                            class="text-sm font-bold text-escBlue uppercase tracking-widest flex items-center gap-2"><i
                                class="ph ph-users-three"></i> Faculty</h3>@if($webinar->speciality)
                            <span
                                class="rounded-full bg-escBlue/10 px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-escBlue">{{ $webinar->speciality->name }}</span>
                        @endif</div>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        @foreach($webinar->people as $person)
                            <article class="flex gap-4 rounded-2xl border border-slate-100 bg-slate-50 p-4">
                                @if($person->image_path)
                                    <img src="{{ $person->image_url }}" alt="{{ $person->name }}"
                                         class="h-16 w-16 shrink-0 rounded-2xl object-cover">
                                @endif
                                <div class="min-w-0"><span
                                        class="text-[9px] font-black uppercase tracking-widest {{ $person->role === 'moderator' ? 'text-escBlue' : 'text-escRed' }}">{{ $person->role }}</span>
                                    <h4 class="truncate text-sm font-black text-slate-900">{{ $person->name }}</h4>@if($person->designation)
                                        <p class="mt-1 text-[10px] font-semibold text-slate-500">{{ $person->designation }}</p>
                                    @endif @if($person->bio)
                                        <p class="mt-2 line-clamp-2 text-[10px] leading-relaxed text-slate-500">{{ $person->bio }}</p>
                                    @endif</div>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endif

            @if($canComment || $webinar->comments->isNotEmpty())
                <section id="live-discussion" data-webinar-comments data-current-user-id="{{ $doctor->id }}"
                         data-comments-channel="comments.webinar.{{ $webinar->id }}"
                         data-upvote-base="{{ url('/webinar/'.$webinar->id.'/comments') }}"
                         class="rounded-[32px] border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between gap-4">
                        <h3 class="flex items-center gap-2 text-sm font-bold uppercase tracking-widest text-escBlue"><i
                                class="ph ph-chat-circle-dots"></i>{{ $isLive ? 'Live Interaction' : 'Webinar Discussion' }}
                        </h3>
                        <span class="text-[10px] font-bold text-slate-400"><span
                                data-webinar-comments-count>{{ $webinar->comments->count() }}</span> questions</span>
                    </div>

                    @if(session('comment_success'))
                        <div
                            class="mt-4 rounded-xl border border-green-200 bg-green-50 p-3 text-xs font-bold text-green-700">
                            {{ session('comment_success') }}
                        </div>
                    @endif
                    @if(session('comment_error'))
                        <div
                            class="mt-4 rounded-xl border border-red-200 bg-red-50 p-3 text-xs font-bold text-escRed">
                            {{ session('comment_error') }}
                        </div>
                    @endif

                    <div data-webinar-comments-list
                         class="mt-5 max-h-80 space-y-4 overflow-y-auto pr-2 {{ $webinar->comments->isEmpty() ? 'hidden' : '' }}">
                        @foreach($webinar->comments as $comment)
                            <article data-comment-id="{{ $comment->id }}" data-upvotes="{{ $comment->upvotes_count }}"
                                     class="webinar-comment flex gap-3">
                                <img src="{{ $comment->user->profile_image }}" alt="{{ $comment->user->name }}"
                                     class="h-9 w-9 shrink-0 rounded-full border border-slate-200 object-cover">
                                <div class="min-w-0 flex-1">
                                    <div class="rounded-2xl rounded-tl-none bg-slate-50 p-4">
                                        <div class="flex flex-wrap items-center justify-between gap-2"><p
                                                class="text-xs font-black text-slate-900">{{ $comment->user->name }}</p>
                                            <time
                                                class="text-[9px] font-semibold text-slate-400">{{ $comment->created_at->format('h:i A') }}</time>
                                        </div>
                                        <p class="mt-1 whitespace-pre-line text-xs leading-6 text-slate-600">{{ $comment->body }}</p>
                                        <div class="mt-3 flex items-center gap-4">
                                            <button type="button" data-comment-upvote="{{ $comment->id }}"
                                                    class="flex items-center gap-1.5 text-[10px] font-bold {{ $comment->upvotes->contains('user_id', $doctor->id) ? 'text-escRed' : 'text-slate-400' }}">
                                                <i class="ph-fill ph-arrow-fat-up"></i><span
                                                    data-upvote-count>{{ $comment->upvotes_count }}</span> Upvote
                                            </button>
                                            @if($canComment)
                                                <button type="button" data-comment-reply="{{ $comment->id }}"
                                                        data-comment-name="{{ $comment->user->name }}"
                                                        class="flex items-center gap-1.5 text-[10px] font-bold text-slate-400 hover:text-escBlue">
                                                    <i class="ph ph-arrow-bend-up-left"></i> Reply
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                    <div data-replies-for="{{ $comment->id }}"
                                         class="ml-5 mt-3 space-y-3 border-l-2 border-slate-100 pl-4">
                                        @foreach($comment->replies as $reply)
                                            <article data-comment-id="{{ $reply->id }}" class="flex gap-3">
                                                <img
                                                    src="{{ $reply->user->profile_image }}"
                                                    alt="{{ $reply->user->name }}"
                                                    class="h-8 w-8 shrink-0 rounded-full border border-slate-200 object-cover">
                                                <div
                                                    class="min-w-0 flex-1 rounded-2xl rounded-tl-none bg-blue-50/60 p-3">
                                                    <div class="flex items-center justify-between gap-2">
                                                        <p
                                                            class="text-[11px] font-black text-slate-900">{{ $reply->user->name }}</p>
                                                        <time
                                                            class="text-[9px] text-slate-400">{{ $reply->created_at->format('h:i A') }}</time>
                                                    </div>
                                                    <p class="mt-1 whitespace-pre-line text-xs leading-5 text-slate-600">{{ $reply->body }}</p>
                                                    <button type="button" data-comment-upvote="{{ $reply->id }}"
                                                            class="mt-2 flex items-center gap-1.5 text-[10px] font-bold {{ $reply->upvotes->contains('user_id', $doctor->id) ? 'text-escRed' : 'text-slate-400' }}">
                                                        <i class="ph-fill ph-arrow-fat-up"></i>
                                                        <span
                                                            data-upvote-count>{{ $reply->upvotes_count }}</span> Upvote
                                                    </button>
                                                </div>
                                            </article>
                                        @endforeach
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    @if($canComment)
                        <form data-webinar-comment-form method="POST"
                              action="{{ route('webinar.comments.store', $webinar) }}" class="mt-5">
                            @csrf
                            <input data-webinar-comment-parent type="hidden" name="parent_id" value="">
                            <div data-webinar-replying
                                 class="mb-2 hidden items-center justify-between rounded-xl bg-blue-50 px-3 py-2 text-[10px] font-bold text-escBlue">
                                <span>Replying to <strong data-webinar-replying-name></strong></span>
                                <button data-webinar-reply-cancel type="button" class="text-slate-400"><i
                                        class="ph ph-x"></i></button>
                            </div>
                            <label for="webinar-question" class="mb-2 block text-xs font-bold text-slate-600">Ask a
                                question or reply</label>
                            <div class="flex gap-3">
                                <textarea data-webinar-comment-input id="webinar-question" name="body" rows="2"
                                          maxlength="1000" required placeholder="Type your question here..."
                                          class="min-w-0 flex-1 resize-none rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-escRed focus:ring-4 focus:ring-red-50">{{ old('body') }}</textarea>
                                <button type="submit"
                                        class="self-stretch rounded-2xl bg-escRed px-5 text-white transition hover:bg-red-700"
                                        aria-label="Submit question">
                                    <i class="ph-bold ph-paper-plane-right text-xl"></i>
                                </button>
                            </div>
                            @error('body')
                            <p class="mt-2 text-xs font-bold text-escRed">{{ $message }}</p>
                            @enderror
                        </form>
                    @endif
                </section>
            @endif

            @if($webinar->certificate_template_path)
                <section class="rounded-[32px] border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex flex-col justify-between gap-5 sm:flex-row sm:items-center">
                        <div class="flex items-center gap-4">
                            <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl {{ $hasCommented ? 'bg-green-50 text-green-600' : 'bg-slate-100 text-slate-400' }}"><i class="ph ph-certificate text-3xl"></i></span>
                            <div>
                                <h3 class="text-sm font-black uppercase tracking-wider text-escBlue">Participation Certificate</h3>
                                <p class="mt-1 text-xs leading-5 text-slate-500">{{ $hasCommented ? 'Thank you for participating. Your personalized certificate is ready.' : 'Submit a comment or question in this webinar to unlock your certificate.' }}</p>
                            </div>
                        </div>
                        @if($hasCommented)
                            <a href="{{ route('webinar.certificate', $webinar) }}" class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-escRed px-5 py-3 text-xs font-extrabold text-white"><i class="ph ph-download-simple text-lg"></i>Download Certificate</a>
                        @else
                            <span class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-slate-100 px-5 py-3 text-xs font-extrabold text-slate-400"><i class="ph ph-lock-key text-lg"></i>Certificate Locked</span>
                        @endif
                    </div>
                    @if(session('certificate_error'))
                        <p class="mt-4 rounded-xl border border-red-200 bg-red-50 p-3 text-xs font-bold text-escRed">{{ session('certificate_error') }}</p>
                    @endif
                </section>
            @endif

            @if($webinar->pre_read_url || $webinar->post_read_url)
                <section
                    class="grid grid-cols-1 gap-4 {{ $webinar->pre_read_url && $webinar->post_read_url ? 'md:grid-cols-2' : '' }}">
                    @if($webinar->pre_read_url)
                        <a href="{{ $webinar->pre_read_url }}" target="_blank" rel="noopener"
                           class="block bg-white p-6 rounded-[32px] border border-slate-200 shadow-sm group hover:border-escRed transition-all cursor-pointer">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="w-12 h-12 rounded-2xl bg-escRed/10 text-escRed flex items-center justify-center">
                                        <i class="ph ph-file-pdf text-2xl"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-bold text-slate-900">Pre-read Material</h4>
                                        <p class="text-[10px] font-bold text-slate-400 uppercase mt-0.5 tracking-wider">
                                            Open supporting material</p>
                                    </div>
                                </div>
                                <i class="ph ph-download-simple text-xl text-slate-400 group-hover:text-escRed transition-colors"></i>
                            </div>
                        </a>
                    @endif
                    @if($webinar->post_read_url)
                        <a href="{{ $webinar->post_read_url }}" target="_blank" rel="noopener"
                           class="block bg-white p-6 rounded-[32px] border border-slate-200 shadow-sm group hover:border-escBlue transition-all cursor-pointer">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="w-12 h-12 rounded-2xl bg-escBlue/10 text-escBlue flex items-center justify-center">
                                        <i class="ph ph-folder-open text-2xl"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-bold text-slate-900">Access Post-read</h4>
                                        <p class="text-[10px] font-bold text-slate-400 uppercase mt-0.5 tracking-wider">
                                            Open supporting material</p>
                                    </div>
                                </div>
                                <i class="ph ph-arrow-circle-right text-xl text-slate-400 group-hover:text-escBlue transition-colors"></i>
                            </div>
                        </a>
                    @endif
                </section>
            @endif
        </div>

        @if($isUpcoming)
            <!-- Right: Countdown & Quick Access -->
            <div class="lg:col-span-4 space-y-6">
                <!-- Countdown Card -->
                <div id="webinar-countdown"
                     class="bg-white p-8 rounded-[40px] border border-slate-200 shadow-xl shadow-slate-200/50 space-y-6">
                    <div class="text-center space-y-1">
                        <h4 class="text-[10px] font-black text-escRed uppercase tracking-[0.2em]">Next Scientific
                            Session Begins In</h4>
                        <div class="flex items-center justify-center gap-4 pt-4">
                            <div class="text-center">
                                <span id="countdown-days"
                                      class="text-3xl font-black text-slate-900 leading-none">00</span>
                                <p class="text-[8px] font-bold text-slate-400 uppercase mt-1">Days</p>
                            </div>
                            <span class="text-2xl font-black text-slate-300 -mt-4">:</span>
                            <div class="text-center">
                                <span id="countdown-hours"
                                      class="text-3xl font-black text-slate-900 leading-none">00</span>
                                <p class="text-[8px] font-bold text-slate-400 uppercase mt-1">Hours</p>
                            </div>
                            <span class="text-2xl font-black text-slate-300 -mt-4">:</span>
                            <div class="text-center">
                                <span id="countdown-minutes"
                                      class="text-3xl font-black text-slate-900 leading-none">00</span>
                                <p class="text-[8px] font-bold text-slate-400 uppercase mt-1">Mins</p>
                            </div>
                        </div>
                    </div>
                    <div class="h-px bg-slate-100"></div>
                    <div class="flex items-center justify-between text-xs font-bold text-slate-600 px-2">
                        <div class="flex items-center gap-2"><i
                                class="ph ph-calendar text-escRed"></i> {{ $webinar->scheduled_at?->format('d F Y') ?: 'Available now' }}
                        </div>
                        @if($webinar->timezone_label)
                            <div class="flex items-center gap-2">
                                <i class="ph ph-globe text-escRed"></i> {{ $webinar->timezone_label }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </main>

    @if($isUpcoming && $webinar->scheduled_at)
        <script>
            (() => {
                const startsAt = new Date(@json($webinar->scheduled_at->toIso8601String())).getTime();
                const renderCountdown = () => {
                    const remaining = startsAt - Date.now();
                    if (remaining <= 0) {
                        document.getElementById('webinar-countdown')?.remove();
                        return false;
                    }
                    document.getElementById('countdown-days').textContent = String(Math.floor(remaining / 86400000)).padStart(2, '0');
                    document.getElementById('countdown-hours').textContent = String(Math.floor((remaining % 86400000) / 3600000)).padStart(2, '0');
                    document.getElementById('countdown-minutes').textContent = String(Math.floor((remaining % 3600000) / 60000)).padStart(2, '0');
                    return true;
                };
                if (renderCountdown()) {
                    const timer = window.setInterval(() => {
                        if (!renderCountdown()) window.clearInterval(timer);
                    }, 30000);
                }
            })();
        </script>
@endif
@endsection
