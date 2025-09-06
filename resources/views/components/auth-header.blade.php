@props([
    'title',
    'description',
])

<div class="flex w-full flex-col text-center space-y-4">
    <!-- Logo with white background -->
    <div class="flex justify-center mb-4">
        <div class="bg-white rounded-lg p-4 shadow-lg">
            <img src="{{ asset('klinikgo.png') }}" alt="KlinikGo Logo" class="h-16 w-auto">
        </div>
    </div>
    
    <!-- Title with blue color -->
    <h2 class="text-2xl font-bold text-[#0c3779]">
        {{ $title }}
    </h2>
    
    <!-- Description with dark color -->
    <p class="text-gray-700 text-sm leading-relaxed">
        {{ $description }}
    </p>
</div>
