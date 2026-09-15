@php($adminUser = auth('admin')->user())

<header class="fixed inset-x-0 top-0 z-50 flex h-16 items-center border-b border-slate-200 bg-white px-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 text-[19px] font-extrabold tracking-tight text-slate-900 no-underline">
            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-cyan-600 text-lg text-white"><i class="bi bi-activity"></i></span>
            <span>MediHub<span class="text-cyan-600">Admin</span></span>
        </a>
        <span class="mx-1 hidden h-6 w-px bg-slate-200 md:block"></span>
        <button type="button" class="hidden items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 transition hover:border-cyan-200 hover:bg-cyan-50 hover:text-cyan-700 md:flex">
            <i class="bi bi-heart-pulse text-cyan-600"></i><span>{{ (empty(site_name) || site_name === 'name') ? 'PULCE Connect 2026' : site_name }}</span>
        </button>
    </div>

    <div class="ml-auto flex items-center gap-4">
        <button type="button" class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 transition hover:border-cyan-200 hover:bg-cyan-50 hover:text-cyan-700" aria-label="Notifications"><i class="bi bi-bell"></i></button>
        <div class="relative">
            <button type="button" id="admin-profile-button" class="flex items-center gap-2 border-0 bg-transparent p-0 text-left">
                <img src="{{ $adminUser->profile_image }}" alt="{{ $adminUser->name }}" class="h-8 w-8 rounded-full object-cover ring-2 ring-slate-100">
                <span class="hidden flex-col text-right leading-tight lg:flex"><strong class="text-xs font-bold text-slate-800">{{ $adminUser->name }}</strong><small class="text-[10px] text-slate-500">Global Administrator</small></span>
                <i class="bi bi-chevron-down hidden text-[9px] text-slate-400 lg:block"></i>
            </button>
            <div id="admin-profile-menu" class="absolute right-0 top-[46px] hidden w-60 rounded-xl border border-slate-200 bg-white p-2 shadow-xl">
                <div class="flex flex-col border-b border-slate-200 px-3 py-2.5"><strong class="text-xs font-bold text-slate-800">{{ $adminUser->name }}</strong><span class="text-[11px] text-slate-500">{{ $adminUser->email }}</span></div>
                <a href="{{ route('admin.profile') }}" class="flex gap-2.5 rounded-lg px-3 py-2.5 text-xs font-semibold text-slate-600 no-underline transition hover:bg-cyan-50 hover:text-cyan-700"><i class="bi bi-person"></i> My Profile</a>
                <a href="{{ route('admin.password') }}" class="flex gap-2.5 rounded-lg px-3 py-2.5 text-xs font-semibold text-slate-600 no-underline transition hover:bg-cyan-50 hover:text-cyan-700"><i class="bi bi-shield-lock"></i> Change Password</a>
                <a href="{{ route('admin.logout') }}" class="flex gap-2.5 rounded-lg px-3 py-2.5 text-xs font-semibold text-slate-600 no-underline transition hover:bg-rose-50 hover:text-rose-600"><i class="bi bi-box-arrow-right"></i> Sign Out</a>
            </div>
        </div>
        <button type="button" id="admin-mobile-menu-button" class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-white text-xl text-slate-600 md:hidden"><i class="bi bi-list"></i></button>
    </div>
</header>

<div id="admin-sidebar-overlay" class="fixed inset-0 top-16 z-30 hidden bg-slate-900/30 md:hidden"></div>
<aside id="admin-sidebar" class="fixed bottom-0 left-0 top-16 z-40 flex w-[260px] -translate-x-full flex-col overflow-y-auto border-r border-slate-200 bg-white shadow-xl transition-transform duration-200 md:translate-x-0 md:shadow-none">
    @include('partials.sidebar')
</aside>

<div class="ml-0 min-h-screen bg-slate-50 pt-16 md:ml-[260px]">

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const profileButton = document.getElementById('admin-profile-button');
    const profileMenu = document.getElementById('admin-profile-menu');
    const mobileButton = document.getElementById('admin-mobile-menu-button');
    const sidebar = document.getElementById('admin-sidebar');
    const sidebarOverlay = document.getElementById('admin-sidebar-overlay');
    const closeSidebar = () => { sidebar?.classList.add('-translate-x-full'); sidebarOverlay?.classList.add('hidden'); };
    profileButton?.addEventListener('click', event => { event.stopPropagation(); profileMenu?.classList.toggle('hidden'); });
    document.addEventListener('click', () => profileMenu?.classList.add('hidden'));
    mobileButton?.addEventListener('click', () => { sidebar?.classList.toggle('-translate-x-full'); sidebarOverlay?.classList.toggle('hidden'); });
    sidebarOverlay?.addEventListener('click', closeSidebar);
    sidebar?.querySelectorAll('a').forEach(link => link.addEventListener('click', () => { if (window.innerWidth < 768) closeSidebar(); }));
    document.querySelectorAll('[data-sidebar-submenu-button]').forEach(button => {
        button.addEventListener('click', () => {
            const submenu = document.getElementById(button.getAttribute('aria-controls'));
            const willOpen = submenu?.classList.contains('hidden');
            submenu?.classList.toggle('hidden');
            button.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
            button.querySelector('[data-sidebar-chevron]')?.classList.toggle('rotate-180', willOpen);
        });
    });
});
</script>
@endpush
