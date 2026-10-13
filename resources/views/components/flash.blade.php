@if (session('success') || session('error'))
    <div class="ui-toast-region" aria-live="polite" aria-atomic="true">
        @if (session('success'))
            <div class="alert alert--success ui-toast" role="status"
                 x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 inline h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6" />
                </svg>
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert--error ui-toast" role="alert"
                 x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 inline h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                    <path stroke-linecap="round" d="M12 8v5m0 3h.01M10.3 3.86 1.82 18.1A2 2 0 0 0 3.53 21h16.94a2 2 0 0 0 1.71-2.9L13.7 3.86a2 2 0 0 0-3.4 0Z" />
                </svg>
                {{ session('error') }}
            </div>
        @endif
    </div>
@endif
