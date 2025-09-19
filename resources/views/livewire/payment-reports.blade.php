<div class="p-6 bg-gray-50 min-h-screen">
    <!-- Sayfa Başlığı -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Ödeme Raporları</h1>
        <p class="text-gray-600">Detaylı ödeme analizi ve raporlama sistemi</p>
    </div>

    <!-- İstatistik Kartları -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <!-- Toplam Ödeme -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Toplam Ödeme</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalPayments }}</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Toplam Tutar -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Toplam Tutar</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $this->formatCurrency($totalAmount) }}</p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Ortalama Ödeme -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Ortalama Ödeme</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $this->formatCurrency($averagePayment) }}</p>
                </div>
                <div class="p-3 bg-yellow-100 rounded-full">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Ödeme Yapmayan Hastalar -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Ödeme Yapmayanlar</p>
                    <p class="text-2xl font-bold text-red-600">{{ $overduePatients->count() }}</p>
                    @if($overduePatients->count() > 0)
                        <p class="text-xs text-red-500 mt-1">{{ $overdueSettings['days'] }} gün üzeri</p>
                    @endif
                </div>
                <div class="p-3 bg-red-100 rounded-full">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Ödeme Yapmayan Hastalar Uyarısı -->
    @if($overduePatients->count() > 0 && $overdueSettings['showWarning'])
        <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">
                            Ödeme Yapmayan {{ $overduePatients->count() }} Hasta Bulundu!
                        </h3>
                        <div class="mt-2 text-sm text-red-700">
                            <p>{{ $overdueSettings['days'] }} günden fazla süredir ödeme yapmayan hastalar var. Detayları görüntülemek için tıklayın.</p>
                        </div>
                    </div>
                </div>
                <div class="flex space-x-2">
                    <button wire:click="showOverduePatients" class="bg-red-600 text-white px-4 py-2 rounded-md text-sm hover:bg-red-700 transition-colors">
                        Detayları Gör
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Filtreleme ve Arama -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
            <!-- Arama -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Arama</label>
                <input type="text" wire:model.live="search" placeholder="Hasta adı, telefon, TC..." 
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Tarih Filtresi -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tarih Filtresi</label>
                <select wire:model.live="dateFilter" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="all">Tüm Zamanlar</option>
                    <option value="today">Bugün</option>
                    <option value="week">Bu Hafta</option>
                    <option value="month">Bu Ay</option>
                    <option value="custom">Özel Tarih</option>
                </select>
            </div>

            <!-- Ödeme Yöntemi -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Ödeme Yöntemi</label>
                <select wire:model.live="paymentMethodFilter" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="all">Tümü</option>
                    <option value="nakit">Nakit</option>
                    <option value="kredi_karti">Kredi Kartı</option>
                    <option value="banka_havalesi">Banka Havalesi</option>
                    <option value="pos">POS</option>
                    <option value="cek">Çek</option>
                </select>
            </div>

            @if(auth()->user()->role === 'admin')
            <!-- Kullanıcı Filtresi -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Kullanıcı</label>
                <select wire:model.live="userFilter" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="all">Tüm Kullanıcılar</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif
        </div>

        <!-- Özel Tarih Aralığı -->
        @if($dateFilter === 'custom')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Başlangıç Tarihi</label>
                    <input type="date" wire:model.live="startDate" 
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bitiş Tarihi</label>
                    <input type="date" wire:model.live="endDate" 
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        @endif

        <!-- Ek Filtreler ve Butonlar -->
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center space-x-4">
                <label class="flex items-center">
                    <input type="checkbox" wire:model.live="showOverdueOnly" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-700">Sadece ödeme yapmayanları göster</span>
                </label>
            </div>
            
            <div class="flex space-x-2">
                <button wire:click="resetFilters" class="bg-gray-500 text-white px-4 py-2 rounded-md text-sm hover:bg-gray-600 transition-colors">
                    Filtreleri Temizle
                </button>
                <button wire:click="showStats" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-700 transition-colors">
                    Detaylı İstatistikler
                </button>
                <button wire:click="exportPayments" class="bg-green-600 text-white px-4 py-2 rounded-md text-sm hover:bg-green-700 transition-colors">
                    Excel'e Aktar
                </button>
            </div>
        </div>
    </div>

    <!-- Ödeme Yapmayan Hastalar Ayarları -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Ödeme Uyarı Ayarları</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Uyarı Süresi (Gün)</label>
                <input type="number" wire:model="overdueDays" min="1" max="365" 
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="text-xs text-gray-500 mt-1">Bu süreden sonra hastalar ödeme yapmayan olarak işaretlenir</p>
            </div>
            <div class="flex items-end">
                <button wire:click="updateOverdueSettings" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-700 transition-colors">
                    Ayarları Kaydet
                </button>
            </div>
        </div>
    </div>

    <!-- Ödemeler Tablosu -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Ödeme Listesi</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-3 sm:px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hasta</th>
                        <th class="px-3 py-3 sm:px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Telefon</th>
                        <th class="px-3 py-3 sm:px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ödeme Yöntemi</th>
                        <th class="px-3 py-3 sm:px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tutar</th>
                        <th class="px-3 py-3 sm:px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarih</th>
                        @if(auth()->user()->role === 'admin')
                        <th class="px-3 py-3 sm:px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kullanıcı</th>
                        @endif
                        <th class="px-3 py-3 sm:px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notlar</th>
                        <th class="px-3 py-3 sm:px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($payments as $payment)
                        <tr class="{{ isset($payment->is_pending_payment) && $payment->is_pending_payment ? 'bg-yellow-50 hover:bg-yellow-100' : (isset($payment->is_completed_payment) && $payment->is_completed_payment ? 'bg-green-50 hover:bg-green-100' : 'hover:bg-gray-50') }}">
                            <td class="px-3 py-4 sm:px-6">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $payment->patient ? trim($payment->patient->first_name . ' ' . $payment->patient->last_name) : 'Bilinmiyor' }}
                                        </div>
                                        @if($payment->patient && $payment->patient->tc_identity)
                                            <div class="text-sm text-gray-500">TC: {{ $payment->patient->tc_identity }}</div>
                                        @endif
                                    </div>
                                    @if($payment->patient && (isset($payment->is_pending_payment) || isset($payment->is_completed_payment)))
                                         <button wire:click="togglePaymentExpansion('{{ $payment->id }}')"
                                                 class="text-gray-400 hover:text-gray-600 transition-colors">
                                             <svg class="w-4 h-4 transform {{ $this->isPaymentExpanded($payment->id) ? 'rotate-180' : '' }} transition-transform" 
                                                  fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                             </svg>
                                         </button>
                                     @endif
                                </div>
                            </td>
                            <td class="px-3 py-4 sm:px-6 text-sm text-gray-900">
                                {{ $payment->patient->phone ?? '-' }}
                            </td>
                            <td class="px-3 py-4 sm:px-6">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if(isset($payment->is_pending_payment) && $payment->is_pending_payment) bg-yellow-100 text-yellow-800
                                    @elseif(isset($payment->is_completed_payment) && $payment->is_completed_payment) bg-green-100 text-green-800
                                    @elseif($payment->payment_method === 'nakit') bg-green-100 text-green-800
                                    @elseif($payment->payment_method === 'kredi_karti') bg-blue-100 text-blue-800
                                    @elseif($payment->payment_method === 'banka_havalesi') bg-purple-100 text-purple-800
                                    @elseif($payment->payment_method === 'pos') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    @if(isset($payment->is_pending_payment) && $payment->is_pending_payment)
                                        Bekleyen Ödeme
                                    @elseif(isset($payment->is_completed_payment) && $payment->is_completed_payment)
                                        Tamamlanan Ödeme
                                    @else
                                        {{ $this->getPaymentMethodDisplayName($payment->payment_method) }}
                                    @endif
                                </span>
                            </td>
                            <td class="px-3 py-4 sm:px-6 text-sm font-medium text-gray-900">
                                @if(isset($payment->is_pending_payment) && $payment->is_pending_payment)
                                    <span class="text-yellow-600 font-bold">{{ $this->formatCurrency($payment->paid_amount) }}</span>
                                @elseif(isset($payment->is_completed_payment) && $payment->is_completed_payment)
                                    <span class="text-green-600 font-bold">{{ $this->formatCurrency($payment->paid_amount) }}</span>
                                @else
                                    {{ $this->formatCurrency($payment->paid_amount) }}
                                @endif
                            </td>
                            <td class="px-3 py-4 sm:px-6 text-sm text-gray-500">
                                {{ $payment->created_at->format('d.m.Y H:i') }}
                            </td>
                            @if(auth()->user()->role === 'admin')
                            <td class="px-3 py-4 sm:px-6 text-sm text-gray-900">
                                @if(isset($payment->is_pending_payment) || isset($payment->is_completed_payment))
                                    <span class="text-gray-400">-</span>
                                @else
                                    {{ $payment->user->name ?? 'Bilinmiyor' }}
                                @endif
                            </td>
                            @endif
                            <td class="px-3 py-4 sm:px-6 text-sm text-gray-900 max-w-xs truncate">
                                {{ $payment->notes ?: '-' }}
                            </td>
                            <td class="px-3 py-4 sm:px-6 text-sm font-medium">
                                @if(isset($payment->is_pending_payment) && $payment->is_pending_payment)
                                    <!-- Bekleyen ödeme için dropdown açma butonu -->
                                    <button wire:click="togglePaymentExpansion('{{ $payment->id }}')" 
                                            class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                        Detaylar
                                    </button>
                                @elseif(isset($payment->is_completed_payment) && $payment->is_completed_payment)
                                    <!-- Tamamlanan ödeme için mesaj -->
                                    <span class="inline-flex items-center px-2.5 py-1.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Ödeme Tamamlandı
                                    </span>
                                @else
                                    <!-- Normal ödemeler için silme butonu -->
                                    <button wire:click="confirmDeletePayment({{ $payment->id }})" 
                                            class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Sil
                                    </button>
                                @endif
                            </td>
                        </tr>
                        
                        <!-- Dropdown Hasta Borç Bilgisi ve Ödeme Geçmişi -->
                        @if($payment->patient && $this->isPaymentExpanded($payment->id) && (isset($payment->is_pending_payment) || isset($payment->is_completed_payment)))
                            <tr class="bg-gray-50">
                                <td colspan="{{ auth()->user()->role === 'admin' ? '8' : '7' }}" class="px-3 py-4 sm:px-6">
                                    <div class="bg-white rounded-lg border border-gray-200 p-4">
                                        <div class="flex justify-between items-center mb-3">
                                            <h4 class="text-sm font-medium text-gray-900">{{ trim($payment->patient->first_name . ' ' . $payment->patient->last_name) }} - Detaylı Bilgiler</h4>
                                            <button wire:click="closeDropdown('{{ $payment->id }}')"
                                                    class="text-gray-400 hover:text-gray-600 p-1 hover:bg-gray-100 rounded transition-colors duration-200"
                                                    title="Kapat">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        </div>
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                            <!-- Kalan Borç Kartı -->
                                            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0">
                                                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                                        </svg>
                                                    </div>
                                                    <div class="ml-4">
                                                        <h5 class="text-sm font-medium text-red-900">Kalan Borç</h5>
                                                        @php
                                                            $totalPaid = \App\Models\Payment::where('patient_id', $payment->patient->id)->sum('paid_amount');
                                                            $remainingDebt = max(0, $payment->patient->needs_paid - $totalPaid);
                                                        @endphp
                                                        <p class="text-2xl font-bold text-red-600">{{ $this->formatCurrency($remainingDebt) }}</p>
                                                        <p class="text-xs text-gray-500 mt-1">Toplam Borç: {{ $this->formatCurrency($payment->patient->needs_paid) }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Hasta Bilgileri Kartı -->
                                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0">
                                                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                        </svg>
                                                    </div>
                                                    <div class="ml-4">
                                                        <h5 class="text-sm font-medium text-blue-900">Hasta Bilgileri</h5>
                                                        <p class="text-sm text-blue-700">TC: {{ $payment->patient->tc_identity ?? 'Belirtilmemiş' }}</p>
                                                        <p class="text-sm text-blue-700">Tel: {{ $payment->patient->phone ?? 'Belirtilmemiş' }}</p>
                                                        @if($payment->patient->birth_date)
                                                            <p class="text-sm text-blue-700">Yaş: {{ $this->calculateAge($payment->patient->birth_date) }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Ödeme Geçmişi -->
                                        <div class="mb-4">
                                            <h5 class="text-sm font-medium text-gray-900 mb-3">Ödeme Geçmişi</h5>
                                            @php
                                                $patientPayments = $this->getPatientPaymentHistory($payment->patient->id);
                                            @endphp
                                            
                                            @if($patientPayments->count() > 0)
                                                <div class="bg-gray-50 rounded-lg p-3">
                                                    <div class="space-y-2">
                                                        @foreach($patientPayments as $patientPayment)
                                                            <div class="flex justify-between items-center py-2 px-3 bg-white rounded border">
                                                                <div class="flex items-center space-x-3">
                                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                                                        @if($patientPayment->payment_method === 'nakit') bg-green-100 text-green-800
                                                                        @elseif($patientPayment->payment_method === 'kredi_karti') bg-blue-100 text-blue-800
                                                                        @elseif($patientPayment->payment_method === 'banka_havalesi') bg-purple-100 text-purple-800
                                                                        @elseif($patientPayment->payment_method === 'pos') bg-yellow-100 text-yellow-800
                                                                        @else bg-gray-100 text-gray-800 @endif">
                                                                        {{ $this->getPaymentMethodDisplayName($patientPayment->payment_method) }}
                                                                    </span>
                                                                    <span class="text-sm text-gray-600">{{ $patientPayment->created_at->format('d.m.Y H:i') }}</span>
                                                                    @if($patientPayment->user)
                                                                        <span class="text-xs text-gray-500">{{ $patientPayment->user->name }}</span>
                                                                    @endif
                                                                </div>
                                                                <div class="flex items-center space-x-2">
                                                    <div class="text-right">
                                                        <span class="text-sm font-medium text-gray-900">{{ $this->formatCurrency($patientPayment->paid_amount) }}</span>
                                                        @if($patientPayment->notes)
                                                            <p class="text-xs text-gray-500 mt-1">{{ $patientPayment->notes }}</p>
                                                        @endif
                                                    </div>
                                                    
                                                    <!-- Silme ve Düzenleme Butonları -->
                                                    <div class="flex space-x-1">
                                                        @if($this->canDeletePayment($patientPayment))
                                                            <button type="button" 
                                                                    wire:click="$set('paymentToDelete', '{{ $patientPayment->id }}')"
                                                                    class="text-red-500 hover:text-red-700 p-1 hover:bg-red-100 rounded transition-colors duration-200"
                                                                    title="Ödemeyi Sil">
                                                                <i class="fas fa-trash text-xs"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @else
                                                <div class="bg-gray-50 rounded-lg p-4 text-center">
                                                    <svg class="w-8 h-8 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                    </svg>
                                                    <p class="text-sm text-gray-500">Henüz ödeme geçmişi bulunmuyor.</p>
                                                </div>
                                            @endif
                                        </div>
                                        
                                        @if($payment->patient->needs_paid > 0)
                                            <!-- Hızlı Ödeme Butonları -->
                                            <div class="mb-4">
                                                <h5 class="text-sm font-medium text-gray-900 mb-2">Hızlı Ödeme Tamamla</h5>
                                                <div class="grid grid-cols-2 md:grid-cols-5 gap-2">
                                                    <button wire:click="quickPayment({{ $payment->patient->id }}, 'nakit')" 
                                                            class="inline-flex items-center justify-center px-3 py-2 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 transition-colors duration-200">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                                        </svg>
                                                        Nakit
                                                    </button>
                                                    <button wire:click="quickPayment({{ $payment->patient->id }}, 'kredi_karti')" 
                                                            class="inline-flex items-center justify-center px-3 py-2 border border-transparent text-xs font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 transition-colors duration-200">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                                        </svg>
                                                        Kredi Kartı
                                                    </button>
                                                    <button wire:click="quickPayment({{ $payment->patient->id }}, 'banka_havalesi')" 
                                                            class="inline-flex items-center justify-center px-3 py-2 border border-transparent text-xs font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 transition-colors duration-200">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path>
                                                        </svg>
                                                        Havale
                                                    </button>
                                                    <button wire:click="quickPayment({{ $payment->patient->id }}, 'pos')" 
                                                            class="inline-flex items-center justify-center px-3 py-2 border border-transparent text-xs font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700 transition-colors duration-200">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                                        </svg>
                                                        POS
                                                    </button>
                                                    <button wire:click="quickPayment({{ $payment->patient->id }}, 'diger')" 
                                                            class="inline-flex items-center justify-center px-3 py-2 border border-transparent text-xs font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700 transition-colors duration-200">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                        Diğer
                                                    </button>
                                                </div>
                                            </div>
                                            
                                            <!-- Mevcut Butonlar -->
                                            <div class="flex space-x-2">
                                                <button wire:click="openPaymentsModal({{ $payment->patient->id }})" 
                                                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                    </svg>
                                                    Ödeme Ekle
                                                </button>
                                                <button wire:click="openDebtModal({{ $payment->patient->id }})" 
                                                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                    Borç Düzenle
                                                </button>
                                            </div>
                                        @else
                                            <div class="p-3 bg-green-50 border border-green-200 rounded-md">
                                                <div class="flex items-center">
                                                    <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                    <span class="text-sm font-medium text-green-800">Bu hastanın borcu bulunmuyor.</span>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->role === 'admin' ? '8' : '7' }}" class="px-6 py-4 text-center text-gray-500">
                                @if($search || $dateFilter !== 'all' || $paymentMethodFilter !== 'all' || $userFilter !== 'all')
                                    Filtrelere uygun ödeme bulunamadı.
                                @else
                                    Henüz ödeme kaydı bulunmuyor.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($payments->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $payments->links() }}
            </div>
        @endif
    </div>

    <!-- Detaylı İstatistikler Modal -->
    @if($showStatsModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="closeStatsModal">
            <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white" wire:click.stop>
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Detaylı İstatistikler</h3>
                        <button wire:click="closeStatsModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Ödeme Yöntemlerine Göre Dağılım -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-semibold text-gray-900 mb-3">Ödeme Yöntemleri</h4>
                            @forelse($paymentsByMethod as $method => $data)
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm text-gray-600">{{ $this->getPaymentMethodDisplayName($method) }}</span>
                                    <div class="text-right">
                                        <div class="text-sm font-medium">{{ $data['count'] }} adet</div>
                                        <div class="text-xs text-gray-500">{{ $this->formatCurrency($data['total']) }}</div>
                                        <div class="text-xs text-blue-600">%{{ number_format($data['percentage'], 1) }}</div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500">Veri bulunamadı</p>
                            @endforelse
                        </div>
                        
                        <!-- Günlük İstatistikler -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-semibold text-gray-900 mb-3">Son 7 Gün</h4>
                            @foreach($dailyStats as $day)
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm text-gray-600">{{ $day['date'] }}</span>
                                    <div class="text-right">
                                        <div class="text-sm font-medium">{{ $day['count'] }} adet</div>
                                        <div class="text-xs text-gray-500">{{ $this->formatCurrency($day['total']) }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- Aylık İstatistikler -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-semibold text-gray-900 mb-3">Son 6 Ay</h4>
                            @foreach($monthlyStats as $month)
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm text-gray-600">{{ $month['month_tr'] }}</span>
                                    <div class="text-right">
                                        <div class="text-sm font-medium">{{ $month['count'] }} adet</div>
                                        <div class="text-xs text-gray-500">{{ $this->formatCurrency($month['total']) }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Ödeme Yapmayan Hastalar Modal -->
    @if($showOverdueModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="closeOverdueModal">
            <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white" wire:click.stop>
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-red-600">Ödeme Yapmayan Hastalar ({{ $overdueSettings['days'] }} gün üzeri)</h3>
                        <button wire:click="closeOverdueModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-red-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-red-600 uppercase tracking-wider">Hasta Adı</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-red-600 uppercase tracking-wider">Telefon</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-red-600 uppercase tracking-wider">TC No</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-red-600 uppercase tracking-wider">Son Güncelleme</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-red-600 uppercase tracking-wider">Geçen Süre</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($selectedOverduePatients as $patient)
                                    <tr class="hover:bg-red-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-2 w-2 bg-red-400 rounded-full mr-3"></div>
                                                <div class="text-sm font-medium text-gray-900">{{ $patient->full_name }}</div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <a href="tel:{{ $patient->phone }}" class="text-blue-600 hover:text-blue-800">
                                                {{ $patient->phone }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $patient->tc_no ?: '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @php
                                                // En son ödeme tarihini kontrol et
                                                $lastPayment = $patient->payments->first();
                                                $referenceDate = $lastPayment ? $lastPayment->created_at : $patient->updated_at;
                                            @endphp
                                            {{ $referenceDate->format('d.m.Y H:i') }}
                                            @if($lastPayment)
                                                <span class="text-xs text-blue-600">(Son Ödeme)</span>
                                            @else
                                                <span class="text-xs text-gray-400">(Hasta Kaydı)</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 font-medium">
                                            @php
                                                $diffInMinutes = $referenceDate->diffInMinutes(now());
                                                if ($diffInMinutes < 60) {
                                                    echo $diffInMinutes . ' dakika';
                                                } elseif ($diffInMinutes < 1440) {
                                                    echo floor($diffInMinutes / 60) . ' saat ' . ($diffInMinutes % 60) . ' dakika';
                                                } else {
                                                    echo floor($diffInMinutes / 1440) . ' gün';
                                                }
                                            @endphp
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                            Ödeme yapmayan hasta bulunamadı.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Payments Modal -->
    @if($showPaymentsModal)
        <div class="fixed inset-0 bg-black bg-opacity-60 h-full w-full z-50 flex items-center justify-center p-4" wire:click="closePaymentsModal">
            <div class="relative w-full max-w-4xl max-h-[90vh] bg-white rounded-2xl shadow-2xl flex flex-col" wire:click.stop>
                <!-- Modal Header -->
                <div class="bg-gradient-to-r from-green-600 to-green-700 px-8 py-6 rounded-t-2xl flex-shrink-0">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="bg-white bg-opacity-20 p-3 rounded-full">
                                <i class="fas fa-money-bill-wave text-white text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold text-white">{{ $selectedPatientForPayments->first_name ?? '' }} {{ $selectedPatientForPayments->last_name ?? '' }} - Ödemeler</h3>
                                <div class="flex items-center space-x-4 text-sm text-green-100">
                                    @if($selectedPatientForPayments && $selectedPatientForPayments->birth_date)
                                        <span class="flex items-center"><i class="fas fa-birthday-cake mr-2"></i>{{ $this->calculateAge($selectedPatientForPayments->birth_date) }} yaş</span>
                                    @endif
                                    <span class="flex items-center"><i class="fas fa-phone mr-2"></i>{{ $selectedPatientForPayments->phone ?? '' }}</span>
                                </div>
                            </div>
                        </div>
                        <button wire:click="closePaymentsModal" class="text-white hover:text-green-200 transition-colors duration-200 p-2 rounded-full hover:bg-white hover:bg-opacity-10">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Modal Content -->
                <div class="flex-1 overflow-hidden flex flex-col p-6">
                    <!-- Ödeme Özeti -->
                    @if($selectedPatientForPayments && $selectedPatientForPayments->needs_paid > 0)
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6 sticky top-0 z-10">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div class="text-center">
                                <div class="text-blue-600 font-semibold">Alınacak Ücret</div>
                                <div class="text-lg font-bold text-blue-800">₺{{ number_format($selectedPatientForPayments->needs_paid, 2) }}</div>
                            </div>
                            <div class="text-center">
                                <div class="text-green-600 font-semibold">Alınan Toplam</div>
                                <div class="text-lg font-bold text-green-800">₺{{ number_format($this->totalPaid, 2) }}</div>
                            </div>
                            <div class="text-center">
                                <div class="text-red-600 font-semibold">Kalan Tutar</div>
                                <div class="text-lg font-bold text-red-800">₺{{ number_format($this->remainingAmount, 2) }}</div>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <!-- Yeni Ödeme Ekleme -->
                    <div class="bg-gray-50 rounded-lg p-4 mb-6">
                        <h5 class="text-md font-semibold text-gray-700 mb-3">Yeni Ödeme Ekle</h5>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Ödeme Yöntemi</label>
                                <select wire:model="newPayment.payment_method" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                    <option value="nakit">Nakit</option>
                                    <option value="kredi_karti">Kredi Kartı</option>
                                    <option value="banka_havalesi">Banka Havalesi</option>
                                    <option value="pos">POS</option>
                                    <option value="diger">Diğer</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tutar (₺)</label>
                                <input type="number" 
                                       step="0.01"
                                       min="0.01"
                                       wire:model="newPayment.paid_amount" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                       placeholder="0.00">
                                @error('newPayment.paid_amount') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Not</label>
                                <input type="text" 
                                       wire:model="newPayment.notes" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                       placeholder="Ödeme notu">
                            </div>
                            <div class="flex items-end">
                                <button type="button" 
                                        wire:click="addPayment"
                                        class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 transition-all duration-200">
                                    <i class="fas fa-plus mr-2"></i>
                                    Ekle
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Ödemeler Listesi -->
                    <div class="flex-1 overflow-y-auto">
                        <h5 class="text-md font-semibold text-gray-700 mb-3">Yapılan Ödemeler</h5>
                        @if(count($patientPayments) > 0)
                            <div class="space-y-3">
                                @foreach($patientPayments as $payment)
                                <div class="flex items-center justify-between bg-white border border-gray-200 p-4 rounded-lg shadow-sm">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-4">
                                            <span class="font-semibold text-green-600 text-lg">₺{{ number_format($payment['paid_amount'], 2) }}</span>
                                            <span class="text-sm text-gray-600 bg-gray-100 px-2 py-1 rounded">
                                                @switch($payment['payment_method'])
                                                    @case('nakit') Nakit @break
                                                    @case('kredi_karti') Kredi Kartı @break
                                                    @case('banka_havalesi') Banka Havalesi @break
                                                    @case('pos') POS @break
                                                    @case('diger') Diğer @break
                                                @endswitch
                                            </span>
                                            @if($payment['notes'])
                                                <span class="text-sm text-gray-500">- {{ $payment['notes'] }}</span>
                                            @endif
                                        </div>
                                        <div class="text-xs text-gray-400 mt-1">
                                              <i class="fas fa-clock mr-1"></i>{{ \Carbon\Carbon::parse($payment['created_at'])->format('d.m.Y H:i') }}
                                              @if(isset($payment['user_name']))
                                                  <span class="ml-2"><i class="fas fa-user mr-1"></i>{{ $payment['user_name'] }}</span>
                                              @endif
                                          </div>
                                    </div>
                                    @if($this->canDeletePayment((object)$payment))
                                    <button type="button" 
                                            wire:click="$set('paymentToDelete', '{{ $payment['id'] }}')"
                                            class="text-red-500 hover:text-red-700 p-2 hover:bg-red-100 rounded-full transition-colors duration-200">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <div class="text-gray-500">
                                    <i class="fas fa-money-bill-wave text-4xl mb-4"></i>
                                    <p class="text-lg font-medium">Henüz ödeme kaydı bulunmuyor</p>
                                    <p class="text-sm">Yukarıdaki formu kullanarak ödeme ekleyebilirsiniz.</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Ödeme Silme Onay Modalı -->
    @if($paymentToDelete)
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center" wire:click="cancelDeletePayment">
        <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full mx-4" wire:click.stop>
            <!-- Modal Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-exclamation-triangle text-red-600"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">Ödeme Silme Onayı</h3>
                    </div>
                </div>
                <button wire:click="cancelDeletePayment" class="text-gray-400 hover:text-gray-600 transition-colors duration-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <!-- Modal Body -->
            <div class="p-6">
                <p class="text-sm text-gray-500">
                    Bu ödeme kaydını silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.
                </p>
            </div>
            
            <!-- Modal Footer -->
            <div class="flex items-center justify-end p-6 border-t border-gray-200 space-x-3">
                <button 
                    wire:click="cancelDeletePayment"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                    <i class="fas fa-times mr-2"></i>Hayır
                </button>
                <button 
                    wire:click="confirmDeletePayment"
                    class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200">
                    <i class="fas fa-trash mr-2"></i>Evet, Sil
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- Borç Düzenleme Modal -->
    @if($showDebtModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="closeDebtModal">
            <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-lg shadow-lg rounded-md bg-white" wire:click.stop>
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Borç Düzenle - {{ $selectedPatientForDebt->name ?? '' }}</h3>
                        <button wire:click="closeDebtModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <form wire:submit.prevent="updateDebt">
                        <div class="mb-4">
                            <label for="debt_amount" class="block text-sm font-medium text-gray-700 mb-2">Toplam Borç Tutarı</label>
                            <input type="number" step="0.01" wire:model="newDebtAmount" id="debt_amount" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                   placeholder="0.00" required>
                            @error('newDebtAmount') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="flex justify-end space-x-3">
                            <button type="button" wire:click="closeDebtModal" 
                                    class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                İptal
                            </button>
                            <button type="submit" 
                                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Güncelle
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Ödeme Silme Onay Modal -->
    @if($showDeleteModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="closeDeleteModal">
            <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-md shadow-lg rounded-md bg-white" wire:click.stop>
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Ödeme Sil</h3>
                        <button wire:click="closeDeleteModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <div class="mb-4">
                        <p class="text-sm text-gray-600">Bu ödeme kaydını silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.</p>
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <button wire:click="closeDeleteModal" 
                                class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            İptal
                        </button>
                        <button wire:click="deletePayment" 
                                class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            Sil
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
            {{ session('error') }}
        </div>
    @endif
</div>

<!-- JavaScript for localStorage integration -->
<script>
    document.addEventListener('livewire:init', () => {
        // localStorage'dan ayarları yükle
        const savedSettings = localStorage.getItem('overdueSettings');
        if (savedSettings) {
            const settings = JSON.parse(savedSettings);
            @this.overdueSettings = settings;
            @this.overdueDays = settings.days;
        }
        
        // Ayarları localStorage'a kaydet
        Livewire.on('save-overdue-settings', (settings) => {
            localStorage.setItem('overdueSettings', JSON.stringify(settings[0]));
        });
        
        // Flash mesajları otomatik gizle
        setTimeout(() => {
            const flashMessages = document.querySelectorAll('.fixed.top-4.right-4');
            flashMessages.forEach(msg => {
                msg.style.transition = 'opacity 0.5s';
                msg.style.opacity = '0';
                setTimeout(() => msg.remove(), 500);
            });
        }, 3000);
    });
</script>