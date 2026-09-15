<header class="relative z-50 border-b border-slate-100 bg-white px-4 py-4 sm:px-6 md:px-10">
    <div class="mx-auto flex max-w-7xl items-center justify-between gap-4">
        <a href="{{ route('home') }}" class="shrink-0" aria-label="EASD home">
            <img src="{{ asset('assets/images/branding/easd-logo.png') }}" alt="European Association for the Study of Diabetes" class="h-11 w-auto object-contain sm:h-16 md:h-18">
        </a>
        <a href="{{ route('home') }}" class="flex flex-col items-center text-center px-2 py-1 max-w-2xl" aria-label="PULCE Connect 2026 Cardio-Renal-Metabolic Educational Series">
            <span class="mx-auto block aspect-[4.05/1] w-48 overflow-hidden sm:w-72 md:w-80 transition-transform hover:scale-[1.02]">
                <img src="{{ asset('assets/images/branding/pulce-logo.png') }}" alt="PULCE Connect 2026" class="block h-auto w-full object-contain object-top">
            </span>
            <div class="mt-2 flex flex-wrap items-center justify-center gap-2">
                <span class="inline-block rounded bg-escRed px-3 py-1 text-xs sm:text-sm font-black uppercase tracking-wider text-white shadow-xs">
                    Cardio-Renal-Metabolic (CRM) Educational Series
                </span>
            </div>
            <div class="mt-2 flex items-center justify-center gap-2 sm:gap-3" aria-label="Cardio-Renal-Metabolic therapeutic focus: Heart, Kidney and Metabolism">
                <span class="inline-flex items-center gap-1.5 rounded-full border border-rose-200 bg-rose-50 px-2.5 py-0.5 text-[11px] sm:text-xs font-bold text-rose-700">
                    <i class="ph-fill ph-heartbeat text-sm text-rose-600"></i>Heart
                </span>
                <span class="inline-flex items-center gap-1.5 rounded-full border border-sky-200 bg-sky-50 px-2.5 py-0.5 text-[11px] sm:text-xs font-bold text-sky-700">
                    <i class="ph-fill ph-drop text-sm text-sky-600"></i>Kidney
                </span>
                <span class="inline-flex items-center gap-1.5 rounded-full border border-amber-200 bg-amber-50 px-2.5 py-0.5 text-[11px] sm:text-xs font-bold text-amber-800">
                    <i class="ph-fill ph-activity text-sm text-amber-600"></i>Metabolism
                </span>
            </div>
        </a>
        <div class="shrink-0">
            <img src="{{ asset('assets/images/branding/hetero-logo.png') }}" alt="Hetero" class="h-11 w-auto object-contain sm:h-14 md:h-16">
        </div>
    </div>
</header>
<aside class="border-b border-slate-200 bg-slate-50 px-4 py-2 text-center" aria-label="Event Framework">
    <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-center gap-x-3 gap-y-1 text-xs md:text-sm">
        <strong class="font-black uppercase tracking-wider text-slate-900">PULCE Connect 2026</strong>
        <span class="hidden text-slate-300 sm:inline">|</span>
        <span class="font-extrabold text-escRed">Cardio-Renal-Metabolic (CRM) Educational Series:</span>
        <span class="font-bold text-escBlue">Redefining Diabetes Care Beyond Glycaemic Control</span>
        <span class="hidden lg:inline-flex items-center gap-2.5 ml-2 pl-3 border-l border-slate-300">
            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-rose-700"><i class="ph-fill ph-heartbeat text-rose-600"></i>Heart</span>
            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-sky-700"><i class="ph-fill ph-drop text-sky-600"></i>Kidney</span>
            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-amber-700"><i class="ph-fill ph-activity text-amber-600"></i>Metabolism</span>
        </span>
    </div>
