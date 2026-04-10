<?php

namespace App\Livewire;

use App\Models\Setting;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app', ['title' => 'Settings'])]
class Settings extends Component
{
    public string $defaultFormat = 'jpeg';
    public int $defaultQuality = 90;
    public int $defaultMaxPages = 0;
    public bool $defaultGalleryImport = true;
    public string $defaultScannerMode = 'full';

    public bool $saved = false;

    public function mount(): void
    {
        $this->defaultFormat = (string) Setting::get('default_format', config('document-scanner.default_output_format', 'jpeg'));
        $this->defaultQuality = (int) Setting::get('default_quality', config('document-scanner.default_jpeg_quality', 90));
        $this->defaultMaxPages = (int) Setting::get('default_max_pages', config('document-scanner.default_max_pages', 0));
        $this->defaultGalleryImport = (bool) Setting::get('default_gallery_import', config('document-scanner.default_gallery_import', true));
        $this->defaultScannerMode = (string) Setting::get('default_scanner_mode', config('document-scanner.default_scanner_mode', 'full'));
    }

    public function save(): void
    {
        Setting::set('default_format', $this->defaultFormat);
        Setting::set('default_quality', (string) $this->defaultQuality);
        Setting::set('default_max_pages', (string) $this->defaultMaxPages);
        Setting::set('default_gallery_import', $this->defaultGalleryImport ? '1' : '0');
        Setting::set('default_scanner_mode', $this->defaultScannerMode);

        $this->saved = true;
    }

    public function render()
    {
        $isAvailable = function_exists('nativephp_call');

        return view('livewire.settings', [
            'scannerAvailable' => $isAvailable,
        ]);
    }
}
