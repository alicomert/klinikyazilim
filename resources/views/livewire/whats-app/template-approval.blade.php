<div>
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
            <i class="fas fa-check-circle text-green-500"></i> Şablon Onay Süreci
        </h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">
            WhatsApp şablonlarınızı oluşturun ve Meta onayına gönderin
        </p>
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

    <!-- Action Buttons -->
    <div class="mb-6 flex justify-between items-center">
        <div class="flex space-x-3">
            <button wire:click="$set('showModal', true)" 
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md transition-colors">
                <i class="fas fa-plus mr-2"></i>
                Yeni Şablon Oluştur
            </button>
            <button wire:click="loadTemplates" 
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition-colors">
                <i class="fas fa-sync-alt mr-2"></i>
                Yenile
            </button>
        </div>
    </div>

    <!-- Templates List -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                <i class="fas fa-list text-blue-500"></i> Şablon Listesi
            </h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Şablon Adı
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Kategori
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Dil
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Durum
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Oluşturulma
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            İşlemler
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($templates as $template)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $template->name }}
                                </div>
                                @if($template->header_text)
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        Başlık: {{ Str::limit($template->header_text, 30) }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($template->category === 'MARKETING') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                    @elseif($template->category === 'UTILITY') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                    @else bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200 @endif">
                                    {{ $template->category }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ strtoupper($template->language) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center space-x-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($template->status === 'APPROVED') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                        @elseif($template->status === 'PENDING') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                        @elseif($template->status === 'REJECTED') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                        @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200 @endif">
                                        @if($template->status === 'APPROVED')
                                            <i class="fas fa-check-circle mr-1"></i> Onaylandı
                                        @elseif($template->status === 'PENDING')
                                            <i class="fas fa-clock mr-1"></i> Beklemede
                                        @elseif($template->status === 'REJECTED')
                                            <i class="fas fa-times-circle mr-1"></i> Reddedildi
                                        @else
                                            <i class="fas fa-question-circle mr-1"></i> {{ $template->status }}
                                        @endif
                                    </span>
                                    
                                    @if(isset($approvalStatus[$template->id]))
                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                            ({{ $approvalStatus[$template->id]['checked_at'] }})
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $template->created_at->format('d.m.Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                                @if($template->status === 'PENDING')
                                    <button wire:click="checkApprovalStatus({{ $template->id }})" 
                                            class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-xs transition-colors">
                                        <i class="fas fa-sync-alt mr-1"></i>
                                        Durum Kontrol
                                    </button>
                                @endif
                                
                                @if($this->canDelete($template))
                                    <button wire:click="delete({{ $template->id }})" 
                                            wire:confirm="Bu şablonu silmek istediğinizden emin misiniz?"
                                            class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs transition-colors">
                                        <i class="fas fa-trash mr-1"></i>
                                        Sil
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center">
                                <div class="text-gray-500 dark:text-gray-400">
                                    <i class="fas fa-file-alt text-3xl mb-2"></i>
                                    <p>Henüz şablon bulunmuyor.</p>
                                    <button wire:click="$set('showModal', true)" 
                                            class="text-green-600 hover:text-green-800 text-sm mt-2">
                                        İlk şablonunuzu oluşturun
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create Template Modal -->
    @if($showModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="$set('showModal', false)">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white dark:bg-gray-800" wire:click.stop>
                <div class="mt-3">
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between pb-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            <i class="fas fa-plus text-green-500 mr-2"></i>
                            Yeni Şablon Oluştur
                        </h3>
                        <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>

                    <!-- Modal Content -->
                    <form wire:submit.prevent="create" class="mt-4">
                        <div class="space-y-4">
                            <!-- Konfigürasyon Seçimi -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    WhatsApp Konfigürasyonu *
                                </label>
                                <select wire:model="selectedConfig" 
                                        class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    <option value="">Konfigürasyon seçin...</option>
                                    @foreach($configs as $config)
                                        <option value="{{ $config->id }}">{{ $config->name }}</option>
                                    @endforeach
                                </select>
                                @error('selectedConfig') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Şablon Adı -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Şablon Adı *
                                </label>
                                <input type="text" wire:model="newTemplate.name" 
                                       placeholder="ornek_sablon_adi"
                                       class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    Sadece küçük harf, rakam ve alt çizgi kullanın
                                </p>
                                @error('newTemplate.name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Kategori -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Kategori *
                                </label>
                                <select wire:model="newTemplate.category" 
                                        class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    <option value="MARKETING">Pazarlama</option>
                                    <option value="UTILITY">Faydalı</option>
                                    <option value="AUTHENTICATION">Kimlik Doğrulama</option>
                                </select>
                                @error('newTemplate.category') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Dil -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Dil *
                                </label>
                                <select wire:model="newTemplate.language" 
                                        class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    <option value="tr">Türkçe</option>
                                    <option value="en">İngilizce</option>
                                    <option value="ar">Arapça</option>
                                </select>
                                @error('newTemplate.language') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Başlık Metni -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Başlık Metni (İsteğe bağlı)
                                </label>
                                <input type="text" wire:model="newTemplate.header_text" 
                                       placeholder="Başlık metni..."
                                       maxlength="60"
                                       class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    Maksimum 60 karakter
                                </p>
                                @error('newTemplate.header_text') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Mesaj İçeriği -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Mesaj İçeriği *
                                </label>
                                <textarea wire:model="newTemplate.body_text" 
                                          rows="4"
                                          placeholder="Mesaj içeriğinizi buraya yazın..."
                                          maxlength="1024"
                                          class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"></textarea>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    Maksimum 1024 karakter
                                </p>
                                @error('newTemplate.body_text') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Alt Metin -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Alt Metin (İsteğe bağlı)
                                </label>
                                <input type="text" wire:model="newTemplate.footer_text" 
                                       placeholder="Alt metin..."
                                       maxlength="60"
                                       class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    Maksimum 60 karakter
                                </p>
                                @error('newTemplate.footer_text') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Modal Footer -->
                        <div class="flex items-center justify-end pt-4 border-t border-gray-200 dark:border-gray-700 mt-6 space-x-3">
                            <button type="button" wire:click="$set('showModal', false)" 
                                    class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-md hover:bg-gray-400 dark:hover:bg-gray-500 transition-colors">
                                İptal
                            </button>
                            <button type="submit" 
                                    class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 transition-colors">
                                <i class="fas fa-paper-plane mr-2"></i>
                                Oluştur ve Onaya Gönder
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Template Approval Guidelines -->
    <div class="mt-8 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6">
        <h4 class="font-semibold text-blue-800 dark:text-blue-200 flex items-center mb-4">
            <i class="fas fa-info-circle mr-2"></i>
            Şablon Onay Kuralları
        </h4>
        <div class="grid md:grid-cols-2 gap-4 text-sm text-blue-700 dark:text-blue-300">
            <div>
                <h5 class="font-medium mb-2">Genel Kurallar:</h5>
                <ul class="space-y-1 list-disc list-inside">
                    <li>Şablon adları benzersiz olmalı</li>
                    <li>Spam içerik kullanmayın</li>
                    <li>Açık ve anlaşılır dil kullanın</li>
                    <li>Yanıltıcı bilgi vermekten kaçının</li>
                </ul>
            </div>
            <div>
                <h5 class="font-medium mb-2">Onay Süreci:</h5>
                <ul class="space-y-1 list-disc list-inside">
                    <li>Onay süresi 1-7 iş günü</li>
                    <li>Reddedilen şablonlar düzenlenebilir</li>
                    <li>Onaylanan şablonlar değiştirilemez</li>
                    <li>Durum kontrolü manuel yapılabilir</li>
                </ul>
            </div>
        </div>
    </div>
</div>