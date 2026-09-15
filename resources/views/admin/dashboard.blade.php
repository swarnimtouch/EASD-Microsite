@extends('layouts.admin')
@section('content')
<main class="h-full flex-1 overflow-y-auto bg-slate-50 px-7 py-8">
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    <div class="mb-7 flex flex-col justify-between gap-4 md:flex-row md:items-end">
        <div><div class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Admin Portal / Overview</div><h1 class="mt-1 text-2xl font-extrabold tracking-tight text-slate-900">Dashboard Overview</h1><p class="mt-1 text-xs font-medium text-slate-500">Monitor content, healthcare professional engagement, and platform activity.</p></div>
        <div class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-600"><i class="bi bi-calendar3 mr-2 text-cyan-600"></i>{{ now()->format('d M Y') }}</div>
    </div>

    @php($kpis = [
        ['route'=>'admin.doctors','icon'=>'bi-collection-play','value'=>$stats['courses'],'label'=>'Series','detail'=>$stats['active_courses'].' active'],
        ['route'=>'admin.doctors','icon'=>'bi-play-btn','value'=>$stats['episodes'],'label'=>'Episodes','detail'=>$stats['active_episodes'].' active'],
        ['route'=>'admin.doctors','icon'=>'bi-people','value'=>$stats['doctors'],'label'=>'Doctors','detail'=>$stats['active_doctors'].' active'],
        ['route'=>'admin.doctors','icon'=>'bi-chat-square-text','value'=>$stats['feedback_submitted'],'label'=>'Feedback Submitted','detail'=>$stats['feedback_questions'].' active questions'],
    ])
    <section class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach($kpis as $item)
        <a href="{{ route($item['route']) }}" class="group rounded-2xl border border-slate-200 bg-white p-5 no-underline shadow-sm transition hover:-translate-y-0.5 hover:border-cyan-200 hover:shadow-md">
            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-cyan-50 text-lg text-cyan-600"><i class="bi {{ $item['icon'] }}"></i></span>
            <strong class="mt-4 block text-2xl font-extrabold text-slate-900">{{ number_format($item['value']) }}</strong><span class="mt-1 block text-sm font-medium text-slate-500">{{ $item['label'] }}</span><span class="mt-2 block text-[11px] font-semibold text-cyan-600">{{ $item['detail'] }}</span>
        </a>
        @endforeach
    </section>

    <section class="mb-6 grid grid-cols-1 gap-6 xl:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-6 py-5"><h2 class="text-base font-bold text-slate-900">Overall Progress</h2><p class="mt-1 text-xs text-slate-400">{{ number_format($progress['expected']) }} expected doctor-episode records</p></div>
            <div class="p-6"><div class="mb-7 text-center"><strong class="text-4xl font-extrabold text-slate-900">{{ $progress['completion_rate'] }}%</strong><p class="mt-2 text-xs text-slate-500">Certificate-ready completion</p></div>
                @foreach([['Video completed',$progress['video_completion_rate']],['Feedback submitted',$progress['feedback_rate']]] as $bar)
                <div class="mb-5"><div class="mb-2 flex justify-between text-xs font-semibold text-slate-600"><span>{{ $bar[0] }}</span><span>{{ $bar[1] }}%</span></div><div class="h-2 overflow-hidden rounded-full bg-slate-100"><div class="h-full rounded-full bg-cyan-500" style="width:{{ $bar[1] }}%"></div></div></div>
                @endforeach
                <div class="grid grid-cols-3 border-t border-slate-100 pt-5 text-center"><div><strong class="text-emerald-500">{{ number_format($progress['completed']) }}</strong><span class="block text-[10px] text-slate-400">Completed</span></div><div><strong class="text-amber-500">{{ number_format($progress['in_progress']) }}</strong><span class="block text-[10px] text-slate-400">In Progress</span></div><div><strong class="text-slate-600">{{ number_format($progress['not_started']) }}</strong><span class="block text-[10px] text-slate-400">Not Started</span></div></div>
            </div>
        </div>
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm xl:col-span-2">
            <div class="border-b border-slate-100 px-6 py-5"><h2 class="text-base font-bold text-slate-900">Episode Progress</h2><p class="mt-1 text-xs text-slate-400">Latest active episodes by doctor activity</p></div>
            <div class="overflow-x-auto p-6"><table class="w-full text-left"><thead><tr class="bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-500"><th class="px-4 py-3">Episode</th><th class="px-4 py-3 text-right">Started</th><th class="px-4 py-3 text-right">Video</th><th class="px-4 py-3 text-right">Feedback</th><th class="px-4 py-3">Avg Progress</th></tr></thead><tbody>
                @forelse($episodeProgress as $module) @php($averageProgress=(int)round($module->average_progress??0))
                <tr class="border-b border-slate-100"><td class="px-4 py-4"><strong class="block text-xs text-slate-800">{{ $module->title }}</strong><span class="text-[10px] text-slate-400">{{ $module->course?->title ?? 'No series assigned' }}</span></td><td class="px-4 py-4 text-right text-xs">{{ number_format($module->started_count) }}</td><td class="px-4 py-4 text-right text-xs text-emerald-500">{{ number_format($module->video_completed_count) }}</td><td class="px-4 py-4 text-right text-xs text-cyan-600">{{ number_format($module->feedback_submitted_count) }}</td><td class="px-4 py-4"><div class="flex items-center gap-3"><div class="h-2 flex-1 rounded-full bg-slate-100"><div class="h-full rounded-full bg-cyan-500" style="width:{{ $averageProgress }}%"></div></div><span class="text-[10px] font-bold">{{ $averageProgress }}%</span></div></td></tr>
                @empty <tr><td colspan="5" class="px-4 py-12 text-center text-xs text-slate-400">No active episodes found.</td></tr>@endforelse
            </tbody></table></div>
        </div>
    </section>

    <section class="grid grid-cols-1 gap-6 xl:grid-cols-3"><div class="rounded-2xl border border-slate-200 bg-white shadow-sm"><div class="border-b border-slate-100 px-6 py-5"><h2 class="text-base font-bold">Quick Counts</h2><p class="mt-1 text-xs text-slate-400">Live totals from the system</p></div><div class="px-6 py-3">@foreach([['Employees',$stats['employees']],['Certificates Unlocked',$stats['certificates_unlocked']],['Progress Started',$progress['started']],['Expected Records',$progress['expected']]] as $count)<div class="flex justify-between border-b border-slate-100 py-3 text-xs text-slate-600"><span>{{ $count[0] }}</span><strong class="text-slate-800">{{ number_format($count[1]) }}</strong></div>@endforeach</div></div>
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm xl:col-span-2"><div class="border-b border-slate-100 px-6 py-5"><h2 class="text-base font-bold">Recent Feedback</h2><p class="mt-1 text-xs text-slate-400">Latest certificate unlock activity</p></div><div class="p-6">@forelse($recentFeedback as $item)<div class="flex items-center gap-4 py-2"><span class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-50 text-emerald-500"><i class="bi bi-check2-circle"></i></span><div class="flex-1"><strong class="block text-xs">{{ $item->user?->name ?? 'Doctor' }}</strong><span class="text-[10px] text-slate-400">{{ $item->module?->title ?? 'Episode' }}</span></div><div class="text-right"><span class="rounded-full bg-emerald-50 px-2 py-1 text-[10px] font-bold text-emerald-600">Submitted</span><time class="mt-1 block text-[9px] text-slate-400">{{ $item->quiz_completed_at?->format('d M Y, h:i A') }}</time></div></div>@empty<div class="py-12 text-center text-xs text-slate-400">No feedback has been submitted yet.</div>@endforelse</div></div>
    </section>
</main>
@endsection
