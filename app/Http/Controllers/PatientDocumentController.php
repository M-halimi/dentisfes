<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\PatientDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PatientDocumentController extends Controller
{
    public function store(Request $request, Patient $patient): RedirectResponse
    {
        $request->validate(['document' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'extensions:pdf,jpg,jpeg,png,webp', 'max:10240']], ['*' => __('patients.invalid_document')]);
        $file = $request->file('document');
        $path = $file->store((string) $patient->id, 'patients');
        try {
            $patient->documents()->create([
                'path' => $path, 'original_name' => mb_substr(basename(str_replace('\\', '/', $file->getClientOriginalName())), 0, 200),
                'mime_type' => $file->getMimeType(), 'size' => $file->getSize(), 'uploaded_by' => session('admin_email'),
            ]);
        } catch (\Throwable $exception) {
            Storage::disk('patients')->delete($path);
            throw $exception;
        }

        return back()->with('success', __('patients.document_saved'));
    }

    public function show(Patient $patient, PatientDocument $document): BinaryFileResponse
    {
        $this->checkDocument($patient, $document);

        return response()->file(Storage::disk('patients')->path($document->path), [
            'Content-Type' => $document->mime_type, 'Content-Disposition' => 'inline; filename="document-'.$document->id.'.'.pathinfo($document->path, PATHINFO_EXTENSION).'"',
            'Cache-Control' => 'private, no-store', 'X-Content-Type-Options' => 'nosniff',
            'Content-Security-Policy' => "sandbox; default-src 'none'; style-src 'unsafe-inline'",
        ]);
    }

    public function download(Patient $patient, PatientDocument $document): StreamedResponse
    {
        $this->checkDocument($patient, $document);

        return Storage::disk('patients')->download($document->path, $document->original_name, ['Cache-Control' => 'private, no-store', 'X-Content-Type-Options' => 'nosniff']);
    }

    private function checkDocument(Patient $patient, PatientDocument $document): void
    {
        abort_unless($document->patient_id === $patient->id, 404);
        abort_unless(Storage::disk('patients')->exists($document->path), 404);
    }
}
