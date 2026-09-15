<?php

namespace Database\Seeders;

use App\Models\Speciality;
use App\Models\Webinar;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProgramWebinarSeeder extends Seeder
{
    public function run(): void
    {
        $sessions = [
            [
                'country' => 'Malaysia',
                'scheduled_at' => '2026-11-18 10:00:00',
                'timezone_label' => 'MYT (UTC+8)',
                'title' => 'Integrated Cardio-Renal-Metabolic Care',
                'speciality' => 'Cardiology',
                'description' => 'Integrated strategies for cardiovascular, renal and metabolic risk management.',
                'speaker' => ['Dr. Aisha Rahman', 'MD, MRCP', 'Senior Consultant Cardiologist', 'University Malaya Medical Centre'],
                'moderator' => ['Dr. Daniel Lim', 'MD, FACC', 'Clinical Lead', 'National Heart Institute Malaysia'],
            ],
            [
                'country' => 'Philippines',
                'scheduled_at' => '2026-11-19 10:00:00',
                'timezone_label' => 'PHT (UTC+8)',
                'title' => 'Translating GDMT into Everyday Clinical Practice',
                'speciality' => 'Internal Medicine',
                'description' => 'Practical adoption of guideline-directed therapy in multidisciplinary clinical care.',
                'speaker' => ['Dr. Maria Santos', 'MD, FPCP', 'Consultant Physician', 'Philippine Heart Center'],
                'moderator' => ['Dr. Ramon Cruz', 'MD, FACC', 'Program Chair', 'Manila Medical Centre'],
            ],
            [
                'country' => 'Indonesia',
                'scheduled_at' => '2026-11-21 10:00:00',
                'timezone_label' => 'WIB (UTC+7)',
                'title' => 'Cardio-Renal-Metabolic Integration in Heart Failure',
                'speciality' => 'Nephrology',
                'description' => 'A multidisciplinary approach to heart failure and cardio-renal comorbidities.',
                'speaker' => ['Dr. Arif Pratama', 'MD, SpJP', 'Consultant Cardiologist', 'National Cardiovascular Center Harapan Kita'],
                'moderator' => ['Dr. Sari Wijaya', 'MD, PhD', 'Senior Clinical Faculty', 'University of Indonesia'],
            ],
            [
                'country' => 'Thailand',
                'scheduled_at' => '2026-11-22 10:00:00',
                'timezone_label' => 'ICT (UTC+7)',
                'title' => 'Optimizing Advanced Patient Outcomes & Future Horizons',
                'speciality' => 'Diabetology',
                'description' => 'Emerging evidence and practical pathways for better long-term patient outcomes.',
                'speaker' => ['Dr. Narin Chaiyaporn', 'MD, FRCPT', 'Consultant Endocrinologist', 'Chulalongkorn Memorial Hospital'],
                'moderator' => ['Dr. Kanya Sutham', 'MD, FACC', 'Clinical Program Director', 'Siriraj Hospital'],
            ],
        ];

        DB::transaction(function () use ($sessions) {
            foreach ($sessions as $session) {
                $speciality = Speciality::firstOrCreate(
                    ['name' => $session['speciality']],
                    ['description' => $session['speciality'].' program speciality', 'status' => 'active']
                );

                $webinar = Webinar::updateOrCreate(
                    ['country' => $session['country']],
                    [
                        'speciality_id' => $speciality->id,
                        'title' => $session['title'],
                        'type' => 'Scientific Session',
                        'duration_minutes' => 180,
                        'description' => $session['description'],
                        'speaker_name' => $session['speaker'][0],
                        'speaker_bio' => 'Regional expert faculty for PULCE Connect 2026.',
                        'meeting_url' => 'https://www.youtube.com/watch?v=M7lc1UVf-VE',
                        'recording_url' => 'https://www.youtube.com/watch?v=M7lc1UVf-VE',
                        'post_read_url' => 'generated',
                        'scheduled_at' => $session['scheduled_at'],
                        'timezone_label' => $session['timezone_label'],
                        'publish_immediately' => false,
                        'status' => 'active',
                    ]
                );

                foreach (['speaker', 'moderator'] as $sortOrder => $role) {
                    [$name, $qualifications, $position, $institution] = $session[$role];

                    $webinar->people()->updateOrCreate(
                        ['role' => $role, 'name' => $name],
                        [
                            'designation' => $position,
                            'qualifications' => $qualifications,
                            'current_position' => $position,
                            'institution' => $institution,
                            'country' => $session['country'],
                            'bio' => ucfirst($role).' for the '.$session['country'].' scientific session.',
                            'sort_order' => $sortOrder + 1,
                        ]
                    );
                }
            }
        });
    }
}
