<?php

namespace App\Http\Controllers;

use App\Services\PdfIntegrityService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PdfIntegrityController extends Controller
{
    public function check(Request $request, PdfIntegrityService $pdfIntegrityService)
    {
        $request->validate([
            'pdf_file' => 'required|file|mimes:pdf|max:10240', // 10MB max
        ]);

        $file = $request->file('pdf_file');
        
        // Use a temporary path or store it temporarily to check
        $path = $file->storeAs('temp', $file->getClientOriginalName());
        $fullPath = \Illuminate\Support\Facades\Storage::path($path);

        $result = $pdfIntegrityService->checkPdfModifications($fullPath);

        // Clean up the temporary file
        \Illuminate\Support\Facades\Storage::delete($path);

        $pdfCheck = \App\Models\PdfCheck::create([
            'user_id' => auth()->id(),
            'file_name' => $file->getClientOriginalName(),
            'is_modified' => $result['modified'],
            'message' => $result['message'],
            'metadata' => $result['metadata'] ?? null,
        ]);

        return back()->with('pdf_check_result', [
            'type' => $result['modified'] === true ? 'error' : ($result['modified'] === false ? 'success' : 'info'),
            'message' => $result['message'],
            'metadata' => $result['metadata'] ?? null,
        ]);
    }
}
