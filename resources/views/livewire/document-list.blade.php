<div class="px-4 py-4 space-y-4">
    {{-- Search and filter --}}
    <div class="flex gap-2">
        <div class="flex-1 relative">
            <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search documents..."
                   class="w-full pl-9 pr-3 py-2.5 text-sm bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
        @if($categories->isNotEmpty())
        <select wire:model.live="category" class="text-sm bg-white border border-gray-200 rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            <option value="">All</option>
            @foreach($categories as $cat)
                <option value="{{ $cat }}">{{ ucfirst($cat) }}</option>
            @endforeach
        </select>
        @endif
    </div>

    {{-- Document count --}}
    <p class="text-xs text-gray-400 px-1">{{ $documents->count() }} document(s)</p>

    {{-- Document cards --}}
    @forelse($documents as $document)
        <a href="/documents/{{ $document->id }}" wire:navigate
           class="block bg-white rounded-2xl shadow-sm border border-gray-100 p-4 active:bg-gray-50 transition-colors">
            <div class="flex items-start justify-between gap-3">
                <div class="flex-1 min-w-0">
                    <h3 class="font-semibold text-gray-900 truncate">{{ $document->title }}</h3>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                            {{ ucfirst($document->category) }}
                        </span>
                        <span class="text-xs text-gray-400">{{ $document->page_count }} page(s)</span>
                        <span class="text-xs text-gray-400 uppercase">{{ $document->output_format }}</span>
                    </div>
                    @if($document->summary)
                        <p class="text-sm text-gray-500 mt-2 line-clamp-2">{{ $document->summary }}</p>
                    @endif
                </div>
                <div class="text-right shrink-0">
                    <p class="text-xs text-gray-400">{{ $document->created_at->diffForHumans() }}</p>
                    @if($document->file_size > 0)
                        <p class="text-xs text-gray-300 mt-1">{{ number_format($document->file_size / 1024, 1) }} KB</p>
                    @endif
                </div>
            </div>
        </a>
    @empty
        <div class="text-center py-16">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-200 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="text-lg font-semibold text-gray-400 mb-1">No documents yet</h3>
            <p class="text-sm text-gray-300 mb-6">Scan your first document to get started</p>
            <a href="/scan" wire:navigate class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-full shadow-md active:bg-blue-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Scan Document
            </a>
        </div>
    @endforelse
</div>
