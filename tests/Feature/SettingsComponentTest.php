<?php

declare(strict_types=1);

use App\Livewire\Settings;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

describe('Settings component', function (): void {
    it('renders successfully', function (): void {
        Livewire::test(Settings::class)
            ->assertStatus(200)
            ->assertSee('Default Scan Settings');
    });

    it('loads default values from config', function (): void {
        Livewire::test(Settings::class)
            ->assertSet('defaultFormat', 'jpeg')
            ->assertSet('defaultQuality', 90)
            ->assertSet('defaultMaxPages', 0)
            ->assertSet('defaultScannerMode', 'full');
    });

    it('loads saved values from database', function (): void {
        Setting::set('default_format', 'pdf');
        Setting::set('default_quality', '75');
        Setting::set('default_scanner_mode', 'base');

        Livewire::test(Settings::class)
            ->assertSet('defaultFormat', 'pdf')
            ->assertSet('defaultQuality', 75)
            ->assertSet('defaultScannerMode', 'base');
    });

    it('persists settings to database on save', function (): void {
        Livewire::test(Settings::class)
            ->set('defaultFormat', 'pdf')
            ->set('defaultQuality', 75)
            ->set('defaultMaxPages', 5)
            ->set('defaultScannerMode', 'filter')
            ->call('save')
            ->assertSet('saved', true);

        expect(Setting::get('default_format'))->toBe('pdf')
            ->and(Setting::get('default_quality'))->toBe('75')
            ->and(Setting::get('default_max_pages'))->toBe('5')
            ->and(Setting::get('default_scanner_mode'))->toBe('filter');
    });

    it('shows scanner mode dropdown', function (): void {
        Livewire::test(Settings::class)
            ->assertSee('Scanner Mode')
            ->assertSee('Android only');
    });

    it('shows save confirmation message after saving', function (): void {
        Livewire::test(Settings::class)
            ->call('save')
            ->assertSee('Settings saved');
    });
});
