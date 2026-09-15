@extends('layouts.admin')

@section('content')
<div id="kt_content" class="h-full overflow-y-auto bg-slate-50 p-7">
  @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
  <div class="mb-6 flex flex-col justify-between gap-4 md:flex-row md:items-end">
    <div><p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Community Management</p><h1 class="mt-1 text-2xl font-extrabold text-slate-900">Webinar Comments</h1><p class="mt-1 text-xs font-medium text-slate-500">Review and moderate doctor questions webinar by webinar.</p></div>
    <div id="new-comment-notice" class="hidden items-center gap-3 rounded-xl border border-cyan-200 bg-cyan-50 px-4 py-3 text-xs font-bold text-cyan-700"><i class="bi bi-broadcast"></i><span>A new live question was received.</span><button type="button" onclick="window.location.reload()" class="rounded-lg bg-cyan-600 px-3 py-1.5 text-white">Refresh</button></div>
  </div>

  <div class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
    @foreach($webinars as $webinar)
    <a href="{{ route('admin.webinar_comments', ['webinar_id' => $webinar->id]) }}" class="rounded-2xl border bg-white p-5 no-underline shadow-sm transition hover:border-cyan-300 hover:shadow-md {{ request('webinar_id') == $webinar->id ? 'border-cyan-500 ring-2 ring-cyan-100' : 'border-slate-200' }}"><div class="flex items-start justify-between gap-3"><div class="min-w-0"><p class="line-clamp-2 text-sm font-bold text-slate-800">{{ $webinar->title }}</p>@if($webinar->scheduled_at)<p class="mt-2 text-[10px] font-semibold text-slate-400">{{ $webinar->scheduled_at->format('d M Y, h:i A') }}</p>@endif</div><span class="flex h-9 min-w-9 items-center justify-center rounded-xl bg-cyan-50 px-2 text-xs font-extrabold text-cyan-700">{{ $webinar->all_comments_count }}</span></div></a>
    @endforeach
  </div>

  <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <form method="GET" action="{{ route('admin.webinar_comments') }}" class="flex flex-col gap-3 border-b border-slate-200 p-5 md:flex-row">
      <select name="webinar_id" class="form-select md:max-w-sm"><option value="">All webinars</option>@foreach($webinars as $webinar)<option value="{{ $webinar->id }}" @selected((string)request('webinar_id') === (string)$webinar->id)>{{ $webinar->title }} ({{ $webinar->all_comments_count }})</option>@endforeach</select>
      <input type="search" name="search" value="{{ request('search') }}" placeholder="Search doctor, email, webinar, or comment" class="form-control flex-1">
      <button class="btn btn-primary" type="submit"><i class="bi bi-search me-2"></i>Filter</button>
      @if(request()->hasAny(['webinar_id','search']))<a href="{{ route('admin.webinar_comments') }}" class="btn btn-light">Clear</a>@endif
    </form>

    <div class="overflow-x-auto">
      <table class="w-full">
        <thead><tr><th>Webinar</th><th>Doctor</th><th>Question / Comment</th><th>Submitted</th><th>Status</th><th class="text-right">Actions</th></tr></thead>
        <tbody>
          @forelse($comments as $comment)
          <tr>
            <td><div class="max-w-xs"><p class="font-bold text-slate-800">{{ $comment->webinar->title }}</p>@if($comment->webinar->scheduled_at)<p class="mt-1 text-[10px] text-slate-400">{{ $comment->webinar->scheduled_at->format('d M Y') }}</p>@endif</div></td>
            <td><div class="flex items-center gap-3"><img src="{{ $comment->user->profile_image }}" alt="{{ $comment->user->name }}" class="h-9 w-9 rounded-full border border-slate-200 object-cover"><div><p class="font-bold text-slate-800">{{ $comment->user->name }}</p><p class="text-[10px] text-slate-400">{{ $comment->user->email }}</p></div></div></td>
            <td><p class="max-w-xl whitespace-pre-line leading-6 text-slate-600">{{ $comment->body }}</p></td>
            <td class="whitespace-nowrap"><p>{{ $comment->created_at->format('d M Y') }}</p><p class="text-[10px] text-slate-400">{{ $comment->created_at->format('h:i A') }}</p></td>
            <td><label class="form-check form-switch form-check-custom form-check-solid"><input class="form-check-input comment-status" data-url="{{ route('admin.webinar_comments.status_change', $comment) }}" type="checkbox" @checked($comment->status === 'active')><span class="text-[10px] font-bold">{{ $comment->status === 'active' ? 'Visible' : 'Hidden' }}</span></label></td>
            <td><form method="POST" action="{{ route('admin.webinar_comments.delete', $comment) }}" onsubmit="return confirm('Delete this comment permanently?')">@csrf @method('DELETE')<button type="submit" class="btn btn-sm" title="Delete"><i class="bi bi-trash text-rose-500"></i></button></form></td>
          </tr>
          @empty<tr><td colspan="6" class="py-12 text-center"><i class="bi bi-chat-left-dots text-4xl text-slate-300"></i><p class="mt-3 font-bold text-slate-600">No webinar comments found</p></td></tr>@endforelse
        </tbody>
      </table>
    </div>
    @if($comments->hasPages())<div class="border-t border-slate-200 p-5">{{ $comments->links() }}</div>@endif
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.comment-status').forEach(function (checkbox) {
    checkbox.addEventListener('change', async function () {
      const previous = !checkbox.checked;
      try {
        const response = await window.axios.post(checkbox.dataset.url);
        checkbox.nextElementSibling.textContent = response.data.status === 'active' ? 'Visible' : 'Hidden';
      } catch (error) {
        checkbox.checked = previous;
        window.Swal?.fire('Error', 'Unable to update comment visibility.', 'error');
      }
    });
  });
  if (window.Echo) {
    window.Echo.private('comments.webinars.admin').listen('.comment.created', function () {
      document.getElementById('new-comment-notice')?.classList.replace('hidden', 'flex');
    });
  }
});
</script>
@endpush
