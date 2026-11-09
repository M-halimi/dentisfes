<?php

namespace App;

use App\Models\ClinicSetting;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;

final class PatientPdf
{
    public function branding(array $clinic): array
    {
        if (! array_key_exists('practitioner_name', $clinic)) {
            $clinic['practitioner_name'] = ClinicSetting::value('practitioner_name', config('clinic.practitioner_name'));
        }
        $clinic += ['website' => config('seo.url')];
        $website = (string) $clinic['website'];
        $parts = parse_url($website);
        $website = is_array($parts) && in_array($parts['scheme'] ?? '', ['http', 'https'], true)
            && ! empty($parts['host']) && ! isset($parts['user']) && ! isset($parts['pass']) ? rtrim($website, '/') : '';
        $logo = public_path('images/brand/dentafes-logo.png');

        return [
            'pdfClinic' => $clinic,
            'pdfLogo' => is_file($logo) ? 'data:image/png;base64,'.base64_encode(File::get($logo)) : null,
            'pdfWebsite' => $website,
            'pdfWebsiteLabel' => preg_replace('#^https?://#', '', $website),
        ];
    }

    public function download(string $template, array $data, string $title, string $filename): Response
    {
        $temp = storage_path('app/patients/.pdf-temp');
        File::ensureDirectoryExists($temp, 0700);
        $pdf = new Mpdf([
            'mode' => 'utf-8', 'format' => 'A4', 'tempDir' => $temp,
            'autoScriptToLang' => true, 'autoLangToFont' => true,
            'margin_left' => 16, 'margin_right' => 16, 'margin_top' => 16,
            'margin_bottom' => 27, 'margin_footer' => 10,
        ]);
        $pdf->SetDirectionality(app()->getLocale() === 'ar' ? 'rtl' : 'ltr');
        $pdf->SetTitle($title);
        $pdf->SetAuthor((string) $data['pdfClinic']['name']);
        $pdf->WriteHTML(view($template, $data)->render());

        return response($pdf->Output('', Destination::STRING_RETURN), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Cache-Control' => 'private, no-store', 'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
