<?php

namespace App\Events;

use App\Models\WebinarComment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WebinarCommentCreated implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public function __construct(public WebinarComment $comment)
    {
        $this->comment->loadMissing(['user', 'webinar']);
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('comments.webinar.'.$this->comment->webinar_id),
            new PrivateChannel('comments.webinars.admin'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'comment.created';
    }

    public function broadcastWith(): array
    {
        return ['comment' => self::payload($this->comment)];
    }

    public static function payload(WebinarComment $comment): array
    {
        $comment->loadMissing(['user', 'webinar']);

        return [
            'id' => $comment->id,
            'name' => $comment->user->name,
            'avatar' => $comment->user->profile_image,
            'body' => $comment->body,
            'created_at' => $comment->created_at->format('h:i A'),
            'parent_id' => $comment->parent_id,
            'upvotes_count' => $comment->upvotes()->count(),
            'webinar_id' => $comment->webinar_id,
            'webinar_title' => $comment->webinar?->title,
        ];
    }
}
