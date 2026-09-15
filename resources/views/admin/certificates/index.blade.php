@extends('layouts.admin', ['title' => 'Webinar Certificates'])

@section('content')
<main class="h-full flex-1 overflow-y-auto bg-slate-50 px-7 py-8">
    @if(session('success'))
        <div class="mb-6 flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-xs font-bold text-emerald-800 shadow-sm">
            <i class="bi bi-check-circle-fill text-lg text-emerald-600 shrink-0"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    <div class="mb-7 flex flex-col justify-between gap-4 md:flex-row md:items-end">
        <div>
            <div class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Admin Portal / Certificates</div>
            <h1 class="mt-1 text-2xl font-extrabold tracking-tight text-slate-900">Webinar Certificates Management</h1>
            <p class="mt-1 text-xs font-medium text-slate-500">Manage certificate background templates (PDF, JPG, PNG, WEBP) and configure Doctor Name X/Y coordinates.</p>
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                        <th class="px-6 py-4">Webinar / Session</th>
                        <th class="px-6 py-4">Schedule Date</th>
                        <th class="px-6 py-4">Template Status</th>
                        <th class="px-6 py-4">Name Position (X, Y)</th>
                        <th class="px-6 py-4">Font Size</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                    @forelse($webinars as $webinar)
                        @php
                            $hasTemplate = $webinar->certificate_template_path && \Storage::disk('public')->exists($webinar->certificate_template_path);
                            $ext = $hasTemplate ? strtolower(pathinfo($webinar->certificate_template_path, PATHINFO_EXTENSION)) : '';
                            $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'webp']);
                        @endphp
                        <tr class="transition hover:bg-slate-50/70">
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.certificates.edit', $webinar) }}" class="block font-bold text-slate-900 hover:text-cyan-700 transition">
                                    {{ $webinar->title }}
                                </a>
                                <span class="text-[11px] text-slate-400">{{ $webinar->speciality?->name ?: 'General Session' }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($webinar->scheduled_at)
                                    <span class="font-medium text-slate-700">{{ $webinar->scheduled_at->format('d M Y, h:i A') }}</span>
                                @else
                                    <span class="inline-block rounded bg-slate-100 px-2 py-0.5 text-[10px] font-bold uppercase text-slate-500">Date TBA</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($hasTemplate)
                                    @if($isImage)
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-cyan-50 border border-cyan-200 px-2.5 py-1 text-[11px] font-bold text-cyan-700">
                                            <i class="bi bi-file-earmark-image"></i> {{ strtoupper($ext) }} Image
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 border border-emerald-200 px-2.5 py-1 text-[11px] font-bold text-emerald-700">
                                            <i class="bi bi-file-earmark-pdf"></i> PDF Template
                                        </span>
                                    @endif
                                @else
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-rose-50 border border-rose-200 px-2.5 py-1 text-[11px] font-bold text-rose-700">
                                        <i class="bi bi-exclamation-triangle"></i> No Template
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-1.5">
                                    <span class="rounded bg-slate-100 px-2 py-1 text-[11px] font-mono font-semibold text-slate-700">
                                        X: {{ $webinar->certificate_name_x !== null ? $webinar->certificate_name_x . ' pt' : 'Auto (Center)' }}
                                    </span>
                                    <span class="rounded bg-slate-100 px-2 py-1 text-[11px] font-mono font-semibold text-slate-700">
                                        Y: {{ $webinar->certificate_name_y !== null ? $webinar->certificate_name_y . ' pt' : '292 pt' }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="rounded bg-slate-100 px-2 py-1 text-[11px] font-mono font-bold text-slate-700">
                                    {{ $webinar->certificate_font_size ?: 28 }} pt
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('admin.certificates.edit', $webinar) }}" class="inline-flex items-center gap-1.5 rounded-lg border border-cyan-200 bg-cyan-50 px-3 py-1.5 text-xs font-bold text-cyan-700 hover:bg-cyan-100 hover:border-cyan-300 transition">
                                        <i class="bi bi-sliders"></i> Edit Coordinates
                                    </a>
                                    @if($hasTemplate)
                                        <a href="{{ route('admin.certificates.preview', $webinar) }}" target="_blank" class="inline-flex items-center gap-1.5 rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-700 hover:bg-emerald-100 hover:border-emerald-300 transition" title="Preview Generated PDF with Doctor Name">
                                            <i class="bi bi-eye"></i> Preview PDF
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                No active webinars found. Please add webinars first.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</main>
@endsection
