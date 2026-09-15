<?php

namespace App\Http\Controllers\Auth;

use App\Mail\DoctorWelcomeMail;
use App\Models\Country;
use App\Models\Speciality;
use App\Models\Module;
use App\Models\User;
use App\Models\Webinar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;

class WebLoginController
{
    public function login()
    {
        if (Auth::guard('web')->check()) {
            return redirect()->route('dashboard');
        }

        $response = [
            'title' => 'Login',
        ];
        return view('website.auth.login', $response);
    }

    public function dashboard()
    {
        if (!Auth::guard('web')->check()) {
            return redirect()
                ->route('login')
                ->with('error', 'Please login first');
        }

        $doctor = Auth::guard('web')->user();
        $currentTime = now();
        $webinars = Webinar::with(['speciality', 'people'])
            ->where('status', 'active')
            ->whereNotNull('scheduled_at')
            ->orderBy('scheduled_at')
            ->get();

        $liveWebinar = $webinars->first(fn (Webinar $webinar) =>
            $webinar->scheduled_at->lte($currentTime)
            && $webinar->scheduled_at->copy()->addMinutes($webinar->duration_minutes)->gt($currentTime)
        );
        $upcomingWebinars = $webinars->filter(fn (Webinar $webinar) => $webinar->scheduled_at->gte($currentTime));
        $userUpcoming = $doctor?->country
            ? $upcomingWebinars->first(fn (Webinar $w) => strcasecmp($w->country, $doctor->country) === 0)
            : null;
        $anyUpcoming = $upcomingWebinars->first();
        $userWebinar = $doctor?->country
            ? $webinars->first(fn ($w) => strcasecmp($w->country, $doctor->country) === 0)
            : null;
        $philippinesWebinar = $webinars->first(fn ($w) => strcasecmp($w->country, 'Philippines') === 0);

        $countdownWebinar = $userUpcoming
            ?: $anyUpcoming
            ?: $userWebinar
            ?: $philippinesWebinar
            ?: $webinars->first();

        $featuredWebinar = $liveWebinar ?: $countdownWebinar;
        $nextWebinar = $liveWebinar ?: $countdownWebinar;
        $certificateWebinar = $webinars
            ->filter(fn (Webinar $webinar) => filled($webinar->certificate_template_path))
            ->sortByDesc('scheduled_at')
            ->first();
        $hasCertificateComment = $certificateWebinar
            ? $certificateWebinar->allComments()->where('user_id', $doctor->id)->where('status', 'active')->exists()
            : false;
        $activeWebinar = $liveWebinar ?: $featuredWebinar;
        $playbackUrl = $activeWebinar?->meeting_url ?: $activeWebinar?->recording_url;
        $youtubeEmbedUrl = Webinar::youtubeEmbedUrl($activeWebinar?->meeting_url)
            ?: Webinar::youtubeEmbedUrl($activeWebinar?->recording_url);
        $isVideoFile = $playbackUrl && (bool) preg_match('/\.(mp4|webm|ogg|m3u8)(\?.*)?$/i', $playbackUrl);
        $hasLiveStream = (bool) ($youtubeEmbedUrl || $isVideoFile || ($playbackUrl && !str_contains($playbackUrl, 'example.com')));

        return view('website.dashboard', [
            'title' => 'Dashboard',
            'doctor' => $doctor,
            'liveWebinar' => $liveWebinar,
            'upcomingWebinars' => $upcomingWebinars,
            'nextWebinar' => $nextWebinar,
            'countdownWebinar' => $countdownWebinar,
            'featuredWebinar' => $featuredWebinar,
            'certificateWebinar' => $certificateWebinar,
            'hasCertificateComment' => $hasCertificateComment,
            'webinarCount' => $webinars->count(),
            'specialityCount' => Speciality::where('status', 'active')->count(),
            'playbackUrl' => $playbackUrl,
            'youtubeEmbedUrl' => $youtubeEmbedUrl,
            'isVideoFile' => $isVideoFile,
            'hasLiveStream' => $hasLiveStream,
        ]);
    }

    public function webinars()
    {
        $doctor = Auth::guard('web')->user();
        $currentTime = now();
        $allWebinars = Webinar::with(['speciality', 'people'])
            ->where('status', 'active')
            ->orderByRaw('scheduled_at IS NULL')
            ->orderBy('scheduled_at')
            ->get();

        $liveWebinars = $allWebinars->filter(fn (Webinar $webinar) =>
            $webinar->scheduled_at
            && $webinar->scheduled_at->lte($currentTime)
            && $webinar->scheduled_at->copy()->addMinutes($webinar->duration_minutes)->gt($currentTime)
        );
        $upcomingWebinars = $allWebinars->filter(fn (Webinar $webinar) =>
            $webinar->scheduled_at?->gt($currentTime)
        );
        $completedWebinars = $allWebinars->filter(fn (Webinar $webinar) =>
            !$webinar->scheduled_at
            || $webinar->scheduled_at->copy()->addMinutes($webinar->duration_minutes)->lte($currentTime)
        )->sortByDesc('scheduled_at');

        return view('website.webinars', compact(
            'doctor',
            'liveWebinars',
            'upcomingWebinars',
            'completedWebinars'
        ));
    }

