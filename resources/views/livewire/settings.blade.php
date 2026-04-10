<div class="px-4 py-4 space-y-4">
    {{-- Scanner status --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $scannerAvailable ? 'bg-green-50' : 'bg-yellow-50' }}">
                @if($scannerAvailable)
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                @endif
            </div>
            <div>
                <h3 class="text-sm font-semibold {{ $scannerAvailable ? 'text-green-700' : 'text-yellow-700' }}">
                    {{ $scannerAvailable ? 'Scanner Ready' : 'Scanner Not Available' }}
                </h3>
                <p class="text-xs text-gray-400">
                    {{ $scannerAvailable ? 'Native bridge is connected' : 'Run: php artisan native:run android|ios' }}
                </p>
            </div>
        </div>
    </div>

    {{-- Default scan settings --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 divide-y divide-gray-100">
        <div class="px-4 py-3">
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Default Scan Settings</h3>
        </div>

        <div class="px-4 py-3 flex items-center justify-between">
            <label class="text-sm font-medium text-gray-700">Output Format</label>
            <select wire:model="defaultFormat" class="text-sm bg-gray-50 border border-gray-200 rounded-lg px-3 py-1.5">
                <option value="jpeg">JPEG</option>
                <option value="pdf">PDF</option>
            </select>
        </div>

        <div class="px-4 py-3">
            <div class="flex items-center justify-between mb-2">
                <label class="text-sm font-medium text-gray-700">JPEG Quality</label>
                <span class="text-sm text-gray-500 tabular-nums">{{ $defaultQuality }}%</span>
            </div>
            <input type="range" wire:model.live="defaultQuality" min="1" max="100" step="1"
                   class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-blue-600">
        </div>

        <div class="px-4 py-3 flex items-center justify-between">
            <div>
                <label class="text-sm font-medium text-gray-700">Max Pages</label>
                <p class="text-xs text-gray-400">0 = unlimited</p>
            </div>
            <input type="number" wire:model="defaultMaxPages" min="0" max="100"
                   class="w-20 text-sm bg-gray-50 border border-gray-200 rounded-lg px-3 py-1.5 text-center">
        </div>

        <div class="px-4 py-3 flex items-center justify-between">
            <div>
                <label class="text-sm font-medium text-gray-700">Gallery Import</label>
                <p class="text-xs text-gray-400">Android only</p>
            </div>
            <button
                wire:click="$toggle('defaultGalleryImport')"
                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors {{ $defaultGalleryImport ? 'bg-blue-600' : 'bg-gray-300' }}"
            >
                <span class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform {{ $defaultGalleryImport ? 'translate-x-6' : 'translate-x-1' }}"></span>
            </button>
        </div>
    </div>

    @if($saved)
        <div class="bg-green-50 border border-green-200 rounded-xl p-3 text-center text-sm text-green-700 font-medium">
            Settings are applied per-session. Config defaults are set in config/document-scanner.php.
        </div>
    @endif

    {{-- App info --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 divide-y divide-gray-100">
        <div class="px-4 py-3">
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">About</h3>
        </div>
        <div class="px-4 py-3 flex justify-between">
            <span class="text-sm text-gray-500">App</span>
            <span class="text-sm font-medium">Smart Docs</span>
        </div>
        <div class="px-4 py-3 flex justify-between">
            <span class="text-sm text-gray-500">Scanner Plugin</span>
            <span class="text-sm font-medium">nativephp-mobile-document-scanner</span>
        </div>
        <div class="px-4 py-3 flex justify-between">
            <span class="text-sm text-gray-500">Framework</span>
            <span class="text-sm font-medium">NativePHP Mobile</span>
        </div>
    </div>
</div>
