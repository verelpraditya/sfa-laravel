<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#0ea5e9">
        <meta name="apple-mobile-web-app-status-bar-style" content="default">

        <title>{{ config('app.name', 'SFA Distributor') }}</title>

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="antialiased bg-slate-50 text-slate-900">
        <div class="min-h-screen xl:flex">
            @include('layouts.navigation')

            <div class="min-w-0 flex-1 xl:pl-1">
                @isset($header)
                    <header class="border-b border-slate-200 bg-white">
                        <div class="mx-auto max-w-7xl px-4 py-4 sm:px-6 sm:py-5 lg:px-8 xl:px-10">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <main class="pb-8 sm:pb-10">
                    {{ $slot }}
                </main>
            </div>
        </div>

        {{-- Toast notification container --}}
        @if (session('status') || session('success'))
            <div x-data="{ show: true }"
                 x-init="setTimeout(() => show = false, 4000)"
                 x-show="show"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 scale-95"
                 class="fixed bottom-6 left-1/2 z-[60] -translate-x-1/2">
                <div class="flex items-center gap-2.5 rounded-full bg-slate-900 px-5 py-3 shadow-xl shadow-slate-900/20">
                    <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-500">
                        <svg class="h-3 w-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="m5 13 4 4L19 7" /></svg>
                    </span>
                    <p class="text-sm font-medium text-white">{{ session('status') ?? session('success') }}</p>
                    <button @click="show = false" class="ml-1 rounded-full p-0.5 text-slate-400 transition hover:text-white">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12" /></svg>
                    </button>
                </div>
            </div>
        @endif

        @stack('scripts')
    </body>
</html>
