<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Setting;
use Illuminate\Contracts\View\View;
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
        $settings = Setting::query()->pluck('value', 'key');

        $this->defaultFormat = (string) ($settings['default_format'] ?? config('document-scanner.default_output_format', 'jpeg'));
        $this->defaultQuality = (int) ($settings['default_quality'] ?? config('document-scanner.default_jpeg_quality', 90));
        $this->defaultMaxPages = (int) ($settings['default_max_pages'] ?? config('document-scanner.default_max_pages', 0));
        $this->defaultGalleryImport = (bool) ($settings['default_gallery_import'] ?? config('document-scanner.default_gallery_import', true));
        $this->defaultScannerMode = (string) ($settings['default_scanner_mode'] ?? config('document-scanner.default_scanner_mode', 'full'));
    }

    public function save(): void
    {
        $this->validate([
            'defaultFormat' => 'required|in:jpeg,pdf',
            'defaultQuality' => 'required|integer|min:1|max:100',
            'defaultMaxPages' => 'required|integer|min:0',
            'defaultScannerMode' => 'required|in:base,filter,full',
        ]);

        Setting::set('default_format', $this->defaultFormat);
        Setting::set('default_quality', (string) $this->defaultQuality);
        Setting::set('default_max_pages', (string) $this->defaultMaxPages);
        Setting::set('default_gallery_import', $this->defaultGalleryImport ? '1' : '0');
        Setting::set('default_scanner_mode', $this->defaultScannerMode);

        $this->saved = true;
    }

    public function render(): View
    {
        return view('livewire.settings', [
            'scannerAvailable' => function_exists('nativephp_call'),
        ]);
    }
}
