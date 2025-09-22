<div>
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">WhatsApp Dashboard</h1>
            <p class="text-gray-600 mt-1">WhatsApp otomasyon sistemi genel bakış</p>
        </div>
        <div class="flex space-x-3">
            <button wire:click="refreshStats" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2">
                <i class="fas fa-sync-alt"></i>
                <span>Yenile</span>
            </button>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <!-- WhatsApp Setup Guide -->
    @if(!$hasPhoneId)
        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-6 mb-6">
            <div class="flex items-start space-x-4">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-yellow-600 dark:text-yellow-400 text-2xl"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-yellow-800 dark:text-yellow-200 mb-2">
                        WhatsApp Business API Kurulumu Gerekli
                    </h3>
                    <p class="text-yellow-700 dark:text-yellow-300 mb-4">
                        WhatsApp entegrasyonunu kullanabilmek için öncelikle WhatsApp Business API hesabınızı kurmanız ve Phone ID almanız gerekmektedir.
                    </p>
                    
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-4 mb-4">
                        <h4 class="font-semibold text-gray-900 dark:text-white mb-3">Kurulum Adımları:</h4>
                        <ol class="list-decimal list-inside space-y-2 text-sm text-gray-700 dark:text-gray-300">
                            <li><strong>Facebook Business Manager Hesabı:</strong> Doğrulanmış bir Facebook Business Manager hesabınız olmalı</li>
                            <li><strong>WhatsApp Business Solution Provider (BSP) Seçimi:</strong> Resmi bir BSP ile çalışmanız gerekiyor</li>
                            <li><strong>İş Belgelerinin Hazırlanması:</strong> Ticaret sicil belgesi, vergi levhası gibi belgeler</li>
                            <li><strong>Telefon Numarası Doğrulama:</strong> İş için kullanılacak telefon numarasının doğrulanması</li>
                            <li><strong>WABA (WhatsApp Business Account) Oluşturma:</strong> BSP aracılığıyla hesap oluşturma</li>
                            <li><strong>Phone ID ve Access Token Alma:</strong> API entegrasyonu için gerekli kimlik bilgileri</li>
                        </ol>
                    </div>

                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-4">
                        <h4 class="font-semibold text-blue-900 dark:text-blue-200 mb-2">
                            <i class="fas fa-info-circle mr-2"></i>Önemli Bilgiler:
                        </h4>
                        <ul class="list-disc list-inside space-y-1 text-sm text-blue-800 dark:text-blue-300">
                            <li>Onay süreci 1-7 iş günü sürebilir</li>
                            <li>Şablon mesajları Facebook tarafından onaylanmalıdır</li>
                            <li>İlk başta günlük 1.000 mesaj limiti vardır</li>
                            <li>Kaliteli mesajlaşma ile limit artırılabilir</li>
                        </ul>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <a href="https://business.facebook.com/" target="_blank" 
                           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            <i class="fas fa-external-link-alt mr-2"></i>Facebook Business Manager
                        </a>
                        <a href="https://developers.facebook.com/docs/whatsapp/business-management-api/get-started/" target="_blank"
                           class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            <i class="fas fa-book mr-2"></i>WhatsApp API Dokümantasyonu
                        </a>
                        <button wire:click="$set('showSetupModal', true)"
                                class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            <i class="fas fa-cog mr-2"></i>Kurulum Rehberi
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Configs -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Toplam Konfigürasyon</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_configs'] ?? 0 }}</p>
                    <p class="text-xs text-green-600 mt-1">
                        <i class="fas fa-check-circle"></i> {{ $stats['active_configs'] ?? 0 }} aktif
                    </p>
                </div>
                <div class="bg-blue-100 dark:bg-blue-900 p-3 rounded-full">
                    <i class="fas fa-cog text-blue-600 dark:text-blue-400 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Templates -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Toplam Şablon</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_templates'] ?? 0 }}</p>
                    <p class="text-xs text-green-600 mt-1">
                        <i class="fas fa-check-circle"></i> {{ $stats['approved_templates'] ?? 0 }} onaylı
                    </p>
                </div>
                <div class="bg-green-100 dark:bg-green-900 p-3 rounded-full">
                    <i class="fas fa-file-alt text-green-600 dark:text-green-400 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Messages Today -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Bugünkü Mesajlar</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['messages_today'] ?? 0 }}</p>
                    <p class="text-xs text-blue-600 mt-1">
                        <i class="fas fa-calendar"></i> Bu ay: {{ $stats['messages_this_month'] ?? 0 }}
                    </p>
                </div>
                <div class="bg-yellow-100 dark:bg-yellow-900 p-3 rounded-full">
                    <i class="fas fa-paper-plane text-yellow-600 dark:text-yellow-400 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Success Rate -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Başarı Oranı</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">%{{ $stats['success_rate'] ?? 0 }}</p>
                    @if($this->canApproveTemplates() && ($stats['pending_approvals'] ?? 0) > 0)
                        <p class="text-xs text-orange-600 mt-1">
                            <i class="fas fa-clock"></i> {{ $stats['pending_approvals'] }} onay bekliyor
                        </p>
                    @endif
                </div>
                <div class="bg-purple-100 dark:bg-purple-900 p-3 rounded-full">
                    <i class="fas fa-chart-line text-purple-600 dark:text-purple-400 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Recent Data -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Message Statistics Chart -->
        <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-chart-bar text-blue-500"></i> Son 7 Günün Mesaj İstatistikleri
            </h3>
            <div class="h-64 flex items-end justify-between space-x-2">
                @foreach($messageStats as $stat)
                    <div class="flex flex-col items-center flex-1">
                        <div class="bg-blue-500 rounded-t w-full flex items-end justify-center text-white text-xs font-medium" 
                             style="height: {{ $stat['count'] > 0 ? max(20, ($stat['count'] / max(array_column($messageStats, 'count'))) * 200) : 20 }}px;">
                            @if($stat['count'] > 0)
                                {{ $stat['count'] }}
                            @endif
                        </div>
                        <div class="text-xs text-gray-600 mt-2">{{ $stat['date'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-bolt text-yellow-500"></i> Hızlı İşlemler
            </h3>
            <div class="space-y-3">
                @if($this->canManageConfigs())
                    <a href="{{ route('whatsapp.configs') }}" class="flex items-center p-3 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors">
                        <div class="bg-blue-500 p-2 rounded-lg mr-3">
                            <i class="fas fa-cog text-white"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Konfigürasyon Yönetimi</p>
                            <p class="text-sm text-gray-600">WhatsApp API ayarlarını yönet</p>
                        </div>
                    </a>
                @endif

                @if($this->canManageTemplates())
                    <a href="{{ route('whatsapp.templates') }}" class="flex items-center p-3 bg-green-50 hover:bg-green-100 rounded-lg transition-colors">
                        <div class="bg-green-500 p-2 rounded-lg mr-3">
                            <i class="fas fa-file-alt text-white"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Şablon Yönetimi</p>
                            <p class="text-sm text-gray-600">Mesaj şablonlarını oluştur ve düzenle</p>
                        </div>
                    </a>
                @endif

                <a href="{{ route('whatsapp.messages') }}" class="flex items-center p-3 bg-purple-50 hover:bg-purple-100 rounded-lg transition-colors">
                    <div class="bg-purple-500 p-2 rounded-lg mr-3">
                        <i class="fas fa-paper-plane text-white"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">Mesaj Gönder</p>
                        <p class="text-sm text-gray-600">Yeni WhatsApp mesajı gönder</p>
                    </div>
                </a>

                <a href="{{ route('whatsapp.reports') }}" class="flex items-center p-3 bg-orange-50 hover:bg-orange-100 rounded-lg transition-colors">
                    <div class="bg-orange-500 p-2 rounded-lg mr-3">
                        <i class="fas fa-chart-pie text-white"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">Raporlar</p>
                        <p class="text-sm text-gray-600">Detaylı mesaj raporlarını görüntüle</p>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Data -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Active Configurations -->
        <div class="bg-white rounded-lg shadow-md border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-cog text-blue-500"></i> Aktif Konfigürasyonlar
                </h3>
            </div>
            <div class="p-6">
                @forelse($activeConfigs as $config)
                    <div class="flex items-center justify-between py-3 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                        <div class="flex-1">
                            <p class="font-medium text-gray-900">{{ $config->name }}</p>
                            <p class="text-sm text-gray-600">{{ $config->phone_number }}</p>
                            <p class="text-xs text-gray-500">{{ $config->updated_at->diffForHumans() }}</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-circle text-green-500 mr-1" style="font-size: 6px;"></i>
                                Aktif
                            </span>
                            @if($this->canManageConfigs())
                                <button wire:click="testConnection({{ $config->id }})" 
                                        class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs">
                                    Test
                                </button>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <i class="fas fa-cog text-gray-400 text-3xl mb-2"></i>
                        <p class="text-gray-500">Aktif konfigürasyon yok</p>
                        @if($this->canManageConfigs())
                            <a href="{{ route('whatsapp.configs') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                                Konfigürasyon oluştur
                            </a>
                        @endif
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Templates -->
        <div class="bg-white rounded-lg shadow-md border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-file-alt text-green-500"></i> Son Şablonlar
                </h3>
            </div>
            <div class="p-6">
                @forelse($recentTemplates as $template)
                    <div class="py-3 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <p class="font-medium text-gray-900">{{ $template->name }}</p>
                                <p class="text-sm text-gray-600 mt-1">{{ Str::limit($template->content, 60) }}</p>
                                <div class="flex items-center space-x-2 mt-2">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $template->categoryColor ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ $template->categoryLabel }}
                                    </span>
                                    @if($template->is_approved)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check mr-1"></i> Onaylı
                                        </span>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-500 mt-1">{{ $template->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <i class="fas fa-file-alt text-gray-400 text-3xl mb-2"></i>
                        <p class="text-gray-500">Henüz şablon yok</p>
                        @if($this->canManageTemplates())
                            <a href="{{ route('whatsapp.templates') }}" class="text-green-600 hover:text-green-800 text-sm">
                                Şablon oluştur
                            </a>
                        @endif
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Messages -->
        <div class="bg-white rounded-lg shadow-md border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-paper-plane text-purple-500"></i> Son Mesajlar
                </h3>
            </div>
            <div class="p-6">
                @forelse($recentMessages as $message)
                    <div class="py-3 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <p class="font-medium text-gray-900">{{ $message->recipient_name ?? $message->recipient_phone }}</p>
                                <p class="text-sm text-gray-600 mt-1">{{ Str::limit($message->content, 60) }}</p>
                                <div class="flex items-center space-x-2 mt-2">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $message->statusColor }}">
                                        {{ $message->statusLabel }}
                                    </span>
                                    @if($message->config)
                                        <span class="text-xs text-gray-500">{{ $message->config->name }}</span>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-500 mt-1">{{ $message->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <i class="fas fa-paper-plane text-gray-400 text-3xl mb-2"></i>
                        <p class="text-gray-500">Henüz mesaj yok</p>
                        <a href="{{ route('whatsapp.messages') }}" class="text-purple-600 hover:text-purple-800 text-sm">
                            İlk mesajı gönder
                        </a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- System Status -->
    @if($this->canViewAllStats())
        <div class="mt-8 bg-white rounded-lg shadow-md p-6 border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-server text-indigo-500"></i> Sistem Durumu
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="flex items-center p-4 bg-green-50 rounded-lg">
                    <div class="bg-green-500 p-2 rounded-full mr-3">
                        <i class="fas fa-check text-white"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">API Bağlantısı</p>
                        <p class="text-sm text-green-600">Çalışıyor</p>
                    </div>
                </div>
                
                <div class="flex items-center p-4 bg-blue-50 rounded-lg">
                    <div class="bg-blue-500 p-2 rounded-full mr-3">
                        <i class="fas fa-database text-white"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">Veritabanı</p>
                        <p class="text-sm text-blue-600">Bağlı</p>
                    </div>
                </div>
                
                <div class="flex items-center p-4 bg-yellow-50 rounded-lg">
                    <div class="bg-yellow-500 p-2 rounded-full mr-3">
                        <i class="fas fa-clock text-white"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">Kuyruk İşlemi</p>
                        <p class="text-sm text-yellow-600">Aktif</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Phone ID Setup Modal -->
    @if($showSetupModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="$set('showSetupModal', false)">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white dark:bg-gray-800" wire:click.stop>
                <div class="mt-3">
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between pb-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            <i class="fas fa-phone text-green-500 mr-2"></i>
                            WhatsApp Phone ID Kurulum Rehberi
                        </h3>
                        <button wire:click="$set('showSetupModal', false)" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>

                    <!-- Modal Content -->
                    <div class="mt-4 max-h-96 overflow-y-auto">
                        <div class="space-y-6">
                            <!-- Adım 1 -->
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0 w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-semibold">1</div>
                                <div>
                                    <h4 class="font-semibold text-gray-900 dark:text-white">Facebook Business Manager Hesabı</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                        Facebook Business Manager hesabınızı oluşturun ve işletme bilgilerinizi doğrulayın.
                                    </p>
                                    <a href="https://business.facebook.com" target="_blank" class="inline-flex items-center text-blue-600 hover:text-blue-800 text-sm mt-2">
                                        <i class="fas fa-external-link-alt mr-1"></i>
                                        Business Manager'a Git
                                    </a>
                                </div>
                            </div>

                            <!-- Adım 2 -->
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0 w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-semibold">2</div>
                                <div>
                                    <h4 class="font-semibold text-gray-900 dark:text-white">WhatsApp Business Solution Provider (BSP) Seçimi</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                        Onaylı bir BSP partneri seçin (Twilio, MessageBird, 360Dialog vb.)
                                    </p>
                                </div>
                            </div>

                            <!-- Adım 3 -->
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0 w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-semibold">3</div>
                                <div>
                                    <h4 class="font-semibold text-gray-900 dark:text-white">Gerekli Belgeler</h4>
                                    <ul class="text-sm text-gray-600 dark:text-gray-400 mt-1 list-disc list-inside space-y-1">
                                        <li>İşletme kayıt belgesi</li>
                                        <li>Vergi kimlik numarası</li>
                                        <li>İşletme adresi belgesi</li>
                                        <li>Yetkili kişi kimlik belgesi</li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Adım 4 -->
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0 w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-semibold">4</div>
                                <div>
                                    <h4 class="font-semibold text-gray-900 dark:text-white">Telefon Numarası Doğrulama</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                        İşletmenize ait telefon numarasını doğrulayın. Bu numara daha önce WhatsApp'ta kullanılmamalı.
                                    </p>
                                </div>
                            </div>

                            <!-- Adım 5 -->
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0 w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-semibold">5</div>
                                <div>
                                    <h4 class="font-semibold text-gray-900 dark:text-white">WABA (WhatsApp Business Account) Oluşturma</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                        BSP üzerinden WABA hesabınızı oluşturun ve Meta tarafından onaylanmasını bekleyin.
                                    </p>
                                </div>
                            </div>

                            <!-- Adım 6 -->
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0 w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center text-sm font-semibold">6</div>
                                <div>
                                    <h4 class="font-semibold text-gray-900 dark:text-white">Phone ID Alma</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                        WABA onaylandıktan sonra Phone ID'nizi alabilir ve sistemimize ekleyebilirsiniz.
                                    </p>
                                </div>
                            </div>

                            <!-- Önemli Bilgiler -->
                            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                                <h4 class="font-semibold text-yellow-800 dark:text-yellow-200 flex items-center">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    Önemli Bilgiler
                                </h4>
                                <ul class="text-sm text-yellow-700 dark:text-yellow-300 mt-2 space-y-1">
                                    <li>• Onay süreci 1-7 iş günü sürebilir</li>
                                    <li>• Şablon onayları ayrıca gereklidir</li>
                                    <li>• Günlük mesaj limitleri vardır</li>
                                    <li>• İşletme doğrulaması zorunludur</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700 mt-6">
                        <div class="flex space-x-3">
                            <a href="https://developers.facebook.com/docs/whatsapp/cloud-api/get-started" target="_blank" 
                               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition-colors">
                                <i class="fas fa-book mr-2"></i>
                                Resmi Dokümantasyon
                            </a>
                            <button wire:click="$set('showSetupModal', true)" 
                                    class="inline-flex items-center px-4 py-2 bg-orange-600 text-white text-sm font-medium rounded-md hover:bg-orange-700 transition-colors">
                                <i class="fas fa-question-circle mr-2"></i>
                                Kurulum Rehberi
                            </button>
                            <a href="{{ route('whatsapp.template-approval') }}" 
                               class="inline-flex items-center px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-md hover:bg-purple-700 transition-colors">
                                <i class="fas fa-check-circle mr-2"></i>
                                Şablon Onayı
                            </a>
                            <a href="{{ route('whatsapp.configs') }}" 
                               class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 transition-colors">
                                <i class="fas fa-cog mr-2"></i>
                                Konfigürasyon Ekle
                            </a>
                        </div>
                        <button wire:click="$set('showSetupModal', false)" 
                                class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-md hover:bg-gray-400 dark:hover:bg-gray-500 transition-colors">
                            Kapat
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
// Define appData function for Livewire pages
if (typeof window.appData === 'undefined') {
    window.appData = function() {
        return {
            sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true',
            currentPage: 'whatsapp',
            loading: false,
            
            init() {
                // Initialize router
                this.initRouter();
            },
            
            toggleSidebar() {
                this.sidebarCollapsed = !this.sidebarCollapsed;
                localStorage.setItem('sidebarCollapsed', this.sidebarCollapsed);
            },
            
            initRouter() {
                // Set current page based on URL
                const path = window.location.pathname;
                if (path.includes('/whatsapp')) {
                    this.currentPage = 'whatsapp';
                } else if (path.includes('/patients')) {
                    this.currentPage = 'patients';
                } else if (path.includes('/operations')) {
                    this.currentPage = 'operations';
                } else if (path.includes('/doctor-panel')) {
                    this.currentPage = 'doctor-panel';
                } else if (path.includes('/payment-reports')) {
                    this.currentPage = 'payment-reports';
                } else {
                    this.currentPage = 'dashboard';
                }
            }
        }
    }
}
</script>
