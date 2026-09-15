<header class="relative z-50 border-b border-slate-100 bg-white px-5 py-4 md:px-10">
    <div class="mx-auto grid max-w-7xl grid-cols-3 items-center">
        <a href="{{ route('home') }}" class="justify-self-start" aria-label="EASD home"><img src="{{ asset('assets/images/branding/easd-logo.png') }}" alt="European Association for the Study of Diabetes" class="h-10 w-auto object-contain sm:h-16"></a>
        <a href="{{ route('home') }}" class="justify-self-center text-center" aria-label="PULCE Connect 2026 Cardio-Renal-Metabolic Educational Series">
            <span class="mx-auto block aspect-[4.05/1] w-36 overflow-hidden sm:w-64">
                <img src="{{ asset('assets/images/branding/pulce-logo.png') }}" alt="PULCE Connect 2026" class="block h-auto w-full object-contain object-top">
            </span>
            <div class="mt-1 flex items-center justify-center gap-3 text-escRed" aria-label="Heart, Kidney and Metabolism">
                <span class="inline-flex items-center gap-1 text-[8px] font-black uppercase sm:text-[10px]"><i class="ph-fill ph-heartbeat text-sm"></i>Heart</span>
                <span class="inline-flex items-center gap-1 text-[8px] font-black uppercase sm:text-[10px]"><i class="ph-fill ph-drop text-sm"></i>Kidney</span>
                <span class="inline-flex items-center gap-1 text-[8px] font-black uppercase sm:text-[10px]"><i class="ph-fill ph-activity text-sm"></i>Metabolism</span>
            </div>
            <p class="mt-1 hidden max-w-xl text-[9px] font-extrabold leading-4 text-escBlue sm:block"><span class="text-escRed">Cardio-Renal-Metabolic (CRM) Educational Series:</span> Redefining Diabetes Care Beyond Glycaemic Control</p>
        </a>
        <div class="justify-self-end"><img src="{{ asset('assets/images/branding/hetero-logo.png') }}" alt="Hetero" class="h-10 w-auto object-contain sm:h-16"></div>
    </div>
</header>
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