</aside>
@unless($hideWebsiteNav ?? false)
<nav class="sticky top-0 z-40 bg-gradient-to-r from-[#b90009] via-[#db0815] to-[#b90009] text-white shadow-md">
    <div class="mx-auto grid max-w-6xl @auth('web') grid-cols-6 @else grid-cols-5 @endauth divide-x divide-white/20 text-center">
        <a href="{{ route('home') }}" class="flex items-center justify-center gap-1.5 px-2 py-3 hover:bg-white/10 transition-colors {{ request()->routeIs('home') && !request()->has('page') ? 'bg-white/10' : '' }}">
            <span class="flex h-7 w-7 sm:h-8 sm:w-8 items-center justify-center rounded-full border border-white/60"><i class="ph ph-house text-sm sm:text-base"></i></span>
            <span class="text-[9px] font-extrabold uppercase md:text-xs">Home</span>
        </a>
        <a href="{{ route('home') }}#about" class="flex items-center justify-center gap-1.5 px-2 py-3 hover:bg-white/10 transition-colors">
            <span class="flex h-7 w-7 sm:h-8 sm:w-8 items-center justify-center rounded-full border border-white/60"><i class="ph ph-info text-sm sm:text-base"></i></span>
            <span class="text-[9px] font-extrabold uppercase md:text-xs">About Program</span>
        </a>
        <a href="{{ route('home') }}#agenda" class="flex items-center justify-center gap-1.5 px-2 py-3 hover:bg-white/10 transition-colors">
            <span class="flex h-7 w-7 sm:h-8 sm:w-8 items-center justify-center rounded-full border border-white/60"><i class="ph ph-calendar-blank text-sm sm:text-base"></i></span>
            <span class="text-[9px] font-extrabold uppercase md:text-xs">Scientific Agenda</span>
        </a>
        <a href="{{ route('home') }}#faculty" class="flex items-center justify-center gap-1.5 px-2 py-3 hover:bg-white/10 transition-colors">
            <span class="flex h-7 w-7 sm:h-8 sm:w-8 items-center justify-center rounded-full border border-white/60"><i class="ph ph-users-three text-sm sm:text-base"></i></span>
            <span class="text-[9px] font-extrabold uppercase md:text-xs">Faculty</span>
        </a>
        @auth('web')
            <a href="{{ route('certificate.page') }}" class="flex items-center justify-center gap-1.5 px-2 py-3 hover:bg-white/10 transition-colors {{ request()->routeIs('certificate.page') ? 'bg-white/20 font-black' : '' }}">
                <span class="flex h-7 w-7 sm:h-8 sm:w-8 items-center justify-center rounded-full border border-white"><i class="ph ph-certificate text-sm sm:text-base"></i></span>
                <span class="text-[9px] font-extrabold uppercase md:text-xs">Certificate</span>
            </a>
            <a href="{{ route('dashboard') }}" class="flex items-center justify-center gap-1.5 px-2 py-3 hover:bg-white/10 transition-colors {{ request()->routeIs('dashboard') ? 'bg-white/20 font-black' : '' }}">
                <span class="flex h-7 w-7 sm:h-8 sm:w-8 items-center justify-center rounded-full border border-white"><i class="ph ph-squares-four text-sm sm:text-base"></i></span>
                <span class="text-[9px] font-extrabold uppercase md:text-xs">Dashboard</span>
            </a>
        @else
            <a href="{{ route('register') }}" class="flex items-center justify-center gap-1.5 px-2 py-3 hover:bg-white/10 transition-colors bg-white/10 {{ request()->routeIs('register') ? 'bg-white/20' : '' }}">
                <span class="flex h-7 w-7 sm:h-8 sm:w-8 items-center justify-center rounded-full border border-white"><i class="ph ph-pencil-simple-line text-sm sm:text-base"></i></span>
                <span class="text-[9px] font-extrabold uppercase md:text-xs">Register Now</span>
            </a>
        @endauth
    </div>
</nav>
@endunless
