<?php

declare(strict_types=1);

namespace App\Services;

use FPDF;

class PdfConverter
{
    /**
     * Convert an array of JPEG image paths into a single PDF file.
     *
     * @param  array<int, string>  $imagePaths
     * @return string|null  The path to the generated PDF, or null on failure.
     */
    public function imagesToPdf(array $imagePaths, string $outputPath): ?string
    {
        if ($imagePaths === []) {
            return null;
        }

        $pdf = new FPDF();
        $pdf->SetAutoPageBreak(false);

        foreach ($imagePaths as $imagePath) {
            if (! file_exists($imagePath)) {
                continue;
            }

            $imageSize = getimagesize($imagePath);

            if ($imageSize === false) {
                continue;
            }

            [$widthPx, $heightPx] = $imageSize;

            $orientation = $widthPx > $heightPx ? 'L' : 'P';
            $pdf->AddPage($orientation);

            $pageWidth = $pdf->GetPageWidth();
            $pageHeight = $pdf->GetPageHeight();

            $scaleW = $pageWidth / $widthPx;
            $scaleH = $pageHeight / $heightPx;
            $scale = min($scaleW, $scaleH);

            $displayWidth = $widthPx * $scale;
            $displayHeight = $heightPx * $scale;

            $x = ($pageWidth - $displayWidth) / 2;
            $y = ($pageHeight - $displayHeight) / 2;

            $pdf->Image($imagePath, $x, $y, $displayWidth, $displayHeight);
        }

        $pdf->Output('F', $outputPath);

        return file_exists($outputPath) ? $outputPath : null;
    }
}
