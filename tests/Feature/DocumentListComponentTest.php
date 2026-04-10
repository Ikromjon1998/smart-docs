<?php

declare(strict_types=1);

use App\Livewire\DocumentList;
use App\Models\Document;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

describe('DocumentList component', function (): void {
    it('renders successfully', function (): void {
        Livewire::test(DocumentList::class)
            ->assertStatus(200)
            ->assertSee('No documents yet');
    });

    it('displays documents', function (): void {
        Document::create([
            'title' => 'My Test Document',
            'file_paths' => ['/path/scan.jpg'],
            'page_count' => 1,
            'output_format' => 'jpeg',
            'file_size' => 1024,
        ]);

        Livewire::test(DocumentList::class)
            ->assertSee('My Test Document')
            ->assertSee('1 document(s)');
    });

    it('filters by search term', function (): void {
        Document::create([
            'title' => 'Invoice 2026',
            'file_paths' => [],
            'page_count' => 1,
            'output_format' => 'jpeg',
            'file_size' => 0,
        ]);

        Document::create([
            'title' => 'Receipt ABC',
            'file_paths' => [],
            'page_count' => 1,
            'output_format' => 'jpeg',
            'file_size' => 0,
        ]);

        Livewire::test(DocumentList::class)
            ->set('search', 'Invoice')
            ->assertSee('Invoice 2026')
            ->assertDontSee('Receipt ABC');
    });

    it('filters by category', function (): void {
        Document::create([
            'title' => 'Doc A',
            'category' => 'work',
            'file_paths' => [],
            'page_count' => 1,
            'output_format' => 'jpeg',
            'file_size' => 0,
        ]);

        Document::create([
            'title' => 'Doc B',
            'category' => 'personal',
            'file_paths' => [],
            'page_count' => 1,
            'output_format' => 'jpeg',
            'file_size' => 0,
        ]);

        Livewire::test(DocumentList::class)
            ->set('category', 'work')
            ->assertSee('Doc A')
            ->assertDontSee('Doc B');
    });

    it('deletes a document', function (): void {
        $doc = Document::create([
            'title' => 'To Delete',
            'file_paths' => [],
            'page_count' => 1,
            'output_format' => 'jpeg',
            'file_size' => 0,
        ]);

        Livewire::test(DocumentList::class)
            ->call('deleteDocument', $doc->id);

        expect(Document::find($doc->id))->toBeNull();
    });
});
