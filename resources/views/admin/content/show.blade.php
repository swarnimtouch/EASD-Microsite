@extends('layouts.admin', ['title' => 'Content'])
@section('content')
<main id="kt_content" class="h-full flex-1 overflow-y-auto bg-slate-50 p-7">
    <article class="mx-auto max-w-4xl rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
        <div class="mb-2 text-[10px] font-bold uppercase tracking-widest text-slate-400">Content / Preview</div>
        <h1 class="mb-6 text-2xl font-extrabold tracking-tight text-slate-900">{{ $content->title }}</h1>
        <div class="whitespace-pre-line text-sm leading-7 text-slate-600">{{ $content->content }}</div>
    </article>
</main>
@endsection
