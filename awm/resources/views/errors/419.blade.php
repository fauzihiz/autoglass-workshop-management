<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>419 - Page Expired</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-50 text-gray-900 antialiased flex items-center justify-center min-h-screen">
    <div class="text-center px-6">
        <h1 class="text-7xl font-extrabold text-amber-500">419</h1>
        <p class="mt-4 text-xl font-semibold text-gray-700">Page Expired</p>
        <p class="mt-2 text-gray-500">Your session has expired. Please refresh the page and try again.</p>
        <a href="/" class="mt-6 inline-block rounded-lg bg-blue-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
            Back to Dashboard
        </a>
    </div>
</body>
</html>
