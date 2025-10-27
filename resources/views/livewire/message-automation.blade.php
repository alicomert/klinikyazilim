<div class="p-4 sm:p-6">
    <h1 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-white mb-4 sm:mb-6">Mesaj Otomasyonu</h1>
    
    <!-- Stats Cards -->
    @if($showStats)
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6 sm:mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 sm:p-6 border border-gray-100 dark:border-gray-700 card-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Aktif Konfigürasyonlar</p>
                    <p class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-gray-200 mt-1">{{ number_format($this->stats['active_configs']) }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                    <i class="fas fa-cog text-green-600 dark:text-green-400 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 sm:p-6 border border-gray-100 dark:border-gray-700 card-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Bugün Gönderilen</p>
                    <p class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-gray-200 mt-1">{{ number_format($this->stats['today_sent']) }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                    <i class="fas fa-paper-plane text-blue-600 dark:text-blue-400 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 sm:p-6 border border-gray-100 dark:border-gray-700 card-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Bu Ay Gönderilen</p>
                    <p class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-gray-200 mt-1">{{ number_format($this->stats['month_sent']) }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-line text-purple-600 dark:text-purple-400 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 sm:p-6 border border-gray-100 dark:border-gray-700 card-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Başarı Oranı</p>
                    <p class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-gray-200 mt-1">%{{ number_format($this->stats['success_rate'], 1) }}</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-orange-600 dark:text-orange-400 text-xl"></i>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Action Buttons -->
    <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 mb-6">
        @if($this->canCreate())
        <button wire:click="$set('showModal', true)" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center justify-center">
            <i class="fas fa-plus mr-2"></i>
            Yeni Konfigürasyon
        </button>
        @endif
        
        <!-- Test Connection button removed to focus on SEND MESSAGE & GET-REPORTS only -->
        
        <button wire:click="$toggle('showStats')" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center justify-center">
            <i class="fas fa-chart-bar mr-2"></i>
            {{ $showStats ? 'İstatistikleri Gizle' : 'İstatistikleri Göster' }}
        </button>

        <button wire:click="$toggle('showReports')" class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center justify-center">
            <i class="fas fa-list-alt mr-2"></i>
            {{ $showReports ? 'Raporları Gizle' : 'Raporları Göster' }}
        </button>
    </div>



    <!-- Configurations Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
        <div class="p-4 sm:p-6 border-b border-gray-100 dark:border-gray-700">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-2 sm:space-y-0">
                <h2 class="text-lg sm:text-xl font-bold text-gray-800 dark:text-gray-200">Mesaj Konfigürasyonları</h2>
            </div>
        </div>
        
        <!-- Mobile Card View -->
        <div class="block sm:hidden">
            @forelse($this->configs as $config)
                <div class="p-4 border-b border-gray-200 dark:border-gray-700 last:border-b-0">
                    <div class="flex items-start justify-between">
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-medium text-gray-900 dark:text-white mb-1">{{ $config->campaign_name }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 space-y-1">
                                <div>📱 {{ $config->phone_number }}</div>
                                <div>⏰ {{ $config->hours_before_appointment }} saat önce</div>
                                <div>⚡ {{ $config->send_speed }} saniye aralık</div>
                                <div class="flex items-center">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $config->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400' }}">
                                        {{ $config->is_active ? 'Aktif' : 'Pasif' }}
                                    </span>
                                </div>
                            </div>
                            <div class="flex space-x-3 mt-3">
                                <button wire:click="edit({{ $config->id }})" class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300" title="Düzenle">
                                    <i class="fas fa-edit text-sm"></i>
                                </button>
                                <button wire:click="toggleStatus({{ $config->id }})" class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-900 dark:hover:text-yellow-300" title="{{ $config->is_active ? 'Pasif Yap' : 'Aktif Yap' }}">
                                    <i class="fas fa-{{ $config->is_active ? 'pause' : 'play' }} text-sm"></i>
                                </button>
                                <button wire:click="delete({{ $config->id }})" wire:confirm="Bu konfigürasyonu silmek istediğinizden emin misiniz?" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300" title="Sil">
                                    <i class="fas fa-trash text-sm"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-4 text-center text-gray-500 dark:text-gray-400">
                    Henüz konfigürasyon bulunmuyor.
                </div>
            @endforelse
        </div>
        
        <!-- Desktop Table View -->
        <div class="hidden sm:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Kampanya Adı</th>
                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Telefon</th>
                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Zaman</th>
                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Hız</th>
                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Durum</th>
                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($this->configs as $config)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-4 lg:px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $config->campaign_name }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ Str::limit($config->message_template, 50) }}</div>
                            </td>
                            <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                {{ $config->phone_number }}
                            </td>
                            <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                {{ $config->hours_before_appointment }} saat önce
                            </td>
                            <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                {{ $config->send_speed }}s aralık
                            </td>
                            <td class="px-4 lg:px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $config->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400' }}">
                                    {{ $config->is_active ? 'Aktif' : 'Pasif' }}
                                </span>
                            </td>
                            <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm space-x-1 lg:space-x-2">
                                <button wire:click="edit({{ $config->id }})" class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 p-1" title="Düzenle">
                                    <i class="fas fa-edit text-sm"></i>
                                </button>
                                <button wire:click="toggleStatus({{ $config->id }})" class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-900 dark:hover:text-yellow-300 p-1" title="{{ $config->is_active ? 'Pasif Yap' : 'Aktif Yap' }}">
                                    <i class="fas fa-{{ $config->is_active ? 'pause' : 'play' }} text-sm"></i>
                                </button>
                                <button wire:click="delete({{ $config->id }})" wire:confirm="Bu konfigürasyonu silmek istediğinizden emin misiniz?" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 p-1" title="Sil">
                                    <i class="fas fa-trash text-sm"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 lg:px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                Henüz konfigürasyon bulunmuyor.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Configuration Modal -->
    @if($showModal)
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-hidden">
            <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                    {{ $editingConfig ? 'Konfigürasyon Düzenle' : 'Yeni Konfigürasyon' }}
                </h3>
                <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form wire:submit.prevent="{{ $editingConfig ? 'update' : 'create' }}" class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Campaign Name -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Kampanya Adı</label>
                        <input type="text" wire:model="newConfig.campaign_name" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('newConfig.campaign_name') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- API Token -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">API Token</label>
                        <input type="text" wire:model="newConfig.api_token" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('newConfig.api_token') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                    </div>


                    <!-- Phone Number -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Telefon Numarası</label>
                        <input type="text" wire:model="newConfig.phone_number" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="905xxxxxxxxx">
                        @error('newConfig.phone_number') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Hours Before -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Kaç Saat Önce</label>
                        <select wire:model="newConfig.hours_before_appointment" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Seçiniz</option>
                            <option value="1">1 Saat Önce</option>
                            <option value="2">2 Saat Önce</option>
                            <option value="4">4 Saat Önce</option>
                            <option value="6">6 Saat Önce</option>
                            <option value="12">12 Saat Önce</option>
                            <option value="24">1 Gün Önce</option>
                            <option value="48">2 Gün Önce</option>
                        </select>
                        @error('newConfig.hours_before_appointment') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Send Speed -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Gönderim Hızı (saniye)</label>
                        <select wire:model="newConfig.send_speed" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Seçiniz</option>
                            <option value="1">1 Saniye</option>
                            <option value="2">2 Saniye</option>
                            <option value="3">3 Saniye</option>
                            <option value="5">5 Saniye</option>
                            <option value="10">10 Saniye</option>
                        </select>
                        @error('newConfig.send_speed') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Active Status -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Durum</label>
                        <div class="flex items-center space-x-3">
                            <label class="inline-flex items-center">
                                <input type="checkbox" wire:model="newConfig.is_active" class="form-checkbox h-4 w-4 text-blue-600 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Aktif</span>
                            </label>
                        </div>
                    </div>

                    <!-- Message Template -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Mesaj Şablonu</label>
                        <textarea wire:model="newConfig.message_template" rows="4" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                        @error('newConfig.message_template') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Kullanılabilir değişkenler: {hasta_adi}, {randevu_tarihi}, {randevu_saati}, {doktor_adi}</p>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" wire:click="$set('showModal', false)" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        İptal
                    </button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        {{ $editingConfig ? 'Güncelle' : 'Kaydet' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Message Logs -->
    @if($this->logs->count() > 0)
    <div class="mt-8 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
        <div class="p-4 sm:p-6 border-b border-gray-100 dark:border-gray-700">
            <h2 class="text-lg sm:text-xl font-bold text-gray-800 dark:text-gray-200">Son Gönderilen Mesajlar</h2>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Hasta</th>
                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Telefon</th>
                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Durum</th>
                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Gönderim Tarihi</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($this->logs as $log)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-4 lg:px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $log->patient->name ?? 'Bilinmeyen' }}</div>
                            </td>
                            <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                {{ $log->phone_number }}
                            </td>
                            <td class="px-4 lg:px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $log->status === 'sent' ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' : 
                                       ($log->status === 'failed' ? 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400' : 
                                        'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400') }}">
                                    {{ $log->status === 'sent' ? 'Gönderildi' : ($log->status === 'failed' ? 'Başarısız' : 'Bekliyor') }}
                                </span>
                            </td>
                            <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                {{ $log->sent_at ? $log->sent_at->format('d.m.Y H:i') : '-' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- GET-REPORTS Section -->
    @if($showReports)
    <div class="mt-8 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
        <div class="p-4 sm:p-6 border-b border-gray-100 dark:border-gray-700">
            <h2 class="text-lg sm:text-xl font-bold text-gray-800 dark:text-gray-200">WhatsApp Gönderim Raporları</h2>
        </div>
        <div class="p-4 sm:p-6">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Başlangıç Tarihi</label>
                    <input type="date" wire:model="reportsFilters.start_date" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Bitiş Tarihi</label>
                    <input type="date" wire:model="reportsFilters.end_date" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Durum</label>
                    <select wire:model="reportsFilters.state" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <option value="0">Tümü</option>
                        <option value="1">Başarılı</option>
                        <option value="2">Başarısız</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Sayfa</label>
                    <input type="number" min="1" wire:model="reportsFilters.page" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Adet</label>
                    <input type="number" min="1" max="100" wire:model="reportsFilters.count" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                </div>
            </div>
            <div class="mt-4">
                <button wire:click="fetchReports" class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    Raporları Getir
                </button>
            </div>

            <div class="mt-6">
                @if($reportsError)
                    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle text-red-600 dark:text-red-400 mr-3"></i>
                            <div>
                                <h4 class="text-red-800 dark:text-red-200 font-medium">Hata</h4>
                                <p class="text-red-700 dark:text-red-300 text-sm">{{ $reportsError }}</p>
                            </div>
                        </div>
                    </div>
                @elseif($reports && isset($reports['wp']) && count($reports['wp']) > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900/20">
                                <tr>
                                    <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tarih</th>
                                    <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Telefon</th>
                                    <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider hidden md:table-cell">Kampanya</th>
                                    <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Başarı</th>
                                    <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider hidden md:table-cell">Hız</th>
                                    <th class="px-4 lg:px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">İşlem</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($reports['wp'] as $idx => $row)
                                    <tr>
                                        <td class="px-4 lg:px-6 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ isset($row['send_time']) ? \Carbon\Carbon::parse($row['send_time'])->format('d.m.Y H:i') : (isset($row['CreatedAt']) ? \Carbon\Carbon::parse($row['CreatedAt'])->format('d.m.Y H:i') : '-') }}
                                        </td>
                                        <td class="px-4 lg:px-6 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                            {{ isset($row['phone']) ? ('+'. $row['phone']) : '-' }}
                                        </td>
                                        <td class="px-4 lg:px-6 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300 hidden md:table-cell">
                                            {{ $row['campaign_name'] ?? '-' }}
                                        </td>
                                        <td class="px-4 lg:px-6 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                            {{ ($row['success'] ?? 0) }}/{{ ($row['total_count'] ?? 0) }}
                                        </td>
                                        <td class="px-4 lg:px-6 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300 hidden md:table-cell">
                                            {{ $row['send_speed'] ?? '-' }}
                                        </td>
                                        <td class="px-4 lg:px-6 py-3 whitespace-nowrap text-right text-sm">
                                            <button wire:click="toggleReportDetails({{ $idx }})" class="bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 px-3 py-1 rounded-md">
                                                {{ $expandedReportIndex === $idx ? 'Gizle' : 'Detay' }}
                                            </button>
                                        </td>
                                    </tr>
                                    @if($expandedReportIndex === $idx)
                                        <tr>
                                            <td colspan="6" class="px-4 lg:px-6 py-3 bg-gray-50 dark:bg-gray-900/20">
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-700 dark:text-gray-300">
                                                    <div>
                                                        <p><span class="font-medium">İçerik:</span> {{ $row['content'] ?? '-' }}</p>
                                                        <p class="mt-2"><span class="font-medium">Kullanıcı:</span> {{ $row['user_name'] ?? '-' }}</p>
                                                        <p class="mt-2"><span class="font-medium">IP:</span> {{ $row['ip'] ?? '-' }}</p>
                                                    </div>
                                                    <div>
                                                        <p><span class="font-medium">Durum:</span> {{ $row['state'] ?? '-' }}</p>
                                                        <p class="mt-2"><span class="font-medium">Report ID:</span> {{ $row['report_id'] ?? '-' }}</p>
                                                        <p class="mt-2"><span class="font-medium">Başarı/Başarısız:</span> {{ ($row['success'] ?? 0) }} / {{ ($row['fail'] ?? 0) }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 lg:px-6 py-4 text-center text-gray-500 dark:text-gray-400">Kayıt bulunamadı.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-3 text-xs text-gray-500 dark:text-gray-400">
                            Toplam: {{ $reports['wpTotalCount'] ?? count($reports['wp']) }} kayıt
                        </div>
                    </div>
                @else
                    <p class="text-sm text-gray-500 dark:text-gray-400">Henüz rapor çekilmedi.</p>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>
