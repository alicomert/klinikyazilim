<div>
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-sm p-6 card-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-gray-500 text-sm font-medium">Toplam Hasta</div>
                    <div class="text-3xl font-bold text-blue-600 mt-2">{{ number_format($stats['total_patients']) }}</div>
                    <div class="text-green-500 text-sm mt-1">
                        <i class="fas fa-users"></i> Kayıtlı hasta
                    </div>
                </div>
                <div class="bg-blue-100 p-4 rounded-full">
                    <i class="fas fa-users text-blue-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 card-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-gray-500 text-sm font-medium">Yeni Hastalar (Bu Ay)</div>
                    <div class="text-3xl font-bold text-green-600 mt-2">{{ $stats['new_patients_this_month'] }}</div>
                    <div class="text-green-500 text-sm mt-1">
                        <i class="fas fa-arrow-up"></i> Bu ay eklenen
                    </div>
                </div>
                <div class="bg-green-100 p-4 rounded-full">
                    <i class="fas fa-user-plus text-green-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 card-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-gray-500 text-sm font-medium">Aktif Tedaviler</div>
                    <div class="text-3xl font-bold text-purple-600 mt-2">{{ $stats['active_treatments'] }}</div>
                    <div class="text-purple-500 text-sm mt-1">
                        <i class="fas fa-procedures"></i> Devam eden
                    </div>
                </div>
                <div class="bg-purple-100 p-4 rounded-full">
                    <i class="fas fa-procedures text-purple-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 card-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-gray-500 text-sm font-medium">Bugünkü Randevular</div>
                    <div class="text-3xl font-bold text-yellow-600 mt-2">{{ $stats['today_appointments'] }}</div>
                    <div class="text-gray-500 text-sm mt-1">
                        <i class="fas fa-calendar-day"></i> Randevu sistemi yakında
                    </div>
                </div>
                <div class="bg-yellow-100 p-4 rounded-full">
                    <i class="fas fa-calendar-day text-yellow-600 text-2xl"></i>
                </div>
        </div>
    </div>
