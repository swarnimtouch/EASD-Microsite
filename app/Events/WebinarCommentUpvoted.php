<?php

namespace App\Events;

use App\Models\WebinarComment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WebinarCommentUpvoted implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public function __construct(public WebinarComment $comment, public int $userId, public bool $upvoted) {}

    public function broadcastOn(): Channel
    {
        return new Channel('comments.webinar.'.$this->comment->webinar_id);
    }

    public function broadcastAs(): string
    {
        return 'comment.upvoted';
    }

    public function broadcastWith(): array
    {
        return ['comment_id' => $this->comment->id, 'user_id' => $this->userId, 'upvoted' => $this->upvoted, 'upvotes_count' => $this->comment->upvotes()->count()];
    }
}
