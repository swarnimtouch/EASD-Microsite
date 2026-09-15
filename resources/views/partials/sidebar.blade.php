<nav class="flex-1 space-y-0.5 overflow-y-auto p-4">
    <div class="px-3 py-2 text-[10px] font-bold uppercase tracking-widest text-slate-400">Navigation</div>
    @foreach (getModules(request()->user()?->type ?? 'admin') as $key => $value)
        @php
            $hasChildren = count($value['child']);
            $groupState = $hasChildren ? trim((string) is_active_module($value['all_routes'], true)) : '';
            $groupOpen = $groupState !== '';
            $submenuId = 'admin-submenu-'.$key;
        @endphp
        @if(!$hasChildren)
            <a href="{{ $value['route'] ?? 'javascript:;' }}" class="{{ is_active_module($value['all_routes']) }} flex items-center gap-3 rounded-lg px-3 py-2.5 text-xs font-medium text-slate-500 no-underline transition hover:bg-cyan-50 hover:text-cyan-700 [&.active]:bg-cyan-50 [&.active]:font-semibold [&.active]:text-cyan-700"><i class="{!! $value['icon'] !!} w-[18px] text-center text-base"></i><span>{{ $value['name'] }}</span></a>
        @else
            <div class="pt-1">
                <button type="button" data-sidebar-submenu-button aria-controls="{{ $submenuId }}" aria-expanded="{{ $groupOpen ? 'true' : 'false' }}" class="flex w-full items-center gap-3 rounded-lg border-0 bg-transparent px-3 py-2.5 text-left text-xs font-semibold text-slate-500 transition hover:bg-cyan-50 hover:text-cyan-700">
                    <i class="{!! $value['icon'] !!} w-[18px] text-center text-base"></i><span class="flex-1">{{ $value['name'] }}</span><i data-sidebar-chevron class="bi bi-chevron-down text-[9px] transition-transform {{ $groupOpen ? 'rotate-180' : '' }}"></i>
                </button>
                <div id="{{ $submenuId }}" data-sidebar-submenu class="space-y-0.5 overflow-hidden {{ $groupOpen ? '' : 'hidden' }}">
                    @foreach ($value['child'] as $child)
                        <a href="{{ $child['route'] }}" class="{{ is_active_module($child['all_routes']) }} flex items-center rounded-lg py-2 pl-11 pr-3 text-[11px] font-medium text-slate-500 no-underline transition hover:bg-cyan-50 hover:text-cyan-700 [&.active]:bg-cyan-50 [&.active]:font-semibold [&.active]:text-cyan-700">{{ $child['name'] }}</a>
                    @endforeach
                </div>
            </div>
        @endif
    @endforeach
</nav>
<div class="m-4 flex items-center gap-2.5 rounded-xl border border-cyan-100 bg-cyan-50 p-3 text-cyan-700"><i class="bi bi-shield-check text-lg"></i><div class="flex flex-col"><strong class="text-[11px]">Secure Portal</strong><span class="text-[9px] text-slate-500">Protected administration</span></div></div>
