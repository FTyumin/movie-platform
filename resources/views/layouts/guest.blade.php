<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Filmstack</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|jetbrains-mono:400,500|jost:300,400,500,600,700,900,400i,500i" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased flex flex-col min-h-screen">
    @include('header')

    {{-- The guest homepage hero runs edge-to-edge, same as the authenticated
         homepage — this layout currently has no other consumer, so no
         opt-in/opt-out gutter mechanism like layouts.app's @yield('main-class'). --}}
    <main class="flex-1 bg-base-100">
        @if (session('success') || session('warning'))
            <div class="mx-auto w-full max-w-7xl px-6 pt-6">
                @if (session('success'))
                    <div class="mb-6 rounded-field bg-green-600/20 border border-green-500/30 px-4 py-3 text-green-400">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('warning'))
                    <div class="mb-6 rounded-field bg-red-600/20 border border-red-500/30 px-4 py-3 text-red-400">
                        {{ session('warning') }}
                    </div>
                @endif
            </div>
        @endif

        {{ $slot }}
    </main>

    @include('footer')

    @stack('scripts')
</body>
</html>
