<?php

namespace App\Http\Controllers\Admin;

use App\Models\Webinar;
use App\Models\WebinarComment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class WebinarCommentController
{
    public function index(Request $request)
    {
        $query = WebinarComment::with(['webinar', 'user'])->latest();

        if ($request->filled('webinar_id')) {
            $query->where('webinar_id', $request->integer('webinar_id'));
        }
        if ($request->filled('search')) {
            $search = trim($request->string('search')->toString());
            $query->where(function ($builder) use ($search) {
                $builder->where('body', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($user) => $user->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"))
                    ->orWhereHas('webinar', fn ($webinar) => $webinar->where('title', 'like', "%{$search}%"));
            });
        }

        return view('admin.webinar_comments.index', [
            'title' => 'Webinar Comments',
            'breadcrumb' => breadcrumb(['Webinar Comments' => route('admin.webinar_comments')]),
            'comments' => $query->paginate(25)->withQueryString(),
            'webinars' => Webinar::withCount('allComments')->having('all_comments_count', '>', 0)->orderBy('title')->get(),
        ]);
    }

    public function statusChange(WebinarComment $comment): JsonResponse
    {
        $comment->update(['status' => $comment->status === 'active' ? 'hidden' : 'active']);

        return response()->json(['success' => true, 'status' => $comment->status]);
    }

    public function delete(WebinarComment $comment): RedirectResponse
    {
        $comment->delete();

        return back()->with('success', 'Webinar comment deleted successfully.');
    }
}
