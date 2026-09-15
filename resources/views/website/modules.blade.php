@extends('layouts.portal', ['title' => 'Modules'])

@section('content')
    <main class="mx-auto max-w-[1500px] space-y-10 p-6 md:p-10">
        <section class="flex flex-col justify-between gap-5 md:flex-row md:items-end">
            <div>
                <p class="text-[10px] font-extrabold uppercase tracking-[.25em] text-escRed">EASD Diabetes
                    Series</p>
                <h1 class="mt-2 text-3xl font-black text-escBlue">Modules</h1>
                <p class="mt-2 text-sm text-slate-500">Access the released scientific learning modules in sequence.</p>
            </div>
            @if($modules->isNotEmpty())
                <span
                    class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-xs font-bold text-slate-500">
                    <strong class="text-escBlue">{{ $modules->count() }}</strong> {{ \Illuminate\Support\Str::plural('module', $modules->count()) }}
                </span>
            @endif
        </section>

        <section class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            @forelse($modules as $module)
                @php($moduleUrl = $module->content_source === 'upload' ? $module->file_url : $module->content_url)
                <article
                    class="group flex min-h-72 flex-col overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:border-escBlue/30 hover:shadow-xl">
                    <div class="flex items-start justify-between bg-gradient-to-br from-blue-50 to-slate-50 p-6">
                        <span
                            class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white text-2xl text-escBlue shadow-sm">
                            <i class="ph {{ $module->content_type === 'pdf' ? 'ph-file-pdf' : 'ph-link' }}"></i>
                        </span>
                        <span
                            class="rounded-full bg-white px-3 py-1.5 text-[9px] font-black uppercase tracking-widest text-escRed shadow-sm">Module {{ $module->sequence_order }}
                        </span>
                    </div>
                    <div class="flex flex-1 flex-col p-6">
                        <p class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">{{ $module->content_type === 'pdf' ? 'PDF Document' : 'Online Resource' }}</p>
                        <h2 class="mt-2 text-lg font-black leading-6 text-slate-900 group-hover:text-escBlue">{{ $module->title }}</h2>
                        @if($module->description)
                            <p class="mt-3 line-clamp-3 text-sm leading-6 text-slate-500">{{ strip_tags($module->description) }}</p>
                        @endif
                        <div class="mt-auto flex items-center justify-between border-t border-slate-100 pt-5">
                            @if($module->minimum_viewing_minutes)
                                <span class="flex items-center gap-2 text-xs font-semibold text-slate-500">
                                    <i class="ph ph-clock text-lg text-escRed"></i>{{ $module->minimum_viewing_minutes }} mins
                                </span>
                            @else
                                <span></span>
                            @endif
                            @if($moduleUrl)
                                <a href="{{ $moduleUrl }}" target="_blank" rel="noopener"
                                   class="flex items-center gap-2 text-xs font-extrabold text-escBlue">Open module
                                    <i class="ph-bold ph-arrow-up-right"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </article>
            @empty
                <div class="col-span-full rounded-3xl border border-slate-200 bg-white p-12 text-center">
                    <i class="ph ph-books text-5xl text-slate-300"></i>
                    <h2 class="mt-4 font-black text-escBlue">No modules available</h2>
                    <p class="mt-2 text-sm text-slate-500">Released learning modules will appear here.</p></div>
            @endforelse
        </section>
    </main>
@endsection