</div>

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
                            <h3 class="text-2xl font-bold text-white">{{ $selectedPatientForPayments->first_name }} {{ $selectedPatientForPayments->last_name }} - Ödemeler</h3>
                            <div class="flex items-center space-x-4 text-sm text-green-100">
                                <span class="flex items-center"><i class="fas fa-birthday-cake mr-2"></i>{{ $this->calculateAge($selectedPatientForPayments->birth_date) }} yaş</span>
                                <span class="flex items-center"><i class="fas fa-phone mr-2"></i>{{ $selectedPatientForPayments->phone }}</span>
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
                @if($selectedPatientForPayments->needs_paid > 0)
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-6 mb-6 shadow-sm sticky top-0 z-10">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-calculator mr-2 text-blue-600"></i>
                        Ödeme Özeti
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="text-center bg-white rounded-lg p-4 shadow-sm">
                            <div class="text-blue-600 font-semibold text-sm mb-1">Toplam Tedavi Ücreti</div>
                            <div class="text-2xl font-bold text-blue-800">₺{{ number_format($selectedPatientForPayments->needs_paid, 2) }}</div>
                            <div class="text-xs text-gray-500 mt-1">Alınması gereken tutar</div>
                        </div>
                        <div class="text-center bg-white rounded-lg p-4 shadow-sm">
                            <div class="text-green-600 font-semibold text-sm mb-1">Ödenen Tutar</div>
                            <div class="text-2xl font-bold text-green-800">₺{{ number_format($this->totalPaid, 2) }}</div>
                            <div class="text-xs text-gray-500 mt-1">Şu ana kadar alınan</div>
                        </div>
                        <div class="text-center bg-white rounded-lg p-4 shadow-sm">
                            @if($this->remainingAmount > 0)
                                <div class="text-red-600 font-semibold text-sm mb-1">Kalan Borç</div>
                                <div class="text-2xl font-bold text-red-800">₺{{ number_format($this->remainingAmount, 2) }}</div>
                                <div class="text-xs text-red-500 mt-1">Ödenmesi gereken</div>
                            @elseif($this->totalPaid > $selectedPatientForPayments->needs_paid)
                                <div class="text-purple-600 font-semibold text-sm mb-1">Fazla Ödeme</div>
                                <div class="text-2xl font-bold text-purple-800">₺{{ number_format($this->totalPaid - $selectedPatientForPayments->needs_paid, 2) }}</div>
                                <div class="text-xs text-purple-500 mt-1">İade edilebilir</div>
                            @else
                                <div class="text-green-600 font-semibold text-sm mb-1">Ödeme Durumu</div>
                                <div class="text-2xl font-bold text-green-800">✓ Tamamlandı</div>
                                <div class="text-xs text-green-500 mt-1">Borç kalmamıştır</div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Ödeme Durumu Çubuğu -->
                    @if($selectedPatientForPayments->needs_paid > 0)
                    <div class="mt-4">
                        <div class="flex justify-between text-sm text-gray-600 mb-2">
                            <span>Ödeme İlerlemesi</span>
                            <span>{{ number_format(($this->totalPaid / $selectedPatientForPayments->needs_paid) * 100, 1) }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            @php
                                $progressPercentage = min(100, ($this->totalPaid / $selectedPatientForPayments->needs_paid) * 100);
                            @endphp
                            <div class="bg-gradient-to-r from-green-400 to-green-600 h-3 rounded-full transition-all duration-300" 
                                 style="width: {{ $progressPercentage }}%"></div>
                        </div>
                    </div>
                    @endif
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
                                <option value="cek">Çek</option>
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
                                                @case('cek') Çek @break
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

    <!-- Search and Filter Section -->
    <div class="bg-white rounded-lg shadow-sm p-4 sm:p-6 mb-6 card-shadow">
        <div class="space-y-4">
            <!-- Search Bar -->
            <div class="w-full">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Hasta adı, TC kimlik, telefon ile ara..." 
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                </div>
            </div>
            
            <!-- Filters and Add Button -->
            <div class="flex flex-col sm:flex-row gap-3">
                <!-- Filters -->
                <div class="flex flex-col sm:flex-row gap-3 flex-1">
                    <select wire:model.live="statusFilter" class="border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <option value="all">Tüm Durumlar</option>
                        <option value="active">Aktif Tedavi</option>
                        <option value="completed">Tedavi Tamamlandı</option>
                        <option value="waiting">Beklemede</option>
                    </select>
                </div>
                
                <!-- Add Button -->
                <button wire:click="openModal" 
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-50 cursor-not-allowed"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 flex items-center justify-center w-full sm:w-auto">
                    <i class="fas fa-plus mr-2" wire:loading.remove wire:target="openModal"></i>
                    <i class="fas fa-spinner fa-spin mr-2" wire:loading wire:target="openModal"></i>
                    <span wire:loading.remove wire:target="openModal">Yeni Hasta Ekle</span>
                    <span wire:loading wire:target="openModal">Yükleniyor...</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Patients Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden card-shadow" x-data="{ 
        columns: JSON.parse(localStorage.getItem('patientListColumns')) || {
            patientInfo: true,
            contact: true,
            registrationPeriod: true,
            actions: true
        },
        saveColumns() {
            localStorage.setItem('patientListColumns', JSON.stringify(this.columns));
        }
    }">
        <div class="px-4 sm:px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h3 class="text-lg font-semibold text-gray-800">Hasta Listesi</h3>
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200 flex items-center w-full sm:w-auto justify-center">
                    <i class="fas fa-columns mr-2"></i>
                    Sütunlar
                    <i class="fas fa-chevron-down ml-2 text-xs" :class="{ 'rotate-180': open }"></i>
                </button>
                <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10 border border-gray-200">
                    <div class="py-2">
                        <label class="flex items-center px-4 py-2 hover:bg-gray-50 cursor-pointer">
                            <input type="checkbox" x-model="columns.patientInfo" @change="saveColumns()" class="mr-3 rounded">
                            <span class="text-sm text-gray-700">Hasta Bilgileri</span>
                        </label>
                        <label class="flex items-center px-4 py-2 hover:bg-gray-50 cursor-pointer">
                            <input type="checkbox" x-model="columns.contact" @change="saveColumns()" class="mr-3 rounded">
                            <span class="text-sm text-gray-700">İletişim</span>
                        </label>
                        <label class="flex items-center px-4 py-2 hover:bg-gray-50 cursor-pointer">
                            <input type="checkbox" x-model="columns.registrationDate" @change="saveColumns()" class="mr-3 rounded">
                            <span class="text-sm text-gray-700">Kayıt Tarihi</span>
                        </label>
                        <label class="flex items-center px-4 py-2 hover:bg-gray-50 cursor-pointer">
                            <input type="checkbox" x-model="columns.actions" @change="saveColumns()" class="mr-3 rounded">
                            <span class="text-sm text-gray-700">İşlemler</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Card View -->
        <div class="block sm:hidden">
            @forelse($patients as $patient)
                <div class="border-b border-gray-200 p-4 hover:bg-gray-50">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0 h-12 w-12 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-blue-600 dark:text-blue-400"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-medium text-gray-900 truncate">
                                {{ $patient->first_name }} {{ $patient->last_name }}
                            </div>
                            
                            <!-- Mobil Eksik Bilgi Badge'leri -->
                            @php
                                $missingBadges = $this->getMissingInfoBadges($patient);
                            @endphp
                            
                            @if(count($missingBadges) > 0)
                                <div class="flex flex-wrap gap-1 mt-2">
                                    @foreach($missingBadges as $badge)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium border {{ $badge['color'] }}" 
                                              title="{{ $badge['tooltip'] }}">
                                            <i class="{{ $badge['icon'] }} mr-1"></i>
                                            {{ $badge['label'] }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <!-- Tamamlandı Badge'i -->
                                <div class="mt-2">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium border bg-green-100 text-green-800 border-green-200" 
                                          title="Tüm bilgiler tamamlandı">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Tamamlandı
                                    </span>
                                </div>
                            @endif
                            
                            <div class="text-sm text-gray-500 mt-1">{{ $this->calculateAge($patient->birth_date) }} yaş</div>
                            <div class="text-xs text-gray-400">TC: {{ $this->maskTcIdentity($patient->tc_identity) }}</div>
                            <div class="text-sm text-gray-600 mt-1">{{ $this->formatPhone($patient->phone) }}</div>
                            <div class="text-xs text-gray-500">Kayıt: {{ $patient->registration_date ? $patient->registration_date->format('d.m.Y H:i') : now()->format('d.m.Y H:i') }}</div>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-2 mt-3">
                        <button wire:click="showPatientDetails({{ $patient->id }})" 
                                class="text-blue-600 hover:text-blue-800 p-2 hover:bg-blue-100 rounded-full transition-colors duration-200" 
                                title="Detayları Görüntüle">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button wire:click="showNotes({{ $patient->id }})" 
                                class="text-purple-600 hover:text-purple-800 p-2 hover:bg-purple-100 rounded-full transition-colors duration-200" 
                                title="Notlar">
                            <i class="fas fa-sticky-note"></i>
                        </button>
                        <button wire:click="showPayments({{ $patient->id }})" 
                                class="text-green-600 hover:text-green-800 p-2 hover:bg-green-100 rounded-full transition-colors duration-200" 
                                title="Ödemeler">
                            <i class="fas fa-money-bill-wave"></i>
                        </button>
                        <button wire:click="editPatient({{ $patient->id }})" 
                                wire:loading.attr="disabled"
                                wire:loading.class="opacity-50 cursor-not-allowed"
                                class="text-yellow-600 hover:text-yellow-800 p-2 hover:bg-yellow-100 rounded-full transition-colors duration-200" 
                                title="Düzenle">
                            <i class="fas fa-edit" wire:loading.remove wire:target="editPatient"></i>
                            <i class="fas fa-spinner fa-spin" wire:loading wire:target="editPatient"></i>
                        </button>
                        <button @click="$dispatch('confirm-delete', { patientId: {{ $patient->id }}, patientName: '{{ $patient->first_name }} {{ $patient->last_name }}' })" 
                                class="text-red-600 hover:text-red-800 p-2 hover:bg-red-100 rounded-full transition-colors duration-200" 
                                title="Sil">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            @empty
                <div class="px-4 py-12 text-center">
                    <div class="text-gray-500">
                        <i class="fas fa-users text-4xl mb-4"></i>
                        <p class="text-lg font-medium">Henüz hasta kaydı bulunmuyor</p>
                        <p class="text-sm">İlk hastanızı eklemek için yukarıdaki "Yeni Hasta Ekle" butonunu kullanın.</p>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Desktop Table View -->
        <div class="hidden sm:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th x-show="columns.patientInfo" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hasta Bilgileri</th>
                        <th x-show="columns.contact" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">İletişim</th>
                        <th x-show="columns.registrationDate" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kayıt Tarihi</th>
                        <th x-show="columns.actions" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($patients as $patient)
                        <tr class="hover:bg-gray-50">
                            <td x-show="columns.patientInfo" class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-12 w-12 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                        <i class="fas fa-user text-blue-600 dark:text-blue-400"></i>
                                    </div>
                                    <div class="ml-4">
                                        <div class="flex items-center space-x-2">
                                            <div class="text-sm font-medium text-gray-900" title="{{ $patient->first_name }} {{ $patient->last_name }}">
                                                {{ Str::limit($patient->first_name . ' ' . $patient->last_name, 25) }}
                                            </div>
                                            
                                            <!-- Eksik Bilgi Badge'leri -->
                                            @php
                                                $missingBadges = $this->getMissingInfoBadges($patient);
                                                $infoStatus = $this->getPatientInfoStatus($patient);
                                            @endphp
                                            
                                            @if(count($missingBadges) > 0)
                                                <div class="flex items-center space-x-1">
                                                    @foreach($missingBadges as $badge)
                                                        <div class="relative group">
                                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium border {{ $badge['color'] }}" 
                                                                  title="{{ $badge['tooltip'] }}">
                                                                <i class="{{ $badge['icon'] }} mr-1"></i>
                                                                {{ $badge['label'] }}
                                                            </span>
                                                            <!-- Tooltip -->
                                                            <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-800 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none whitespace-nowrap z-10">
                                                                {{ $badge['tooltip'] }}
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <!-- Tamamlandı Badge'i -->
                                                <div class="relative group">
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium border bg-green-100 text-green-800 border-green-200" 
                                                          title="Tüm bilgiler tamamlandı">
                                                        <i class="fas fa-check-circle mr-1"></i>
                                                        Tamamlandı
                                                    </span>
                                                    <!-- Tooltip -->
                                                    <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-800 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none whitespace-nowrap z-10">
                                                        Anamnez, fiziki muayene ve operasyon bilgileri tamamlandı
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="text-sm text-gray-500">{{ $this->calculateAge($patient->birth_date) }} yaş</div>
                                        <div class="text-xs text-gray-400">TC: {{ $this->maskTcIdentity($patient->tc_identity) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td x-show="columns.contact" class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900" title="{{ $this->formatPhone($patient->phone) }}">
                                    {{ Str::limit($this->formatPhone($patient->phone), 15) }}
                                </div>
                            </td>
                            <td x-show="columns.registrationDate" class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $patient->registration_date ? $patient->registration_date->format('d.m.Y H:i') : now()->format('d.m.Y H:i') }}</div>
                            </td>
                            <td x-show="columns.actions" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <button wire:click="showPatientDetails({{ $patient->id }})" 
                                        class="text-blue-600 hover:text-blue-800 mr-3 p-2 hover:bg-blue-100 rounded-full transition-colors duration-200" 
                                        title="Detayları Görüntüle">
                                    <i class="fas fa-eye text-lg"></i>
                                </button>
                                <button wire:click="showNotes({{ $patient->id }})" 
                                        class="text-purple-600 hover:text-purple-800 mr-3 p-2 hover:bg-purple-100 rounded-full transition-colors duration-200" 
                                        title="Notlar">
                                    <i class="fas fa-sticky-note text-lg"></i>
                                </button>
                                <button wire:click="showPayments({{ $patient->id }})" 
                                        class="text-green-600 hover:text-green-800 mr-3 p-2 hover:bg-green-100 rounded-full transition-colors duration-200" 
                                        title="Ödemeler">
                                    <i class="fas fa-money-bill-wave text-lg"></i>
                                </button>
                                <button wire:click="editPatient({{ $patient->id }})" 
                                        wire:loading.attr="disabled"
                                        wire:loading.class="opacity-50 cursor-not-allowed"
                                        class="text-yellow-600 hover:text-yellow-800 mr-3 p-2 hover:bg-yellow-100 rounded-full transition-colors duration-200" 
                                        title="Düzenle">
                                    <i class="fas fa-edit text-lg" wire:loading.remove wire:target="editPatient"></i>
                                    <i class="fas fa-spinner fa-spin text-lg" wire:loading wire:target="editPatient"></i>
                                </button>
                                <button @click="$dispatch('confirm-delete', { patientId: {{ $patient->id }}, patientName: '{{ $patient->first_name }} {{ $patient->last_name }}' })" 
                                        class="text-red-600 hover:text-red-800 p-2 hover:bg-red-100 rounded-full transition-colors duration-200" 
                                        title="Sil">
                                    <i class="fas fa-trash text-lg"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center">
                                <div class="text-gray-500">
                                    <i class="fas fa-users text-4xl mb-4"></i>
                                    <p class="text-lg font-medium">Henüz hasta kaydı bulunmuyor</p>
                                    <p class="text-sm">İlk hastanızı eklemek için yukarıdaki "Yeni Hasta Ekle" butonunu kullanın.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($patients->hasPages())
            <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                <div class="flex-1 flex justify-between sm:hidden">
                    {{ $patients->previousPageUrl() ? 
                        '<a href="' . $patients->previousPageUrl() . '" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Önceki</a>' : 
                        '<span class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-400 bg-gray-100 cursor-not-allowed">Önceki</span>' 
                    }}
                    {{ $patients->nextPageUrl() ? 
                        '<a href="' . $patients->nextPageUrl() . '" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Sonraki</a>' : 
                        '<span class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-400 bg-gray-100 cursor-not-allowed">Sonraki</span>' 
                    }}
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            <span class="font-medium">{{ $patients->firstItem() }}</span> - <span class="font-medium">{{ $patients->lastItem() }}</span> arası, toplam <span class="font-medium">{{ $patients->total() }}</span> hasta
                        </p>
                    </div>
                    <div>
                        {{ $patients->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Notes Modal -->
    @if($showNotesModal)
        <div class="fixed inset-0 bg-black bg-opacity-60 h-full w-full z-50 flex items-center justify-center p-4" wire:click="closeNotesModal">
            <div class="relative w-full max-w-6xl max-h-[90vh] bg-white rounded-2xl shadow-2xl flex flex-col" wire:click.stop>
                <!-- Modal Header -->
                <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-8 py-6 rounded-t-2xl flex-shrink-0">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="bg-white bg-opacity-20 p-3 rounded-full">
                                <i class="fas fa-sticky-note text-white text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold text-white">{{ $selectedPatientForNotes->first_name }} {{ $selectedPatientForNotes->last_name }} - Notlar</h3>
                                <div class="flex items-center space-x-4 text-sm text-purple-100">
                                    <span class="flex items-center"><i class="fas fa-birthday-cake mr-2"></i>{{ $this->calculateAge($selectedPatientForNotes->birth_date) }} yaş</span>
                                    <span class="flex items-center"><i class="fas fa-phone mr-2"></i>{{ $selectedPatientForNotes->phone }}</span>
                                </div>
                            </div>
                        </div>
                        <button wire:click="closeNotesModal" class="text-white hover:text-purple-200 transition-colors duration-200 p-2 rounded-full hover:bg-white hover:bg-opacity-10">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Modal Content -->
                <div class="flex-1 overflow-hidden flex">
                    <!-- Notes List -->
                    <div class="flex-1 p-6 overflow-y-auto">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @forelse($patientNotes as $note)
                                <div class="relative bg-gradient-to-br bg-yellow-100 text-yellow-800 border-yellow-200 p-4 rounded-lg shadow-md transform rotate-1 hover:rotate-0 transition-transform duration-200 border-l-4">
                                    <!-- Post-it Header -->
                                    <div class="flex items-start justify-between mb-3">
                                        <div class="flex items-center space-x-2">
                                            <i class="{{ $this->getNoteTypeIcon($note->note_type) }} text-sm"></i>
                                         <span class="text-xs font-medium uppercase tracking-wide">{{ $this->getNoteTypeText($note->note_type) }}</span>
                                            @if($note->is_private)
                                                <i class="fas fa-lock text-xs" title="Özel Not"></i>
                                            @endif
                                        </div>
                                        <div class="flex items-center space-x-1">
                                            @if($this->canEditNote($note))
                                                <button wire:click="editNote({{ $note->id }})" class="text-gray-600 hover:text-gray-800 p-1 rounded" title="Düzenle">
                                                    <i class="fas fa-edit text-xs"></i>
                                                </button>
                                                <button wire:click="deleteNote({{ $note->id }})" class="text-red-600 hover:text-red-800 p-1 rounded" title="Sil">
                                                    <i class="fas fa-trash text-xs"></i>
                                                </button>
                                            @else
                                                <div class="text-xs text-gray-500 bg-gray-200 px-2 py-1 rounded" title="Sadece doktor düzenleyebilir">
                                                    <i class="fas fa-lock"></i>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <!-- Note Content -->
                                    <p class="text-sm mb-3 line-clamp-4">{{ $note->content }}</p>
                                    
                                    <!-- Note Footer -->
                                    <div class="text-xs text-gray-600 space-y-1">
                                        <div class="flex items-center justify-between">
                                            <span class="flex items-center">
                                                <i class="fas fa-user mr-1"></i>
                                                {{ $note->user->name }}
                                                @if($note->user->role === 'doctor')
                                                    <span class="ml-1 text-blue-600 font-medium">(Dr.)</span>
                                                @endif
                                            </span>
                                            <span class="flex items-center">
                                                <i class="fas fa-calendar mr-1"></i>
                                                {{ $note->created_at->format('d.m.Y') }}
                                            </span>
                                        </div>
                                        @if($note->last_updated && $note->last_updated != $note->created_at)
                                            <div class="text-xs text-gray-500">
                                                <i class="fas fa-edit mr-1"></i>
                                                Güncellendi: {{ $note->last_updated->format('d.m.Y H:i') }}
                                            </div>
                                        @endif
                                    </div>
                                    
                                    @if($note->user->role === 'doctor' && !$this->canEditNote($note))
                                        <div class="absolute top-2 right-2 bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">
                                            <i class="fas fa-user-md mr-1"></i>Doktor Notu
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <div class="col-span-full text-center py-12">
                                    <i class="fas fa-sticky-note text-4xl text-gray-300 mb-4"></i>
                                    <p class="text-gray-500 text-lg">Henüz not bulunmuyor</p>
                                    <p class="text-gray-400 text-sm">İlk notu eklemek için sağdaki formu kullanın.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                    
                    <!-- Note Form -->
                    <div class="w-80 bg-gray-50 border-l border-gray-200 p-6 overflow-y-auto">
                        <h4 class="text-lg font-semibold text-gray-800 mb-4">
                            @if($editingNote)
                                <i class="fas fa-edit mr-2"></i>Not Düzenle
                            @else
                                <i class="fas fa-plus mr-2"></i>Yeni Not Ekle
                            @endif
                        </h4>
                        
                        <form wire:submit.prevent="saveNote" class="space-y-4">
                            <!-- Note Type -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Not Türü</label>
                                <select wire:model="newNote.note_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                    <option value="general">Genel</option>
                                    <option value="medical">Tıbbi</option>
                                    <option value="appointment">Randevu</option>
                                    <option value="treatment">Tedavi</option>
                                </select>
                            </div>
                            

                            

                            
                            <!-- Content -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">İçerik</label>
                                <textarea wire:model="newNote.content" rows="6" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" placeholder="Not içeriği"></textarea>
                                @error('newNote.content') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <!-- Private Note (Only for Doctors) -->
                            @if(Auth::user()->role === 'doctor')
                            <div class="flex items-center">
                                <input type="checkbox" wire:model="newNote.is_private" id="is_private" class="rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-300 focus:ring focus:ring-purple-200 focus:ring-opacity-50">
                                <label for="is_private" class="ml-2 text-sm text-gray-700">Özel not (sadece ben görebilirim)</label>
                            </div>
                            @endif
                            
                            <!-- Buttons -->
                            <div class="flex space-x-2">
                                <button type="submit" class="flex-1 bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                                    @if($editingNote)
                                        <i class="fas fa-save mr-2"></i>Güncelle
                                    @else
                                        <i class="fas fa-plus mr-2"></i>Ekle
                                    @endif
                                </button>
                                @if($editingNote)
                                    <button type="button" wire:click="resetNoteForm" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors duration-200">
                                        <i class="fas fa-times mr-2"></i>İptal
                                    </button>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Patient Details Modal -->
    @if($showDetailsModal)
        <div class="fixed inset-0 bg-black bg-opacity-60 h-full w-full z-50 flex items-center justify-center p-4" wire:click="closeDetailsModal">
            <div class="relative w-full max-w-4xl max-h-[90vh] bg-white rounded-2xl shadow-2xl flex flex-col" wire:click.stop>
                <!-- Fixed Header -->
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-8 py-6 rounded-t-2xl flex-shrink-0">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="bg-white bg-opacity-20 p-3 rounded-full">
                                <i class="fas fa-user text-white text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold text-white">{{ $selectedPatient->first_name }} {{ $selectedPatient->last_name }}</h3>
                                <div class="flex items-center space-x-4 text-sm text-blue-100">
                                    <span class="flex items-center"><i class="fas fa-birthday-cake mr-2"></i>{{ $this->calculateAge($selectedPatient->birth_date) }} yaş</span>
                                    <span class="flex items-center"><i class="fas fa-calendar-alt mr-2"></i>{{ $selectedPatient->registration_date ? $selectedPatient->registration_date->format('d.m.Y H:i') : now()->format('d.m.Y H:i') }}</span>
                                    <span class="flex items-center"><i class="fas fa-phone mr-2"></i>{{ $selectedPatient->phone }}</span>
                                </div>
                            </div>
                        </div>
                        <button wire:click="closeDetailsModal" class="text-white hover:text-blue-200 transition-colors duration-200 p-2 rounded-full hover:bg-white hover:bg-opacity-10">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Scrollable Content -->
                <div class="flex-1 overflow-y-auto">
                
                @if($selectedPatient)
                     <div class="p-8">

                        <!-- Information Cards Grid -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                            <!-- Personal Information -->
                            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow duration-200">
                                <div class="flex items-center mb-4">
                                    <div class="bg-blue-100 p-2 rounded-lg mr-3">
                                        <i class="fas fa-id-card text-blue-600"></i>
                                    </div>
                                    <h5 class="text-lg font-semibold text-gray-800">Kişisel Bilgiler</h5>
                                </div>
                                <div class="space-y-3">
                                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                        <span class="text-sm font-medium text-gray-600">TC Kimlik</span>
                                        <span class="text-sm text-gray-900 font-mono">{{ $selectedPatient->tc_identity }}</span>
                                    </div>
                                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                        <span class="text-sm font-medium text-gray-600">Doğum Tarihi</span>
                                        <span class="text-sm text-gray-900">{{ $selectedPatient->birth_date ? $selectedPatient->birth_date->format('d.m.Y') : '-' }}</span>
                                    </div>
                                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                        <span class="text-sm font-medium text-gray-600">Kayıt Tarihi</span>
                                        <span class="text-sm text-gray-900">{{ $selectedPatient->registration_date ? $selectedPatient->registration_date->format('d.m.Y H:i') : '-' }}</span>
                                    </div>
                                    <div class="flex justify-between items-center py-2">
                                        <span class="text-sm font-medium text-gray-600">Telefon</span>
                                        <span class="text-sm text-gray-900 font-mono">{{ $selectedPatient->phone }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Medical Information -->
                            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow duration-200">
                                <div class="flex items-center mb-4">
                                    <div class="bg-red-100 p-2 rounded-lg mr-3">
                                        <i class="fas fa-heartbeat text-red-600"></i>
                                    </div>
                                    <h5 class="text-lg font-semibold text-gray-800">Tıbbi Bilgiler</h5>
                                </div>
                                <div class="space-y-3">
                                    <div class="py-2 border-b border-gray-100">
                                        <span class="text-sm font-medium text-gray-600 block mb-1">İlaçlar</span>
                                        <span class="text-sm text-gray-900">{{ $selectedPatient->medications ?? 'Belirtilmemiş' }}</span>
                                    </div>
                                    <div class="py-2 border-b border-gray-100">
                                        <span class="text-sm font-medium text-gray-600 block mb-1">Alerjiler</span>
                                        <span class="text-sm text-gray-900 {{ $selectedPatient->allergies ? 'text-red-600 font-medium' : '' }}">{{ $selectedPatient->allergies ?? 'Belirtilmemiş' }}</span>
                                    </div>
                                    <div class="py-2">
                                        <span class="text-sm font-medium text-gray-600 block mb-1">Önceki Operasyonlar</span>
                                        <span class="text-sm text-gray-900">{{ $selectedPatient->previous_operations ?? 'Belirtilmemiş' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Clinical Information -->
                        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow duration-200">
                            <div class="flex items-center mb-6">
                                <div class="bg-green-100 p-2 rounded-lg mr-3">
                                    <i class="fas fa-stethoscope text-green-600"></i>
                                </div>
                                <h5 class="text-lg font-semibold text-gray-800">Klinik Bilgiler</h5>
                            </div>
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <div class="space-y-4">
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <h6 class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                            <i class="fas fa-comment-medical mr-2 text-blue-500"></i>Şikayetler
                                        </h6>
                                        <p class="text-sm text-gray-900 leading-relaxed">{{ $selectedPatient->complaints ?? 'Belirtilmemiş' }}</p>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <h6 class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                            <i class="fas fa-search mr-2 text-purple-500"></i>Fizik Muayene
                                        </h6>
                                        <p class="text-sm text-gray-900 leading-relaxed">{{ $selectedPatient->physical_examination ?? 'Belirtilmemiş' }}</p>
                                    </div>
                                </div>
                                <div class="space-y-4">
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <h6 class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                            <i class="fas fa-calendar-check mr-2 text-green-500"></i>Planlanan Operasyon
                                        </h6>
                                        <p class="text-sm text-gray-900 leading-relaxed">{{ $selectedPatient->planned_operation ?? 'Belirtilmemiş' }}</p>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <h6 class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                            <i class="fas fa-exclamation-triangle mr-2 text-orange-500"></i>Kronik Hastalıklar
                                        </h6>
                                        <p class="text-sm text-gray-900 leading-relaxed {{ $selectedPatient->chronic_conditions ? 'text-orange-600 font-medium' : '' }}">{{ $selectedPatient->chronic_conditions ?? 'Belirtilmemiş' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                         <div class="flex justify-end space-x-3 mt-8 pt-6 border-t border-gray-200">
                             <button wire:click="closeDetailsModal" class="px-6 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-colors duration-200">
                                 <i class="fas fa-times mr-2"></i>Kapat
                             </button>
                             <button wire:click="editPatient({{ $selectedPatient->id }})" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors duration-200">
                                 <i class="fas fa-edit mr-2"></i>Düzenle
                             </button>
                             <button wire:click="scheduleAppointment({{ $selectedPatient->id }})" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors duration-200">
                                 <i class="fas fa-calendar-plus mr-2"></i>Randevu Ver
                             </button>
                         </div>
                     </div>
                 @endif
                </div>
             </div>
         </div>
    @endif
    
    <!-- Delete Confirmation Modal -->
    <div x-data="{
        showDeleteModal: false,
        patientToDelete: null,
        patientName: ''
    }"
    @confirm-delete.window="
        showDeleteModal = true;
        patientToDelete = $event.detail.patientId;
        patientName = $event.detail.patientName;
    "
    x-show="showDeleteModal"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-50 overflow-y-auto"
    style="display: none;"
    >
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas fa-exclamation-triangle text-red-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                Hastayı Sil
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    <span x-text="patientName"></span> adlı hastayı silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button 
                        @click="$wire.deletePatient(patientToDelete); showDeleteModal = false;"
                        type="button" 
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors duration-200"
                    >
                        <i class="fas fa-trash mr-2"></i>
                        Evet, Sil
                    </button>
                    <button 
                        @click="showDeleteModal = false"
                        type="button" 
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors duration-200"
                    >
                        <i class="fas fa-times mr-2"></i>
                        Hayır, İptal
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Add/Edit Patient Modal -->
    @livewire('add-patient-modal')
</div>