    public function modules()
    {
        $doctor = Auth::guard('web')->user();
        $modules = Module::query()
            ->where('status', 'active')
            ->where(function ($query) {
                $query->whereNull('release_at')->orWhere('release_at', '<=', now());
            })
            ->orderBy('sequence_order')
            ->orderBy('id')
            ->get();

        return view('website.modules', compact('doctor', 'modules'));
    }

    public function snippets()
    {
        $doctor = Auth::guard('web')->user();
        $webinars = Webinar::with(['speciality', 'people'])
            ->where('status', 'active')
            ->orderByRaw('scheduled_at IS NULL')
            ->orderBy('scheduled_at')
            ->get();

        return view('website.snippets', [
            'title' => 'Snippet Videos',
            'doctor' => $doctor,
            'snippetWebinars' => $webinars->filter(fn (Webinar $webinar) => filled($webinar->recording_url))->take(4),
            'materialWebinars' => $webinars
                ->filter(fn (Webinar $webinar) => filled($webinar->post_read_url) || filled($webinar->post_read_file_path))
                ->take(4),
        ]);
    }

    public function certificatePage(?int $id = null)
    {
        $doctor = Auth::guard('web')->user();
        $query = Webinar::where('status', 'active');
        if ($id) {
            $webinar = (clone $query)->findOrFail($id);
        } else {
            $webinar = (clone $query)->whereNotNull('certificate_template_path')->orderByDesc('scheduled_at')->first()
                ?: (clone $query)->orderByDesc('scheduled_at')->firstOrFail();
        }

        $latestComment = $webinar->allComments()
            ->where('user_id', $doctor->id)
            ->where('status', 'active')
            ->latest()
            ->first();

        $hasCommented = !is_null($latestComment);

        return view('website.certificate', compact('doctor', 'webinar', 'hasCommented', 'latestComment'));
    }

    public function register()
    {
        if (Auth::guard('web')->check()) {
            return redirect()->route('dashboard');
        }

        $response = [
            'title' => 'Register',
            'country' => $this->registrationCountryNames(),
            'specialities' => Speciality::where('status', 'active')->orderBy('name')->get(),
        ];
        return view('website.auth.registration', $response);
    }

    public function registerPost(Request $request)
    {
        $registrationCountries = $this->registrationCountryNames();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'hospital' => ['required', 'string', 'max:255'],
            'speciality' => [
                'required',
                'string',
                'max:255',
                Rule::exists('specialities', 'name')->where(fn ($query) => $query->where('status', 'active')),
            ],
            'medical_registration_number' => ['required', 'string', 'max:255'],
            'country' => ['required', 'string', 'max:255', Rule::in($registrationCountries->all())],
            'mobile' => ['required', 'string', 'max:25', 'regex:/^\+?[0-9\s\-()]{7,25}$/', 'unique:users,mobile'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'hcp_confirmation' => ['accepted'],
            'terms' => ['accepted'],
        ]);

        $doctor = User::create([
            'type' => 'doctor',
            'status' => 'active',
            'name' => $validated['name'],
            'hospital' => $validated['hospital'],
            'speciality' => $validated['speciality'],
            'medical_registration_number' => $validated['medical_registration_number'],
            'country' => $validated['country'],
            'mobile' => $validated['mobile'],
            'email' => $validated['email'],
            'hcp_confirmed' => true,
            'terms_accepted_at' => now(),
        ]);

        Auth::guard('web')->login($doctor);
        $doctor->forceFill(['last_login_at' => now()])->save();
        $request->session()->regenerate();

        try {
            Mail::to($doctor->email)->send(new DoctorWelcomeMail($doctor));
        } catch (\Throwable $exception) {
            Log::error('Doctor welcome email could not be sent.', [
                'doctor_id' => $doctor->id,
                'message' => $exception->getMessage(),
            ]);
        }

        return redirect()
            ->route('dashboard')
            ->with('success', 'Registration successful. Welcome to PULCE Connect 2026.');
    }

    private function registrationCountryNames(): Collection
    {
        return Country::query()
            ->where('flag', 1)
            ->orderBy('name')
            ->pluck('name');
    }

    public function loginPost(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $doctor = User::where('email', $validated['email'])
            ->where('type', 'doctor')
            ->where('status', 'active')
            ->first();

        if (!$doctor) {
            return redirect()
                ->route('login')
                ->withInput()
                ->with('error', 'No active doctor account found for this email address');
        }

        Auth::guard('web')->login($doctor);
        $doctor->forceFill(['last_login_at' => now()])->save();
        $request->session()->regenerate();

        return redirect()
            ->route('dashboard')
            ->with('success', 'Welcome back, '.$doctor->name.'.');
    }
}
