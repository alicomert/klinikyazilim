<div class="p-4 sm:p-6">
    <h1 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-white mb-4 sm:mb-6">{{ Auth::user()->getRoleDisplayName() }} Paneli</h1>
    
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mb-6 sm:mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 sm:p-6 border border-gray-100 dark:border-gray-700 card-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Bu Haftadaki Hastalar</p>
                    <p class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-gray-200 mt-1">{{ number_format($this->stats['weekly_patients']) }}</p>
                    @if($this->stats['new_patients'] > 0)
                        <p class="text-green-600 dark:text-green-400 text-sm mt-1">
                            <i class="fas fa-arrow-up"></i> {{ $this->stats['new_patients'] }} yeni hasta
                        </p>
                    @elseif($this->stats['new_patients'] < 0)
                        <p class="text-red-600 dark:text-red-400 text-sm mt-1">
                            <i class="fas fa-arrow-down"></i> {{ abs($this->stats['new_patients']) }} azalma
                        </p>
                    @else
                        <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">
                            <i class="fas fa-minus"></i> Değişim yok
                        </p>
                    @endif
                </div>
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user-injured text-blue-600 dark:text-blue-400 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 sm:p-6 border border-gray-100 dark:border-gray-700 card-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Bu Haftadaki Randevular</p>
                    <p class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-gray-200 mt-1">{{ number_format($this->stats['weekly_appointments']) }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar-check text-green-600 dark:text-green-400 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 sm:p-6 border border-gray-100 dark:border-gray-700 card-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Bu Aydaki Operasyonlar</p>
                    <p class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-gray-200 mt-1">{{ number_format($this->stats['monthly_operations']) }}</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900 rounded-lg flex items-center justify-center">
                    <i class="fas fa-procedures text-orange-600 dark:text-orange-400 text-xl"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bugünkü Program ve Hızlı İşlemler Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 sm:gap-6 mb-6 sm:mb-8">
        <!-- Gelişmiş Randevu Takvimi (3/4 genişlik) -->
        <div class="lg:col-span-3 order-2 lg:order-1">
            @livewire('doctor-appointment-calendar')
        </div>

        <!-- Quick Actions -->
        <div class="w-auto order-1 lg:order-2">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col">
                <div class="p-4 sm:p-6 border-b border-gray-100 dark:border-gray-700 flex-shrink-0">
                    <h2 class="text-lg sm:text-xl font-bold text-gray-800 dark:text-gray-200">Hızlı İşlemler</h2>
                </div>
                <div class="p-4 sm:p-6 overflow-y-auto">
                    <div class="space-y-3 sm:space-y-4">
                        <button onclick="openNotesModal()" class="w-full flex items-center p-3 sm:p-4 bg-indigo-50 dark:bg-indigo-900/20 hover:bg-indigo-100 dark:hover:bg-indigo-900/30 rounded-lg border border-indigo-100 dark:border-indigo-800 transition-colors">
                            <div class="w-8 h-8 sm:w-10 sm:h-10 bg-indigo-600 rounded-lg flex items-center justify-center">
                                <i class="fas fa-sticky-note text-white text-sm sm:text-base"></i>
                            </div>
                            <div class="ml-3 text-left">
                                <p class="font-semibold text-gray-800 dark:text-gray-200 text-sm sm:text-base">Notlarım</p>
                                <p class="text-gray-600 dark:text-gray-400 text-xs sm:text-sm">Notlarınızı görüntüleyin</p>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Doctor Notes Modal -->
        <div id="notesModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50 flex items-center justify-center">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-6xl mx-4 max-h-[90vh] overflow-hidden">
                <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Doktor Notları</h3>
                    <button onclick="closeNotesModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
                    @livewire('doctor-notes')
                </div>
            </div>
        </div>
    </div>

    <!-- Son Hastalar -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
        <div class="p-4 sm:p-6 border-b border-gray-100 dark:border-gray-700">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-2 sm:space-y-0">
                <h2 class="text-lg sm:text-xl font-bold text-gray-800 dark:text-gray-200">Son Hastalar</h2>
                <a href="{{ route('patients') }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium text-sm sm:text-base">
                    Tümünü Görüntüle <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
        
        <!-- Mobil Kart Görünümü -->
        <div class="block sm:hidden">
            @forelse($this->recentPatients as $patient)
                <div class="p-4 border-b border-gray-200 dark:border-gray-700 last:border-b-0">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0 h-10 w-10 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-blue-600 dark:text-blue-400"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-medium text-gray-900 dark:text-white mb-1">{{ $patient['name'] }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 space-y-1">
                                <div>📞 {{ $patient['phone'] ?: 'Belirtilmemiş' }}</div>
                                <div>🎂 {{ $patient['age'] ? $patient['age'] . ' yaş' : 'Belirtilmemiş' }}</div>
                                <div>📅 {{ $patient['created_at']->format('d.m.Y') }}</div>
                            </div>
                            <div class="flex space-x-3 mt-2">
                                <button class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300" title="Detayları Görüntüle">
                                    <i class="fas fa-eye text-sm"></i>
                                </button>
                                <button class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300" title="Randevu Ver">
                                    <i class="fas fa-calendar-plus text-sm"></i>
                                </button>
                                <button class="text-purple-600 dark:text-purple-400 hover:text-purple-900 dark:hover:text-purple-300" title="Dosya">
                                    <i class="fas fa-file-medical text-sm"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-4 text-center text-gray-500 dark:text-gray-400">
                    Henüz hasta kaydı bulunmuyor.
                </div>
            @endforelse
        </div>
        
        <!-- Masaüstü Tablo Görünümü -->
        <div class="hidden sm:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Hasta</th>
                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Telefon</th>
                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Yaş</th>
                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Kayıt Tarihi</th>
                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($this->recentPatients as $patient)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-4 lg:px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8 lg:h-10 lg:w-10 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                        <i class="fas fa-user text-blue-600 dark:text-blue-400 text-sm lg:text-base"></i>
                                    </div>
                                    <div class="ml-3 lg:ml-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $patient['name'] }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                {{ $patient['phone'] ?: 'Belirtilmemiş' }}
                            </td>
                            <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                {{ $patient['age'] ? $patient['age'] . ' yaş' : 'Belirtilmemiş' }}
                            </td>
                            <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                {{ $patient['created_at']->format('d.m.Y') }}
                            </td>
                            <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm space-x-1 lg:space-x-2">
                                <button class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 p-1" title="Detayları Görüntüle">
                                    <i class="fas fa-eye text-sm"></i>
                                </button>
                                <button class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300 p-1" title="Randevu Ver">
                                    <i class="fas fa-calendar-plus text-sm"></i>
                                </button>
                                <button class="text-purple-600 dark:text-purple-400 hover:text-purple-900 dark:hover:text-purple-300 p-1" title="Dosya">
                                    <i class="fas fa-file-medical text-sm"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 lg:px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                Henüz hasta kaydı bulunmuyor.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function openNotesModal() {
        document.getElementById('notesModal').classList.remove('hidden');
    }
    
    function closeNotesModal() {
        document.getElementById('notesModal').classList.add('hidden');
    }
    
    // Modal dışına tıklandığında kapat
    document.getElementById('notesModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeNotesModal();
        }
    });
</script>