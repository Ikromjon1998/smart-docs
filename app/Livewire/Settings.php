<?php

namespace App\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app', ['title' => 'Settings'])]
class Settings extends Component
{
    public string $defaultFormat = 'jpeg';
    public int $defaultQuality = 90;
    public int $defaultMaxPages = 0;
    public bool $defaultGalleryImport = true;

    public bool $saved = false;

    public function mount(): void
    {
        $this->defaultFormat = config('document-scanner.default_output_format', 'jpeg');
        $this->defaultQuality = (int) config('document-scanner.default_jpeg_quality', 90);
        $this->defaultMaxPages = (int) config('document-scanner.default_max_pages', 0);
        $this->defaultGalleryImport = (bool) config('document-scanner.default_gallery_import', true);
    }

    public function save(): void
    {
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
