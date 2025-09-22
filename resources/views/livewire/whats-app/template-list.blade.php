<div>
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">WhatsApp Şablonları</h1>
            <p class="text-gray-600 mt-1">Mesaj şablonlarınızı oluşturun ve yönetin</p>
        </div>
        @if($this->canCreate())
            <button wire:click="$set('showModal', true)" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2">
                <i class="fas fa-plus"></i>
                <span>Yeni Şablon</span>
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

    <!-- Category Filter -->
    <div class="mb-6 flex flex-wrap gap-2">
        <span class="text-sm font-medium text-gray-700">Kategoriler:</span>
        @foreach($this->getCategoryOptions() as $key => $label)
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                {{ $label }}
            </span>
        @endforeach
    </div>

    <!-- Templates Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($templates as $template)
            <div class="bg-white rounded-lg shadow-md border border-gray-200 hover:shadow-lg transition-shadow">
                <!-- Template Header -->
                <div class="p-4 border-b border-gray-200">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900">{{ $template->name }}</h3>
                            <p class="text-sm text-gray-500 mt-1">{{ $template->description }}</p>
                        </div>
                        <div class="flex space-x-1 ml-2">
                            <!-- Status Badges -->
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $template->statusColor }}">
                                {{ $template->statusLabel }}
                            </span>
                            @if($template->is_approved)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i> Onaylı
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Category -->
                    <div class="mt-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $template->categoryColor ?? 'bg-gray-100 text-gray-800' }}">
                            {{ $template->categoryLabel }}
                        </span>
                    </div>
                </div>

                <!-- Template Content Preview -->
                <div class="p-4">
                    <div class="bg-gray-50 rounded-lg p-3 mb-3">
                        <p class="text-sm text-gray-700 line-clamp-3">
                            {{ Str::limit($template->content, 120) }}
                        </p>
                    </div>
                    
                    <!-- Variables -->
                    @if($template->variables)
                        <div class="mb-3">
                            <span class="text-xs font-medium text-gray-500">Değişkenler:</span>
                            <div class="flex flex-wrap gap-1 mt-1">
                                @foreach(explode(',', $template->variables) as $variable)
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs bg-blue-100 text-blue-800">
                                        {{ trim($variable) }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Variable Count -->
                    <div class="text-xs text-gray-500 mb-3">
                        <i class="fas fa-code"></i> {{ $template->variableCount }} değişken
                    </div>

                    <!-- Creator Info -->
                    <div class="text-xs text-gray-500 mb-4">
                        <i class="fas fa-user"></i> {{ $template->user->name }} • {{ $template->created_at->diffForHumans() }}
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="px-4 py-3 bg-gray-50 border-t border-gray-200 flex justify-between items-center">
                    <div class="flex space-x-2">
                        <!-- Preview Button -->
                        <button wire:click="preview({{ $template->id }})" 
                                class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-1 rounded text-sm">
                            <i class="fas fa-eye"></i> Önizle
                        </button>

                        <!-- Edit Button -->
                        @if($this->canEdit($template))
                            <button wire:click="edit({{ $template->id }})" 
                                    class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-sm">
                                <i class="fas fa-edit"></i> Düzenle
                            </button>
                        @endif
                    </div>

                    <div class="flex space-x-2">
                        <!-- Toggle Active -->
                        @if($this->canEdit($template))
                            <button wire:click="toggleActive({{ $template->id }})" 
                                    class="px-3 py-1 rounded text-sm {{ $template->is_active ? 'bg-yellow-500 hover:bg-yellow-600 text-white' : 'bg-green-500 hover:bg-green-600 text-white' }}">
                                {{ $template->is_active ? 'Pasif' : 'Aktif' }}
                            </button>
                        @endif

                        <!-- Toggle Approval (Admin only) -->
                        @if($this->canApprove())
                            <button wire:click="toggleApproval({{ $template->id }})" 
                                    class="px-3 py-1 rounded text-sm {{ $template->is_approved ? 'bg-orange-500 hover:bg-orange-600 text-white' : 'bg-green-500 hover:bg-green-600 text-white' }}">
                                {{ $template->is_approved ? 'Onayı Kaldır' : 'Onayla' }}
                            </button>
                        @endif

                        <!-- Delete Button -->
                        @if($this->canDelete($template))
                            <button wire:click="delete({{ $template->id }})" 
                                    wire:confirm="Bu şablonu silmek istediğinizden emin misiniz?"
                                    class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm">
                                <i class="fas fa-trash"></i>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <i class="fas fa-file-alt text-gray-400 text-6xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Henüz şablon yok</h3>
                <p class="text-gray-500 mb-4">WhatsApp mesajları için şablon oluşturun.</p>
                @if($this->canCreate())
                    <button wire:click="$set('showModal', true)" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                        İlk Şablonu Oluştur
                    </button>
                @endif
            </div>
        @endforelse
    </div>

    <!-- Create/Edit Modal -->
    @if($showModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click.self="$set('showModal', false)">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-2/3 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        {{ $editingTemplate ? 'Şablon Düzenle' : 'Yeni Şablon Oluştur' }}
                    </h3>
                    
                    <form wire:submit.prevent="{{ $editingTemplate ? 'update' : 'create' }}">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Name -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Şablon Adı *</label>
                                <input type="text" wire:model="newTemplate.name" 
                                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="Örn: Randevu Hatırlatma">
                                @error('newTemplate.name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Category -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Kategori *</label>
                                <select wire:model="newTemplate.category" 
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    @foreach($this->getCategoryOptions() as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('newTemplate.category') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Status Checkboxes -->
                            <div class="flex items-center space-x-4">
                                <div class="flex items-center">
                                    <input type="checkbox" wire:model="newTemplate.is_active" id="is_active"
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <label for="is_active" class="ml-2 block text-sm text-gray-900">Aktif</label>
                                </div>
                                
                                @if($this->canApprove())
                                    <div class="flex items-center">
                                        <input type="checkbox" wire:model="newTemplate.is_approved" id="is_approved"
                                               class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                                        <label for="is_approved" class="ml-2 block text-sm text-gray-900">Onaylı</label>
                                    </div>
                                @endif
                            </div>

                            <!-- Description -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Açıklama</label>
                                <textarea wire:model="newTemplate.description" rows="2"
                                          class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                          placeholder="Bu şablonun kullanım amacını açıklayın"></textarea>
                                @error('newTemplate.description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Content -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Mesaj İçeriği *</label>
                                <textarea wire:model="newTemplate.content" rows="6"
                                          class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                          placeholder="Merhaba {{patient_name}}, randevunuz {{appointment_date}} tarihinde {{appointment_time}} saatindedir."></textarea>
                                @error('newTemplate.content') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Variables -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Değişkenler (virgülle ayırın)</label>
                                <input type="text" wire:model="newTemplate.variables" 
                                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="patient_name, appointment_date, appointment_time">
                                @error('newTemplate.variables') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                
                                <!-- Variable Help -->
                                <div class="mt-2 p-3 bg-blue-50 rounded-md">
                                    <p class="text-sm font-medium text-blue-800 mb-2">Kullanılabilir değişkenler:</p>
                                    <div class="grid grid-cols-2 gap-2 text-xs text-blue-700">
                                        @foreach($this->getVariableHelp() as $variable => $description)
                                            <div><code>{{ $variable }}</code> - {{ $description }}</div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3 mt-6 pt-4 border-t">
                            <button type="button" wire:click="$set('showModal', false)" 
                                    class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                                İptal
                            </button>
                            <button type="submit" 
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                                {{ $editingTemplate ? 'Güncelle' : 'Oluştur' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Preview Modal -->
    @if($showPreviewModal && $previewTemplate)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-eye text-blue-500"></i> Şablon Önizleme: {{ $previewTemplate->name }}
                    </h3>
                    
                    <!-- Variable Inputs -->
                    @if(count($previewVariables) > 0)
                        <div class="mb-4">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Değişken Değerleri:</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                @foreach($previewVariables as $variable => $value)
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">{{ $variable }}</label>
                                        <input type="text" wire:model="previewVariables.{{ $variable }}" 
                                               class="w-full border border-gray-300 rounded px-2 py-1 text-sm"
                                               placeholder="Değer girin">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Preview Content -->
                    <div class="mb-4">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Mesaj Önizlemesi:</h4>
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <div class="bg-white rounded-lg p-3 shadow-sm">
                                <p class="text-sm text-gray-800 whitespace-pre-wrap">{{ $this->getPreviewContent() }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Template Info -->
                    <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="font-medium text-gray-600">Kategori:</span>
                                <span class="ml-2">{{ $previewTemplate->categoryLabel }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-600">Durum:</span>
                                <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs {{ $previewTemplate->statusColor }}">
                                    {{ $previewTemplate->statusLabel }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4 border-t">
                        <button wire:click="$set('showPreviewModal', false)" 
                                class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                            Kapat
                        </button>
                        @if($this->canEdit($previewTemplate))
                            <button wire:click="edit({{ $previewTemplate->id }})" 
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                                Düzenle
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
