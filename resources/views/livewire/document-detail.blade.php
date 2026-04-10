<div class="px-4 py-4 space-y-4 overflow-x-hidden">
    {{-- Status message --}}
    @if($actionStatus)
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-3 text-center text-sm text-blue-700 font-medium">
            {{ $actionStatus }}
        </div>
    @endif

    {{-- Document header --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
        @if($editing)
            <div class="space-y-3">
                <div>
                    <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Title</label>
                    <input type="text" wire:model="title"
                           class="w-full mt-1 text-sm bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('title') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Category</label>
                    <input type="text" wire:model="category"
                           class="w-full mt-1 text-sm bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('category') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Summary</label>
                    <textarea wire:model="summary" rows="4"
                              class="w-full mt-1 text-sm bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Add a summary or notes about this document..."></textarea>
                    @error('summary') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="flex gap-2">
                    <button wire:click="save" class="flex-1 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg active:bg-blue-700">Save</button>
                    <button wire:click="cancelEditing" class="flex-1 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg active:bg-gray-200">Cancel</button>
                </div>
            </div>
        @else
            <div class="flex items-start justify-between">
                <div class="flex-1 min-w-0">
                    <h2 class="text-xl font-bold text-gray-900">{{ $document->title }}</h2>
                    <span class="inline-flex items-center mt-2 px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                        {{ ucfirst($document->category) }}
                    </span>
                </div>
                <button wire:click="startEditing" class="text-blue-600 text-sm font-medium shrink-0">Edit</button>
            </div>
            @if($document->summary)
                <p class="text-sm text-gray-600 mt-3 leading-relaxed">{{ $document->summary }}</p>
            @else
                <p class="text-sm text-gray-400 italic mt-3">No summary yet. Tap Edit to add one.</p>
            @endif
        @endif
    </div>

    {{-- Action buttons --}}
    <div class="grid grid-cols-2 gap-3">
        {{-- Share --}}
        <button wire:click="shareDocument"
                class="flex flex-col items-center gap-1.5 bg-white rounded-2xl shadow-sm border border-gray-100 py-4 active:bg-gray-50">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
            </svg>
            <span class="text-xs font-medium text-gray-700">Share</span>
        </button>

        {{-- Convert to PDF --}}
        @if($document->output_format === 'jpeg')
        <button wire:click="convertToPdf"
                wire:confirm="Convert this document to PDF? The original JPEG files will be replaced."
                class="flex flex-col items-center gap-1.5 bg-white rounded-2xl shadow-sm border border-gray-100 py-4 active:bg-gray-50">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
            </svg>
            <span class="text-xs font-medium text-gray-700">Convert to PDF</span>
        </button>
        @else
        <div class="flex flex-col items-center gap-1.5 bg-white rounded-2xl shadow-sm border border-gray-100 py-4 opacity-50">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-xs font-medium text-gray-700">Already PDF</span>
        </div>
        @endif
    </div>

    {{-- Document info --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 divide-y divide-gray-100">
        <div class="px-4 py-3">
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Details</h3>
        </div>
        <div class="px-4 py-3 flex justify-between">
            <span class="text-sm text-gray-500">Pages</span>
            <span class="text-sm font-medium">{{ $document->page_count }}</span>
        </div>
        <div class="px-4 py-3 flex justify-between">
            <span class="text-sm text-gray-500">Format</span>
            <span class="text-sm font-medium uppercase">{{ $document->output_format }}</span>
        </div>
        @if($document->file_size > 0)
        <div class="px-4 py-3 flex justify-between">
            <span class="text-sm text-gray-500">Size</span>
            <span class="text-sm font-medium">
                @if($document->file_size >= 1048576)
                    {{ number_format($document->file_size / 1048576, 1) }} MB
                @else
                    {{ number_format($document->file_size / 1024, 1) }} KB
                @endif
            </span>
        </div>
        @endif
        <div class="px-4 py-3 flex justify-between">
            <span class="text-sm text-gray-500">Scanned</span>
            <span class="text-sm font-medium">{{ $document->created_at->format('M j, Y g:i A') }}</span>
        </div>
    </div>

    {{-- Scanned pages preview --}}
    @if(!empty($images))
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
        <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Scanned Pages</h3>
        <div class="grid grid-cols-2 gap-3">
            @foreach($images as $index => $dataUri)
                <div class="aspect-[3/4] bg-gray-100 rounded-xl overflow-hidden border border-gray-200">
                    <img src="{{ $dataUri }}" alt="Page {{ $index + 1 }}" class="w-full h-full object-cover" loading="lazy">
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- File locations --}}
    @if(!empty($document->file_paths))
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 divide-y divide-gray-100">
        <div class="px-4 py-3">
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Storage</h3>
        </div>
        <div class="px-4 py-3">
            <p class="text-sm text-gray-700">Saved in <span class="font-medium">Smart Docs</span> app storage</p>
            <p class="text-xs text-gray-400 mt-1">{{ count($document->file_paths) }} file(s) &middot; {{ strtoupper($document->output_format) }}</p>
            <p class="text-xs text-gray-400 mt-2">To save to your device or cloud storage, tap <span class="font-medium text-gray-500">Share</span> above and choose "Save to Files", Google Drive, or another app.</p>
        </div>
    </div>
    @endif

    {{-- Delete button --}}
    <div class="pt-2 pb-4">
        <button wire:click="delete" wire:confirm="Are you sure you want to delete this document?"
                class="w-full py-3 bg-red-50 text-red-600 text-sm font-medium rounded-xl border border-red-100 active:bg-red-100">
            Delete Document
        </button>
    </div>
</div>
