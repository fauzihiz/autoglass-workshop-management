<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') - {{ config('app.name') }}</title>
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-gray-50 text-gray-900 antialiased" x-data="{ sidebarOpen: false }">
    <div class="flex min-h-screen">
        <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-40 bg-black/50 lg:hidden" @click="sidebarOpen = false"></div>
        @include('layouts.sidebar')
        <div class="flex flex-1 flex-col">
            <header class="sticky top-0 z-30 flex h-16 items-center gap-4 border-b border-gray-200 bg-white px-4 shadow-sm sm:px-6">
                <button @click="sidebarOpen = true" class="inline-flex items-center justify-center rounded-lg p-2 text-gray-500 hover:bg-gray-100 lg:hidden">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
                </button>
                <h1 class="text-lg font-semibold text-gray-900">@yield('header', 'Dashboard')</h1>
                <div class="ml-auto flex items-center gap-3">
                    <span class="hidden text-sm text-gray-500 sm:inline">{{ now()->translatedFormat('l, d M Y') }}</span>
                </div>
            </header>
            <main class="flex-1 p-4 sm:p-6">
                @yield('content')
            </main>
        </div>
    </div>
    @stack('scripts')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
