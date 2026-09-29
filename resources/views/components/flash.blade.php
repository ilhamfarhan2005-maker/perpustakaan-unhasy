@if(session('success'))
    <div class="max-w-7xl mx-auto px-4 mt-4">
        <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded"
             x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)">
            ✓ {{ session('success') }}
        </div>
    </div>
@endif

@if(session('error'))
    <div class="max-w-7xl mx-auto px-4 mt-4">
        <div class="bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded"
             x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
            ✗ {{ session('error') }}
        </div>
    </div>
@endif
