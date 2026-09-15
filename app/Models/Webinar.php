<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Webinar extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'publish_immediately' => 'boolean',
        ];
    }

    public function speciality(): BelongsTo
    {
        return $this->belongsTo(Speciality::class);
    }

    public function people(): HasMany
    {
        return $this->hasMany(WebinarPerson::class)->orderBy('sort_order')->orderBy('id');
    }

    public function speakers(): HasMany
    {
        return $this->hasMany(WebinarPerson::class)->where('role', 'speaker')->orderBy('sort_order');
    }

    public function moderators(): HasMany
    {
        return $this->hasMany(WebinarPerson::class)->where('role', 'moderator')->orderBy('sort_order');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(WebinarComment::class)
            ->where('status', 'active')
            ->whereNull('parent_id')
            ->with(['user', 'replies'])
            ->withCount('upvotes')
            ->orderByDesc('upvotes_count')
            ->latest();
    }

    public function allComments(): HasMany
    {
        return $this->hasMany(WebinarComment::class);
    }

    public function getCoverImageAttribute(): string
    {
        if ($this->cover_image_path) return asset('storage/' . $this->cover_image_path);
        if ($this->cover_image_url) return $this->cover_image_url;

        return asset('assets/images/branding/pulce-logo.png');
    }

    public static function youtubeEmbedUrl(?string $url): ?string
    {
        if (!$url) return null;

        $parts = parse_url($url);
        $host = strtolower($parts['host'] ?? '');
        $path = trim($parts['path'] ?? '', '/');
        $videoId = null;

        if (in_array($host, ['youtu.be', 'www.youtu.be'], true)) {
            $videoId = explode('/', $path)[0] ?? null;
        } elseif (in_array($host, ['youtube.com', 'www.youtube.com', 'm.youtube.com', 'youtube-nocookie.com', 'www.youtube-nocookie.com'], true)) {
            parse_str($parts['query'] ?? '', $query);
            if ($path === 'watch') $videoId = $query['v'] ?? null;
            if (preg_match('#^(?:embed|shorts|live)/([^/]+)#', $path, $matches)) $videoId = $matches[1];
        }

        if (!$videoId || !preg_match('/^[A-Za-z0-9_-]{6,20}$/', $videoId)) return null;

        return 'https://www.youtube-nocookie.com/embed/'.$videoId.'?rel=0';
    }
}
