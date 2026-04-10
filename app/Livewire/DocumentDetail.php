<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Document;
use App\Services\DocumentService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Native\Mobile\Facades\Share;

#[Layout('layouts.app', ['title' => 'Document'])]
class DocumentDetail extends Component
{
    public Document $document;

    public bool $editing = false;

    public string $title = '';

    public string $category = '';

    public string $summary = '';

    public string $actionStatus = '';

    public function mount(Document $document): void
    {
        $this->document = $document;
        $this->title = $document->title;
        $this->category = $document->category;
        $this->summary = $document->summary ?? '';
    }

    public function startEditing(): void
    {
        $this->editing = true;
    }

    public function save(): void
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'summary' => 'nullable|string|max:5000',
        ]);

        $this->document->update([
            'title' => $this->title,
            'category' => $this->category,
            'summary' => $this->summary,
        ]);

        $this->editing = false;
    }

    public function cancelEditing(): void
    {
        $this->title = $this->document->title;
        $this->category = $this->document->category;
        $this->summary = $this->document->summary ?? '';
        $this->editing = false;
    }

    public function shareDocument(): void
    {
        $service = app(DocumentService::class);
        $pdfPath = $service->getPdfForSharing($this->document);

        if ($pdfPath !== null) {
            Share::file(
                $this->document->title,
                "Scanned document: {$this->document->title} ({$this->document->page_count} pages)",
                $pdfPath,
            );
        } elseif (! empty($this->document->file_paths)) {
            $firstPath = $this->document->file_paths[0];

            if (is_string($firstPath) && file_exists($firstPath)) {
                Share::file(
                    $this->document->title,
                    "Scanned document: {$this->document->title}",
                    $firstPath,
                );
            }
        }
    }

    public function convertToPdf(): void
    {
        if ($this->document->output_format === 'pdf') {
            $this->actionStatus = 'Already in PDF format.';

            return;
        }

        $service = app(DocumentService::class);
        $pdfPath = $service->convertToPdf($this->document);

        if ($pdfPath !== null) {
            $this->document->update([
                'output_format' => 'pdf',
                'file_paths' => [$pdfPath],
                'file_size' => (int) filesize($pdfPath),
            ]);
            $this->document->refresh();
            $this->actionStatus = 'Converted to PDF!';
        } else {
            $this->actionStatus = 'PDF conversion failed.';
        }
    }

    public function delete(): void
    {
        $this->document->delete();
        $this->redirect('/', navigate: true);
    }

    public function render(): View
    {
        $service = app(DocumentService::class);

        return view('livewire.document-detail', [
            'images' => $service->getPageImages($this->document),
        ]);
    }
}
