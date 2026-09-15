<?php

namespace Database\Seeders;

use App\Models\Speciality;
use App\Models\User;
use App\Models\Webinar;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class PulceDemoSeeder extends Seeder
{
    public function run(): void
    {
        $speciality = Speciality::updateOrCreate(
            ['name' => 'Cardiology'],
            ['description' => 'Heart failure and cardiovascular medicine', 'status' => 'active']
        );

        User::updateOrCreate(
            ['email' => 'demo.doctor@pulce.test'],
            [
                'type' => 'doctor',
                'status' => 'active',
                'name' => 'Dr. Demo Delegate',
                'mobile' => '+639171234567',
                'country' => 'Philippines',
                'hospital' => 'PULCE Test Medical Centre',
                'speciality' => 'Cardiology',
                'medical_registration_number' => 'PULCE-TEST-2026',
                'hcp_confirmed' => true,
                'terms_accepted_at' => now(),
            ]
        );

        $certificatePath = 'uploads/webinar-certificates/pulce-demo-certificate.pdf';
        Storage::disk('public')->put($certificatePath, $this->certificateTemplate());

        $webinars = [
            [
                'title' => 'PULCE Connect Live Test Session',
                'type' => 'International',
                'duration_minutes' => 120,
                'description' => 'A live test webinar for validating the delegate dashboard, discussion and certificate workflow.',
                'meeting_url' => 'https://example.com/pulce-live-session',
                'recording_url' => 'https://example.com/pulce-snippet-live',
                'post_read_url' => 'https://example.com/pulce-live-material.pdf',
                'scheduled_at' => now()->subMinutes(5),
                'timezone_label' => 'Philippines (PHT)',
                'certificate_template_path' => $certificatePath,
            ],
            [
                'title' => 'Indonesia Scientific Session',
                'type' => 'Regional',
                'duration_minutes' => 90,
                'description' => 'Guideline-directed medical therapy in everyday clinical practice.',
                'meeting_url' => 'https://example.com/pulce-indonesia',
                'recording_url' => 'https://example.com/pulce-snippet-indonesia',
                'post_read_url' => 'https://example.com/pulce-indonesia-material.pdf',
                'scheduled_at' => now()->addDay(),
                'timezone_label' => 'Indonesia (WIB)',
                'certificate_template_path' => null,
            ],
            [
                'title' => 'Thailand Scientific Session',
                'type' => 'Regional',
                'duration_minutes' => 90,
                'description' => 'Early intervention across the cardio-renal-metabolic continuum.',
                'meeting_url' => 'https://example.com/pulce-thailand',
                'recording_url' => 'https://example.com/pulce-snippet-thailand',
                'post_read_url' => 'https://example.com/pulce-thailand-material.pdf',
                'scheduled_at' => now()->addDays(2),
                'timezone_label' => 'Thailand (ICT)',
                'certificate_template_path' => null,
            ],
        ];

        foreach ($webinars as $index => $data) {
            $webinar = Webinar::updateOrCreate(
                ['title' => $data['title']],
                array_merge($data, [
                    'speciality_id' => $speciality->id,
                    'publish_immediately' => false,
                    'status' => 'active',
                ])
            );

            $webinar->people()->delete();
            $webinar->people()->createMany([
                [
                    'role' => 'speaker',
                    'name' => $index === 0 ? 'Dr. Maria Santos' : 'Dr. Arif Pratama',
                    'designation' => 'Consultant Cardiologist',
                    'qualifications' => 'MD, FESC, FACC, FHFA',
                    'current_position' => 'Professor of Cardiology',
                    'institution' => 'Regional Heart Institute',
                    'country' => $index === 2 ? 'Thailand' : ($index === 1 ? 'Indonesia' : 'Philippines'),
                    'bio' => 'Leading cardiologist with extensive experience in heart failure management, clinical research and medical education across Asia.',
                    'sort_order' => 0,
                ],
                [
                    'role' => 'moderator',
                    'name' => 'Dr. James Lim',
                    'designation' => 'Senior Cardiology Consultant',
                    'qualifications' => 'MD, FESC',
                    'current_position' => 'Clinical Program Director',
                    'institution' => 'Asia Cardiovascular Centre',
                    'country' => 'Singapore',
                    'bio' => 'Regional medical educator focused on practical implementation of evidence-based heart failure care.',
                    'sort_order' => 1,
                ],
            ]);

            $firstSpeaker = $webinar->people()->where('role', 'speaker')->first();
            $webinar->update(['speaker_name' => $firstSpeaker?->name, 'speaker_bio' => $firstSpeaker?->bio]);
        }
    }

    private function certificateTemplate(): string
    {
        $content = "q\n0.745 0.118 0.176 RG\n5 w\n20 20 802 555 re S\nQ\nBT\n/F1 30 Tf\n0.031 0.231 0.561 rg\n210 480 Td\n(PULCE CONNECT 2026) Tj\nET\nBT\n/F1 18 Tf\n0.745 0.118 0.176 rg\n305 435 Td\n(CERTIFICATE) Tj\nET\nBT\n/F2 13 Tf\n0.2 0.2 0.2 rg\n255 380 Td\n(This certificate is proudly presented to) Tj\nET\nBT\n/F2 12 Tf\n0.3 0.3 0.3 rg\n205 235 Td\n(for participation in the EASD Diabetes Series) Tj\nET\n";

        $objects = [
            1 => '<</Type/Catalog/Pages 2 0 R>>',
            2 => '<</Type/Pages/Kids[3 0 R]/Count 1>>',
            3 => '<</Type/Page/Parent 2 0 R/MediaBox[0 0 842 595]/Resources<</Font<</F1 5 0 R/F2 6 0 R>>>>/Contents 4 0 R>>',
            4 => '<</Length '.strlen($content).">>\nstream\n{$content}endstream",
            5 => '<</Type/Font/Subtype/Type1/BaseFont/Helvetica-Bold>>',
            6 => '<</Type/Font/Subtype/Type1/BaseFont/Helvetica>>',
        ];

        $pdf = "%PDF-1.4\n";
        $offsets = [0 => 0];
        foreach ($objects as $id => $object) {
            $offsets[$id] = strlen($pdf);
            $pdf .= "{$id} 0 obj\n{$object}\nendobj\n";
        }

        $xref = strlen($pdf);
        $pdf .= "xref\n0 7\n0000000000 65535 f \n";
        for ($id = 1; $id <= 6; $id++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$id]);
        }
        $pdf .= "trailer\n<</Size 7/Root 1 0 R>>\nstartxref\n{$xref}\n%%EOF\n";

        return $pdf;
    }
}
