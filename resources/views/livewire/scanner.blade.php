<div class="px-4 py-6 space-y-6">
    {{-- Status messages --}}
    @if($error)
        <div class="bg-red-50 border border-red-200 rounded-xl p-4">
            <div class="flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
                <p class="text-sm text-red-700">{{ $error }}</p>
            </div>
        </div>
    @endif

    @if($status)
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
            <div class="flex items-center gap-3">
                @if($scanning)
                    <svg class="animate-spin h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                @endif
                <p class="text-sm text-blue-700">{{ $status }}</p>
            </div>
        </div>
    @endif

    @if($lastDocumentId)
        <a href="/documents/{{ $lastDocumentId }}" wire:navigate
           class="block bg-green-50 border border-green-200 rounded-xl p-4 text-center text-green-700 font-medium text-sm">
            View scanned document &rarr;
        </a>
    @endif

    {{-- Scan button --}}
    <div class="flex justify-center pt-4">
        <button
            wire:click="startScan"
            wire:loading.attr="disabled"
            @if($scanning) disabled @endif
            class="relative w-40 h-40 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 shadow-xl
                   flex flex-col items-center justify-center gap-2 text-white
                   active:scale-95 transition-transform disabled:opacity-60 disabled:scale-100"
        >
            @if($scanning)
                <svg class="animate-spin h-10 w-10" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <span class="text-sm font-medium">Scanning...</span>
            @else
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span class="text-sm font-medium">Tap to Scan</span>
            @endif
        </button>
    </div>

    {{-- Scan options --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 divide-y divide-gray-100">
        <div class="px-4 py-3">
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Scan Options</h3>
        </div>

        {{-- Output format --}}
        <div class="px-4 py-3 flex items-center justify-between">
            <label class="text-sm font-medium text-gray-700">Output Format</label>
            <select wire:model="outputFormat" class="text-sm bg-gray-50 border border-gray-200 rounded-lg px-3 py-1.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="jpeg">JPEG Images</option>
                <option value="pdf">PDF Document</option>
            </select>
        </div>

        {{-- JPEG quality --}}
        @if($outputFormat === 'jpeg')
        <div class="px-4 py-3">
            <div class="flex items-center justify-between mb-2">
                <label class="text-sm font-medium text-gray-700">JPEG Quality</label>
                <span class="text-sm text-gray-500 tabular-nums">{{ $jpegQuality }}%</span>
            </div>
            <input type="range" wire:model.live="jpegQuality" min="1" max="100" step="1"
                   class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-blue-600">
        </div>
        @endif

        {{-- Max pages --}}
        <div class="px-4 py-3 flex items-center justify-between">
            <div>
                <label class="text-sm font-medium text-gray-700">Max Pages</label>
                <p class="text-xs text-gray-400">0 = unlimited</p>
            </div>
            <input type="number" wire:model="maxPages" min="0" max="100"
                   class="w-20 text-sm bg-gray-50 border border-gray-200 rounded-lg px-3 py-1.5 text-center focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>

        {{-- Gallery import --}}
        <div class="px-4 py-3 flex items-center justify-between">
            <div>
                <label class="text-sm font-medium text-gray-700">Gallery Import</label>
                <p class="text-xs text-gray-400">Android only</p>
            </div>
            <button
                wire:click="$toggle('galleryImport')"
                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors {{ $galleryImport ? 'bg-blue-600' : 'bg-gray-300' }}"
            >
                <span class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform {{ $galleryImport ? 'translate-x-6' : 'translate-x-1' }}"></span>
            </button>
        </div>
    </div>
</div>
