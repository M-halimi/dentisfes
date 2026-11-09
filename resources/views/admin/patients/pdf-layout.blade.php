<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <style>
        body{font-family:dejavusans;color:#163244;font-size:9pt;line-height:1.55}
        a{color:#087b76;text-decoration:none}table{width:100%;border-collapse:collapse}td,th{vertical-align:top;text-align:{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}}
        .masthead td{padding:0 0 7mm}.logo-cell{width:54%}.identity-cell{width:46%;text-align:{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}}
        .brand-name{font-size:15pt;color:#082234;font-weight:bold}.practitioner{font-size:12pt;font-weight:bold;color:#082234}.profession{font-size:8.5pt;color:#5b6d78}.gold-rule{height:1mm;background:#c7a36b;margin-bottom:8mm}
        .eyebrow{font-size:7.5pt;letter-spacing:1.2px;text-transform:uppercase;color:#087b76;font-weight:bold}.document-title{font-size:28pt;font-weight:bold;color:#082234;line-height:1.2;margin:2mm 0 2mm}.muted{color:#6c7c87;font-size:8pt}.document-heading{margin-bottom:7mm}
        .panel{border:0.3mm solid #dce6e9;border-radius:3mm;background:#f7fafb;padding:5mm;margin-bottom:6mm}.person-name{font-size:16pt;font-weight:bold;color:#082234}.ref{font-size:8pt;color:#087b76}.contact{font-size:9pt;color:#4c6371;line-height:1.8}.metadata td{padding:1mm 0}.metadata .label{color:#6c7c87;width:42%}.metadata .value{font-weight:bold;color:#163244}
        .section-title{font-size:12pt;font-weight:bold;color:#082234;margin:7mm 0 3mm;border-bottom:0.3mm solid #dce6e9;padding-bottom:2mm;page-break-after:avoid}
        .data-table{font-size:8pt;table-layout:fixed}.data-table thead th{padding:3mm 2.5mm;background:#082234;color:white;font-size:7.5pt;font-weight:bold}.data-table td{padding:3mm 2.5mm;border-bottom:0.25mm solid #e2eaed;word-wrap:break-word}.data-table .stripe td{background:#f5f9fa}.money{white-space:nowrap;font-weight:bold}.teal{color:#087b76}.amber{color:#b96a17}
        .summary{margin:2mm 0 7mm}.summary td{width:33.33%;padding:4mm 3mm;background:#edf6f5;border:1mm solid white}.summary .outstanding{background:#fff3e5}.summary-label{font-size:7pt;color:#506875}.summary-amount{font-size:15pt;font-weight:bold;color:#087b76}.outstanding .summary-amount{color:#b96a17}
        .amount-banner{background:#082234;border-radius:3mm;color:white;padding:7mm;margin:6mm 0}.amount-caption{font-size:8pt;color:#c0d9df}.amount-value{font-size:30pt;font-weight:bold;line-height:1.25;color:white}.amount-subtitle{font-size:8pt;color:#c6dcdf;margin-top:2mm}
        .badge{font-size:7.5pt;font-weight:bold;color:#087b76;background:#e0f3ed;padding:1mm 2mm}.cancelled{padding:4mm;border:0.3mm solid #ebc3b9;background:#fff0eb;color:#a72f27;font-size:9pt;margin-bottom:5mm}.cancelled-text{color:#a72f27;font-weight:bold}.notice{font-size:8pt;color:#6c7c87;background:#f5f8f9;padding:4mm;margin-top:7mm;border-left:0.7mm solid #c7a36b}.empty{padding:4mm;color:#6c7c87;font-size:8pt;background:#f5f8f9}.footer{border-top:0.3mm solid #c7a36b;padding-top:3mm;font-size:7pt;color:#637986}.footer td{padding:0;line-height:1.7}.footer-page{width:18%;text-align:{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}}
        @yield('pdf-styles')
    </style>
</head>
<body>
    <htmlpagefooter name="patientDocumentFooter">
        <table class="footer"><tr><td><strong>{{ $pdfClinic['name'] }}</strong> · <span dir="ltr">{{ $pdfClinic['phone'] }}</span> · <span dir="ltr">{{ $pdfClinic['email'] }}</span><br>{{ $pdfClinic['address'] }}@if($pdfWebsite) · <a dir="ltr" href="{{ $pdfWebsite }}">{{ $pdfWebsiteLabel }}</a>@endif</td><td class="footer-page">{{ __('patients.pdf_page') }} <span dir="ltr">{PAGENO} / {nbpg}</span></td></tr></table>
    </htmlpagefooter>
    <sethtmlpagefooter name="patientDocumentFooter" page="ALL" value="ON" />
    <table class="masthead"><tr><td class="logo-cell">@if($pdfLogo)<img src="{{ $pdfLogo }}" width="245" alt="{{ $pdfClinic['name'] }}">@else<span class="brand-name">{{ $pdfClinic['name'] }}</span>@endif</td><td class="identity-cell"><div class="practitioner">{{ $pdfClinic['practitioner_name'] }}</div><div class="profession">{{ $pdfClinic['profession'] }}</div><div class="contact">{{ $pdfClinic['name'] }} · {{ $pdfClinic['city'] }}</div>@if($pdfWebsite)<a href="{{ $pdfWebsite }}">{{ $pdfWebsiteLabel }}</a>@endif</td></tr></table>
    <div class="gold-rule"></div>
    @yield('pdf-content')
</body>
</html>
