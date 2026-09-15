<?php

namespace App\Http\Controllers\Admin;

use App\Models\GeneralSettings;
use App\Models\Webinar;
use App\Support\CertificatePdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CertificateController
{
    public function __construct()
    {
        GeneralSettings::define_const();
    }
    public function index()
    {
        $title = 'Webinar Certificates';
        $webinars = Webinar::with('speciality')
            ->orderByRaw('certificate_template_path IS NULL')
            ->orderBy('scheduled_at')
            ->get();

        return view('admin.certificates.index', compact('webinars', 'title'));
    }

    public function edit(Webinar $webinar)
    {
        $title = 'Configure Certificate';
        return view('admin.certificates.edit', compact('webinar', 'title'));
    }

    public function update(Request $request, Webinar $webinar)
    {
        $request->validate([
            'certificate_template' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:15360'],
            'certificate_name_x' => ['nullable', 'numeric', 'min:0', 'max:2000'],
            'certificate_name_y' => ['nullable', 'numeric', 'min:0', 'max:2000'],
            'certificate_font_size' => ['nullable', 'integer', 'min:12', 'max:100'],
            'certificate_font_color' => ['nullable', 'string', 'max:20'],
            'remove_certificate_template' => ['nullable', 'boolean'],
        ]);

        if ($request->boolean('remove_certificate_template')) {
            if ($webinar->certificate_template_path && Storage::disk('public')->exists($webinar->certificate_template_path)) {
                Storage::disk('public')->delete($webinar->certificate_template_path);
            }
            $webinar->certificate_template_path = null;
        }

        if ($request->hasFile('certificate_template')) {
            if ($webinar->certificate_template_path && Storage::disk('public')->exists($webinar->certificate_template_path)) {
                Storage::disk('public')->delete($webinar->certificate_template_path);
            }

            $path = $request->file('certificate_template')->store('uploads/webinar-certificates', 'public');
            $webinar->certificate_template_path = $path;
        }

        $webinar->certificate_name_x = $request->filled('certificate_name_x') ? (float) $request->input('certificate_name_x') : null;
        $webinar->certificate_name_y = $request->filled('certificate_name_y') ? (float) $request->input('certificate_name_y') : 292.00;
        $webinar->certificate_font_size = $request->filled('certificate_font_size') ? (int) $request->input('certificate_font_size') : 28;
        $webinar->certificate_font_color = $request->filled('certificate_font_color') ? $request->input('certificate_font_color') : '#8e5f16';
        $webinar->save();

        return redirect()->route('admin.certificates')->with('success', 'Certificate settings updated successfully for ' . $webinar->title);
    }

    public function preview(Request $request, Webinar $webinar)
    {
        $templatePath = null;
        if ($webinar->certificate_template_path && Storage::disk('public')->exists($webinar->certificate_template_path)) {
            $templatePath = Storage::disk('public')->path($webinar->certificate_template_path);
        } elseif (file_exists(resource_path('certificates/delegate_certificate.pdf'))) {
            $templatePath = resource_path('certificates/delegate_certificate.pdf');
        }

        if (!$templatePath) {
            return response('No certificate template PDF uploaded yet for this webinar.', 404);
        }

        $testName = $request->query('test_name', 'Dr. Demo Delegate');
        $nameX = $request->filled('x') ? (float) $request->query('x') : ($webinar->certificate_name_x !== null ? (float) $webinar->certificate_name_x : null);
        $nameY = $request->filled('y') ? (float) $request->query('y') : ($webinar->certificate_name_y !== null ? (float) $webinar->certificate_name_y : 292.00);
        $fontSize = $request->filled('font_size') ? (int) $request->query('font_size') : ($webinar->certificate_font_size ?: 28);
        $fontColor = $request->query('font_color', $webinar->certificate_font_color ?: '#8e5f16');

        $pdf = CertificatePdf::delegateCertificate(
            $templatePath,
            $testName,
            $nameX,
            $nameY,
            $fontSize,
            public_path('assets/fonts/Abril_Display_Italic.otf'),
            $fontColor
        );

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="certificate-preview.pdf"',
        ]);
    }
}
