<?php

declare(strict_types=1);

use App\Livewire\Scanner;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

describe('Scanner component', function (): void {
    it('renders successfully', function (): void {
        Livewire::test(Scanner::class)
            ->assertStatus(200)
            ->assertSee('Tap to Scan');
    });

    it('loads default values from settings', function (): void {
        Setting::set('default_format', 'pdf');
        Setting::set('default_quality', '75');
        Setting::set('default_max_pages', '3');
        Setting::set('default_scanner_mode', 'base');

        Livewire::test(Scanner::class)
            ->assertSet('outputFormat', 'pdf')
            ->assertSet('jpegQuality', 75)
            ->assertSet('maxPages', 3)
            ->assertSet('scannerMode', 'base');
    });

    it('falls back to config defaults when no saved settings', function (): void {
        Livewire::test(Scanner::class)
            ->assertSet('outputFormat', 'jpeg')
            ->assertSet('jpegQuality', 90)
            ->assertSet('maxPages', 0)
            ->assertSet('scannerMode', 'full');
    });

    it('shows scanner mode option', function (): void {
        Livewire::test(Scanner::class)
            ->assertSee('Scanner Mode');
    });

    it('hides JPEG quality when format is PDF', function (): void {
        Livewire::test(Scanner::class)
            ->set('outputFormat', 'pdf')
            ->assertDontSee('JPEG Quality');
    });

    it('shows JPEG quality when format is JPEG', function (): void {
        Livewire::test(Scanner::class)
            ->set('outputFormat', 'jpeg')
            ->assertSee('JPEG Quality');
    });
});
