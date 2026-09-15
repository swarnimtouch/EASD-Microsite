<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebinarCommentUpvote extends Model
{
    protected $guarded = [];

    public function comment(): BelongsTo
    {
        return $this->belongsTo(WebinarComment::class, 'webinar_comment_id');
    }
}
