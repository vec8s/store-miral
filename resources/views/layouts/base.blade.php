<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Rafal Store'))</title>
    <meta name="description" content="@yield('description', 'متجر رافال — صيحة في عالم الحلي والهدايا')">

    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

    {{-- Tailwind via CDN (إصدار Play CDN مع @apply غير مدعوم، نستخدم classes مباشرة) --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Cairo', 'Tajawal', 'system-ui', 'sans-serif'],
                        display: ['Tajawal', 'Cairo', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50:  '#fdf6ed', 100: '#fae8cd', 200: '#f4d199', 300: '#edb260',
                            400: '#e69637', 500: '#d97b1c', 600: '#bf6115', 700: '#994914',
                            800: '#7c3c17', 900: '#673318',
                        },
                        gold: {
                            400: '#d4af37', 500: '#c5a028', 600: '#a88520',
                        },
                    },
                    boxShadow: {
                        soft: '0 2px 12px -2px rgb(0 0 0 / 0.08)',
                        card: '0 4px 24px -6px rgb(0 0 0 / 0.10)',
                    },
                },
            },
        };
    </script>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800&family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Cairo', 'Tajawal', system-ui, sans-serif; }
        h1, h2, h3, h4, h5 { font-family: 'Tajawal', 'Cairo', system-ui, sans-serif; letter-spacing: -0.025em; }
        .container-rtl { max-width: 80rem; margin-left: auto; margin-right: auto; padding-left: 1rem; padding-right: 1rem; }
        @media (min-width: 640px) { .container-rtl { padding-left: 1.5rem; padding-right: 1.5rem; } }
        @media (min-width: 1024px) { .container-rtl { padding-left: 2rem; padding-right: 2rem; } }
        [x-cloak] { display: none !important; }
    </style>

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.5/dist/cdn.min.js"></script>

    @stack('head')
</head>
<body class="min-h-screen flex flex-col bg-gray-50 text-gray-800">

    @if (session('status'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition
             class="fixed top-4 left-1/2 -translate-x-1/2 z-50 bg-green-50 border border-green-200 text-green-800 px-5 py-3 rounded-xl shadow-lg">
            {{ session('status') }}
        </div>
    @endif

    @yield('body')

    @stack('scripts')
</body>
</html>
