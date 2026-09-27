<?php

namespace App\Services;



class PdfIntegrityService
{
    /**
     * Verify if a signed PDF has been modified using SetaPDF-Signer.
     *
     * @param string $pdfPath The absolute path to the PDF file.
     * @return array Returns an array with 'modified' boolean and a 'message'.
     */
    public function checkPdfModifications(string $pdfPath): array
    {
        if (!file_exists($pdfPath)) {
            return ['modified' => null, 'message' => 'File not found.'];
        }

        try {
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($pdfPath);
            $details = $pdf->getDetails();

            $creationDate = $details['CreationDate'] ?? null;
            $modDate = $details['ModDate'] ?? null;

            if (!$creationDate && !$modDate) {
                return [
                    'modified' => null,
                    'message' => 'No metadata (CreationDate/ModDate) found to verify.',
                    'metadata' => $details,
                ];
            }

            if ($modDate && $creationDate && $modDate !== $creationDate) {
                return [
                    'modified' => true,
                    'message' => 'The document has been modified (Modification Date differs from Creation Date).',
                    'metadata' => $details,
                ];
            }
            
            if ($modDate && !$creationDate) {
                return [
                    'modified' => true,
                    'message' => 'The document has a Modification Date but no Creation Date.',
                    'metadata' => $details,
                ];
            }

            return [
                'modified' => false,
                'message' => 'The document metadata indicates it has not been modified.',
                'metadata' => $details,
            ];

        } catch (\Throwable $e) {
            return [
                'modified' => null,
                'message' => 'Failed to parse PDF metadata: ' . $e->getMessage()
            ];
        }
    }
}
