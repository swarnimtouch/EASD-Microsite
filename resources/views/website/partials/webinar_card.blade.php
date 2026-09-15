<a href="{{ route('webinar', $webinar->id) }}"
   class="group block overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:border-escBlue/30 hover:shadow-xl">
    <div class="relative h-48 overflow-hidden">
        <img src="{{ $webinar->cover_image }}" alt="{{ $webinar->title }}"
             class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/60 via-transparent to-transparent"></div>
        <span
            class="absolute left-4 top-4 inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-[9px] font-black uppercase tracking-widest shadow-sm {{ $state === 'live' ? 'bg-escRed text-white' : ($state === 'upcoming' ? 'bg-white text-escBlue' : 'bg-slate-900/80 text-white') }}">
            @if($state === 'live')
                <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-white"></span>
            @endif
            {{ $state === 'live' ? 'Live now' : ($state === 'upcoming' ? 'Upcoming' : ($webinar->recording_url ? 'Recording available' : 'Completed')) }}
    </span>
        @if($webinar->speciality)
            <span
                class="absolute bottom-4 left-4 rounded-full bg-white/95 px-3 py-1 text-[9px] font-black uppercase tracking-wider text-escRed">{{ $webinar->speciality->name }}</span>
        @endif
    </div>
    <div class="p-6">
        <p class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">{{ $webinar->type }}</p>
        <h3 class="mt-2 line-clamp-2 min-h-12 text-lg font-black leading-6 text-slate-900 transition group-hover:text-escBlue">{{ $webinar->title }}</h3>
        <div class="mt-5 space-y-2.5 text-xs font-semibold text-slate-500">
            <p class="flex items-center gap-2"><i
                    class="ph ph-calendar-blank text-lg text-escRed"></i>{{ $webinar->scheduled_at?->format('d M Y') ?: 'Available on demand' }}
            </p>
            <p class="flex items-center gap-2"><i
                    class="ph ph-clock text-lg text-escRed"></i>{{ $webinar->scheduled_at?->format('h:i A') ?: 'Any time' }}{{ $webinar->timezone_label ? ' · '.$webinar->timezone_label : '' }}
                · {{ $webinar->duration_minutes }} mins</p>
            <p class="flex items-center gap-2"><i
                    class="ph ph-users-three text-lg text-escRed"></i>{{ $webinar->people->count() }} {{ \Illuminate\Support\Str::plural('faculty member', $webinar->people->count()) }}
            </p>
        </div>
        <div class="mt-6 flex items-center justify-between border-t border-slate-100 pt-4"><span
                class="text-xs font-extrabold text-escBlue">{{ $state === 'live' ? 'Join webinar' : 'View webinar' }}</span><span
                class="flex h-9 w-9 items-center justify-center rounded-full bg-blue-50 text-escBlue transition group-hover:bg-escBlue group-hover:text-white"><i
                    class="ph-bold ph-arrow-right"></i></span></div>
    </div>
</a>
