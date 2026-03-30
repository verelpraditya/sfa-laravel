<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SFA Distributor') }}</title>

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-[radial-gradient(circle_at_top_left,rgba(103,232,249,0.14),transparent_22%),radial-gradient(circle_at_top_right,rgba(56,189,248,0.16),transparent_24%),linear-gradient(180deg,#f3f7fc_0%,#edf3fb_52%,#e8eef7_100%)] xl:flex">
            @include('layouts.navigation')

            <div class="min-w-0 flex-1 xl:pl-1">
                @isset($header)
                    <header class="border-b border-white/70 bg-white/68 shadow-[0_20px_50px_-44px_rgba(15,23,42,0.55)] backdrop-blur-xl">
                        <div class="mx-auto max-w-7xl px-4 py-5 sm:px-6 lg:px-8 xl:px-10">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <main class="relative">
                    <div class="pointer-events-none absolute inset-x-0 top-0 h-64 bg-[radial-gradient(circle_at_top,rgba(14,165,233,0.09),transparent_62%)]"></div>
                    {{ $slot }}
                </main>
            </div>
        </div>

        @stack('scripts')
    </body>
</html>
