<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
    <!-- Header with Navigation -->
    <div class="p-3 sm:p-6 border-b border-gray-100 dark:border-gray-700">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-4 space-y-3 sm:space-y-0">
            <h2 class="text-lg sm:text-xl font-bold text-gray-800 dark:text-gray-200">Randevu Takvimi</h2>
            
            <!-- View Mode Toggle -->
            <div class="flex bg-gray-100 dark:bg-gray-700 rounded-lg p-1 w-full sm:w-auto">
                <button 
                    wire:click="switchViewMode('daily')"
                    class="flex-1 sm:flex-none px-3 sm:px-4 py-2 text-xs sm:text-sm font-medium rounded-md transition-colors {{ $viewMode === 'daily' ? 'bg-white dark:bg-gray-600 text-blue-600 dark:text-blue-400 shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200' }}"
                >
                    <i class="fas fa-calendar-day mr-1 sm:mr-2"></i>Günlük
                </button>
                <button 
                    wire:click="switchViewMode('weekly')"
                    class="flex-1 sm:flex-none px-3 sm:px-4 py-2 text-xs sm:text-sm font-medium rounded-md transition-colors {{ $viewMode === 'weekly' ? 'bg-white dark:bg-gray-600 text-blue-600 dark:text-blue-400 shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200' }}"
                >
                    <i class="fas fa-calendar-week mr-1 sm:mr-2"></i>Haftalık
                </button>
            </div>
        </div>
        
        <!-- Date Navigation -->
        <div class="flex flex-col sm:flex-row items-center justify-between space-y-3 sm:space-y-0">
            <div class="flex items-center space-x-2 sm:space-x-4 w-full sm:w-auto justify-center sm:justify-start">
                <button 
                    wire:click="previousDate"
                    class="p-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
                >
                    <i class="fas fa-chevron-left"></i>
                </button>
                
                <h3 class="text-base sm:text-lg font-semibold text-gray-800 dark:text-gray-200 min-w-[150px] sm:min-w-[200px] text-center">
                    {{ $this->formattedDate }}
                </h3>
                
                <button 
                    wire:click="nextDate"
                    class="p-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
                >
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
            
            <button 
                wire:click="goToToday"
                class="px-3 sm:px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors text-xs sm:text-sm font-medium w-full sm:w-auto"
            >
                <i class="fas fa-calendar-day mr-1 sm:mr-2"></i>Bugün
            </button>
        </div>
    </div>
    
    <!-- Content Area -->
    <div class="p-3 sm:p-6">
        @if($viewMode === 'daily')
            <!-- Daily View -->
            @if($appointments->count() > 0)
                <div class="space-y-4">
                    @foreach($appointments as $appointment)
                        <div class="flex items-center p-3 sm:p-4 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-lg border border-blue-100 dark:border-blue-800 hover:shadow-md transition-shadow cursor-pointer"
                             wire:click="showAppointmentDetails({{ $appointment->id }})">
                            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-blue-600 rounded-lg flex items-center justify-center text-white font-bold text-xs sm:text-sm flex-shrink-0">
                                {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i') }}
                            </div>
                            <div class="ml-3 sm:ml-4 flex-1 min-w-0">
                                <h3 class="font-semibold text-gray-800 dark:text-gray-200 text-sm sm:text-base truncate">
                                    @if($appointment->patient)
                                        {{ $appointment->patient->full_name }}
                                    @elseif($appointment->patient_name)
                                        {{ $appointment->patient_name }}
                                    @else
                                        Hasta Bilgisi Yok
                                    @endif
                                </h3>
                                <p class="text-gray-600 dark:text-gray-400 text-xs sm:text-sm truncate">
                                    {{ $appointment->operation->process_label ?? $appointment->notes ?? 'Genel Muayene' }}
                                </p>
                                <p class="text-gray-500 dark:text-gray-500 text-xs mt-1 truncate hidden sm:block">
                                    Tel: 
                                    @if($appointment->patient)
                                        {{ $appointment->patient->phone ?? 'Belirtilmemiş' }}
                                    @elseif($appointment->patient_phone)
                                        {{ $appointment->patient_phone }}
                                    @else
                                        Belirtilmemiş
                                    @endif
                                </p>
                            </div>
                            <div class="flex flex-col items-end space-y-1 sm:space-y-2 ml-2">
                                <span class="px-2 sm:px-3 py-1 text-xs font-medium rounded-full whitespace-nowrap
                                    @if($appointment->status === 'completed') bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400
                                    @elseif($appointment->status === 'cancelled') bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400
                                    @elseif($appointment->status === 'in_progress') bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400
                                    @else bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400
                                    @endif">
                                    @switch($appointment->status)
                                        @case('completed')
                                            <i class="fas fa-check mr-1"></i>Tamamlandı
                                            @break
                                        @case('cancelled')
                                            <i class="fas fa-times mr-1"></i>İptal
                                            @break
                                        @case('in_progress')
                                            <i class="fas fa-clock mr-1"></i>Devam Ediyor
                                            @break
                                        @default
                                            <i class="fas fa-calendar mr-1"></i>Beklemede
                                    @endswitch
                                </span>
                                <button class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-xs sm:text-sm">
                                    <i class="fas fa-eye mr-1"></i><span class="hidden sm:inline">Detay</span>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-calendar-times text-gray-400 dark:text-gray-500 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-2">Bu gün için randevu bulunmuyor</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">Başka bir tarih seçebilir veya yeni randevu oluşturabilirsiniz.</p>
                    <div class="flex justify-center space-x-3">
                        <button wire:click="nextDate" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors text-sm">
                            <i class="fas fa-arrow-right mr-2"></i>Sonraki Gün
                        </button>
                        <a href="{{ route('clinic') }}" class="inline-block px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors text-sm">
                            <i class="fas fa-plus mr-2"></i>Randevu Oluştur
                        </a>
                    </div>
                </div>
            @endif
        @else
            <!-- Weekly View -->
            <div class="grid grid-cols-7 gap-1 sm:gap-4">
                @foreach($this->weekDays as $day)
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden {{ $day['isToday'] ? 'ring-1 sm:ring-2 ring-blue-500' : '' }}">
                        <div class="p-1 sm:p-3 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                            <div class="text-center">
                                <div class="text-xs font-medium text-gray-600 dark:text-gray-400 uppercase hidden sm:block">
                                {{ $day['dayShort'] }}
                            </div>
                                <div class="text-sm sm:text-lg font-bold text-gray-800 dark:text-gray-200 {{ $day['isToday'] ? 'text-blue-600 dark:text-blue-400' : '' }}">
                                    {{ $day['date']->format('j') }}
                                </div>
                            </div>
                        </div>
                        <div class="p-1 sm:p-2 min-h-[80px] sm:min-h-[120px]">
                            @if($day['hasAppointments'])
                                <div class="space-y-1">
                                    @foreach($day['appointments'] as $appointment)
                                        <div class="p-1 sm:p-2 bg-blue-50 dark:bg-blue-900/20 rounded text-xs cursor-pointer hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors"
                                             wire:click="showAppointmentDetails({{ $appointment->id }})">
                                            <div class="font-medium text-blue-800 dark:text-blue-300 text-xs">
                                                {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i') }}
                                            </div>
                                            <div class="text-gray-600 dark:text-gray-400 truncate text-xs hidden sm:block">
                                                @if($appointment->patient)
                                                    {{ $appointment->patient->full_name }}
                                                @elseif($appointment->patient_name)
                                                    {{ $appointment->patient_name }}
                                                @else
                                                    Hasta
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center text-gray-400 dark:text-gray-500 text-xs mt-4 sm:mt-8">
                                    Randevu yok
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
    
    <!-- Appointment Details Modal -->
    @if($showAppointmentModal && $selectedAppointment)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full mx-4">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Randevu Detayları</h3>
                        <button wire:click="closeAppointmentModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Hasta</label>
                            <p class="text-gray-900 dark:text-gray-100">
                                @if($selectedAppointment->patient)
                                    {{ $selectedAppointment->patient->full_name }}
                                    @if($selectedAppointment->patient_id)
                                        <button wire:click="showPatientDetails({{ $selectedAppointment->patient_id }})" 
                                                class="ml-2 text-blue-600 hover:text-blue-800 text-sm">
                                            <i class="fas fa-user-circle mr-1"></i>Hasta Detayları
                                        </button>
                                        <button wire:click="showPatientOperations({{ $selectedAppointment->patient_id }})" 
                                                class="ml-2 text-purple-600 hover:text-purple-800 text-sm">
                                            <i class="fas fa-history mr-1"></i>Geçmiş İşlemler
                                        </button>
                                    @endif
                                @elseif($selectedAppointment->patient_name)
                                    {{ $selectedAppointment->patient_name }}
                                @else
                                    Belirtilmemiş
                                @endif
                            </p>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tarih</label>
                                <p class="text-gray-900 dark:text-gray-100">{{ \Carbon\Carbon::parse($selectedAppointment->appointment_date)->format('d.m.Y') }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Saat</label>
                                <p class="text-gray-900 dark:text-gray-100">{{ \Carbon\Carbon::parse($selectedAppointment->appointment_time)->format('H:i') }}</p>
                            </div>
                        </div>
                        
                        @if($selectedAppointment->operation)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">İşlem</label>
                                <p class="text-gray-900 dark:text-gray-100">{{ $selectedAppointment->operation->name }}</p>
                            </div>
                        @endif
                        
                        @if($selectedAppointment->notes)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notlar</label>
                                <p class="text-gray-900 dark:text-gray-100">{{ $selectedAppointment->notes }}</p>
                            </div>
                        @endif
                        
                        @if($selectedAppointment->patient)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">İletişim</label>
                                <p class="text-gray-900 dark:text-gray-100">
                                    <i class="fas fa-phone mr-2"></i>{{ $selectedAppointment->patient->phone ?? 'Belirtilmemiş' }}
                                </p>
                                @if($selectedAppointment->patient->email)
                                    <p class="text-gray-900 dark:text-gray-100 mt-1">
                                        <i class="fas fa-envelope mr-2"></i>{{ $selectedAppointment->patient->email }}
                                    </p>
                                @endif
                            </div>
                        @endif
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Durum</label>
                            <span class="px-3 py-1 text-sm font-medium rounded-full
                                @if($selectedAppointment->status === 'completed') bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400
                                @elseif($selectedAppointment->status === 'cancelled') bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400
                                @elseif($selectedAppointment->status === 'in_progress') bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400
                                @else bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400
                                @endif">
                                @switch($selectedAppointment->status)
                                    @case('completed')
                                        <i class="fas fa-check mr-1"></i>Tamamlandı
                                        @break
                                    @case('cancelled')
                                        <i class="fas fa-times mr-1"></i>İptal Edildi
                                        @break
                                    @case('in_progress')
                                        <i class="fas fa-clock mr-1"></i>Devam Ediyor
                                        @break
                                    @default
                                        <i class="fas fa-calendar mr-1"></i>Beklemede
                                @endswitch
                            </span>
                        </div>
                    </div>
                </div>
                <div class="p-6 border-t border-gray-200 dark:border-gray-700 flex justify-end space-x-3">
                    <button wire:click="closeAppointmentModal" class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-400 dark:hover:bg-gray-500 transition-colors">
                        Kapat
                    </button>
                    <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                        <i class="fas fa-edit mr-2"></i>Düzenle
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Hasta Detayları Modal -->
     @if($showPatientModal && $selectedPatient)
         <div class="fixed inset-0 bg-black bg-opacity-50 h-full w-full z-50 flex items-center justify-center p-4" wire:click="closePatientModal">
             <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] flex flex-col" wire:click.stop>
                 <!-- Header -->
                 <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white p-6 rounded-t-2xl">
                     <div class="flex justify-between items-center">
                         <div class="flex items-center space-x-3">
                             <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                 <i class="fas fa-user-circle text-2xl"></i>
                             </div>
                             <div>
                                 <h3 class="text-xl font-bold">Hasta Detayları</h3>
                                 <p class="text-blue-100 text-sm">{{ $selectedPatient->full_name }}</p>
                             </div>
                         </div>
                         <button wire:click="closePatientModal" class="text-white hover:text-gray-200 transition-colors p-2 rounded-full hover:bg-white hover:bg-opacity-10">
                             <i class="fas fa-times text-xl"></i>
                         </button>
                     </div>
                 </div>
                 
                 <!-- Content -->
                 <div class="p-6 overflow-y-auto flex-1">
                     <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                         <!-- Kişisel Bilgiler Kartı -->
                         <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-xl p-4 border border-blue-200 dark:border-blue-700">
                             <div class="flex items-center mb-3">
                                 <i class="fas fa-user text-blue-600 dark:text-blue-400 mr-2"></i>
                                 <h4 class="font-semibold text-gray-800 dark:text-gray-200">Kişisel Bilgiler</h4>
                             </div>
                             <div class="space-y-3">
                                 <div>
                                     <label class="text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wide">Ad Soyad</label>
                                     <p class="text-gray-900 dark:text-gray-100 font-medium">{{ $selectedPatient->full_name }}</p>
                                 </div>
                                 <div>
                                     <label class="text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wide">TC Kimlik</label>
                                     <p class="text-gray-900 dark:text-gray-100 font-mono">{{ $selectedPatient->formatted_tc ?? 'Belirtilmemiş' }}</p>
                                 </div>
                                 <div>
                                     <label class="text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wide">Yaş</label>
                                     <p class="text-gray-900 dark:text-gray-100">{{ $selectedPatient->age ?? 'Belirtilmemiş' }} yaş</p>
                                 </div>
                             </div>
                         </div>
                         
                         <!-- İletişim Bilgileri Kartı -->
                         <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 rounded-xl p-4 border border-green-200 dark:border-green-700">
                             <div class="flex items-center mb-3">
                                 <i class="fas fa-phone text-green-600 dark:text-green-400 mr-2"></i>
                                 <h4 class="font-semibold text-gray-800 dark:text-gray-200">İletişim</h4>
                             </div>
                             <div class="space-y-3">
                                 <div>
                                     <label class="text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wide">Telefon</label>
                                     <p class="text-gray-900 dark:text-gray-100 font-medium">{{ $selectedPatient->phone ?? 'Belirtilmemiş' }}</p>
                                 </div>
                                 <div>
                                     <label class="text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wide">E-posta</label>
                                     <p class="text-gray-900 dark:text-gray-100 break-all">{{ $selectedPatient->email ?? 'Belirtilmemiş' }}</p>
                                 </div>
                             </div>
                         </div>
                     </div>
                     
                     <!-- Tıbbi Bilgiler Bölümü -->
                     <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                         <!-- Tıbbi Bilgiler Kartı -->
                         <div class="bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/20 rounded-xl p-4 border border-red-200 dark:border-red-700">
                             <div class="flex items-center mb-3">
                                 <i class="fas fa-heartbeat text-red-600 dark:text-red-400 mr-2"></i>
                                 <h4 class="font-semibold text-gray-800 dark:text-gray-200">Tıbbi Bilgiler</h4>
                             </div>
                             <div class="space-y-3">
                                 <div>
                                     <label class="text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wide">İlaçlar</label>
                                     <p class="text-gray-900 dark:text-gray-100">{{ $selectedPatient->medications ?? 'Belirtilmemiş' }}</p>
                                 </div>
                                 <div>
                                     <label class="text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wide">Alerjiler</label>
                                     <p class="text-gray-900 dark:text-gray-100 {{ $selectedPatient->allergies ? 'text-red-600 font-medium' : '' }}">{{ $selectedPatient->allergies ?? 'Belirtilmemiş' }}</p>
                                 </div>
                                 <div>
                                     <label class="text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wide">Önceki Operasyonlar</label>
                                     <p class="text-gray-900 dark:text-gray-100">{{ $selectedPatient->previous_operations ?? 'Belirtilmemiş' }}</p>
                                 </div>
                             </div>
                         </div>
                         
                         <!-- Klinik Bilgiler Kartı -->
                         <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 dark:from-emerald-900/20 dark:to-emerald-800/20 rounded-xl p-4 border border-emerald-200 dark:border-emerald-700">
                             <div class="flex items-center mb-3">
                                 <i class="fas fa-stethoscope text-emerald-600 dark:text-emerald-400 mr-2"></i>
                                 <h4 class="font-semibold text-gray-800 dark:text-gray-200">Klinik Bilgiler</h4>
                             </div>
                             <div class="space-y-3">
                                 <div>
                                     <label class="text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wide">Şikayetler</label>
                                     <p class="text-gray-900 dark:text-gray-100 leading-relaxed">{{ $selectedPatient->complaints ?? 'Belirtilmemiş' }}</p>
                                 </div>
                                 <div>
                                     <label class="text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wide">Fizik Muayene</label>
                                     <p class="text-gray-900 dark:text-gray-100 leading-relaxed">{{ $selectedPatient->physical_examination ?? 'Belirtilmemiş' }}</p>
                                 </div>
                             </div>
                         </div>
                     </div>
                     
                     <!-- Ek Tıbbi Bilgiler -->
                     <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                         <!-- Planlanan Operasyon Kartı -->
                         <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 dark:from-yellow-900/20 dark:to-yellow-800/20 rounded-xl p-4 border border-yellow-200 dark:border-yellow-700">
                             <div class="flex items-center mb-3">
                                 <i class="fas fa-calendar-check text-yellow-600 dark:text-yellow-400 mr-2"></i>
                                 <h4 class="font-semibold text-gray-800 dark:text-gray-200">Planlanan Operasyon</h4>
                             </div>
                             <div>
                                 <p class="text-gray-900 dark:text-gray-100 leading-relaxed">{{ $selectedPatient->planned_operation ?? 'Belirtilmemiş' }}</p>
                             </div>
                         </div>
                         
                         <!-- Kronik Hastalıklar Kartı -->
                         <div class="bg-gradient-to-br from-orange-50 to-orange-100 dark:from-orange-900/20 dark:to-orange-800/20 rounded-xl p-4 border border-orange-200 dark:border-orange-700">
                             <div class="flex items-center mb-3">
                                 <i class="fas fa-exclamation-triangle text-orange-600 dark:text-orange-400 mr-2"></i>
                                 <h4 class="font-semibold text-gray-800 dark:text-gray-200">Kronik Hastalıklar</h4>
                             </div>
                             <div>
                                 <p class="text-gray-900 dark:text-gray-100 leading-relaxed {{ $selectedPatient->chronic_conditions ? 'text-orange-600 font-medium' : '' }}">{{ $selectedPatient->chronic_conditions ?? 'Belirtilmemiş' }}</p>
                             </div>
                         </div>
                         

                     </div>
                 </div>
                 
                 <!-- Footer -->
                 <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 rounded-b-2xl flex justify-end space-x-3 flex-shrink-0">
                     <button wire:click="closePatientModal" class="px-6 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors font-medium">
                         <i class="fas fa-times mr-2"></i>Kapat
                     </button>
                 </div>
             </div>
         </div>
     @endif

    <!-- Geçmiş İşlemler Modal -->
    @if($showOperationsModal && $selectedPatient)
        <div class="fixed inset-0 bg-black bg-opacity-50 h-full w-full z-50 flex items-center justify-center p-4" wire:click="closeOperationsModal">
            <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-5xl max-h-[90vh] flex flex-col" wire:click.stop>
                <!-- Header -->
                <div class="bg-gradient-to-r from-emerald-600 to-teal-600 text-white p-6 rounded-t-2xl">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                <i class="fas fa-clipboard-list text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold">Geçmiş İşlemler</h3>
                                <p class="text-emerald-100 text-sm">{{ $selectedPatient->full_name }}</p>
                            </div>
                        </div>
                        <button wire:click="closeOperationsModal" class="text-white hover:text-gray-200 transition-colors p-2 rounded-full hover:bg-white hover:bg-opacity-10">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Content -->
                <div class="flex-1 overflow-y-auto">
                    @if($patientOperations->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Operasyon Bilgileri</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hasta Bilgileri</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kayıt Dönemi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($patientOperations as $operation)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-12 w-12 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                                        <i class="fas fa-procedures text-blue-600 dark:text-blue-400"></i>
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900" title="{{ $operation->process ?? 'Belirtilmemiş' }}">
                                                            @php
                                                                $processLabels = [
                                                                    'surgery' => 'Ameliyat',
                                                                    'mesotherapy' => 'Mezoterapi',
                                                                    'botox' => 'Botoks',
                                                                    'filler' => 'Dolgu'
                                                                ];
                                                                $processLabel = $processLabels[$operation->process] ?? $operation->process ?? 'Belirtilmemiş';
                                                            @endphp
                                                            {{ Str::limit($processLabel, 25) }}
                                                        </div>
                                                        <div class="text-xs text-gray-400">{{ Str::limit($operation->process_detail ?? '', 30) }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900" title="{{ $selectedPatient->first_name }} {{ $selectedPatient->last_name }}">
                                                    {{ Str::limit($selectedPatient->first_name . ' ' . $selectedPatient->last_name, 25) }}
                                                </div>
                                                <div class="text-sm text-gray-500">TC: {{ substr($selectedPatient->tc_identity, 0, 3) }}***{{ substr($selectedPatient->tc_identity, -2) }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $operation->registration_period ?? 'Belirtilmemiş' }}</div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-16">
                            <div class="w-24 h-24 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 rounded-full flex items-center justify-center mx-auto mb-6">
                                <i class="fas fa-clipboard-list text-4xl text-gray-400 dark:text-gray-500"></i>
                            </div>
                            <h4 class="text-xl font-semibold text-gray-700 dark:text-gray-300 mb-2">Henüz İşlem Kaydı Yok</h4>
                            <p class="text-gray-500 dark:text-gray-400 max-w-md mx-auto">Bu hasta için henüz herhangi bir işlem kaydı bulunmuyor. İlk işlem kaydedildiğinde burada görünecektir.</p>
                        </div>
                    @endif
                </div>
                
                <!-- Footer -->
                <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 rounded-b-2xl flex justify-between items-center flex-shrink-0">
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        @if($patientOperations->count() > 0)
                            <i class="fas fa-list-ul mr-1"></i>
                            Toplam {{ $patientOperations->count() }} işlem kaydı
                        @endif
                    </div>
                    <button wire:click="closeOperationsModal" class="px-6 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors font-medium">
                        <i class="fas fa-times mr-2"></i>Kapat
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
