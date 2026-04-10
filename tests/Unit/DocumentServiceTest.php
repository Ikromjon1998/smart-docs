<?php

declare(strict_types=1);

use App\Models\Document;
use App\Services\DocumentService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->service = app(DocumentService::class);
});

describe('createFromScan', function (): void {
    it('creates a document record', function (): void {
        $document = $this->service->createFromScan(
            ['/path/scan_0.jpg', '/path/scan_1.jpg'],
            2,
            'jpeg',
        );

        expect($document)->toBeInstanceOf(Document::class)
            ->and($document->exists)->toBeTrue()
            ->and($document->page_count)->toBe(2)
            ->and($document->output_format)->toBe('jpeg')
            ->and($document->file_paths)->toBe(['/path/scan_0.jpg', '/path/scan_1.jpg'])
            ->and($document->category)->toBe('general');
    });

    it('sets title with current timestamp', function (): void {
        $document = $this->service->createFromScan([], 0, 'jpeg');

        expect($document->title)->toStartWith('Scan ');
    });

    it('calculates file size from existing files', function (): void {
        $tmpFile = tempnam(sys_get_temp_dir(), 'test');
        file_put_contents($tmpFile, 'hello world');

        $document = $this->service->createFromScan([$tmpFile], 1, 'jpeg');

        expect($document->file_size)->toBe(11);

        unlink($tmpFile);
    });

    it('handles non-existent file paths gracefully', function (): void {
        $document = $this->service->createFromScan(['/nonexistent/file.jpg'], 1, 'jpeg');

        expect($document->file_size)->toBe(0);
    });
});

describe('formatFileSize', function (): void {
    it('formats bytes as KB', function (): void {
        expect(DocumentService::formatFileSize(1024))->toBe('1.0 KB');
        expect(DocumentService::formatFileSize(512))->toBe('0.5 KB');
    });

    it('formats bytes as MB', function (): void {
        expect(DocumentService::formatFileSize(1_048_576))->toBe('1.0 MB');
        expect(DocumentService::formatFileSize(2_621_440))->toBe('2.5 MB');
    });

    it('uses MB for values at exactly 1 MB', function (): void {
        expect(DocumentService::formatFileSize(1_048_576))->toContain('MB');
    });

    it('uses KB for values below 1 MB', function (): void {
        expect(DocumentService::formatFileSize(1_048_575))->toContain('KB');
    });
});

describe('getPageImages', function (): void {
    it('returns empty array for PDF documents', function (): void {
        $document = Document::create([
            'title' => 'Test',
            'file_paths' => ['/path/file.pdf'],
            'page_count' => 1,
            'output_format' => 'pdf',
            'file_size' => 100,
        ]);

        expect($this->service->getPageImages($document))->toBe([]);
    });

    it('returns empty array when file_paths is empty', function (): void {
        $document = Document::create([
            'title' => 'Test',
            'file_paths' => [],
            'page_count' => 0,
            'output_format' => 'jpeg',
            'file_size' => 0,
        ]);

        expect($this->service->getPageImages($document))->toBe([]);
    });

    it('returns base64 data URIs for existing JPEG files', function (): void {
        $tmpFile = tempnam(sys_get_temp_dir(), 'test');
        file_put_contents($tmpFile, 'fake-jpeg-data');

        $document = Document::create([
            'title' => 'Test',
            'file_paths' => [$tmpFile],
            'page_count' => 1,
            'output_format' => 'jpeg',
            'file_size' => 100,
        ]);

        $images = $this->service->getPageImages($document);

        expect($images)->toHaveCount(1)
            ->and($images[0])->toStartWith('data:image/jpeg;base64,');

        unlink($tmpFile);
    });

    it('skips non-existent files', function (): void {
        $document = Document::create([
            'title' => 'Test',
            'file_paths' => ['/nonexistent/file.jpg'],
            'page_count' => 1,
            'output_format' => 'jpeg',
            'file_size' => 0,
        ]);

        expect($this->service->getPageImages($document))->toBe([]);
    });
});
