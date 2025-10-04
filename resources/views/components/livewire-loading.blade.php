<!-- Global Livewire Loading Indicator -->
<div 
    wire:loading.delay.longer 
    class="fixed inset-0 z-50 flex items-center justify-center bg-white bg-opacity-90 backdrop-blur-sm"
>
    <div class="text-center">
        <!-- Simple Spinner -->
        <div class="mb-4">
            <div class="w-12 h-12 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin mx-auto"></div>
        </div>
        
        <!-- Loading Text -->
        <div class="text-lg font-medium text-gray-700">
            <span>Yükleniyor</span>
            <span class="animate-pulse">...</span>
        </div>
    </div>
</div>