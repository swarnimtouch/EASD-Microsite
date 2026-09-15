<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\MyController;
use App\Models\Course;
use App\Models\Module;
use App\Models\QuizQuestion;
use App\Models\User;
use App\Models\Webinar;
use App\Models\WebinarPerson;
use App\Support\CertificatePdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends MyController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $webinarRelations = ['people', 'speciality'];
        $currentTime = now();
        $programCountries = [
            ['name' => 'Malaysia', 'flag' => 'malaysia'],
            ['name' => 'Philippines', 'flag' => 'philippines'],
            ['name' => 'Indonesia', 'flag' => 'indonesia'],
            ['name' => 'Thailand', 'flag' => 'thailand'],
        ];

        $countryWebinars = Webinar::with($webinarRelations)
            ->where('status', 'active')
            ->whereIn('country', collect($programCountries)->pluck('name'))
            ->orderByRaw('scheduled_at IS NULL')
            ->orderBy('scheduled_at')
            ->get()
            ->groupBy(fn (Webinar $webinar) => strtolower($webinar->country));

        $agendaCountries = collect($programCountries)->map(function (array $country) use ($countryWebinars) {
            $country['webinar'] = $countryWebinars->get(strtolower($country['name']))?->first();

            return $country;
        });

        $liveWebinar = Webinar::with($webinarRelations)
            ->where('status', 'active')
            ->whereBetween('scheduled_at', [$currentTime->copy()->subDay(), $currentTime])
            ->orderByDesc('scheduled_at')
            ->get()
            ->first(fn (Webinar $webinar) => $webinar->scheduled_at
                ->copy()
                ->addMinutes($webinar->duration_minutes)
                ->isAfter($currentTime));

        $upcomingWebinars = Webinar::with($webinarRelations)->where('status', 'active')
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '>=', $currentTime)
            ->orderBy('scheduled_at')
            ->get();

        $doctor = Auth::guard('web')->user();

        $userUpcoming = $doctor?->country
            ? $upcomingWebinars->first(fn ($w) => strcasecmp($w->country, $doctor->country) === 0)
            : null;
        $anyUpcoming = $upcomingWebinars->first();
        $userWebinar = $doctor?->country
            ? Webinar::with($webinarRelations)->where('status', 'active')->where('country', $doctor->country)->latest('scheduled_at')->first()
            : null;
        $philippinesWebinar = Webinar::with($webinarRelations)->where('status', 'active')->where('country', 'Philippines')->latest('scheduled_at')->first();

        $featuredWebinar = $liveWebinar
            ?: $userUpcoming
            ?: $anyUpcoming
            ?: $userWebinar
            ?: $philippinesWebinar
            ?: Webinar::with($webinarRelations)->where('status', 'active')->latest('scheduled_at')->first();

        return view('website.home', [
            'featuredWebinar' => $featuredWebinar,
            'isFeaturedLive' => $liveWebinar?->is($featuredWebinar) ?? false,
            'upcomingWebinars' => $upcomingWebinars->take(3),
            'agendaCountries' => $agendaCountries,
            'registeredDoctors' => User::where('type', 'doctor')->where('status', 'active')->count(),
            'facultyList' => WebinarPerson::query()
                ->whereHas('webinar', fn ($query) => $query->where('status', 'active'))
                ->with('webinar:id,title')
                ->orderByRaw("CASE role WHEN 'speaker' THEN 0 ELSE 1 END")
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function webinar(?int $id = null)
    {
        $doctor = Auth::guard('web')->user();
        $currentTime = now();
        $hostCountryNames = ['Philippines', 'Malaysia', 'Indonesia', 'Thailand'];

        $query = Webinar::with(['people', 'speciality', 'comments.user', 'comments.upvotes', 'comments.replies.upvotes'])->where('status', 'active');

        $requestedCountry = request('country');

        if ($id) {
            $webinar = $query->findOrFail($id);
        } elseif ($requestedCountry) {
            $webinar = (clone $query)->where('country', $requestedCountry)
                ->where('scheduled_at', '>=', $currentTime)
                ->orderBy('scheduled_at')
                ->first()
                ?: (clone $query)->where('country', $requestedCountry)->latest('scheduled_at')->first()
                ?: (clone $query)->where('country', 'Philippines')->firstOrFail();
        } else {
            // 1. Logged in user's country upcoming webinar (if any)
            $userUpcoming = ($doctor && $doctor->country)
                ? (clone $query)->where('country', $doctor->country)->where('scheduled_at', '>=', $currentTime)->orderBy('scheduled_at')->first()
                : null;

            // 2. Earliest upcoming webinar across ALL countries
            $anyUpcoming = (clone $query)->where('scheduled_at', '>=', $currentTime)->orderBy('scheduled_at')->first();

            // 3. Otherwise user's country webinar (recorded/latest)
            $userCountryWebinar = ($doctor && $doctor->country)
                ? (clone $query)->where('country', $doctor->country)->latest('scheduled_at')->first()
                : null;

            // 4. Default: Philippines webinar
            $philippinesWebinar = (clone $query)->where('country', 'Philippines')->latest('scheduled_at')->first();

            $webinar = $userUpcoming
                ?: $anyUpcoming
                ?: $userCountryWebinar
                ?: $philippinesWebinar
                ?: (clone $query)->orderByRaw('scheduled_at IS NULL')->orderBy('scheduled_at')->firstOrFail();
        }

        $tourWebinars = collect($hostCountryNames)->mapWithKeys(function ($countryName) use ($currentTime) {
            $cWebinar = Webinar::where('status', 'active')
                ->where('country', $countryName)
                ->where('scheduled_at', '>=', $currentTime)
                ->orderBy('scheduled_at')
                ->first()
                ?: Webinar::where('status', 'active')
                    ->where('country', $countryName)
                    ->latest('scheduled_at')
                    ->first();
            return [$countryName => $cWebinar];
        })->filter();

        $isUpcoming = $webinar->scheduled_at?->isAfter($currentTime) ?? false;
        $endsAt = $webinar->scheduled_at?->copy()->addMinutes($webinar->duration_minutes);
        $isLive = $webinar->scheduled_at
            && $webinar->scheduled_at->lte($currentTime)
            && $endsAt->gt($currentTime);
        $hasEnded = $endsAt?->lte($currentTime) ?? false;
        $playbackUrl = $hasEnded
            ? ($webinar->recording_url ?: $webinar->meeting_url)
            : ($webinar->meeting_url ?: $webinar->recording_url);

        return view('website.webinar', [
            'webinar' => $webinar,
            'doctor' => $doctor,
            'tourWebinars' => $tourWebinars,
            'hostCountryNames' => $hostCountryNames,
            'isUpcoming' => $isUpcoming,
            'isLive' => $isLive,
            'hasEnded' => $hasEnded,
            'canComment' => !$webinar->scheduled_at || $webinar->scheduled_at->lte($currentTime),
            'playbackUrl' => $playbackUrl,
            'youtubeEmbedUrl' => Webinar::youtubeEmbedUrl($playbackUrl),
            'hasCommented' => $doctor ? $webinar->allComments()->where('user_id', $doctor->id)->where('status', 'active')->exists() : false,
        ]);
    }

    public function certificate(Webinar $webinar)
    {
        abort_unless($webinar->status === 'active', 404);

        $doctor = Auth::guard('web')->user();
        $isInlinePreview = request()->boolean('preview') || request()->boolean('inline');

        $hasCommented = $webinar->allComments()
            ->where('user_id', $doctor->id)
            ->where('status', 'active')
            ->exists();

        if (!$hasCommented && !$isInlinePreview) {
            return redirect()->route('webinar', $webinar->id)
                ->with('certificate_error', 'Please submit a comment or question before downloading your certificate.');
        }

        if (!$webinar->certificate_template_path || !\Storage::disk('public')->exists($webinar->certificate_template_path)) {
            return redirect()->route('webinar', $webinar->id)
                ->with('certificate_error', 'The certificate template has not been published yet.');
        }

        $pdf = CertificatePdf::delegateCertificate(
            \Storage::disk('public')->path($webinar->certificate_template_path),
            $doctor->name,
            $webinar->certificate_name_x !== null ? (float) $webinar->certificate_name_x : null,
            $webinar->certificate_name_y !== null ? (float) $webinar->certificate_name_y : null,
            $webinar->certificate_font_size ? (int) $webinar->certificate_font_size : null,
            public_path('assets/fonts/Abril_Display_Italic.otf'),
            $webinar->certificate_font_color ?: '#8e5f16'
        );

        $filename = 'PULCE-Certificate-'.preg_replace('/[^A-Za-z0-9_-]+/', '-', $doctor->name).'.pdf';
        $disposition = $isInlinePreview ? 'inline' : 'attachment';

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => $disposition . '; filename="'.$filename.'"',
        ]);
    }

    public function certificateTemplateFile(Webinar $webinar)
    {
        abort_unless($webinar->certificate_template_path, 404);

        $disk = \Storage::disk('public');
        if ($disk->exists($webinar->certificate_template_path)) {
            $path = $disk->path($webinar->certificate_template_path);
            $mime = mime_content_type($path) ?: 'application/octet-stream';
            return response()->file($path, [
                'Content-Type' => $mime,
                'Cache-Control' => 'public, max-age=3600',
            ]);
        }

        abort(404);
    }

    public function downloadPostRead(Webinar $webinar)
    {
        abort_unless($webinar->status === 'active', 404);

        // 1. Check if uploaded file exists in public storage
        if ($webinar->post_read_file_path && \Storage::disk('public')->exists($webinar->post_read_file_path)) {
            $path = \Storage::disk('public')->path($webinar->post_read_file_path);
            $filename = 'PULCE-' . \Str::slug($webinar->title) . '-PostRead.pdf';
            return response()->download($path, $filename, ['Content-Type' => 'application/pdf']);
        }

        // 2. Check if post_read_url points to a local file
        if ($webinar->post_read_url && !str_starts_with($webinar->post_read_url, 'http://') && !str_starts_with($webinar->post_read_url, 'https://')) {
            if (\Storage::disk('public')->exists($webinar->post_read_url)) {
                $path = \Storage::disk('public')->path($webinar->post_read_url);
                return response()->download($path, 'PULCE-' . \Str::slug($webinar->title) . '-PostRead.pdf', ['Content-Type' => 'application/pdf']);
            }
        }

        // 3. Generate official branded Post-read PDF summary for this session
        $pdf = CertificatePdf::generateSessionPostReadPdf($webinar);
        $filename = 'PULCE-' . \Str::slug($webinar->title) . '-PostRead-Material.pdf';

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function checkEmail(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        return response()->json(!User::where('email', $request->email)->exists());
    }

    public function checkMobile(Request $request)
    {
        $request->validate([
            'mobile' => ['nullable', 'regex:/^\+[0-9]{7,20}$/'],
        ]);

        if (!$request->filled('mobile')) {
            return response()->json(true);
        }

        return response()->json(!User::where('mobile', $request->mobile)->exists());
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Logout Successfully');
    }

}
