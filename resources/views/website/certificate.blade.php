@extends('layouts.website', ['title' => 'Certificate of Participation', 'bodyClass' => 'bg-slate-50 font-sans antialiased text-slate-800'])

@section('content')
<main class="dot-pattern relative min-h-[calc(100vh-140px)] overflow-hidden px-4 py-8 sm:px-6 md:py-10">
    <div class="relative z-10 mx-auto max-w-7xl">
        <!-- Top Navigation -->
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-xs font-extrabold text-escBlue transition hover:text-blue-700">
                <i class="ph-bold ph-arrow-left text-sm"></i> Back to Dashboard
            </a>
            <div class="flex items-center gap-3">
                <span class="hidden text-xs font-semibold text-slate-500 sm:inline-block">Logged in as: <strong class="text-escBlue">{{ $doctor->name }}</strong></span>
                <a href="{{ route('logout') }}" class="inline-flex items-center gap-1.5 rounded-lg border border-escRed/40 bg-white px-3.5 py-1.5 text-xs font-extrabold text-escRed shadow-sm transition hover:bg-escRed hover:text-white">
                    <i class="ph ph-sign-out"></i> Logout
                </a>
            </div>
        </div>

        <!-- Notification Alerts -->
        @if(session('comment_success') || session('success'))
            <div class="mb-6 flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-xs font-bold text-emerald-800 shadow-sm">
                <i class="ph-fill ph-check-circle text-xl text-emerald-600 shrink-0"></i>
                <div class="flex-1">{{ session('comment_success') ?: session('success') }}</div>
            </div>
        @endif

        @if(session('comment_error') || session('certificate_error'))
            <div class="mb-6 flex items-center gap-3 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-xs font-bold text-escRed shadow-sm">
                <i class="ph-fill ph-warning-circle text-xl text-escRed shrink-0"></i>
                <div class="flex-1">{{ session('comment_error') ?: session('certificate_error') }}</div>
            </div>
        @endif

        @if(isset($errors) && $errors->any())
            <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-xs font-bold text-escRed shadow-sm">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Section Heading -->
        <div class="mb-6">
            <h1 class="text-xl font-black uppercase tracking-wide text-escBlue sm:text-2xl">Certificate of Participation</h1>
            <div class="mt-2 h-1 w-14 rounded-full bg-escRed"></div>
            <p class="mt-2 text-xs text-slate-500">View your personalized certificate below. Complete the comment requirement to download the official PDF.</p>
        </div>

        <div class="grid items-start gap-8 lg:grid-cols-[1.5fr_1fr]">
@php
    $hasTemplate = $webinar->certificate_template_path && \Storage::disk('public')->exists($webinar->certificate_template_path);
    $ext = $hasTemplate ? strtolower(pathinfo($webinar->certificate_template_path, PATHINFO_EXTENSION)) : '';
    $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'webp']);
    $templateUrl = $hasTemplate ? url('storage/' . ltrim($webinar->certificate_template_path, '/')) : null;
    
    $aspectRatio = '842 / 595';
    $totalW = 842.0;
    $totalH = 595.0;
    if ($hasTemplate && $isImage) {
        $imgInfo = @getimagesize(\Storage::disk('public')->path($webinar->certificate_template_path));
        if ($imgInfo && !empty($imgInfo[0]) && !empty($imgInfo[1])) {
            $pixelW = (float) $imgInfo[0];
            $pixelH = (float) $imgInfo[1];
            $aspectRatio = $pixelW . ' / ' . $pixelH;
            if ($pixelW >= $pixelH) {
                $totalW = 842.0;
                $totalH = round((842.0 / $pixelW) * $pixelH, 2);
            } else {
                $totalH = 842.0;
                $totalW = round((842.0 / $pixelH) * $pixelW, 2);
            }
        }
    }

    $yPercent = (($webinar->certificate_name_y ?? ($totalH * 0.49)) / $totalH) * 100;
    $isCentered = is_null($webinar->certificate_name_x);
    $xPercent = $isCentered ? 50 : (($webinar->certificate_name_x / $totalW) * 100);
    $fontSize = $webinar->certificate_font_size ?: 28;
    $fontColor = $webinar->certificate_font_color ?: '#8e5f16';
    $isLightColor = in_array(strtolower(trim($fontColor)), ['#ffffff', '#fff', '#f8fafc', '#f1f5f9', '#fefefe']);
    $textShadow = $isLightColor ? '0 1px 3px rgba(0,0,0,0.6)' : '0 1px 2px rgba(255,255,255,0.85)';
