<div>
    <style>
        .sidebar {
            transition: all 0.3s;
        }
        .sidebar-collapsed {
            width: 70px;
        }
        .sidebar-collapsed .sidebar-text {
            display: none;
        }
        .sidebar-collapsed .logo-text {
            display: none;
        }
        .sidebar-collapsed .menu-item {
            justify-content: center;
        }
        .content-area {
            transition: all 0.3s;
        }
        .content-expanded {
            margin-left: 70px;
        }
        .calendar-day {
            min-height: 100px;
        }
        .calendar-day:hover {
            background-color: #f3f4f6;
        }
        .appointment-slot {
            transition: all 0.2s;
            cursor: pointer;
        }
        .appointment-slot:hover {
            transform: translateY(-2px);
        }
        .chart-container {
            height: 300px;
        }
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                z-index: 50;
                transform: translateX(-100%);
            }
            .sidebar-open {
                transform: translateX(0);
            }
            .content-area {
                margin-left: 0 !important;
            }
        }
    </style>

    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('message') }}
        </div>
    @endif

    <!-- Main Content -->
    <main class="p-6">
        <!-- Calendar Controls -->
        <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div class="flex items-center space-x-4 mb-4 md:mb-0">
                    <button wire:click="previousPeriod" class="p-2 rounded-lg hover:bg-gray-100">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <h2 class="text-lg font-semibold">{{ $currentPeriodText }}</h2>
                    <button wire:click="nextPeriod" class="p-2 rounded-lg hover:bg-gray-100">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                    <button wire:click="goToToday" class="ml-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Bugün
                    </button>
                </div>
                <div class="flex items-center space-x-2">
                    <select wire:model.live="viewMode" class="border rounded-lg px-3 py-2 text-sm">
                        <option value="weekly">Haftalık Görünüm</option>
                        <option value="monthly">Aylık Görünüm</option>
                    </select>
                    <button wire:click="openModal()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center space-x-2">
                        <i class="fas fa-plus"></i>
                        <span>Yeni Randevu</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Arama Sonuçları -->
        @if($search && count($searchResults) > 0)
            <div class="bg-white rounded-lg shadow-sm border mb-6">
                <div class="p-4 border-b">
                    <h3 class="text-lg font-medium text-gray-900">Arama Sonuçları ({{ count($searchResults) }} sonuç)</h3>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($searchResults as $result)
                            <div class="border rounded-lg p-3 hover:bg-gray-50 cursor-pointer" wire:click="showAppointmentDetails({{ $result->id }})">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="font-medium text-gray-900">{{ $result->patient_name }}</h4>
                                        <p class="text-sm text-gray-600">{{ $result->patient_phone }}</p>
                                        <p class="text-sm text-gray-500">{{ $result->appointment_date->format('d.m.Y H:i') }}</p>
                                        <span class="inline-block px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800 mt-1">
                                            {{ ucfirst($result->appointment_type) }}
                                        </span>
                                    </div>
                                    <button class="text-blue-600 hover:text-blue-800 text-sm">Ayrıntı</button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- Search and Filter -->
        <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <input wire:model.live="search" type="text" placeholder="Hasta ara..." class="w-full border rounded-lg px-3 py-2">
                </div>
                <div>
                    <input wire:model.live="filterDate" type="date" class="w-full border rounded-lg px-3 py-2">
                </div>
                <div>
                    <select wire:model.live="filterStatus" class="w-full border rounded-lg px-3 py-2">
                        <option value="">Tüm Durumlar</option>
                        <option value="scheduled">Planlandı</option>
                        <option value="completed">Tamamlandı</option>
                        <option value="cancelled">İptal Edildi</option>
                        <option value="no_show">Gelmedi</option>
                    </select>
                </div>
                <div>
                    <select wire:model.live="filterType" class="w-full border rounded-lg px-3 py-2">
                        <option value="">Tüm Türler</option>
                        <option value="consultation">Konsültasyon</option>
                        <option value="operation">Operasyon</option>
                        <option value="control">Kontrol</option>
                        <option value="botox">Botoks</option>
                        <option value="filler">Dolgu</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Calendar View -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
            @if($viewMode === 'weekly')
                <!-- Weekly View -->
                <!-- Week Header -->
                <div class="grid grid-cols-7 border-b">
                    <div class="py-2 text-center font-medium text-gray-500">Pazar</div>
                    <div class="py-2 text-center font-medium text-gray-500">Pazartesi</div>
                    <div class="py-2 text-center font-medium text-gray-500">Salı</div>
                    <div class="py-2 text-center font-medium text-gray-500">Çarşamba</div>
                    <div class="py-2 text-center font-medium text-gray-500">Perşembe</div>
                    <div class="py-2 text-center font-medium text-gray-500">Cuma</div>
                    <div class="py-2 text-center font-medium text-gray-500">Cumartesi</div>
                </div>
                
                <!-- Weekly Calendar Grid -->
                <div class="grid grid-cols-7">
                    @for ($day = 1; $day <= 7; $day++)
                        @php
                            $currentDate = \Carbon\Carbon::parse($currentWeek)->startOfWeek()->addDays($day - 1);
                            $dateString = $currentDate->format('Y-m-d');
                            $dayAppointments = $appointmentsByDate[$dateString] ?? collect();
                        @endphp
                        <div class="calendar-day border-r border-b p-2 cursor-pointer min-h-[120px] {{ $currentDate->isToday() ? 'bg-blue-50' : '' }}" 
                             wire:click="openModal('{{ $dateString }}')"
                             ondrop="@this.call('dropAppointment', event.dataTransfer.getData('text/plain'), '{{ $dateString }}')"
                             ondragover="event.preventDefault()"
                             ondragenter="event.preventDefault()">
                            <div class="text-right {{ $currentDate->isToday() ? 'font-bold text-blue-600' : 'text-gray-400' }}">
                                {{ $currentDate->day }}
                            </div>
                            <div class="mt-2 space-y-1">
                                @foreach ($dayAppointments as $appointment)
                                    <div wire:click.stop="editAppointment({{ $appointment->id }})" 
                                         class="appointment-slot bg-{{ $appointment->appointment_type_color }}-100 text-{{ $appointment->appointment_type_color }}-800 p-1 rounded text-xs cursor-move hover:bg-{{ $appointment->appointment_type_color }}-200 transition-colors"
                                         draggable="true"
                                         ondragstart="event.dataTransfer.setData('text/plain', '{{ $appointment->id }}'); this.style.opacity='0.5'"
                                         ondragend="this.style.opacity='1'">
                                        <div class="flex items-center justify-between">
                                            <div class="font-medium">{{ $appointment->appointment_time->format('H:i') }}</div>
                                            @if($appointment->status === 'completed')
                                                <span class="text-green-600">✓</span>
                                            @elseif($appointment->status === 'cancelled')
                                                <span class="text-red-600">✗</span>
                                            @elseif($appointment->status === 'no_show')
                                                <span class="text-yellow-600">!</span>
                                            @else
                                                <span class="text-blue-600">●</span>
                                            @endif
                                        </div>
                                        <div class="truncate">{{ $appointment->patient_display_name }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endfor
                </div>
            @else
                <!-- Monthly View -->
                <!-- Month Header -->
                <div class="grid grid-cols-7 border-b">
                    <div class="py-2 text-center font-medium text-gray-500">Pazar</div>
                    <div class="py-2 text-center font-medium text-gray-500">Pazartesi</div>
                    <div class="py-2 text-center font-medium text-gray-500">Salı</div>
                    <div class="py-2 text-center font-medium text-gray-500">Çarşamba</div>
                    <div class="py-2 text-center font-medium text-gray-500">Perşembe</div>
                    <div class="py-2 text-center font-medium text-gray-500">Cuma</div>
                    <div class="py-2 text-center font-medium text-gray-500">Cumartesi</div>
                </div>
                
                <!-- Monthly Calendar Grid -->
                <div class="grid grid-cols-7">
                    @php
                        $startOfMonth = \Carbon\Carbon::create($currentYear, $currentMonth, 1);
                        $endOfMonth = $startOfMonth->copy()->endOfMonth();
                        $startOfCalendar = $startOfMonth->copy()->startOfWeek();
                        $endOfCalendar = $endOfMonth->copy()->endOfWeek();
                        $totalDays = $startOfCalendar->diffInDays($endOfCalendar) + 1;
                    @endphp
                    
                    @for ($i = 0; $i < $totalDays; $i++)
                        @php
                            $currentDate = $startOfCalendar->copy()->addDays($i);
                            $dateString = $currentDate->format('Y-m-d');
                            $dayAppointments = $appointmentsByDate[$dateString] ?? collect();
                            $isCurrentMonth = $currentDate->month == $currentMonth;
                        @endphp
                        
                        <div class="calendar-day border-r border-b p-1 cursor-pointer min-h-[80px] {{ $currentDate->isToday() ? 'bg-blue-50' : (!$isCurrentMonth ? 'bg-gray-50' : '') }}" 
                             wire:click="openModal('{{ $dateString }}')"
                             ondrop="@this.call('dropAppointment', event.dataTransfer.getData('text/plain'), '{{ $dateString }}')"
                             ondragover="event.preventDefault()"
                             ondragenter="event.preventDefault()">
                            <div class="text-right text-sm {{ $currentDate->isToday() ? 'font-bold text-blue-600' : ($isCurrentMonth ? 'text-gray-700' : 'text-gray-400') }}">
                                {{ $currentDate->day }}
                            </div>
                            @if($dayAppointments->count() > 0)
                                <div class="mt-1">
                                    @foreach ($dayAppointments->take(2) as $appointment)
                                        <div wire:click.stop="editAppointment({{ $appointment->id }})" 
                                             class="appointment-slot bg-{{ $appointment->appointment_type_color }}-100 text-{{ $appointment->appointment_type_color }}-800 p-1 rounded text-xs cursor-move hover:bg-{{ $appointment->appointment_type_color }}-200 transition-colors mb-1"
                                             draggable="true"
                                             ondragstart="event.dataTransfer.setData('text/plain', '{{ $appointment->id }}'); this.style.opacity='0.5'"
                                             ondragend="this.style.opacity='1'">
                                            <div class="flex items-center justify-between">
                                                <div class="truncate">{{ $appointment->appointment_time->format('H:i') }}</div>
                                                @if($appointment->status === 'completed')
                                                    <span class="text-green-600 text-xs">✓</span>
                                                @elseif($appointment->status === 'cancelled')
                                                    <span class="text-red-600 text-xs">✗</span>
                                                @elseif($appointment->status === 'no_show')
                                                    <span class="text-yellow-600 text-xs">!</span>
                                                @else
                                                    <span class="text-blue-600 text-xs">●</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                    @if($dayAppointments->count() > 2)
                                        <div class="text-xs text-gray-500">+{{ $dayAppointments->count() - 2 }} daha</div>
                                    @endif
                                </div>
                            @endif
                        </div>
                        
                        @if(($i + 1) % 7 == 0 && $i + 1 < $totalDays)
                            <!-- Week break for monthly view -->
                        @endif
                    @endfor
                </div>
            @endif
        </div>

        <!-- Appointments List -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b">
                <h3 class="text-lg font-semibold">Randevular</h3>
            </div>
            <div class="overflow-x-auto">
                <!-- Toplu İşlemler -->
                @if(count($selectedAppointments) > 0)
                    <div class="mb-4 p-4 bg-blue-50 rounded-lg">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-blue-800">{{ count($selectedAppointments) }} randevu seçildi</span>
                            <div class="flex items-center space-x-2">
                                <select wire:model="bulkStatus" class="border rounded px-2 py-1 text-sm">
                                    <option value="">Durum Seçin</option>
                                    <option value="scheduled">Planlandı</option>
                                    <option value="completed">Tamamlandı</option>
                                    <option value="cancelled">İptal Edildi</option>
                                    <option value="no_show">Gelmedi</option>
                                </select>
                                <button wire:click="updateBulkStatus" class="px-3 py-1 bg-blue-600 text-white rounded text-sm hover:bg-blue-700">Güncelle</button>
                                <button wire:click="clearSelection" class="px-3 py-1 bg-gray-600 text-white rounded text-sm hover:bg-gray-700">Temizle</button>
                            </div>
                        </div>
                    </div>
                @endif
                
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <input type="checkbox" wire:model.live="selectAll" class="rounded">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hasta</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarih/Saat</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tür</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Durum</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($appointments as $appointment)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <input type="checkbox" wire:model.live="selectedAppointments" value="{{ $appointment->id }}" class="rounded">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-user text-blue-600"></i>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $appointment->patient_display_name }}</div>
                                            <div class="text-sm text-gray-500">{{ $appointment->patient_display_phone }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $appointment->appointment_date->format('d.m.Y') }}</div>
                                    <div class="text-sm text-gray-500">{{ $appointment->appointment_time->format('H:i') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $appointment->appointment_type_color }}-100 text-{{ $appointment->appointment_type_color }}-800">
                                        {{ $this->getAppointmentTypeText($appointment->appointment_type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <select wire:change="updateAppointmentStatus({{ $appointment->id }}, $event.target.value)" class="border rounded px-2 py-1 text-xs
                                        @if($appointment->status == 'scheduled') border-blue-300 text-blue-600
                                        @elseif($appointment->status == 'completed') border-green-300 text-green-600
                                        @elseif($appointment->status == 'cancelled') border-red-300 text-red-600
                                        @else border-yellow-300 text-yellow-600 @endif">
                                        <option value="scheduled" {{ $appointment->status == 'scheduled' ? 'selected' : '' }}>Planlandı</option>
                                        <option value="completed" {{ $appointment->status == 'completed' ? 'selected' : '' }}>Tamamlandı</option>
                                        <option value="cancelled" {{ $appointment->status == 'cancelled' ? 'selected' : '' }}>İptal Edildi</option>
                                        <option value="no_show" {{ $appointment->status == 'no_show' ? 'selected' : '' }}>Gelmedi</option>
                                    </select>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex justify-center space-x-2">
                                        <button wire:click="editAppointment({{ $appointment->id }})" class="text-blue-600 hover:text-blue-900" title="Düzenle">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button wire:click="showNotes({{ $appointment->id }})" class="text-green-600 hover:text-green-900" title="Notlar">
                                            <i class="fas fa-sticky-note"></i>
                                        </button>
                                        <button wire:click="confirmDelete({{ $appointment->id }})" 
                                                class="text-red-600 hover:text-red-900" title="Sil">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                    Henüz randevu bulunmuyor.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4">
                {{ $appointments->links() }}
            </div>
        </div>
    </main>

    <!-- Appointment Modal -->
    @if ($showModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">
                            {{ $editingAppointment ? 'Randevu Düzenle' : 'Yeni Randevu' }}
                        </h3>
                        <button wire:click="closeModal()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <form wire:submit.prevent="saveAppointment">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Hasta Seçimi -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Hasta Seçimi</label>
                                
                                <!-- Arama Input -->
                                <div class="relative mb-3">
                                    <input type="text" wire:model.live="patientSearch" 
                                           placeholder="Hasta adı veya TC kimlik ile arayın..."
                                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <i class="fas fa-search absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                </div>
                                
                                <!-- Seçilen Hasta -->
                                @if($selectedPatient)
                                    <div class="bg-blue-50 border border-blue-200 rounded-md p-3 mb-3">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-3">
                                                <div class="bg-blue-100 p-2 rounded-full">
                                                    <i class="fas fa-user text-blue-600"></i>
                                                </div>
                                                <div>
                                                    <div class="font-medium text-gray-900">
                                                        {{ $selectedPatient->first_name }} {{ $selectedPatient->last_name }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        TC: {{ $selectedPatient->tc_identity }}
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="button" wire:click="clearPatientSelection" 
                                                    class="bg-red-500 text-white p-2 rounded-full hover:bg-red-600 transition-colors">
                                                <i class="fas fa-times text-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endif
                                
                                <!-- Hasta Listesi -->
                                @if(!$selectedPatient && count($filteredPatients) > 0)
                                    <div class="border border-gray-300 rounded-md max-h-48 overflow-y-auto">
                                        @foreach($filteredPatients as $patient)
                                            <div class="flex items-center justify-between p-3 hover:bg-gray-50 border-b border-gray-100 last:border-b-0">
                                                <div class="flex items-center space-x-3 flex-1" 
                                                     wire:click="selectPatient({{ $patient->id }})" 
                                                     class="cursor-pointer">
                                                    <div class="bg-gray-100 p-2 rounded-full">
                                                        <i class="fas fa-user text-gray-600"></i>
                                                    </div>
                                                    <div>
                                                        <div class="font-medium text-gray-900">
                                                            {{ $patient->first_name }} {{ $patient->last_name }}
                                                        </div>
                                                        <div class="text-sm text-gray-500">
                                                            TC: {{ $patient->tc_identity }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @elseif(!$selectedPatient && !empty($patientSearch))
                                    <div class="text-center py-4 text-gray-500">
                                        Arama kriterinize uygun hasta bulunamadı.
                                    </div>
                                @endif
                            </div>

                            <!-- Manuel Hasta Bilgileri (Hasta seçilmemişse) -->
                            @if (!$selectedPatient)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Hasta Adı *</label>
                                    <input wire:model="patient_name" type="text" class="w-full border rounded-lg px-3 py-2" required>
                                    @error('patient_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Telefon</label>
                                    <input wire:model="patient_phone" type="text" class="w-full border rounded-lg px-3 py-2">
                                    @error('patient_phone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                            @else
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Hasta Adı</label>
                                    <input type="text" value="{{ $selectedPatient->first_name }} {{ $selectedPatient->last_name }}" class="w-full border rounded-lg px-3 py-2 bg-gray-100" readonly>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Telefon</label>
                                    <input type="text" value="{{ $selectedPatient->phone }}" class="w-full border rounded-lg px-3 py-2 bg-gray-100" readonly>
                                </div>
                            @endif

                            <!-- Randevu Bilgileri -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tarih *</label>
                                <input wire:model="appointment_date" type="date" class="w-full border rounded-lg px-3 py-2" required>
                                @error('appointment_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Saat *</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <select wire:model="appointment_hour" class="border rounded-lg px-3 py-2" required>
                                        <option value="">Saat</option>
                                        @for($hour = 8; $hour <= 21; $hour++)
                                            <option value="{{ sprintf('%02d', $hour) }}">{{ sprintf('%02d', $hour) }}</option>
                                        @endfor
                                    </select>
                                    <select wire:model="appointment_minute" class="border rounded-lg px-3 py-2" required>
                                        <option value="">Dakika</option>
                                        <option value="00">00</option>
                                        <option value="15">15</option>
                                        <option value="30">30</option>
                                        <option value="45">45</option>
                                    </select>
                                </div>
                                @error('appointment_time') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Randevu Türü *</label>
                                <div class="grid grid-cols-3 md:grid-cols-5 gap-2">
                                    <label class="flex items-center p-2 border rounded-lg cursor-pointer hover:bg-gray-50 {{ $appointment_type == 'control' ? 'border-blue-500 bg-blue-50' : 'border-gray-300' }}">
                                        <input type="radio" wire:model.live="appointment_type" value="control" class="sr-only">
                                        <div class="flex items-center">
                                            <div class="w-3 h-3 border-2 rounded-full mr-1 {{ $appointment_type == 'control' ? 'border-blue-500 bg-blue-500' : 'border-gray-300' }}">
                                                @if($appointment_type == 'control')
                                                    <div class="w-1 h-1 bg-white rounded-full mx-auto mt-0.5"></div>
                                                @endif
                                            </div>
                                            <span class="text-xs font-medium">Kontrol</span>
                                        </div>
                                    </label>
                                    <label class="flex items-center p-2 border rounded-lg cursor-pointer hover:bg-gray-50 {{ $appointment_type == 'consultation' ? 'border-blue-500 bg-blue-50' : 'border-gray-300' }}">
                                        <input type="radio" wire:model.live="appointment_type" value="consultation" class="sr-only">
                                        <div class="flex items-center">
                                            <div class="w-3 h-3 border-2 rounded-full mr-1 {{ $appointment_type == 'consultation' ? 'border-blue-500 bg-blue-500' : 'border-gray-300' }}">
                                                @if($appointment_type == 'consultation')
                                                    <div class="w-1 h-1 bg-white rounded-full mx-auto mt-0.5"></div>
                                                @endif
                                            </div>
                                            <span class="text-xs font-medium">Konsültasyon</span>
                                        </div>
                                    </label>
                                    <label class="flex items-center p-2 border rounded-lg cursor-pointer hover:bg-gray-50 {{ $appointment_type == 'operation' ? 'border-blue-500 bg-blue-50' : 'border-gray-300' }}">
                                        <input type="radio" wire:model.live="appointment_type" value="operation" class="sr-only">
                                        <div class="flex items-center">
                                            <div class="w-3 h-3 border-2 rounded-full mr-1 {{ $appointment_type == 'operation' ? 'border-blue-500 bg-blue-500' : 'border-gray-300' }}">
                                                @if($appointment_type == 'operation')
                                                    <div class="w-1 h-1 bg-white rounded-full mx-auto mt-0.5"></div>
                                                @endif
                                            </div>
                                            <span class="text-xs font-medium">Operasyon</span>
                                        </div>
                                    </label>
                                    <label class="flex items-center p-2 border rounded-lg cursor-pointer hover:bg-gray-50 {{ $appointment_type == 'botox' ? 'border-blue-500 bg-blue-50' : 'border-gray-300' }}">
                                        <input type="radio" wire:model.live="appointment_type" value="botox" class="sr-only">
                                        <div class="flex items-center">
                                            <div class="w-3 h-3 border-2 rounded-full mr-1 {{ $appointment_type == 'botox' ? 'border-blue-500 bg-blue-500' : 'border-gray-300' }}">
                                                @if($appointment_type == 'botox')
                                                    <div class="w-1 h-1 bg-white rounded-full mx-auto mt-0.5"></div>
                                                @endif
                                            </div>
                                            <span class="text-xs font-medium">Botoks</span>
                                        </div>
                                    </label>
                                    <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 {{ $appointment_type == 'filler' ? 'border-blue-500 bg-blue-50' : 'border-gray-300' }}">
                                        <input type="radio" wire:model.live="appointment_type" value="filler" class="sr-only">
                                        <div class="flex items-center">
                                            <div class="w-4 h-4 border-2 rounded-full mr-2 {{ $appointment_type == 'filler' ? 'border-blue-500 bg-blue-500' : 'border-gray-300' }}">
                                                @if($appointment_type == 'filler')
                                                    <div class="w-2 h-2 bg-white rounded-full mx-auto mt-0.5"></div>
                                                @endif
                                            </div>
                                            <span class="text-sm font-medium">Dolgu</span>
                                        </div>
                                    </label>
                                </div>
                                @error('appointment_type') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Notlar</label>
                                <textarea wire:model="notes" rows="3" class="w-full border rounded-lg px-3 py-2"></textarea>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3 mt-6">
                            <button type="button" wire:click="closeModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                                İptal
                            </button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                {{ $editingAppointment ? 'Güncelle' : 'Kaydet' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
    
    <!-- Notes Modal -->
    @if($showNotesModal && $selectedAppointmentForNotes)
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
                                <h3 class="text-2xl font-bold text-white">{{ $selectedAppointmentForNotes->patient_display_name }} - Randevu Notları</h3>
                                <div class="flex items-center space-x-4 text-sm text-purple-100">
                                    <span class="flex items-center"><i class="fas fa-calendar mr-2"></i>{{ $selectedAppointmentForNotes->appointment_date->format('d.m.Y') }} {{ $selectedAppointmentForNotes->appointment_time->format('H:i') }}</span>
                                    <span class="flex items-center"><i class="fas fa-stethoscope mr-2"></i>{{ $this->getAppointmentTypeText($selectedAppointmentForNotes->appointment_type) }}</span>
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

                            @forelse($appointmentNotes as $note)
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
                            
                            <!-- Private Note (For Doctors and their staff) -->
                            @if(Auth::user()->role === 'doctor' || Auth::user()->doctor_id)
                            <div class="flex items-center">
                                <input type="checkbox" wire:model="newNote.is_private" id="is_private" class="rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-300 focus:ring focus:ring-purple-200 focus:ring-opacity-50">
                                <label for="is_private" class="ml-2 text-sm text-gray-700">
                                    @if(Auth::user()->role === 'doctor')
                                        Özel not (sadece ben görebilirim)
                                    @else
                                        Özel not (sadece doktor görebilir)
                                    @endif
                                </label>
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

    <!-- Delete Confirmation Modal -->
    @if($showDeleteModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3 text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                        <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                    </div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Randevuyu Sil</h3>
                    <div class="mt-2 px-7 py-3">
                        <p class="text-sm text-gray-500">
                            Bu randevuyu silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.
                        </p>
                    </div>
                    <div class="flex justify-center space-x-3 mt-4">
                        <button wire:click="cancelDelete()" 
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                            İptal
                        </button>
                        <button wire:click="deleteAppointment()" 
                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                            Sil
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
    document.addEventListener('livewire:init', () => {
        // Tümünü seç/seçme işlevi
        Livewire.on('selectAllChanged', (value) => {
            const checkboxes = document.querySelectorAll('input[name="selectedAppointments[]"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = value;
            });
        });
        
        // Seçilen randevu sayısını güncelle
        function updateSelectedCount() {
            const selectedCount = document.querySelectorAll('input[name="selectedAppointments[]"]:checked').length;
            const countElement = document.getElementById('selectedCount');
            if (countElement) {
                countElement.textContent = selectedCount;
            }
            
            // Toplu işlem alanını göster/gizle
            const bulkActions = document.getElementById('bulkActions');
            if (bulkActions) {
                bulkActions.style.display = selectedCount > 0 ? 'block' : 'none';
            }
        }
        
        // Checkbox değişikliklerini dinle
        document.addEventListener('change', function(e) {
            if (e.target.name === 'selectedAppointments[]') {
                updateSelectedCount();
            }
        });
        
        // Sayfa yüklendiğinde sayıyı güncelle
        updateSelectedCount();
    });
</script>
