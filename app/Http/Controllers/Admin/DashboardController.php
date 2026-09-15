<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $activeDoctorCount = User::where('type', 'doctor')->where('status', 'active')->count();
        $activeModuleCount = 0;
        $expectedProgressCount = $activeDoctorCount * $activeModuleCount;


        $startedCount = 0;
        $videoCompletedCount = 0;
        $feedbackSubmittedCount = 0;
        $completedCount = 0;
        $inProgressCount = 0;
        $notStartedCount = max(0, $expectedProgressCount - $startedCount);

        $completionRate = $expectedProgressCount
            ? round(($completedCount / $expectedProgressCount) * 100)
            : 0;

        $videoCompletionRate = $expectedProgressCount
            ? round(($videoCompletedCount / $expectedProgressCount) * 100)
            : 0;

        $feedbackRate = $expectedProgressCount
            ? round(($feedbackSubmittedCount / $expectedProgressCount) * 100)
            : 0;

        $episodeProgress = collect([]);

        $recentFeedback = collect([]);

        return view('admin.dashboard', [
            'title' => __('Dashboard'),
            'breadcrumb' => breadcrumb([__('Dashboard') => route('admin.dashboard')]),
            'stats' => [
                'courses' => 0,
                'active_courses' => 0,
                'episodes' => 0,
                'active_episodes' => $activeModuleCount,
                'doctors' => User::where('type', 'doctor')->count(),
                'active_doctors' => $activeDoctorCount,
                'employees' => User::where('type', 'employee')->count(),
                'feedback_questions' => 0,
                'feedback_submitted' => $feedbackSubmittedCount,
                'certificates_unlocked' => $completedCount,
            ],
            'progress' => [
                'expected' => $expectedProgressCount,
                'started' => $startedCount,
                'not_started' => $notStartedCount,
                'in_progress' => $inProgressCount,
                'completed' => $completedCount,
                'completion_rate' => $completionRate,
                'video_completion_rate' => $videoCompletionRate,
                'feedback_rate' => $feedbackRate,
            ],
            'episodeProgress' => $episodeProgress,
            'recentFeedback' => $recentFeedback,
        ]);
    }

}
