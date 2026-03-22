<x-filament-panels::page.simple>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-50 via-white to-slate-100 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900 p-4">
        <div class="w-full max-w-md">
            {{-- Logo & Header --}}
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-primary-500 to-primary-600 rounded-2xl shadow-lg shadow-primary-500/30 mb-4">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                
                <h1 class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight">
                    {{ $this->getHeading() }}
                </h1>
                <p class="mt-2 text-slate-500 dark:text-slate-400 text-sm">
                    Masuk untuk mengelola absensi sekolah
                </p>
            </div>

            {{-- Login Card --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-100 dark:border-slate-700 overflow-hidden">
                <div class="p-8">
                    {{ $this->form }}
                </div>
                
                <div class="px-8 pb-6">
                    <x-filament::button
                        type="submit"
                        form="form"
                        color="primary"
                        class="w-full py-3 text-base font-semibold"
                    >
                        <span class="flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                            </svg>
                            Masuk
                        </span>
                    </x-filament::button>
                </div>
            </div>

            {{-- Footer --}}
            <div class="mt-8 text-center">
                <p class="text-sm text-slate-400 dark:text-slate-500">
                    SiAbsen &copy; {{ date('Y') }} - Sistem Absensi Sekolah
                </p>
                <p class="text-xs text-slate-300 dark:text-slate-600 mt-1">
                    Powered by Laravel & Filament
                </p>
            </div>
        </div>
    </div>

    @push('scripts')
    <style>
        .fi-simple-main {
            background: transparent !important;
            box-shadow: none !important;
            padding: 0 !important;
        }
        .fi-simple-main-ctn {
            background: transparent !important;
        }
        .fi-header-heading {
            display: none;
        }
    </style>
    @endpush
</x-filament-panels::page.simple>
