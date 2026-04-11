<?php

declare(strict_types=1);

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Setting model', function (): void {
    it('stores and retrieves a value', function (): void {
        Setting::set('theme', 'dark');

        expect(Setting::get('theme'))->toBe('dark');
    });

    it('returns default when key does not exist', function (): void {
        expect(Setting::get('missing', 'fallback'))->toBe('fallback');
    });

    it('returns null when key does not exist and no default', function (): void {
        expect(Setting::get('missing'))->toBeNull();
    });

    it('updates existing value', function (): void {
        Setting::set('format', 'jpeg');
        Setting::set('format', 'pdf');

        expect(Setting::get('format'))->toBe('pdf');
        expect(Setting::where('key', 'format')->count())->toBe(1);
    });

    it('stores boolean as string', function (): void {
        Setting::set('enabled', '1');

        expect(Setting::get('enabled'))->toBe('1');
        expect((bool) Setting::get('enabled'))->toBeTrue();
    });

    it('stores integer as string', function (): void {
        Setting::set('quality', '85');

        expect(Setting::get('quality'))->toBe('85');
        expect((int) Setting::get('quality'))->toBe(85);
    });
});
