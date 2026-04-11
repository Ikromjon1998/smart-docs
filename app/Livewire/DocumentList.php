<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Document;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app', ['title' => 'My Documents'])]
class DocumentList extends Component
{
    public string $search = '';

    public string $category = '';

    /** No authorization check — this is a single-user NativePHP mobile app. */
    public function deleteDocument(int $id): void
    {
        Document::findOrFail($id)->delete();
    }

    public function render(): View
    {
        $query = Document::query()->latest();

        if ($this->search !== '') {
            $query->where('title', 'like', '%'.addcslashes($this->search, '%_').'%');
        }

        if ($this->category !== '') {
            $query->where('category', $this->category);
        }

        return view('livewire.document-list', [
            'documents' => $query->get(),
            'categories' => Document::query()->distinct()->pluck('category')->sort()->values(),
        ]);
    }
}
