<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dim">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($title) ? $title . ' - TaskFlow' : 'TaskFlow' }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5/themes.css" rel="stylesheet" type="text/css" />
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Instrument Sans', sans-serif; }
        .animate-fade-out {
            animation: fadeOut 4s ease forwards;
        }
        @keyframes fadeOut {
            0%   { opacity: 1; }
            70%  { opacity: 1; }
            100% { opacity: 0; pointer-events: none; }
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.tsx'])
</head>

<body class="min-h-screen flex flex-col bg-base-200 font-sans">

    {{-- Navigation --}}
    <nav class="navbar bg-base-100 shadow-sm">
        <div class="navbar-start">
            <a href="{{ url('/') }}" class="flex items-center gap-2 px-4">
                <svg class="w-5 h-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
                <span class="font-bold text-base-content">TaskFlow</span>
            </a>
        </div>
        <div class="navbar-end gap-2 px-4">
            @auth
                <a href="{{ route('projects.index') }}"
                   class="btn btn-ghost btn-sm {{ request()->routeIs('projects.*') ? 'bg-base-200' : '' }}">
                    Projects
                </a>

                <span class="text-sm text-base-content/70 hidden sm:inline">
                    {{ auth()->user()->name }}
                </span>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline btn-sm">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}"
                   class="btn btn-ghost btn-sm {{ request()->routeIs('login') ? 'bg-base-200' : '' }}">
                    Login
                </a>
                <a href="{{ route('register') }}"
                   class="btn btn-primary btn-sm {{ request()->routeIs('register') ? 'btn-secondary' : '' }}">
                    Register
                </a>
            @endauth
        </div>
    </nav>

    {{-- Success Toast --}}
    @if(session('success'))
        <div class="toast toast-top toast-center z-50">
            <div class="alert alert-success animate-fade-out">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    {{-- Error Toast --}}
    @if(session('error'))
        <div class="toast toast-top toast-center z-50">
            <div class="alert alert-error animate-fade-out">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    {{-- Main Content --}}
    <main class="flex-1 container mx-auto px-4 py-8">
        @yield('content')
    </main>

    <footer class="w-full mt-16 py-6 border-t border-base-300">
        <div class="container mx-auto px-4 text-center text-sm text-base-content/40">
            made with Laravel &amp; ♥
        </div>
    </footer>

</body>

</html>
