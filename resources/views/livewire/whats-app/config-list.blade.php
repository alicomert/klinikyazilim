<div>
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">WhatsApp Konfigürasyonu</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">WhatsApp Business API ayarlarınızı yönetin</p>
        </div>
        @if($this->canCreate())
            <button wire:click="$set('showModal', true)" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                <i class="fas fa-plus"></i>
                <span>Yeni Konfigürasyon</span>
            </button>
        @endif
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

    <!-- Configuration List -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        @if(count($configs) > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Konfigürasyon Adı
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Telefon Numarası
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Durum
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Son Test
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Oluşturan
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                İşlemler
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($configs as $config)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $config->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $config->description }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $config->phone_number }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $config->statusColor }}">
                                        {{ $config->statusLabel }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($config->last_test_at)
                                        {{ $config->last_test_at->diffForHumans() }}
                                        @if($config->last_test_success)
                                            <i class="fas fa-check-circle text-green-500 ml-1"></i>
                                        @else
                                            <i class="fas fa-times-circle text-red-500 ml-1"></i>
                                        @endif
                                    @else
                                        <span class="text-gray-400">Test edilmedi</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $config->user->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                                    <!-- Test Button -->
                                    <button wire:click="testConnection({{ $config->id }})" 
                                            class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm">
                                        <i class="fas fa-vial"></i> Test
                                    </button>

                                    <!-- Toggle Active -->
                                    @if($this->canEdit($config))
                                        <button wire:click="toggleActive({{ $config->id }})" 
                                                class="px-3 py-1 rounded text-sm {{ $config->is_active ? 'bg-yellow-500 hover:bg-yellow-600 text-white' : 'bg-green-500 hover:bg-green-600 text-white' }}">
                                            {{ $config->is_active ? 'Pasifleştir' : 'Aktifleştir' }}
                                        </button>
                                    @endif

                                    <!-- Edit Button -->
                                    @if($this->canEdit($config))
                                        <button wire:click="edit({{ $config->id }})" 
                                                class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-sm">
                                            <i class="fas fa-edit"></i> Düzenle
                                        </button>
                                    @endif

                                    <!-- Delete Button -->
                                    @if($this->canDelete($config))
                                        <button wire:click="delete({{ $config->id }})" 
                                                wire:confirm="Bu konfigürasyonu silmek istediğinizden emin misiniz?"
                                                class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm">
                                            <i class="fas fa-trash"></i> Sil
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-cog text-gray-400 text-6xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Henüz konfigürasyon yok</h3>
                <p class="text-gray-500 mb-4">WhatsApp Business API kullanmak için bir konfigürasyon oluşturun.</p>
                @if($this->canCreate())
                    <button wire:click="$set('showModal', true)" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                        İlk Konfigürasyonu Oluştur
                    </button>
                @endif
            </div>
        @endif
    </div>

    <!-- Modal Form -->
    @if($showModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click.self="$set('showModal', false)">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        {{ $editingConfig ? 'Konfigürasyon Düzenle' : 'Yeni Konfigürasyon Ekle' }}
                    </h3>
                    
                    <form wire:submit.prevent="{{ $editingConfig ? 'update' : 'create' }}">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Name -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Konfigürasyon Adı *</label>
                                <input type="text" wire:model="newConfig.name" 
                                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
                                       placeholder="Örn: Ana WhatsApp Hesabı">
                                @error('newConfig.name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Description -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Açıklama</label>
                                <textarea wire:model="newConfig.description" rows="2"
                                          class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
                                          placeholder="Bu konfigürasyonun kullanım amacını açıklayın"></textarea>
                                @error('newConfig.description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Phone Number -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Telefon Numarası *</label>
                                <input type="text" wire:model="newConfig.phone_number" 
                                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
                                       placeholder="905551234567">
                                @error('newConfig.phone_number') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Phone Number ID -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number ID *</label>
                                <input type="text" wire:model="newConfig.phone_number_id" 
                                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
                                       placeholder="123456789012345">
                                @error('newConfig.phone_number_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Business Account ID -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Business Account ID *</label>
                                <input type="text" wire:model="newConfig.business_account_id" 
                                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
                                       placeholder="123456789012345">
                                @error('newConfig.business_account_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Access Token -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Access Token *</label>
                                <input type="password" wire:model="newConfig.access_token" 
                                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
                                       placeholder="EAAxxxxxxxxxx">
                                @error('newConfig.access_token') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Webhook URL -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Webhook URL</label>
                                <input type="url" wire:model="newConfig.webhook_url" 
                                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
                                       placeholder="https://yourdomain.com/webhook/whatsapp">
                                @error('newConfig.webhook_url') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Webhook Token -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Webhook Token</label>
                                <input type="text" wire:model="newConfig.webhook_token" 
                                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
                                       placeholder="your_webhook_token">
                                @error('newConfig.webhook_token') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Is Active -->
                            <div class="flex items-center">
                                <input type="checkbox" wire:model="newConfig.is_active" id="is_active"
                                       class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                                <label for="is_active" class="ml-2 block text-sm text-gray-900">
                                    Aktif konfigürasyon
                                </label>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3 mt-6 pt-4 border-t">
                            <button type="button" wire:click="$set('showModal', false)" 
                                    class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                                İptal
                            </button>
                            <button type="submit" 
                                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                                {{ $editingConfig ? 'Güncelle' : 'Kaydet' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Test Modal -->
    @if($showTestModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-vial text-blue-500"></i> Bağlantı Testi
                    </h3>
                    
                    <form wire:submit.prevent="sendTestMessage">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Test Telefon Numarası *</label>
                                <input type="text" wire:model="testPhone" 
                                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="905551234567">
                                @error('testPhone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Test Mesajı</label>
                                <textarea wire:model="testMessage" rows="3"
                                          class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                          placeholder="Bu bir test mesajıdır."></textarea>
                                @error('testMessage') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3 mt-6 pt-4 border-t">
                            <button type="button" wire:click="$set('showTestModal', false)" 
                                    class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                                İptal
                            </button>
                            <button type="submit" 
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                                <i class="fas fa-paper-plane"></i> Test Mesajı Gönder
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
