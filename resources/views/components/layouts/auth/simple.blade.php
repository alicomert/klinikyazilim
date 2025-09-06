<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-[#1e40af] dark:from-slate-900 dark:via-blue-900 dark:to-indigo-900 flex items-center justify-center p-4 relative overflow-hidden">
        <!-- Animated background elements -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-white/20 rounded-full blur-3xl animate-pulse" style="animation-delay: 0s;"></div>
            <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-white/15 rounded-full blur-3xl animate-pulse" style="animation-delay: 2s;"></div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-64 h-64 bg-white/10 rounded-full blur-3xl animate-pulse" style="animation-delay: 4s;"></div>
        </div>
        
        <div class="relative flex min-h-screen flex-col items-center justify-center gap-8 p-6 md:p-10">
            <div class="flex w-full max-w-md flex-col gap-6">
                <!-- Logo Section -->
                <a href="{{ route('home') }}" class="flex flex-col items-center gap-3 font-medium group" wire:navigate>
                    <div class="relative">
                        <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl blur opacity-25 group-hover:opacity-40 transition-opacity duration-300"></div>
                    </div>
                    <span class="sr-only">{{ config('app.name', 'klinikgo') }}</span>
                </a>
                
                <!-- Auth Form Container -->
                <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-2xl shadow-2xl border border-white/20 dark:border-slate-700/50 p-8">
                    <div class="flex flex-col gap-6">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
        
        <style>
            .animation-delay-2000 {
                animation-delay: 2s;
            }
            .animation-delay-4000 {
                animation-delay: 4s;
            }
        </style>
        @fluxScripts
        @livewireScripts
    </body>
</html>
