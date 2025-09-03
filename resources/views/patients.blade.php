@extends('layouts.app')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Hasta Kayıtları</h1>
    
    @livewire('patient-list')
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toast mesajları için container oluştur
        if (!document.getElementById('toast-container')) {
            const toastContainer = document.createElement('div');
            toastContainer.id = 'toast-container';
            toastContainer.className = 'fixed top-4 right-4 z-50 space-y-2';
            document.body.appendChild(toastContainer);
        }
        
        // Toast mesajı gösterme fonksiyonu
        function showToast(type, message) {
            const toast = document.createElement('div');
            const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
            const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
            
            toast.className = `${bgColor} text-white px-6 py-3 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full opacity-0`;
            toast.innerHTML = `
                <div class="flex items-center">
                    <i class="fas ${icon} mr-2"></i>
                    <span>${message}</span>
                </div>
            `;
            
            document.getElementById('toast-container').appendChild(toast);
            
            // Animasyon ile göster
            setTimeout(() => {
                toast.classList.remove('translate-x-full', 'opacity-0');
            }, 100);
            
            // 5 saniye sonra kaldır
            setTimeout(() => {
                toast.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.parentNode.removeChild(toast);
                    }
                }, 300);
            }, 5000);
        }
        
        // Livewire event listener'ları
        window.addEventListener('show-toast', event => {
            showToast(event.detail[0].type, event.detail[0].message);
        });
        
        window.addEventListener('patient-added', () => {
            // Sayfa yenileme yerine sadece başarı mesajı göster
            setTimeout(() => {
                location.reload();
            }, 2000);
        });
    });
</script>
@endpush