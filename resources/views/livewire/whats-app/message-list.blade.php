<div>
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">WhatsApp Mesajları</h1>
            <p class="text-gray-600 mt-1">Gönderilen ve alınan WhatsApp mesajlarını yönetin</p>
        </div>
        <div class="flex space-x-3">
            <button wire:click="refreshMessages" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2">
                <i class="fas fa-sync-alt"></i>
                <span>Yenile</span>
            </button>
            <button wire:click="$set('showModal', true)" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2">
                <i class="fas fa-plus"></i>
                <span>Yeni Mesaj</span>
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

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Search -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Arama</label>
                <input type="text" wire:model.live="search" placeholder="İsim, telefon veya mesaj içeriği..." 
                       class="w-full border border-gray-300 rounded-md px-3 py-2">
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Durum</label>
                <select wire:model.live="statusFilter" class="w-full border border-gray-300 rounded-md px-3 py-2">
                    <option value="">Tüm Durumlar</option>
                    @foreach($this->statusOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Date From -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Başlangıç Tarihi</label>
                <input type="date" wire:model.live="dateFrom" class="w-full border border-gray-300 rounded-md px-3 py-2">
            </div>

            <!-- Date To -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Bitiş Tarihi</label>
                <input type="date" wire:model.live="dateTo" class="w-full border border-gray-300 rounded-md px-3 py-2">
            </div>

            <!-- Config Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Konfigürasyon</label>
                <select wire:model.live="configFilter" class="w-full border border-gray-300 rounded-md px-3 py-2">
                    <option value="">Tüm Konfigürasyonlar</option>
                    @foreach($configs as $config)
                        <option value="{{ $config->id }}">{{ $config->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Template Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Şablon</label>
                <select wire:model.live="templateFilter" class="w-full border border-gray-300 rounded-md px-3 py-2">
                    <option value="">Tüm Şablonlar</option>
                    @foreach($templates as $template)
                        <option value="{{ $template->id }}">{{ $template->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Reset Button -->
            <div class="flex items-end">
                <button wire:click="resetFilters" class="w-full bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md">
                    Filtreleri Temizle
                </button>
            </div>
        </div>
    </div>

    <!-- Messages Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alıcı</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mesaj</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durum</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Şablon</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarih</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($messagesList as $message)
                        <tr wire:key="message-{{ $message->id }}" class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $message->recipient_name }}</div>
                                <div class="text-sm text-gray-500">{{ $message->recipient_phone }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 max-w-xs truncate">{{ $message->message_content }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($message->status === 'sent') bg-blue-100 text-blue-800
                                    @elseif($message->status === 'delivered') bg-green-100 text-green-800
                                    @elseif($message->status === 'read') bg-green-100 text-green-800
                                    @elseif($message->status === 'failed') bg-red-100 text-red-800
                                    @else bg-yellow-100 text-yellow-800
                                    @endif">
                                    {{ $message->status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $message->template ? $message->template->name : 'Manuel' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $message->created_at->format('d.m.Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                                <button wire:click="showDetail({{ $message->id }})" 
                                        class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm">
                                    <i class="fas fa-eye"></i>
                                </button>
                                
                                @if($this->canEdit($message))
                                    <button wire:click="edit({{ $message->id }})" 
                                            class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-sm">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                @endif
                                
                                @if($this->canDelete($message))
                                    <button wire:click="delete({{ $message->id }})" 
                                            wire:confirm="Bu mesajı silmek istediğinizden emin misiniz?"
                                            class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                Henüz mesaj bulunmuyor.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create/Edit Modal -->
    @if($showModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">
                        {{ $editingMessage ? 'Mesaj Düzenle' : 'Yeni Mesaj Oluştur' }}
                    </h3>
                    <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <form wire:submit.prevent="{{ $editingMessage ? 'update' : 'create' }}">
                    <div class="space-y-4">
                        <!-- Config Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">WhatsApp Konfigürasyonu *</label>
                            <select wire:model="newMessage.whatsapp_config_id" class="w-full border border-gray-300 rounded-md px-3 py-2">
                                <option value="">Konfigürasyon seçin...</option>
                                @foreach($configs as $config)
                                    <option value="{{ $config->id }}">{{ $config->name }}</option>
                                @endforeach
                            </select>
                            @error('newMessage.whatsapp_config_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Template Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Mesaj Şablonu (Opsiyonel)</label>
                            <select wire:model="newMessage.whatsapp_template_id" class="w-full border border-gray-300 rounded-md px-3 py-2">
                                <option value="">Şablon seçin (manuel mesaj için boş bırakın)...</option>
                                @foreach($templates as $template)
                                    <option value="{{ $template->id }}">{{ $template->name }}</option>
                                @endforeach
                            </select>
                            @error('newMessage.whatsapp_template_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Recipient Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Alıcı Adı *</label>
                            <input type="text" wire:model="newMessage.recipient_name" 
                                   class="w-full border border-gray-300 rounded-md px-3 py-2"
                                   placeholder="Alıcının adını girin">
                            @error('newMessage.recipient_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Recipient Phone -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Alıcı Telefon *</label>
                            <input type="text" wire:model="newMessage.recipient_phone" 
                                   class="w-full border border-gray-300 rounded-md px-3 py-2"
                                   placeholder="90xxxxxxxxxx formatında">
                            @error('newMessage.recipient_phone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Message Content -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Mesaj İçeriği *</label>
                            <textarea wire:model="newMessage.message_content" rows="4"
                                      class="w-full border border-gray-300 rounded-md px-3 py-2"
                                      placeholder="Mesaj içeriğini girin"></textarea>
                            @error('newMessage.message_content') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" wire:click="$set('showModal', false)" 
                                class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md">
                            İptal
                        </button>
                        <button type="submit" 
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                            {{ $editingMessage ? 'Güncelle' : 'Oluştur' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Detail Modal -->
    @if($showDetailModal && $selectedMessage)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Mesaj Detayları</h3>
                    <button wire:click="$set('showDetailModal', false)" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Alıcı Adı</label>
                            <p class="text-sm text-gray-900">{{ $selectedMessage->recipient_name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Telefon</label>
                            <p class="text-sm text-gray-900">{{ $selectedMessage->recipient_phone }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Durum</label>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($selectedMessage->status === 'sent') bg-blue-100 text-blue-800
                                @elseif($selectedMessage->status === 'delivered') bg-green-100 text-green-800
                                @elseif($selectedMessage->status === 'read') bg-green-100 text-green-800
                                @elseif($selectedMessage->status === 'failed') bg-red-100 text-red-800
                                @else bg-yellow-100 text-yellow-800
                                @endif">
                                {{ $selectedMessage->status_label }}
                            </span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Konfigürasyon</label>
                            <p class="text-sm text-gray-900">{{ $selectedMessage->config->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Şablon</label>
                            <p class="text-sm text-gray-900">{{ $selectedMessage->template->name ?? 'Manuel' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Oluşturulma Tarihi</label>
                            <p class="text-sm text-gray-900">{{ $selectedMessage->created_at->format('d.m.Y H:i:s') }}</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Mesaj İçeriği</label>
                        <div class="bg-gray-50 p-3 rounded-md">
                            <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $selectedMessage->message_content }}</p>
                        </div>
                    </div>

                    @if($selectedMessage->error_message)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Hata Mesajı</label>
                            <div class="bg-red-50 p-3 rounded-md">
                                <p class="text-sm text-red-900">{{ $selectedMessage->error_message }}</p>
                            </div>
                        </div>
                    @endif

                    @if($selectedMessage->template_variables)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Şablon Değişkenleri</label>
                            <div class="bg-gray-50 p-3 rounded-md">
                                <pre class="text-sm text-gray-900">{{ json_encode($selectedMessage->template_variables, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="flex justify-end mt-6">
                    <button wire:click="$set('showDetailModal', false)" 
                            class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md">
                        Kapat
                    </button>
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
            // Component specific data
        }
    }
}
</script>