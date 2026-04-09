<?php

namespace App\Livewire;

use App\Models\Document;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app', ['title' => 'My Documents'])]
class DocumentList extends Component
{
    public string $search = '';
    public string $category = '';

    public function deleteDocument(int $id): void
    {
        Document::findOrFail($id)->delete();
    }

    public function render()
    {
        $query = Document::query()->latest();

        if ($this->search !== '') {
            $query->where('title', 'like', "%{$this->search}%");
        }

        if ($this->category !== '') {
            $query->where('category', $this->category);
        }

        return view('livewire.document-list', [
            'documents' => $query->get(),
            'categories' => Document::distinct()->pluck('category')->sort()->values(),
        ]);
    }
}
