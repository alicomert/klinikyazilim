@props(['message' => 'Yükleniyor...', 'size' => 'medium'])

@php
    $sizeClasses = [
        'small' => 'w-6 h-6',
        'medium' => 'w-12 h-12',
        'large' => 'w-16 h-16'
    ];
    $spinnerSize = $sizeClasses[$size] ?? $sizeClasses['medium'];
@endphp

<div {{ $attributes->merge(['class' => 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50']) }} 
     x-data="{ show: true }" 
     x-show="show" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
    
    <div class="bg-white rounded-2xl shadow-2xl p-8 flex flex-col items-center space-y-4 max-w-sm mx-4">
        <!-- Hospital Icon with Pulse Animation -->
        <div class="relative">
            <div class="absolute inset-0 bg-blue-400 rounded-full animate-ping opacity-75"></div>
            <div class="relative bg-blue-500 rounded-full p-4 shadow-lg">
                <svg class="{{ $spinnerSize }} text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.94-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
                </svg>
            </div>
        </div>

        <!-- Spinning Ring -->
        <div class="relative {{ $spinnerSize }}">
            <div class="absolute inset-0 border-4 border-gray-200 rounded-full"></div>
            <div class="absolute inset-0 border-4 border-blue-500 rounded-full border-t-transparent animate-spin"></div>
        </div>

        <!-- Loading Text -->
        <div class="text-center">
            <h3 class="text-lg font-semibold text-gray-800 mb-1">{{ $message }}</h3>
            <div class="flex space-x-1 justify-center">
                <div class="w-2 h-2 bg-blue-500 rounded-full animate-bounce"></div>
                <div class="w-2 h-2 bg-blue-500 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                <div class="w-2 h-2 bg-blue-500 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes pulse-ring {
    0% {
        transform: scale(0.33);
    }
    40%, 50% {
        opacity: 1;
    }
    100% {
        opacity: 0;
        transform: scale(1.33);
    }
}

.animate-pulse-ring {
    animation: pulse-ring 1.25s cubic-bezier(0.455, 0.03, 0.515, 0.955) infinite;
}

@keyframes float {
    0%, 100% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-10px);
    }
}

.animate-float {
    animation: float 3s ease-in-out infinite;
}
</style>