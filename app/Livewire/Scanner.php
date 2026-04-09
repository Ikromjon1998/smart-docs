<?php

namespace App\Livewire;

use App\Models\Document;
use Ikromjon\DocumentScanner\Data\ScanOptions;
use Ikromjon\DocumentScanner\Enums\OutputFormat;
use Ikromjon\DocumentScanner\Events\DocumentScanned;
use Ikromjon\DocumentScanner\Events\ScanCancelled;
use Ikromjon\DocumentScanner\Events\ScanFailed;
use Ikromjon\DocumentScanner\Facades\DocumentScanner;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Native\Mobile\Attributes\OnNative;

#[Layout('layouts.app', ['title' => 'Scan Document'])]
class Scanner extends Component
{
    public string $outputFormat = 'jpeg';
    public int $jpegQuality = 90;
    public int $maxPages = 0;
    public bool $galleryImport = true;

    public bool $scanning = false;
    public string $status = '';
    public string $error = '';

    public ?int $lastDocumentId = null;

    public function startScan(): void
    {
        $this->reset(['status', 'error', 'lastDocumentId']);
        $this->scanning = true;
        $this->status = 'Opening scanner...';

        $options = new ScanOptions(
            maxPages: $this->maxPages,
            outputFormat: OutputFormat::from($this->outputFormat),
            jpegQuality: $this->jpegQuality,
            galleryImport: $this->galleryImport,
        );

        DocumentScanner::scan($options);
    }

    #[OnNative(DocumentScanned::class)]
    public function handleScanned(array $paths, int $pageCount, string $outputFormat): void
    {
        $this->scanning = false;
        $this->status = 'Scan complete! Saving document...';

        $totalSize = 0;
        foreach ($paths as $path) {
            if (file_exists($path)) {
                $totalSize += (int) filesize($path);
            }
        }

        $document = Document::create([
            'title' => 'Scan ' . now()->format('M j, Y g:i A'),
            'category' => 'general',
            'file_paths' => $paths,
            'page_count' => $pageCount,
            'output_format' => $outputFormat,
            'file_size' => $totalSize,
        ]);

        $this->lastDocumentId = $document->id;
        $this->status = "Saved! {$pageCount} page(s) scanned.";
    }

    #[OnNative(ScanCancelled::class)]
    public function handleCancelled(): void
    {
        $this->scanning = false;
        $this->status = 'Scan cancelled.';
    }

    #[OnNative(ScanFailed::class)]
    public function handleFailed(string $error): void
    {
        $this->scanning = false;
        $this->error = $error;
        $this->status = '';
    }

    public function render()
    {
        return view('livewire.scanner');
    }
}
