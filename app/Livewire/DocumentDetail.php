<?php

namespace App\Livewire;

use App\Models\Document;
use App\Services\PdfConverter;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Native\Mobile\Facades\Dialog;
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
        $pdfPath = $this->getOrCreatePdf();

        if ($pdfPath) {
            Share::file(
                $this->document->title,
                "Scanned document: {$this->document->title} ({$this->document->page_count} pages)",
                $pdfPath,
            );
        } elseif (!empty($this->document->file_paths)) {
            // Share first image if PDF conversion failed
            $firstPath = $this->document->file_paths[0];
            if (file_exists($firstPath)) {
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

        $pdfPath = $this->generatePdf();

        if ($pdfPath) {
            // Update document to PDF format
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

    public function render()
    {
        $images = [];
        if ($this->document->output_format === 'jpeg' && !empty($this->document->file_paths)) {
            foreach ($this->document->file_paths as $path) {
                if (is_string($path) && file_exists($path)) {
                    $data = file_get_contents($path);
                    if ($data !== false) {
                        $images[] = 'data:image/jpeg;base64,' . base64_encode($data);
                    }
                }
            }
        }

        return view('livewire.document-detail', ['images' => $images]);
    }

    private function getOrCreatePdf(): ?string
    {
        // If already PDF, return existing path
        if ($this->document->output_format === 'pdf' && !empty($this->document->file_paths)) {
            $path = $this->document->file_paths[0];
            if (file_exists($path)) {
                return $path;
            }
        }

        return $this->generatePdf();
    }

    private function generatePdf(): ?string
    {
        $paths = array_filter($this->document->file_paths, fn ($p) => is_string($p) && file_exists($p));

        if (empty($paths)) {
            return null;
        }

        $outputDir = storage_path('app/documents');
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        $filename = 'doc_' . $this->document->id . '_' . time() . '.pdf';
        $outputPath = $outputDir . '/' . $filename;

        return app(PdfConverter::class)->imagesToPdf(array_values($paths), $outputPath);
    }
}
