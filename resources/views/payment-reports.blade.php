@extends('layouts.app')

@section('title', 'Ödeme Raporları')

@section('content')
    @livewire('payment-reports')
@endsection

@push('scripts')
<script>
    // Sayfa yüklendiğinde localStorage'dan ayarları yükle
    document.addEventListener('DOMContentLoaded', function() {
        const savedSettings = localStorage.getItem('overdueSettings');
        if (savedSettings) {
            const settings = JSON.parse(savedSettings);
            // Livewire component'e ayarları gönder
            if (window.Livewire) {
                window.Livewire.find(document.querySelector('[wire\\:id]').getAttribute('wire:id')).set('overdueSettings', settings);
                window.Livewire.find(document.querySelector('[wire\\:id]').getAttribute('wire:id')).set('overdueDays', settings.days);
            }
        }
    });
</script>
@endpush