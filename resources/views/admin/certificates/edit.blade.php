@extends('layouts.admin', ['title' => 'Configure Certificate'])

@push('styles')
<style>
    .cert-editor-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 24px;
        width: 100%;
    }
    @media (min-width: 1024px) {
        .cert-editor-grid {
            grid-template-columns: minmax(0, 1.25fr) minmax(0, 1fr);
            gap: 28px;
        }
    }
    #interactive-preview-box {
        position: relative;
        width: 100%;
        margin: 0 auto;
        overflow: hidden;
        border-radius: 14px;
        border: 2px solid #0891b2;
        background-color: #f8fafc;
        cursor: crosshair;
        user-select: none;
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.08);
    }
</style>
@endpush

@section('content')
<main class="h-full flex-1 overflow-y-auto bg-slate-50 px-7 py-8">
    @if(session('success'))
        <div class="mb-6 flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-xs font-bold text-emerald-800 shadow-sm">
            <i class="bi bi-check-circle-fill text-lg text-emerald-600 shrink-0"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if(isset($errors) && $errors->any())
        <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-xs font-bold text-rose-700 shadow-sm">
            <ul class="list-disc pl-5 space-y-1 mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Header Navigation -->
    <div class="mb-7 flex flex-col justify-between gap-4 md:flex-row md:items-end">
        <div>
            <div class="flex items-center gap-2 text-[10px] font-bold uppercase tracking-widest text-slate-400">
                <a href="{{ route('admin.certificates') }}" class="text-cyan-600 hover:underline">Certificates</a>
                <span>/</span>
                <span>Edit Coordinates</span>
            </div>
            <h1 class="mt-1 text-2xl font-extrabold tracking-tight text-slate-900">Configure Certificate &amp; Coordinates</h1>
            <p class="mt-1 text-xs font-medium text-slate-500">{{ $webinar->title }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.certificates') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-bold text-slate-600 shadow-sm transition hover:bg-slate-50">
                <i class="bi bi-arrow-left"></i> Back to List
            </a>
            @if($webinar->certificate_template_path)
                <a id="btn-preview-top" href="{{ route('admin.certificates.preview', $webinar) }}" target="_blank" class="inline-flex items-center gap-2 rounded-xl border border-cyan-200 bg-cyan-50 px-4 py-2.5 text-xs font-bold text-cyan-700 shadow-sm transition hover:bg-cyan-100">
                    <i class="bi bi-eye"></i> Preview Generated PDF
                </a>
            @endif
        </div>
    </div>

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
        $fontColor = $webinar->certificate_font_color ?: '#8e5f16';
    @endphp

    <!-- Two-Column Editor Grid -->
    <div class="cert-editor-grid">
        <!-- LEFT COLUMN: Form & Coordinate Settings -->
        <div style="min-width: 0;">
            <form method="POST" action="{{ route('admin.certificates.update', $webinar) }}" enctype="multipart/form-data" class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                @csrf

                <div class="border-b border-slate-100 px-6 py-5">
                    <h2 class="text-base font-bold text-slate-900">Certificate Settings &amp; Name Placement</h2>
                    <p class="mt-1 text-xs text-slate-400">Upload background template and configure delegate name coordinates.</p>
                </div>

                <div class="p-6 space-y-6">
                    <!-- Webinar Information Banner -->
                    <div class="rounded-xl border border-cyan-200 bg-cyan-50/60 p-4">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-cyan-700">Selected Session</span>
                                <h3 class="mt-0.5 text-sm font-extrabold text-slate-900">{{ $webinar->title }}</h3>
                            </div>
                            <span class="rounded-full bg-cyan-600 px-3 py-1 text-[10px] font-bold text-white">
                                {{ $webinar->speciality?->name ?: 'Cardiology' }}
                            </span>
                        </div>
                        <div class="mt-2 text-xs font-medium text-slate-500">
                            <i class="bi bi-calendar-event me-1 text-cyan-600"></i>
                            Scheduled: {{ $webinar->scheduled_at ? $webinar->scheduled_at->format('d F Y, h:i A') : 'Date TBA' }}
                        </div>
                    </div>

                    <!-- Template Upload Box -->
                    <div>
                        <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-2">
                            Certificate Template (PDF, JPG, PNG, WebP)
                        </label>
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                            <input type="file" name="certificate_template" id="certificate_template" class="block w-full rounded-lg border border-slate-200 bg-white p-2.5 text-xs text-slate-700 outline-none focus:border-cyan-600" accept=".pdf,.jpg,.jpeg,.png,.webp,application/pdf,image/*">
                            <p class="mt-2 text-[11px] text-slate-500">
                                Upload an official A4 Landscape certificate template (<strong>PDF</strong> or high-resolution <strong>JPG / PNG / WebP</strong>) without the doctor's name. The delegate's name is dynamically overlaid at the coordinates set below.
                            </p>

                            @if($hasTemplate)
                                <div class="mt-3 flex items-center justify-between border-t border-slate-200 pt-3">
                                    <div class="flex items-center gap-3">
                                        @if($isImage)
                                            <img src="{{ $templateUrl }}" alt="Template" class="h-10 w-16 rounded border border-slate-200 object-cover shadow-sm">
                                        @else
                                            <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-rose-50 text-xl text-rose-600">
                                                <i class="bi bi-file-earmark-pdf"></i>
                                            </span>
                                        @endif
                                        <div>
                                            <strong class="block text-xs text-slate-800">
                                                Current Template: {{ strtoupper($ext) }} {{ $isImage ? 'Image' : 'PDF' }}
                                            </strong>
                                            <span class="text-[11px] text-slate-400">{{ basename($webinar->certificate_template_path) }}</span>
                                        </div>
                                    </div>
                                    <label class="inline-flex items-center gap-2 text-xs font-bold text-rose-600 cursor-pointer">
                                        <input type="checkbox" name="remove_certificate_template" value="1" class="rounded border-slate-300 text-rose-600 focus:ring-rose-500">
                                        Remove Template
                                    </label>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Coordinates Configuration -->
                    <div>
                        <div class="flex items-center justify-between border-b border-slate-100 pb-2 mb-4">
                            <h3 class="text-sm font-bold text-slate-900">
                                <i class="bi bi-crosshair text-cyan-600 me-1.5"></i> Doctor Name Coordinates (X &amp; Y)
                            </h3>
                            <span class="text-[11px] text-slate-400">Canvas Dimensions: {{ round($totalW) }} pt &times; {{ round($totalH) }} pt ({{ $totalW < $totalH ? 'Portrait' : 'Landscape' }})</span>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <!-- X Coordinate -->
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label class="text-xs font-bold text-slate-700">X Position (Horizontal)</label>
                                    <button type="button" id="btn-auto-center" class="text-[11px] font-bold text-cyan-600 hover:underline">
                                        Auto-Center
                                    </button>
                                </div>
                                <div class="relative flex items-center">
                                    <span class="absolute left-3 text-xs font-bold text-cyan-600">X</span>
                                    <input type="number" step="any" name="certificate_name_x" id="certificate_name_x" value="{{ old('certificate_name_x', $webinar->certificate_name_x) }}" placeholder="Auto (Center)" class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-8 pr-12 text-xs font-semibold text-slate-800 outline-none focus:border-cyan-600 focus:bg-white">
                                    <span class="absolute right-3 text-[11px] text-slate-400">pt</span>
                                </div>
                                <p class="mt-1 text-[10px] text-slate-400">
                                    Leave blank to <strong>auto-center</strong> across page width.
                                </p>
                            </div>

                            <!-- Y Coordinate -->
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label class="text-xs font-bold text-slate-700">Y Position (Vertical) <span class="text-rose-500">*</span></label>
                                    <div class="flex items-center gap-1.5 text-[10px]">
                                        <button type="button" id="btn-y-up" class="rounded bg-slate-100 px-1.5 py-0.5 font-bold text-slate-600 hover:bg-slate-200">+10</button>
                                        <button type="button" id="btn-y-down" class="rounded bg-slate-100 px-1.5 py-0.5 font-bold text-slate-600 hover:bg-slate-200">-10</button>
                                        <button type="button" id="btn-y-reset" class="text-cyan-600 hover:underline">Reset</button>
                                    </div>
                                </div>
                                <div class="relative flex items-center">
                                    <span class="absolute left-3 text-xs font-bold text-cyan-600">Y</span>
                                    <input type="number" step="any" name="certificate_name_y" id="certificate_name_y" value="{{ old('certificate_name_y', $webinar->certificate_name_y ?? 292.00) }}" required class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-8 pr-12 text-xs font-semibold text-slate-800 outline-none focus:border-cyan-600 focus:bg-white">
                                    <span class="absolute right-3 text-[11px] text-slate-400">pt</span>
                                </div>
                                <p class="mt-1 text-[10px] text-slate-400">
                                    Distance from bottom edge. <strong>Default: 292</strong>. (Up = +Y, Down = -Y).
                                </p>
                            </div>

                            <!-- Font Size -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Font Size (pt)</label>
                                <div class="relative flex items-center">
                                    <span class="absolute left-3 text-xs text-slate-400"><i class="bi bi-fonts"></i></span>
                                    <input type="number" name="certificate_font_size" id="certificate_font_size" value="{{ old('certificate_font_size', $webinar->certificate_font_size ?? 28) }}" min="12" max="100" class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-8 pr-12 text-xs font-semibold text-slate-800 outline-none focus:border-cyan-600 focus:bg-white">
                                    <span class="absolute right-3 text-[11px] text-slate-400">pt</span>
                                </div>
                                <p class="mt-1 text-[10px] text-slate-400">
                                    Doctor name font size. <strong>Default: 28</strong>. Recommended: 24 - 34.
                                </p>
                            </div>

                            <!-- Sample Test Name -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Sample Name for Live Preview</label>
                                <div class="relative flex items-center">
                                    <span class="absolute left-3 text-xs text-slate-400"><i class="bi bi-person"></i></span>
                                    <input type="text" id="test_name" value="Dr. Demo Delegate" class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-8 pr-3 text-xs font-semibold text-slate-800 outline-none focus:border-cyan-600 focus:bg-white">
                                </div>
                                <p class="mt-1 text-[10px] text-slate-400">
                                    Used for real-time placement testing on the right.
                                </p>
                            </div>

                            <!-- Font Color -->
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-bold text-slate-700 mb-1">Doctor Name Text Color</label>
                                <div class="flex items-center gap-3">
                                    <input type="color" id="color_picker" value="{{ old('certificate_font_color', $fontColor) }}" class="h-10 w-12 cursor-pointer rounded-lg border border-slate-200 bg-white p-1">
                                    <input type="text" name="certificate_font_color" id="certificate_font_color" value="{{ old('certificate_font_color', $fontColor) }}" class="w-32 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-bold text-slate-800 outline-none focus:border-cyan-600 focus:bg-white uppercase">
                                    <div class="flex items-center gap-2">
                                        <button type="button" class="btn-color-chip flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2.5 py-1 text-[11px] font-bold text-slate-600 hover:bg-slate-50" data-color="#8e5f16">
                                            <span class="h-3 w-3 rounded-full border border-black/10" style="background-color: #8e5f16;"></span> Gold
                                        </button>
                                        <button type="button" class="btn-color-chip flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2.5 py-1 text-[11px] font-bold text-slate-600 hover:bg-slate-50" data-color="#ffffff">
                                            <span class="h-3 w-3 rounded-full border border-slate-300" style="background-color: #ffffff;"></span> White
                                        </button>
                                        <button type="button" class="btn-color-chip flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2.5 py-1 text-[11px] font-bold text-slate-600 hover:bg-slate-50" data-color="#0F4C81">
                                            <span class="h-3 w-3 rounded-full border border-black/10" style="background-color: #0F4C81;"></span> Navy
                                        </button>
                                        <button type="button" class="btn-color-chip flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2.5 py-1 text-[11px] font-bold text-slate-600 hover:bg-slate-50" data-color="#0f172a">
                                            <span class="h-3 w-3 rounded-full border border-black/10" style="background-color: #0f172a;"></span> Dark
                                        </button>
                                    </div>
                                </div>
                                <p class="mt-1 text-[10px] text-slate-400">
                                    Pick high contrast color (e.g. <strong>#ffffff White</strong> for purple/dark badges, or <strong>#8e5f16 Gold</strong> for light paper).
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Action Buttons -->
                <div class="flex items-center justify-between border-t border-slate-100 bg-slate-50/70 px-6 py-4">
                    <a href="{{ route('admin.certificates') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-600 shadow-sm transition hover:bg-slate-50">
                        Back to List
                    </a>
                    <div class="flex items-center gap-3">
                        <button type="button" id="btn-live-test" class="inline-flex items-center gap-2 rounded-xl border border-cyan-200 bg-cyan-50 px-4 py-2 text-xs font-bold text-cyan-700 shadow-sm transition hover:bg-cyan-100">
                            <i class="bi bi-eye"></i> Live Test Preview
                        </button>
                        <button type="submit" class="inline-flex items-center gap-2 rounded-xl border border-cyan-600 bg-cyan-600 px-5 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-cyan-700">
                            <i class="bi bi-check-lg"></i> Save Certificate Settings
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- RIGHT COLUMN: Live Interactive Visual Canvas -->
        <div style="min-width: 0;">
            <div class="sticky top-20 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 px-6 py-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-bold text-slate-900">
                                <i class="bi bi-eye text-cyan-600 me-1.5"></i> Live Coordinate &amp; Canvas Preview
                            </h3>
                            <p class="mt-1 text-xs text-slate-400">
                                Click anywhere on the certificate canvas to place Doctor Name.
                            </p>
                        </div>
                        <span id="coord-indicator-badge" class="rounded-full bg-cyan-50 border border-cyan-200 px-2.5 py-1 text-[11px] font-bold text-cyan-700">
                            X: Auto · Y: 292 pt
                        </span>
                    </div>
                </div>

                <div class="p-6">
                    <!-- Canvas Box (Adaptive Aspect Ratio: Landscape or Portrait) -->
                    <div id="interactive-preview-box" style="aspect-ratio: {{ $aspectRatio }}; max-width: {{ $totalW < $totalH ? '420px' : '560px' }};">
                        <!-- PDF Canvas for PDF template -->
                        <canvas id="pdf-template-canvas" style="position: absolute; inset: 0; width: 100%; height: 100%; object-fit: contain; pointer-events: none; {{ $hasTemplate && !$isImage ? '' : 'display: none;' }}"></canvas>

                        <img id="image-template-bg" src="{{ $webinar->certificate_template_path ? route('webinar.certificate_template_file', $webinar) : '' }}" alt="Template" style="position: absolute; inset: 0; width: 100%; height: 100%; object-fit: contain; pointer-events: none; {{ $hasTemplate && $isImage ? '' : 'display: none;' }}" onerror="if(!this.dataset.fallback){this.dataset.fallback='1'; this.src='/storage/{{ ltrim($webinar->certificate_template_path, '/') }}';}">

                        <!-- Empty Placeholder (if no template uploaded) -->
                        <div id="no-template-placeholder" class="absolute inset-0 flex flex-col items-center justify-center p-6 text-center" style="{{ $hasTemplate ? 'display: none;' : '' }}">
                            <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-cyan-50 text-2xl text-cyan-600 mb-3 shadow-inner">
                                <i class="bi bi-file-earmark-plus"></i>
                            </span>
                            <strong class="text-xs font-bold text-slate-800">No Certificate Template Uploaded</strong>
                            <p class="mt-1 max-w-xs text-[11px] text-slate-400">
                                Upload an A4 Landscape or Portrait PDF or JPG/PNG image on the left. You can still click to calibrate coordinates.
                            </p>
                        </div>

                        <!-- Origin Labels -->
                        <div style="position: absolute; top: 0; left: 0; padding: 3px 8px; font-size: 9px; font-weight: 700; color: #64748b; background: rgba(255,255,255,0.85); border-bottom-right-radius: 8px; pointer-events: none; z-index: 5;">
                            (0, {{ round($totalH) }}) Top
                        </div>
                        <div style="position: absolute; bottom: 0; left: 0; padding: 3px 8px; font-size: 9px; font-weight: 700; color: #64748b; background: rgba(255,255,255,0.85); border-top-right-radius: 8px; pointer-events: none; z-index: 5;">
                            (0, 0) Origin
                        </div>

                        <!-- Target marker guide line for Y position -->
                        <div id="preview-marker-line" style="position: absolute; left: 0; right: 0; bottom: 49%; border-top: 2px dashed #e11d48; pointer-events: none; z-index: 6;"></div>

                        <!-- Target marker badge for Doctor Name -->
                        <div id="preview-name-label" style="position: absolute; bottom: 49%; left: 50%; transform: translate(-50%, 50%); font-family: 'Times New Roman', Georgia, serif; font-style: italic; font-weight: 900; color: {{ $fontColor }}; text-shadow: 0 1px 2px rgba(255,255,255,0.9); font-size: 18px; pointer-events: none; z-index: 7; white-space: nowrap; padding: 2px 10px; background: rgba(255,255,255,0.7); border: 1px solid rgba(142,95,22,0.3); border-radius: 8px;">
                            Dr. Demo Delegate
                        </div>
                    </div>

                    <p class="mt-3 text-center text-[11px] text-slate-400">
                        <i class="bi bi-cursor me-1 text-cyan-600"></i> Click anywhere inside the box above to automatically position the Doctor Name.
                    </p>

                    <!-- Coordinate Reference Cards -->
                    <div class="mt-5 space-y-2.5 border-t border-slate-100 pt-4 text-xs text-slate-600">
                        <div class="flex items-start gap-2.5">
                            <i class="bi bi-check2-circle text-emerald-600 text-base shrink-0 mt-0.5"></i>
                            <div>
                                <strong class="text-slate-800">Auto-Centering:</strong> Leaving X empty automatically centers the Doctor Name horizontally on the page width.
                            </div>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <i class="bi bi-arrow-up-circle text-cyan-600 text-base shrink-0 mt-0.5"></i>
                            <div>
                                <strong class="text-slate-800">Move Name UP:</strong> Increase Y (e.g. from <code>292</code> to <code>315</code>).
                            </div>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <i class="bi bi-arrow-down-circle text-amber-500 text-base shrink-0 mt-0.5"></i>
                            <div>
                                <strong class="text-slate-800">Move Name DOWN:</strong> Decrease Y (e.g. from <code>292</code> to <code>275</code>).
                            </div>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <i class="bi bi-file-earmark-check text-indigo-600 text-base shrink-0 mt-0.5"></i>
                            <div>
                                <strong class="text-slate-800">Supported Formats:</strong> High-res JPG, PNG, WebP, and official PDF certificate files.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
    if (window.pdfjsLib) {
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
    }

    document.addEventListener('DOMContentLoaded', function () {
        const previewBox = document.getElementById('interactive-preview-box');
        const xInput = document.getElementById('certificate_name_x');
        const yInput = document.getElementById('certificate_name_y');
        const fontInput = document.getElementById('certificate_font_size');
        const nameInput = document.getElementById('test_name');
        const fileInput = document.getElementById('certificate_template');
        const nameLabel = document.getElementById('preview-name-label');
        const markerLine = document.getElementById('preview-marker-line');
        const coordBadge = document.getElementById('coord-indicator-badge');
        const imgBg = document.getElementById('image-template-bg');
        const pdfCanvas = document.getElementById('pdf-template-canvas');
        const placeholder = document.getElementById('no-template-placeholder');
        const colorPicker = document.getElementById('color_picker');
        const colorInput = document.getElementById('certificate_font_color');

        let totalWidth = {{ $totalW }};
        let totalHeight = {{ $totalH }};
        const previewUrlBase = "{{ route('admin.certificates.preview', $webinar) }}";

        // Initial PDF Rendering if template is PDF
        @if($hasTemplate && !$isImage)
            renderPdfTemplate("{{ $templateUrl }}");
        @endif

        function renderPdfTemplate(urlOrBuffer) {
            if (!window.pdfjsLib) return;
            const loadingTask = (typeof urlOrBuffer === 'string')
                ? pdfjsLib.getDocument(urlOrBuffer)
                : pdfjsLib.getDocument({ data: urlOrBuffer });

            loadingTask.promise.then(function (pdf) {
                return pdf.getPage(1);
            }).then(function (page) {
                const viewport = page.getViewport({ scale: 1.5 });
                pdfCanvas.width = viewport.width;
                pdfCanvas.height = viewport.height;
                const ctx = pdfCanvas.getContext('2d');
                const renderContext = {
                    canvasContext: ctx,
                    viewport: viewport
                };
                return page.render(renderContext).promise;
            }).then(function () {
                pdfCanvas.style.display = 'block';
                if (imgBg) imgBg.style.display = 'none';
                if (placeholder) placeholder.style.display = 'none';
            }).catch(function (err) {
                console.warn('PDF.js render warning:', err);
            });
        }

        // Handle Immediate File Preview on selecting new file
        if (fileInput) {
            fileInput.addEventListener('change', function (e) {
                const file = e.target.files && e.target.files[0];
                if (!file) return;

                const nameLower = file.name.toLowerCase();
                const isImg = file.type.startsWith('image/') || /\.(jpg|jpeg|png|webp)$/i.test(nameLower);
                const isPdf = file.type === 'application/pdf' || /\.pdf$/i.test(nameLower);

                if (isImg) {
                    const reader = new FileReader();
                    reader.onload = function (evt) {
                        imgBg.src = evt.target.result;
                        imgBg.onload = function () {
                            const nw = imgBg.naturalWidth;
                            const nh = imgBg.naturalHeight;
                            if (nw && nh) {
                                previewBox.style.aspectRatio = nw + ' / ' + nh;
                                if (nw >= nh) {
                                    totalWidth = 842.0;
                                    totalHeight = Math.round((842.0 / nw) * nh);
                                    previewBox.style.maxWidth = '560px';
                                } else {
                                    totalHeight = 842.0;
                                    totalWidth = Math.round((842.0 / nh) * nw);
                                    previewBox.style.maxWidth = '420px';
                                }
                                updatePreview();
                            }
                        };
                        imgBg.style.display = 'block';
                        pdfCanvas.style.display = 'none';
                        if (placeholder) placeholder.style.display = 'none';
                    };
                    reader.readAsDataURL(file);
                } else if (isPdf) {
                    const reader = new FileReader();
                    reader.onload = function (evt) {
                        totalWidth = 842.0;
                        totalHeight = 595.0;
                        previewBox.style.aspectRatio = '842 / 595';
                        previewBox.style.maxWidth = '560px';
                        renderPdfTemplate(evt.target.result);
                        updatePreview();
                    };
                    reader.readAsArrayBuffer(file);
                }
            });
        }

        // Live Coordinate & Label Update
        function updatePreview() {
            const yVal = parseFloat(yInput.value) || 292;
            const yPercent = (yVal / totalHeight) * 100;
            markerLine.style.bottom = yPercent + '%';
            nameLabel.style.bottom = yPercent + '%';

            let xText = 'Auto-Center';
            if (xInput.value.trim() !== '') {
                const xVal = parseFloat(xInput.value);
                const xPercent = (xVal / totalWidth) * 100;
                nameLabel.style.left = xPercent + '%';
                nameLabel.style.transform = 'translate(-50%, 50%)';
                xText = Math.round(xVal) + ' pt';
            } else {
                nameLabel.style.left = '50%';
                nameLabel.style.transform = 'translate(-50%, 50%)';
            }

            const rawFontSize = parseFloat(fontInput.value) || 28;
            const scaledFontSize = Math.max(12, Math.round(rawFontSize * (previewBox.clientWidth / totalWidth) * 1.3));
            nameLabel.style.fontSize = scaledFontSize + 'px';

            const name = nameInput.value.trim() || 'Dr. Demo Delegate';
            nameLabel.textContent = name;

            const chosenColor = (colorInput && colorInput.value.trim()) || '#8e5f16';
            nameLabel.style.color = chosenColor;

            if (coordBadge) {
                coordBadge.textContent = 'X: ' + xText + ' · Y: ' + Math.round(yVal) + ' pt';
            }
        }

        // Interactive Click on Canvas
        if (previewBox) {
            previewBox.addEventListener('click', function (e) {
                const rect = previewBox.getBoundingClientRect();
                const clickX = e.clientX - rect.left;
                const clickY = e.clientY - rect.top;

                const pdfX = Math.round((clickX / rect.width) * totalWidth);
                const pdfY = Math.round((1 - (clickY / rect.height)) * totalHeight);

                xInput.value = pdfX;
                yInput.value = pdfY;
                updatePreview();
            });
        }

        // Quick button handlers
        document.getElementById('btn-auto-center')?.addEventListener('click', function () {
            xInput.value = '';
            updatePreview();
        });

        document.getElementById('btn-y-up')?.addEventListener('click', function () {
            const current = parseFloat(yInput.value) || 292;
            yInput.value = Math.min(totalHeight, Math.round(current + 10));
            updatePreview();
        });

        document.getElementById('btn-y-down')?.addEventListener('click', function () {
            const current = parseFloat(yInput.value) || 292;
            yInput.value = Math.max(10, Math.round(current - 10));
            updatePreview();
        });

        document.getElementById('btn-y-reset')?.addEventListener('click', function () {
            yInput.value = 292;
            updatePreview();
        });

        // Color picker and presets sync
        if (colorPicker && colorInput) {
            colorPicker.addEventListener('input', function () {
                colorInput.value = colorPicker.value;
                updatePreview();
            });
            colorInput.addEventListener('input', function () {
                if (/^#[0-9a-f]{6}$/i.test(colorInput.value.trim())) {
                    colorPicker.value = colorInput.value.trim();
                }
                updatePreview();
            });
        }

        document.querySelectorAll('.btn-color-chip').forEach(btn => {
            btn.addEventListener('click', function () {
                const c = btn.getAttribute('data-color');
                if (colorPicker) colorPicker.value = c;
                if (colorInput) colorInput.value = c;
                updatePreview();
            });
        });

        [xInput, yInput, fontInput, nameInput].forEach(el => {
            el?.addEventListener('input', updatePreview);
        });

        window.addEventListener('resize', updatePreview);
        updatePreview();

        // Live Test Preview Function
        function openLivePreview() {
            const params = new URLSearchParams();
            if (xInput.value.trim() !== '') params.append('x', xInput.value.trim());
            if (yInput.value.trim() !== '') params.append('y', yInput.value.trim());
            if (fontInput.value.trim() !== '') params.append('font_size', fontInput.value.trim());
            if (nameInput.value.trim() !== '') params.append('test_name', nameInput.value.trim());
            if (colorInput && colorInput.value.trim() !== '') params.append('font_color', colorInput.value.trim());

            const fullUrl = previewUrlBase + (params.toString() ? '?' + params.toString() : '');
            window.open(fullUrl, '_blank');
        }

        document.getElementById('btn-live-test')?.addEventListener('click', openLivePreview);
        document.getElementById('btn-preview-top')?.addEventListener('click', function(e) {
            e.preventDefault();
            openLivePreview();
        });
    });
</script>
@endpush
