<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Document;
use Illuminate\Support\Str;

class DocumentService
{
    public function __construct(
        private readonly PdfConverter $pdfConverter,
    ) {}

    /**
     * Create a document record from scan results.
     *
     * @param  array<int, string>  $paths
     */
    public function createFromScan(array $paths, int $pageCount, string $outputFormat): Document
    {
        $totalSize = 0;

        foreach ($paths as $path) {
            if (file_exists($path)) {
                $totalSize += (int) filesize($path);
            }
        }

        return Document::create([
            'title' => 'Scan '.now()->format('M j, Y g:i A'),
            'category' => 'general',
            'file_paths' => $paths,
            'page_count' => $pageCount,
            'output_format' => $outputFormat,
            'file_size' => $totalSize,
        ]);
    }

    /**
     * Convert a JPEG document to PDF.
     */
    public function convertToPdf(Document $document): ?string
    {
        $paths = array_filter(
            $document->file_paths,
            fn (mixed $p): bool => is_string($p) && file_exists($p),
        );

        if ($paths === []) {
            return null;
        }

        $outputDir = storage_path('app/documents');

        if (! is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        $filename = 'doc_'.$document->id.'_'.Str::random(8).'.pdf';
        $outputPath = $outputDir.'/'.$filename;

        return $this->pdfConverter->imagesToPdf(array_values($paths), $outputPath);
    }

    /**
     * Get the PDF path for sharing — returns existing PDF or creates one.
     */
    public function getPdfForSharing(Document $document): ?string
    {
        if ($document->output_format === 'pdf' && ! empty($document->file_paths)) {
            $path = $document->file_paths[0];

            if (is_string($path) && file_exists($path)) {
                return $path;
            }
        }

        return $this->convertToPdf($document);
    }

    /**
     * Load page images as base64 data URIs for display.
     *
     * @return array<int, string>
     */
    public function getPageImages(Document $document): array
    {
        if ($document->output_format !== 'jpeg' || empty($document->file_paths)) {
            return [];
        }

        $images = [];

        foreach ($document->file_paths as $path) {
            if (! is_string($path) || ! file_exists($path)) {
                continue;
            }

            $data = file_get_contents($path);

            if ($data !== false) {
                $images[] = 'data:image/jpeg;base64,'.base64_encode($data);
            }
        }

        return $images;
    }

    /**
     * Format file size for display.
     */
    public static function formatFileSize(int $bytes): string
    {
        if ($bytes >= 1_048_576) {
            return number_format($bytes / 1_048_576, 1).' MB';
        }

        return number_format($bytes / 1024, 1).' KB';
    }
}
