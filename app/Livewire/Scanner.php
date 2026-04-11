<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Setting;
use App\Services\DocumentService;
use Ikromjon\DocumentScanner\Data\ScanOptions;
use Ikromjon\DocumentScanner\Enums\OutputFormat;
use Ikromjon\DocumentScanner\Enums\ScannerMode;
use Ikromjon\DocumentScanner\Events\DocumentScanned;
use Ikromjon\DocumentScanner\Events\ScanCancelled;
use Ikromjon\DocumentScanner\Events\ScanFailed;
use Ikromjon\DocumentScanner\Facades\DocumentScanner;
use Illuminate\Contracts\View\View;
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

    public string $scannerMode = 'full';

    public bool $scanning = false;

    public string $status = '';

    public string $error = '';

    public ?int $lastDocumentId = null;

    public function mount(): void
    {
        $settings = Setting::query()->pluck('value', 'key');

        $this->outputFormat = (string) ($settings['default_format'] ?? config('document-scanner.default_output_format', 'jpeg'));
        $this->jpegQuality = (int) ($settings['default_quality'] ?? config('document-scanner.default_jpeg_quality', 90));
        $this->maxPages = (int) ($settings['default_max_pages'] ?? config('document-scanner.default_max_pages', 0));
        $this->galleryImport = (bool) ($settings['default_gallery_import'] ?? config('document-scanner.default_gallery_import', true));
        $this->scannerMode = (string) ($settings['default_scanner_mode'] ?? config('document-scanner.default_scanner_mode', 'full'));
    }

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
            scannerMode: ScannerMode::from($this->scannerMode),
        );

        DocumentScanner::scan($options);
    }

    /**
     * @param  array<int, string>  $paths
     */
    #[OnNative(DocumentScanned::class)]
    public function handleScanned(array $paths, int $pageCount, string $outputFormat): void
    {
        $this->scanning = false;
        $this->status = 'Scan complete! Saving document...';

        $service = app(DocumentService::class);
        $document = $service->createFromScan($paths, $pageCount, $outputFormat);

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

    public function render(): View
    {
        return view('livewire.scanner');
    }
}
