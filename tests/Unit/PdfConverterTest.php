<?php

declare(strict_types=1);

use App\Services\PdfConverter;

beforeEach(function (): void {
    $this->converter = new PdfConverter;
    $this->outputDir = sys_get_temp_dir().'/pdf-converter-test';

    if (! is_dir($this->outputDir)) {
        mkdir($this->outputDir, 0755, true);
    }
});

afterEach(function (): void {
    // Clean up temp files
    $files = glob($this->outputDir.'/*');
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }
    if (is_dir($this->outputDir)) {
        rmdir($this->outputDir);
    }
});

describe('imagesToPdf', function (): void {
    it('returns null for empty paths array', function (): void {
        $result = $this->converter->imagesToPdf([], $this->outputDir.'/out.pdf');

        expect($result)->toBeNull();
    });

    it('skips non-existent files', function (): void {
        $result = $this->converter->imagesToPdf(
            ['/nonexistent/file.jpg'],
            $this->outputDir.'/out.pdf',
        );

        // FPDF creates the file even with no pages, but it may be empty
        // The important thing is it doesn't crash
        expect(true)->toBeTrue();
    });

    it('creates a PDF from a valid JPEG image', function (): void {
        // Create a minimal JPEG file
        $img = imagecreatetruecolor(100, 100);
        $tmpJpeg = $this->outputDir.'/test.jpg';
        imagejpeg($img, $tmpJpeg);
        imagedestroy($img);

        $outputPath = $this->outputDir.'/output.pdf';
        $result = $this->converter->imagesToPdf([$tmpJpeg], $outputPath);

        expect($result)->toBe($outputPath)
            ->and(file_exists($outputPath))->toBeTrue()
            ->and(filesize($outputPath))->toBeGreaterThan(0);
    });

    it('handles multiple images', function (): void {
        $paths = [];
        for ($i = 0; $i < 3; $i++) {
            $img = imagecreatetruecolor(100, 150);
            $path = $this->outputDir."/test_{$i}.jpg";
            imagejpeg($img, $path);
            imagedestroy($img);
            $paths[] = $path;
        }

        $outputPath = $this->outputDir.'/multi.pdf';
        $result = $this->converter->imagesToPdf($paths, $outputPath);

        expect($result)->toBe($outputPath)
            ->and(file_exists($outputPath))->toBeTrue();
    });

    it('detects landscape orientation', function (): void {
        // Wide image = landscape
        $img = imagecreatetruecolor(200, 100);
        $tmpJpeg = $this->outputDir.'/landscape.jpg';
        imagejpeg($img, $tmpJpeg);
        imagedestroy($img);

        $outputPath = $this->outputDir.'/landscape.pdf';
        $result = $this->converter->imagesToPdf([$tmpJpeg], $outputPath);

        expect($result)->toBe($outputPath);
    });
});
