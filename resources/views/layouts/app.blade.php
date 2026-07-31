<!DOCTYPE html>
<html lang="{{ str_replace('_','-',app()->getLocale()) }}">
<head>

  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  
  <title>Filmstack</title>
 
   <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=anton:400|fraunces:500,600,600i,700|inter:400,500,600,700|jetbrains-mono:400,500|oswald:400,500,600,700" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased flex flex-col min-h-screen">
  @include('header')

  <main class="px-6 sm:px-4  py-8 flex-1 bg-gradient-to-b from-base-100 to-[#120d0a]">
    @if (session('success'))
      <div class="mb-6 rounded-lg bg-green-600/20 border border-green-500/30 px-4 py-3 text-green-400">
          {{ session('success') }}
      </div>
    @endif
    @if (session('warning'))
      <div class="mb-6 rounded-lg bg-red-600/20 border border-red-500/30 px-4 py-3 text-red-400">
          {{ session('warning') }}
      </div>
    @endif

    @yield('content')
  </main>

  @include('footer')
  
    @stack('scripts')
</body>
</html>
