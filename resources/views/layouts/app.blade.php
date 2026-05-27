<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-950 text-slate-100 antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Longtail Workspace') }} - Administrative Portal</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400..700;1,400..700&family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <!-- Flatpickr (Datepicker) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/dark.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>


    <style>
        body {
            font-family: 'Instrument Sans', 'Outfit', sans-serif;
            background: radial-gradient(circle at 0% 0%, rgba(30, 27, 75, 0.4) 0%, transparent 50%),
                        radial-gradient(circle at 100% 100%, rgba(76, 29, 149, 0.3) 0%, transparent 50%),
                        #030712;
            background-attachment: fixed;
        }

        /* Custom Scrollbar for sleek UI */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: rgba(15, 23, 42, 0.6);
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(99, 102, 241, 0.2);
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(99, 102, 241, 0.4);
        }

        /* Premium glow effects */
        .glass-card {
            background: rgba(15, 23, 42, 0.55);
            backdrop-filter: blur(16px) saturate(120%);
            -webkit-backdrop-filter: blur(16px) saturate(120%);
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
        }
        
        .glass-sidebar {
            background: rgba(7, 11, 23, 0.7);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-right: 1px solid rgba(255, 255, 255, 0.05);
        }

        .neon-glow {
            text-shadow: 0 0 10px rgba(129, 140, 248, 0.3), 0 0 20px rgba(129, 140, 248, 0.2);
        }
    </style>
</head>
<body class="h-full min-h-screen">
    
    @auth
        <div class="flex h-screen w-full overflow-hidden text-slate-100 relative">
            <!-- Sidebar -->
            @include('layouts.partials.sidebar')

            <!-- Main Content Area -->
            <main class="flex-1 flex flex-col h-full overflow-hidden bg-slate-950/20 relative z-10">
                <!-- Header -->
                @include('layouts.partials.header')

                <!-- Page Content -->
                <div class="flex-1 overflow-y-auto p-8">
                    @isset($slot)
                        {{ $slot }}
                    @else
                        @yield('content')
                    @endisset
                </div>
            </main>
        </div>
    @else
        <div class="flex h-screen w-full overflow-hidden text-slate-100 relative">
            @isset($slot)
                {{ $slot }}
            @else
                @yield('content')
            @endisset
        </div>
    @endauth

    <!-- Toast Notification Banner -->
    @if (session()->has('message'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" class="fixed bottom-5 right-5 z-50 transition-all duration-500 transform hover:scale-105">
            <div class="glass-card bg-slate-900/90 border border-emerald-500/30 rounded-2xl p-4 flex items-center gap-3 shadow-[0_0_20px_rgba(16,185,129,0.15)]">
                <div class="w-8 h-8 rounded-full bg-emerald-500/20 flex items-center justify-center text-emerald-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <div>
                    <h4 class="font-bold text-slate-100 text-sm">Success Operation</h4>
                    <p class="text-xs text-slate-400 mt-0.5">{{ session('message') }}</p>
                </div>
                <button @click="show = false" class="text-slate-400 hover:text-slate-200 transition ml-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="fixed bottom-5 right-5 z-50 transition-all duration-500 transform hover:scale-105">
            <div class="glass-card bg-slate-900/90 border border-rose-500/30 rounded-2xl p-4 flex items-center gap-3 shadow-[0_0_20px_rgba(244,63,94,0.15)]">
                <div class="w-8 h-8 rounded-full bg-rose-500/20 flex items-center justify-center text-rose-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <div>
                    <h4 class="font-bold text-slate-100 text-sm">Action Blocked</h4>
                    <p class="text-xs text-slate-400 mt-0.5">{{ session('error') }}</p>
                </div>
                <button @click="show = false" class="text-slate-400 hover:text-slate-200 transition ml-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        </div>
    @endif

    @livewireScripts
</body>
</html>