@endphp

            <!-- LEFT COLUMN: Certificate Snapshot Preview (Displays Actual Template) -->
            <section class="relative rounded-3xl border border-slate-200/80 bg-white p-4 shadow-sm sm:p-6 select-none">
                <!-- Preview watermark/pill badge -->
                <div class="mb-4 flex items-center justify-between border-b border-slate-100 pb-3">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-3 py-1 text-[10px] font-extrabold uppercase tracking-wider text-slate-600">
                        <i class="ph ph-certificate text-xs text-escRed"></i> Official Certificate Preview
                    </span>
                    <span class="text-[10px] font-semibold text-slate-400">
                        {{ $hasTemplate ? ($isImage ? strtoupper($ext).' Template' : 'PDF Template') : 'Standard Template' }}
                    </span>
                </div>

                @if($hasTemplate && $isImage)
                    <!-- IMAGE BACKGROUND TEMPLATE WITH DOCTOR NAME AT (X, Y) -->
                    <div class="relative mx-auto w-full overflow-hidden rounded-2xl border-2 border-slate-200 shadow-md bg-slate-50"
                         style="max-width: {{ $totalW < $totalH ? '420px' : '640px' }}; aspect-ratio: {{ $aspectRatio }};">
                        <img src="{{ route('webinar.certificate_template_file', $webinar) }}" alt="Certificate Template" class="absolute inset-0 h-full w-full object-contain pointer-events-none" onerror="if(!this.dataset.fallback){this.dataset.fallback='1'; this.src='/storage/{{ ltrim($webinar->certificate_template_path, '/') }}';}">

                        <!-- Authenticated Doctor Name Overlay at X, Y -->
                        <div class="absolute z-10 pointer-events-none font-serif italic font-black tracking-wide whitespace-nowrap"
                             style="bottom: {{ $yPercent }}%; left: {{ $xPercent }}%; transform: translate(-50%, 50%); font-size: clamp(15px, 3.2vw, {{ $fontSize }}px); color: {{ $fontColor }}; text-shadow: {{ $textShadow }};">
                            {{ $doctor->name }}
                        </div>

                        @if(!$hasCommented)
                            <div class="absolute inset-x-0 bottom-3 flex justify-center pointer-events-none">
                                <span class="rounded-full bg-slate-900/85 px-4 py-1.5 text-xs font-bold text-white shadow-lg backdrop-blur-sm">
                                    <i class="ph ph-lock mr-1.5"></i> Preview Mode · Submit Comment Below to Download Official PDF
                                </span>
                            </div>
                        @endif
                    </div>
                @elseif($hasTemplate && !$isImage)
                    <!-- PDF BACKGROUND TEMPLATE (Live Generated with Doctor Name at X,Y) -->
                    <div class="relative mx-auto w-full aspect-[842/595] overflow-hidden rounded-2xl border-2 border-slate-200 shadow-md bg-slate-50">
                        <iframe src="{{ route('webinar.certificate', ['webinar' => $webinar, 'inline' => 1]) }}#toolbar=0&navpanes=0" class="absolute inset-0 h-full w-full border-0 rounded-2xl" title="Certificate PDF Preview"></iframe>

                        @if(!$hasCommented)
                            <div class="absolute inset-x-0 bottom-3 flex justify-center pointer-events-none">
                                <span class="rounded-full bg-slate-900/85 px-4 py-1.5 text-xs font-bold text-white shadow-lg backdrop-blur-sm">
                                    <i class="ph ph-lock mr-1.5"></i> Preview Mode · Submit Comment Below to Unlock Official PDF
                                </span>
                            </div>
                        @endif
                    </div>
                @else
                    <!-- Fallback Elegant Certificate Mock Paper -->
                    <div class="pointer-events-none relative overflow-hidden rounded-[28px] border-2 border-slate-200 bg-gradient-to-b from-white via-[#fafcff] to-white px-6 py-10 text-center shadow-inner sm:px-10 sm:py-12">
                        <div class="absolute -left-6 -top-6 h-24 w-24 rounded-full bg-escBlue/90 shadow-md"></div>
                        <div class="absolute -bottom-6 -right-6 h-24 w-24 rounded-full bg-escRed/90 shadow-md"></div>
                        <div class="pointer-events-none absolute inset-3 rounded-[20px] border border-dashed border-slate-300/80"></div>

                        <div class="relative z-10 flex items-center justify-between gap-4 px-2">
                            <img src="{{ asset('assets/images/branding/easd-logo.png') }}" alt="EASD Logo" class="h-10 w-auto object-contain sm:h-12">
                            <img src="{{ asset('assets/images/branding/pulce-logo.png') }}" alt="PULCE Connect 2026" class="h-12 w-auto object-contain sm:h-16">
                            <img src="{{ asset('assets/images/branding/hetero-logo.png') }}" alt="Hetero Logo" class="h-10 w-auto object-contain sm:h-12">
                        </div>

                        <div class="relative z-10 mx-auto mt-8 max-w-xl">
                            <h2 class="text-2xl font-black tracking-wider text-escBlue sm:text-3xl">CERTIFICATE</h2>
                            <p class="mt-0.5 text-[11px] font-black uppercase tracking-[0.25em] text-escBlue">of Participation</p>

                            <p class="mt-6 text-xs font-medium text-slate-500">This is to certify that</p>
                            <p class="mt-3 font-serif text-2xl italic tracking-wide text-escBlue sm:text-3xl">{{ $doctor->name }}</p>

                            <p class="mt-6 text-xs font-medium text-slate-500">has participated in</p>
                            <p class="mt-2 text-base font-black text-escRed sm:text-lg">PULCE Connect 2026 | Cardio-Renal-Metabolic Educational Series</p>

                            <p class="mt-6 text-xs font-bold text-slate-700">{{ $webinar->title }}</p>
                            @if($webinar->scheduled_at)
                                <p class="mt-1 text-[11px] text-slate-500">{{ $webinar->scheduled_at->format('d F Y') }}</p>
                            @endif

                            <div class="mx-auto mt-10 grid max-w-md grid-cols-2 gap-12 sm:gap-20">
                                <div class="border-t border-slate-400 pt-2 text-center">
                                    <span class="block text-[10px] font-bold text-slate-600">Program Director</span>
                                    <span class="block text-[8px] text-slate-400">EASD Scientific Committee</span>
                                </div>
                                <div class="border-t border-slate-400 pt-2 text-center">
                                    <span class="block text-[10px] font-bold text-slate-600">Authorized Signatory</span>
                                    <span class="block text-[8px] text-slate-400">PULCE Connect 2026</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <p class="mt-3 text-center text-[11px] text-slate-400">
                    <i class="ph ph-shield-check mr-1 text-emerald-600"></i> Personalized for <strong>{{ $doctor->name }}</strong>. Complete Step 1 on the right to download your official PDF certificate.
                </p>
            </section>

            <!-- RIGHT COLUMN: Action Steps (Submit Comment Here & Download) -->
            <aside class="space-y-6">
                <!-- STEP 1: SUBMIT COMMENT -->
                <section class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-2">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-escRed text-[11px] font-black text-white">1</span>
                            <h2 class="text-sm font-black uppercase tracking-wide text-slate-900">Submit Comment <span class="text-escRed">*</span></h2>
                        </div>
                        @if($hasCommented)
                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-0.5 text-[10px] font-extrabold text-emerald-700">
                                <i class="ph-bold ph-check"></i> Verified
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-0.5 text-[10px] font-extrabold text-amber-800">
                                <i class="ph-bold ph-clock"></i> Required
                            </span>
                        @endif
                    </div>

                    @if($hasCommented)
                        <!-- Comment already submitted state -->
                        <div class="mt-4 rounded-2xl border border-emerald-200 bg-emerald-50/70 p-4">
                            <div class="flex items-center gap-3 text-emerald-800">
                                <i class="ph-fill ph-check-circle text-2xl text-emerald-600"></i>
                                <div>
                                    <strong class="block text-xs font-bold">Comment Submitted Successfully</strong>
                                    <p class="text-[11px] font-medium text-emerald-700">Participation requirement completed. Your certificate is unlocked.</p>
                                </div>
                            </div>
                            @if($latestComment)
                                <div class="mt-3 rounded-xl border border-emerald-200/80 bg-white/90 p-3 text-xs">
                                    <span class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Your Submitted Question / Comment:</span>
                                    <p class="mt-1 italic text-slate-700">“{{ $latestComment->body }}”</p>
                                    <span class="mt-1.5 block text-[10px] text-slate-400">{{ $latestComment->created_at?->diffForHumans() }}</span>
                                </div>
                            @endif
                        </div>
                    @else
                        <!-- In-page comment form right here! -->
                        <div class="mt-4">
                            <p class="text-xs leading-5 text-slate-600">
                                Enter your question, clinical feedback, or key takeaway from this webinar to immediately unlock your certificate.
                            </p>
                            <form method="POST" action="{{ route('webinar.comments.store', $webinar) }}" class="mt-4 space-y-3">
                                @csrf
                                <input type="hidden" name="redirect_to" value="certificate">
                                <div>
                                    <label for="certificate-comment-body" class="block text-[11px] font-bold text-slate-700 mb-1.5">
                                        Your Question or Comment:
                                    </label>
                                    <textarea id="certificate-comment-body" name="body" rows="3" required maxlength="1000" placeholder="Type your session takeaway or question here..." class="w-full rounded-xl border border-slate-200 p-3.5 text-xs text-slate-800 placeholder-slate-400 outline-none transition focus:border-escRed focus:ring-1 focus:ring-escRed"></textarea>
                                </div>
                                <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#b90009] to-[#e20b18] px-5 py-3.5 text-xs font-black uppercase tracking-wider text-white shadow-md transition hover:brightness-105 active:scale-[0.99]">
                                    <i class="ph-fill ph-paper-plane-tilt text-base"></i>
                                    Submit Comment &amp; Unlock Certificate
                                </button>
                            </form>
                            <p class="mt-2.5 text-[10px] text-slate-400">
                                <i class="ph ph-info mr-1"></i> Submitting this comment validates your session participation.
                            </p>
                        </div>
                    @endif
                </section>

                <!-- Step Divider Connector -->
                <div class="relative flex justify-center">
                    <div class="h-6 w-0.5 bg-slate-200"></div>
                </div>

                <!-- STEP 2: DOWNLOAD CERTIFICATE -->
                <section class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-2">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full {{ $hasCommented ? 'bg-escBlue text-white' : 'bg-slate-200 text-slate-600' }} text-[11px] font-black">2</span>
                            <h2 class="text-sm font-black uppercase tracking-wide text-slate-900">Download Certificate</h2>
                        </div>
                        @if($hasCommented)
                            <span class="inline-flex items-center gap-1 rounded-full bg-blue-100 px-2.5 py-0.5 text-[10px] font-extrabold text-escBlue">
                                Ready
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-0.5 text-[10px] font-extrabold text-slate-400">
                                Locked
                            </span>
                        @endif
                    </div>

                    <div class="mt-4">
                        @if($hasCommented)
                            <a href="{{ route('webinar.certificate', $webinar) }}" class="flex items-center justify-between rounded-2xl bg-gradient-to-r from-[#083b8f] to-[#1d5bb6] p-4 text-white shadow-md transition hover:brightness-105 hover:shadow-lg active:scale-[0.99]">
                                <div class="flex items-center gap-3.5">
                                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-white/15 text-2xl">
                                        <i class="ph ph-download-simple"></i>
                                    </span>
                                    <div>
                                        <strong class="block text-sm font-black">Download Certificate</strong>
                                        <small class="block text-[11px] text-white/80">Official PDF · Instant Download</small>
                                    </div>
                                </div>
                                <i class="ph-fill ph-check-circle text-2xl text-emerald-300"></i>
                            </a>
                            <p class="mt-3 text-[11px] text-slate-500">
                                <i class="ph ph-file-pdf mr-1 text-escRed"></i> High-quality printable PDF certificate personalized for <strong>{{ $doctor->name }}</strong>.
                            </p>
                        @else
                            <div class="flex items-center justify-between rounded-2xl border border-slate-200 bg-slate-100/90 p-4 text-slate-400">
                                <div class="flex items-center gap-3.5">
                                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-slate-200 text-2xl text-slate-400">
                                        <i class="ph ph-download-simple"></i>
                                    </span>
                                    <div>
                                        <strong class="block text-sm font-bold text-slate-500">Download Certificate</strong>
                                        <small class="block text-[11px] text-slate-400">Locked until Step 1 is submitted</small>
                                    </div>
                                </div>
                                <i class="ph-fill ph-lock-key text-2xl text-slate-400"></i>
                            </div>
                            <div class="mt-3 flex items-start gap-2 rounded-xl bg-amber-50 border border-amber-200/70 p-3 text-[11px] text-amber-900">
                                <i class="ph-bold ph-info text-base shrink-0 text-amber-700 mt-0.5"></i>
                                <span>Please submit your comment or question in Step 1 above to unlock this certificate.</span>
                            </div>
                        @endif
                    </div>
                </section>
            </aside>
        </div>
    </div>
    <div class="asia-wave"></div>
</main>
@endsection
