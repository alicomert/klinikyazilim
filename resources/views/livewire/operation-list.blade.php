<div>
    <!-- Başarı/Hata Mesajları -->
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

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-sm p-6 card-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-gray-500 text-sm font-medium">Toplam Operasyon</div>
                    <div class="text-3xl font-bold text-blue-600 mt-2">{{ number_format($this->stats['total_operations']) }}</div>
                    @if(isset($this->stats['yearly_percentage_change']) && $this->stats['yearly_percentage_change'] !== null)
                        <div class="text-{{ $this->stats['yearly_percentage_change'] >= 0 ? 'green' : 'red' }}-500 text-sm mt-1">
                            <i class="fas fa-arrow-{{ $this->stats['yearly_percentage_change'] >= 0 ? 'up' : 'down' }}"></i> 
                            %{{ abs($this->stats['yearly_percentage_change']) }} geçen yıla göre
                        </div>
                    @else
                        <div class="text-gray-500 text-sm mt-1">
                            <i class="fas fa-procedures"></i> Tüm operasyonlar
                        </div>
                    @endif
                </div>
                <div class="bg-blue-100 p-4 rounded-full">
                    <i class="fas fa-procedures text-blue-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 card-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-gray-500 text-sm font-medium">Bu Yıl</div>
                    <div class="text-3xl font-bold text-green-600 mt-2">{{ $this->stats['this_year_operations'] }}</div>
                    @if(isset($this->stats['yearly_percentage_change']) && $this->stats['yearly_percentage_change'] !== null)
                        <div class="text-{{ $this->stats['yearly_percentage_change'] >= 0 ? 'green' : 'red' }}-500 text-sm mt-1">
                            <i class="fas fa-arrow-{{ $this->stats['yearly_percentage_change'] >= 0 ? 'up' : 'down' }}"></i> 
                            %{{ abs($this->stats['yearly_percentage_change']) }} değişim
                        </div>
                    @else
                        <div class="text-green-500 text-sm mt-1">
                            <i class="fas fa-calendar-alt"></i> {{ date('Y') }} yılı
                        </div>
                    @endif
                </div>
                <div class="bg-green-100 p-4 rounded-full">
                    <i class="fas fa-calendar-alt text-green-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 card-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-gray-500 text-sm font-medium">Bu Ay</div>
                    <div class="text-3xl font-bold text-purple-600 mt-2">{{ $this->stats['this_month_operations'] }}</div>
                    @if(isset($this->stats['monthly_percentage_change']) && $this->stats['monthly_percentage_change'] !== null)
                        <div class="text-{{ $this->stats['monthly_percentage_change'] >= 0 ? 'green' : 'red' }}-500 text-sm mt-1">
                            <i class="fas fa-arrow-{{ $this->stats['monthly_percentage_change'] >= 0 ? 'up' : 'down' }}"></i> 
                            %{{ abs($this->stats['monthly_percentage_change']) }} geçen aya göre
                        </div>
                    @else
                        <div class="text-purple-500 text-sm mt-1">
                            <i class="fas fa-calendar-day"></i> {{ date('F Y') }}
                        </div>
                    @endif
                </div>
                <div class="bg-purple-100 p-4 rounded-full">
                    <i class="fas fa-calendar-day text-purple-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 card-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-gray-500 text-sm font-medium">En Popüler İşlem</div>
                    <div class="text-3xl font-bold text-yellow-600 mt-2">{{ $this->botoxCount }}</div>
                    <div class="text-gray-500 text-sm mt-1">
                        <i class="fas fa-star"></i> Botoks işlemi
                    </div>
                </div>
                <div class="bg-yellow-100 p-4 rounded-full">
                    <i class="fas fa-star text-yellow-600 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6 card-shadow">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex-1 max-w-md">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <input type="text" wire:model.live.debounce.300ms="searchTerm" placeholder="Hasta adı, TC kimlik, işlem detayı ile ara..." 
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <select wire:model.live="filterProcess" class="border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    <option value="">Tüm İşlemler</option>
                    <option value="surgery">Ameliyat</option>
                    <option value="mesotherapy">Mezoterapi</option>
                    <option value="botox">Botoks</option>
                    <option value="filler">Dolgu</option>
                </select>
                <select wire:model.live="filterRegistrationPeriod" class="border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    <option value="">Tüm Dönemler</option>
                    @foreach($this->getAvailablePeriods() as $period)
                        <option value="{{ $period }}">{{ $period }}</option>
                    @endforeach
                </select>
                <button wire:click="$set('showModal', true)" 
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-50 cursor-not-allowed"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 flex items-center whitespace-nowrap">
                    <i class="fas fa-plus mr-2" wire:loading.remove wire:target="$set('showModal', true)"></i>
                    <i class="fas fa-spinner fa-spin mr-2" wire:loading wire:target="$set('showModal', true)"></i>
                    <span wire:loading.remove wire:target="$set('showModal', true)">Yeni Operasyon</span>
                    <span wire:loading wire:target="$set('showModal', true)">Yükleniyor...</span>
                </button>
            </div>
        </div>
    </div>
    <!-- Operations Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden card-shadow" x-data="{ 
        columns: JSON.parse(localStorage.getItem('operationListColumns')) || {
            operationInfo: true,
            patientInfo: true,
            registrationPeriod: true,
            actions: true
        },
        saveColumns() {
            localStorage.setItem('operationListColumns', JSON.stringify(this.columns));
        }
    }">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-800">Operasyon Listesi</h3>
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200 flex items-center">
                    <i class="fas fa-columns mr-2"></i>
                    Sütunlar
                    <i class="fas fa-chevron-down ml-2 text-xs" :class="{ 'rotate-180': open }"></i>
                </button>
                <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10 border border-gray-200">
                    <div class="py-2">
                        <label class="flex items-center px-4 py-2 hover:bg-gray-50 cursor-pointer">
                            <input type="checkbox" x-model="columns.operationInfo" @change="saveColumns()" class="mr-3 rounded">
                            <span class="text-sm text-gray-700">Operasyon Bilgileri</span>
                        </label>
                        <label class="flex items-center px-4 py-2 hover:bg-gray-50 cursor-pointer">
                            <input type="checkbox" x-model="columns.patientInfo" @change="saveColumns()" class="mr-3 rounded">
                            <span class="text-sm text-gray-700">Hasta Bilgileri</span>
                        </label>
                        <label class="flex items-center px-4 py-2 hover:bg-gray-50 cursor-pointer">
                            <input type="checkbox" x-model="columns.registrationPeriod" @change="saveColumns()" class="mr-3 rounded">
                            <span class="text-sm text-gray-700">Kayıt Dönemi</span>
                        </label>
                        <label class="flex items-center px-4 py-2 hover:bg-gray-50 cursor-pointer">
                            <input type="checkbox" x-model="columns.actions" @change="saveColumns()" class="mr-3 rounded">
                            <span class="text-sm text-gray-700">İşlemler</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th x-show="columns.operationInfo" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Operasyon Bilgileri</th>
                        <th x-show="columns.patientInfo" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hasta Bilgileri</th>
                        <th x-show="columns.registrationPeriod" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kayıt Dönemi</th>
                        <th x-show="columns.actions" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($operations as $operation)
                        <tr class="hover:bg-gray-50">
                            <td x-show="columns.operationInfo" class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-12 w-12 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                        <i class="fas fa-procedures text-blue-600 dark:text-blue-400"></i>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900" title="{{ $operation->process_label }}">
                                            {{ Str::limit($operation->process_label, 25) }}
                                        </div>
                                        <div class="text-sm text-gray-500">{{ $operation->process_date->format('d.m.Y') }}</div>
                                        <div class="text-xs text-gray-400">{{ Str::limit($operation->process_detail, 30) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td x-show="columns.patientInfo" class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900" title="{{ $operation->patient->first_name }} {{ $operation->patient->last_name }}">
                                    {{ Str::limit($operation->patient->first_name . ' ' . $operation->patient->last_name, 25) }}
                                </div>
                                <div class="text-sm text-gray-500">TC: {{ substr($operation->patient->tc_identity, 0, 3) }}***{{ substr($operation->patient->tc_identity, -2) }}</div>
                            </td>
                            <td x-show="columns.registrationPeriod" class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $operation->registration_period }}</div>
                            </td>
                            <td x-show="columns.actions" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <button wire:click="showNotes({{ $operation->id }})" 
                                        class="text-purple-600 hover:text-purple-800 mr-3 p-2 hover:bg-purple-100 rounded-full transition-colors duration-200" 
                                        title="Notlar">
                                    <i class="fas fa-sticky-note text-lg"></i>
                                </button>
                                <button wire:click="edit({{ $operation->id }})" 
                                        wire:loading.attr="disabled"
                                        wire:loading.class="opacity-50 cursor-not-allowed"
                                        class="text-yellow-600 hover:text-yellow-800 mr-3 p-2 hover:bg-yellow-100 rounded-full transition-colors duration-200" 
                                        title="Düzenle">
                                    <i class="fas fa-edit text-lg" wire:loading.remove wire:target="edit"></i>
                                    <i class="fas fa-spinner fa-spin text-lg" wire:loading wire:target="edit"></i>
                                </button>
                                <button @click="$dispatch('confirm-delete', { operationId: {{ $operation->id }}, operationName: '{{ $operation->process_label }}' })" 
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
                                    <i class="fas fa-procedures text-4xl mb-4"></i>
                                    <p class="text-lg font-medium">Henüz operasyon kaydı bulunmuyor</p>
                                    <p class="text-sm">İlk operasyonunuzu eklemek için yukarıdaki "Yeni Operasyon" butonunu kullanın.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Notes Modal -->
    @if($showNotesModal)
        <div class="fixed inset-0 bg-black bg-opacity-60 h-full w-full z-50 flex items-center justify-center p-4" wire:click="closeNotesModal">
            <div class="relative w-full max-w-6xl max-h-[90vh] bg-white rounded-2xl shadow-2xl flex flex-col" wire:click.stop>
                <!-- Modal Header -->
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-8 py-6 rounded-t-2xl flex-shrink-0">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="bg-white bg-opacity-20 p-3 rounded-full">
                                <i class="fas fa-sticky-note text-white text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold text-white">{{ $selectedOperationForNotes ? $selectedOperationForNotes->process_label : '' }} - Notlar</h3>
                                <div class="flex items-center space-x-4 text-sm text-blue-100">
                                    <span class="flex items-center"><i class="fas fa-calendar mr-2"></i>{{ $selectedOperationForNotes ? $selectedOperationForNotes->process_date->format('d.m.Y') : '' }}</span>
                                    <span class="flex items-center"><i class="fas fa-user mr-2"></i>{{ $selectedOperationForNotes ? $selectedOperationForNotes->patient->first_name . ' ' . $selectedOperationForNotes->patient->last_name : '' }}</span>
                                </div>
                            </div>
                        </div>
                        <button wire:click="closeNotesModal" class="text-white hover:text-blue-200 transition-colors duration-200 p-2 rounded-full hover:bg-white hover:bg-opacity-10">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Modal Content -->
                <div class="flex-1 overflow-hidden flex">
                    <!-- Notes List -->
                    <div class="flex-1 p-6 overflow-y-auto">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @forelse($operationNotes as $note)
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
                                                <button @click="$dispatch('confirm-note-delete', { noteId: {{ $note->id }}, noteContent: '{{ Str::limit($note->content, 50) }}' })" class="text-red-600 hover:text-red-800 p-1 rounded" title="Sil">
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
                                <select wire:model="newNote.note_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="general">Genel</option>
                                    <option value="medical">Tıbbi</option>
                                    <option value="administrative">İdari</option>
                                    <option value="follow_up">Takip</option>
                                </select>
                            </div>
                            
                            <!-- Content -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">İçerik</label>
                                <textarea wire:model="newNote.content" rows="6" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Not içeriği"></textarea>
                                @error('newNote.content') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <!-- Private Note (Only for Doctors) -->
                            @if(Auth::user()->role === 'doctor')
                            <div class="flex items-center">
                                <input type="checkbox" wire:model="newNote.is_private" id="is_private" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <label for="is_private" class="ml-2 text-sm text-gray-700">Özel not (sadece ben görebilirim)</label>
                            </div>
                            @endif
                            
                            <!-- Buttons -->
                            <div class="flex space-x-2">
                                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
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

    <!-- Note Delete Confirmation Modal -->
    <div x-data="{ 
        showNoteDeleteModal: false, 
        noteToDelete: null, 
        noteContent: '' 
    }"
         @confirm-note-delete.window="showNoteDeleteModal = true; noteToDelete = $event.detail.noteId; noteContent = $event.detail.noteContent"
         x-show="showNoteDeleteModal" 
         x-cloak
         class="fixed inset-0 bg-black bg-opacity-60 h-full w-full z-50 flex items-center justify-center p-4">
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md" @click.stop>
            <div class="p-6">
                <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-red-100 rounded-full">
                    <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 text-center mb-2">Notu Sil</h3>
                <p class="text-gray-600 text-center mb-6">
                    "<span x-text="noteContent"></span>" notunu silmek istediğinizden emin misiniz?
                    <br><span class="text-sm text-red-500 mt-2 block">Bu işlem geri alınamaz.</span>
                </p>
                <div class="flex space-x-3">
                    <button @click="showNoteDeleteModal = false" 
                            class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-3 px-4 rounded-lg transition-colors duration-200">
                        İptal
                    </button>
                    <button @click="$wire.deleteNote(noteToDelete); showNoteDeleteModal = false" 
                            class="flex-1 bg-red-600 hover:bg-red-700 text-white font-medium py-3 px-4 rounded-lg transition-colors duration-200">
                        <i class="fas fa-trash mr-2"></i>Sil
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div x-data="{ 
        showDeleteModal: false, 
        operationToDelete: null, 
        operationName: '' 
    }"
         @confirm-delete.window="showDeleteModal = true; operationToDelete = $event.detail.operationId; operationName = $event.detail.operationName"
         x-show="showDeleteModal" 
         x-cloak
         class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mt-4">Operasyonu Sil</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">
                        <span x-text="operationName"></span> operasyonunu silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.
                    </p>
                </div>
                <div class="flex justify-center space-x-3 mt-4">
                    <button @click="showDeleteModal = false" 
                            class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-4 rounded">
                        İptal
                    </button>
                    <button @click="$wire.delete(operationToDelete); showDeleteModal = false" 
                            class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded">
                        Sil
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Form -->
    @if($showModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="closeModal">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white" wire:click.stop>
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">
                            {{ $editingOperation ? 'Operasyon Düzenle' : 'Yeni Operasyon Ekle' }}
                        </h3>
                        <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    
                    <form wire:submit.prevent="{{ $editingOperation ? 'update' : 'create' }}">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Hasta Seçimi -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Hasta *</label>
                                
                                <!-- Arama Input -->
                                <div class="relative mb-3">
                                    <input type="text" wire:model.live="patientSearch" 
                                           placeholder="Hasta adı veya TC kimlik ile arayın..."
                                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <i class="fas fa-search absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                </div>
                                
                                <!-- Seçilen Hasta -->
                                @if($newOperation['patient_id'] ?? false)
                                    @php
                                        $selectedPatientData = collect($patients)->firstWhere('id', $newOperation['patient_id']);
                                    @endphp
                                    @if($selectedPatientData)
                                        <div class="bg-blue-50 border border-blue-200 rounded-md p-3 mb-3">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center space-x-3">
                                                    <div class="bg-blue-100 p-2 rounded-full">
                                                        <i class="fas fa-user text-blue-600"></i>
                                                    </div>
                                                    <div>
                                                        <div class="font-medium text-gray-900">
                                                            {{ $selectedPatientData->first_name }} {{ $selectedPatientData->last_name }}
                                                        </div>
                                                        <div class="text-sm text-gray-500">
                                                            TC: {{ $selectedPatientData->tc_identity }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="flex items-center space-x-2">
                                                    <button type="button" wire:click="showPatientDetails({{ $selectedPatientData->id }})" 
                                                            class="bg-blue-500 text-white p-2 rounded-full hover:bg-blue-600 transition-colors">
                                                        <i class="fas fa-info text-sm"></i>
                                                    </button>
                                                    <button type="button" wire:click="$set('newOperation.patient_id', '')" 
                                                            class="bg-red-500 text-white p-2 rounded-full hover:bg-red-600 transition-colors">
                                                        <i class="fas fa-times text-sm"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endif
                                
                                <!-- Hasta Listesi -->
                                @if(!($newOperation['patient_id'] ?? false) && count($filteredPatients) > 0)
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
                                                <button type="button" wire:click="showPatientDetails({{ $patient->id }})" 
                                                        class="bg-blue-500 text-white p-2 rounded-full hover:bg-blue-600 transition-colors">
                                                    <i class="fas fa-info text-sm"></i>
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                @elseif(!($newOperation['patient_id'] ?? false) && !empty($patientSearch))
                                    <div class="text-center py-4 text-gray-500">
                                        Arama kriterinize uygun hasta bulunamadı.
                                    </div>
                                @endif
                                
                                @error('newOperation.patient_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- İşlem Türü -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">İşlem Türü *</label>
                                <select wire:model="newOperation.process" 
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">İşlem Türü Seçiniz</option>
                                    <option value="surgery">Ameliyat</option>
                                    <option value="mesotherapy">Mezoterapi</option>
                                    <option value="botox">Botoks</option>
                                    <option value="filler">Dolgu</option>
                                </select>
                                @error('newOperation.process') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Kayıt Dönemi -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Kayıt Dönemi *</label>
                                <input type="month" wire:model="newOperation.registration_period" 
                                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @error('newOperation.registration_period') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                <p class="text-xs text-gray-500 mt-1">İşlem tarihi otomatik olarak bugünün tarihi olarak kaydedilecektir.</p>
                            </div>

                            <!-- İşlem Detayı -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">İşlem Detayı *</label>
                                <textarea wire:model="newOperation.process_detail" rows="3" 
                                          class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                          placeholder="İşlem detaylarını açıklayınız..."></textarea>
                                @error('newOperation.process_detail') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>


                        </div>

                        <div class="flex justify-end space-x-3 mt-6">
                            <button type="button" wire:click="closeModal" 
                                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                İptal
                            </button>
                            <button type="submit" 
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                {{ $editingOperation ? 'Güncelle' : 'Kaydet' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Hasta Detay Modalı -->
    @if($showPatientDetails && $selectedPatient)
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="closePatientDetails">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white" wire:click.stop>
            <div class="mt-3">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Hasta Detayları</h3>
                    <button wire:click="closePatientDetails" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Kişisel Bilgiler -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-gray-700 mb-3">Kişisel Bilgiler</h4>
                        <div class="space-y-2">
                            <div><span class="font-medium">Ad Soyad:</span> {{ $selectedPatient->first_name }} {{ $selectedPatient->last_name }}</div>
                            <div><span class="font-medium">TC Kimlik:</span> {{ $selectedPatient->tc_number }}</div>
                            <div><span class="font-medium">Doğum Tarihi:</span> {{ $selectedPatient->birth_date ? \Carbon\Carbon::parse($selectedPatient->birth_date)->format('d.m.Y') : '-' }}</div>
                            <div><span class="font-medium">Cinsiyet:</span> {{ $selectedPatient->gender == 'male' ? 'Erkek' : ($selectedPatient->gender == 'female' ? 'Kadın' : '-') }}</div>
                            <div><span class="font-medium">Telefon:</span> {{ $selectedPatient->phone ?? '-' }}</div>
                            <div><span class="font-medium">E-posta:</span> {{ $selectedPatient->email ?? '-' }}</div>
                        </div>
                    </div>
                    
                    <!-- Adres Bilgileri -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-gray-700 mb-3">Adres Bilgileri</h4>
                        <div class="space-y-2">
                            <div><span class="font-medium">Adres:</span> {{ $selectedPatient->address ?? '-' }}</div>
                            <div><span class="font-medium">İl:</span> {{ $selectedPatient->city ?? '-' }}</div>
                            <div><span class="font-medium">İlçe:</span> {{ $selectedPatient->district ?? '-' }}</div>
                            <div><span class="font-medium">Posta Kodu:</span> {{ $selectedPatient->postal_code ?? '-' }}</div>
                        </div>
                    </div>
                    
                    <!-- Tıbbi Bilgiler -->
                    <div class="bg-gray-50 p-4 rounded-lg md:col-span-2">
                        <h4 class="font-semibold text-gray-700 mb-3">Tıbbi Bilgiler</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div><span class="font-medium">Kan Grubu:</span> {{ $selectedPatient->blood_type ?? '-' }}</div>
                            <div><span class="font-medium">Alerjiler:</span> {{ $selectedPatient->allergies ?? '-' }}</div>
                            <div><span class="font-medium">Kronik Hastalıklar:</span> {{ $selectedPatient->chronic_diseases ?? '-' }}</div>
                            <div><span class="font-medium">Kullandığı İlaçlar:</span> {{ $selectedPatient->medications ?? '-' }}</div>
                        </div>
                        @if($selectedPatient->medical_notes)
                            <div class="mt-3">
                                <span class="font-medium">Tıbbi Notlar:</span>
                                <p class="mt-1 text-gray-600">{{ $selectedPatient->medical_notes }}</p>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Acil Durum İletişim -->
                    @if($selectedPatient->emergency_contact_name || $selectedPatient->emergency_contact_phone)
                    <div class="bg-red-50 p-4 rounded-lg md:col-span-2">
                        <h4 class="font-semibold text-red-700 mb-3">Acil Durum İletişim</h4>
                        <div class="space-y-2">
                            <div><span class="font-medium">İsim:</span> {{ $selectedPatient->emergency_contact_name ?? '-' }}</div>
                            <div><span class="font-medium">Telefon:</span> {{ $selectedPatient->emergency_contact_phone ?? '-' }}</div>
                            <div><span class="font-medium">Yakınlık:</span> {{ $selectedPatient->emergency_contact_relationship ?? '-' }}</div>
                        </div>
                    </div>
                    @endif
                </div>
                
                <div class="mt-6 flex justify-end">
                    <button wire:click="closePatientDetails" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Kapat
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
