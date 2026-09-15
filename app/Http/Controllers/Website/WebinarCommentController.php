<?php

namespace App\Http\Controllers\Website;

use App\Events\WebinarCommentCreated;
use App\Events\WebinarCommentUpvoted;
use App\Http\Controllers\MyController;
use App\Models\Webinar;
use App\Models\WebinarComment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebinarCommentController extends MyController
{
    public function store(Request $request, Webinar $webinar): RedirectResponse|JsonResponse
    {
        abort_unless($webinar->status === 'active', 404);

        $currentTime = now();
        $hasStarted = !$webinar->scheduled_at || $webinar->scheduled_at->lte($currentTime);
        $isCertificateFlow = $request->input('redirect_to') === 'certificate';

        if (!$hasStarted && !$isCertificateFlow && empty($webinar->certificate_template_path)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Comments can be submitted once the webinar begins.'], 422);
            }
            return back()->with('comment_error', 'Comments can be submitted once the webinar begins.');
        }

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:1000'],
            'parent_id' => ['nullable', 'integer'],
        ], [], ['body' => 'question']);

        $parent = null;
        if (!empty($validated['parent_id'])) {
            $parent = $webinar->allComments()->whereNull('parent_id')->where('status', 'active')->findOrFail($validated['parent_id']);
        }

        $comment = $webinar->allComments()->create([
            'user_id' => $request->user('web')->id,
            'parent_id' => $parent?->id,
            'body' => trim($validated['body']),
            'status' => 'active',
        ]);

        try {
            broadcast(new WebinarCommentCreated($comment))->toOthers();
        } catch (\Throwable $exception) {
            Log::warning('Webinar comment broadcast failed.', [
                'comment_id' => $comment->id,
                'message' => $exception->getMessage(),
            ]);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Your question has been submitted.',
                'comment' => WebinarCommentCreated::payload($comment),
            ]);
        }

        if ($request->input('redirect_to') === 'certificate') {
            return redirect()->route('certificate.page', $webinar->id)
                ->with('comment_success', 'Your comment has been submitted. Your certificate is now unlocked.');
        }

        $redirectRoute = $request->input('redirect_to') === 'dashboard' ? 'dashboard' : 'webinar';
        $parameters = $redirectRoute === 'webinar' ? [$webinar->id] : [];

        return redirect()->route($redirectRoute, $parameters)->with('comment_success', 'Your question has been submitted. Your certificate is now unlocked.');
    }

    public function upvote(Request $request, Webinar $webinar, WebinarComment $comment): JsonResponse
    {
        abort_unless($webinar->status === 'active' && $comment->webinar_id === $webinar->id && $comment->status === 'active', 404);

        $vote = $comment->upvotes()->where('user_id', $request->user('web')->id)->first();
        $upvoted = !$vote;

        if ($vote) {
            $vote->delete();
        } else {
            $comment->upvotes()->create(['user_id' => $request->user('web')->id]);
        }

        $count = $comment->upvotes()->count();
        try {
            broadcast(new WebinarCommentUpvoted($comment, $request->user('web')->id, $upvoted))->toOthers();
        } catch (\Throwable $exception) {
            Log::warning('Webinar comment upvote broadcast failed.', ['comment_id' => $comment->id, 'message' => $exception->getMessage()]);
        }

        return response()->json(['upvoted' => $upvoted, 'upvotes_count' => $count]);
    }
}
