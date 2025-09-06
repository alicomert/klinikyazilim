<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
    <!-- Header -->
    <div class="p-6 border-b border-gray-100 dark:border-gray-700">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center">
                <i class="fas fa-sticky-note text-indigo-600 mr-3"></i>
                Doktor Notları
            </h2>
            <button wire:click="openModal" class="bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white px-6 py-3 rounded-lg flex items-center space-x-2 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
                <i class="fas fa-plus"></i>
                <span>Yeni Not Ekle</span>
            </button>
        </div>

        <!-- Notes Tab Navigation -->
        <div class="mb-6">
            <div class="border-b border-gray-200 dark:border-gray-700">
                <nav class="-mb-px flex space-x-8">
                    <button 
                        wire:click="switchNotesTab('my_notes')"
                        class="py-3 px-1 border-b-2 font-medium text-sm transition-all duration-200 flex items-center space-x-2
                            {{ $activeNotesTab === 'my_notes' 
                                ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' 
                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}"
                    >
                        <i class="fas fa-user"></i>
                        <span>Benim Notlarım</span>
                        <span class="bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200 text-xs px-2 py-1 rounded-full font-semibold">
                            {{ count($myNotes) }}
                        </span>
                    </button>
                    <button 
                        wire:click="switchNotesTab('team_notes')"
                        class="py-3 px-1 border-b-2 font-medium text-sm transition-all duration-200 flex items-center space-x-2
                            {{ $activeNotesTab === 'team_notes' 
                                ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400' 
                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}"
                    >
                        <i class="fas fa-users"></i>
                        <span>Ekip Notları</span>
                        <span class="bg-emerald-100 dark:bg-emerald-900 text-emerald-800 dark:text-emerald-200 text-xs px-2 py-1 rounded-full font-semibold">
                            {{ count($teamNotes) }}
                        </span>
                    </button>
                </nav>
            </div>
        </div>
    </div>
    
    <!-- Filters -->
    <div class="p-6 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Search -->
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input 
                    type="text" 
                    wire:model.live="search" 
                    placeholder="Notlarda ara..." 
                    class="w-full pl-10 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100 transition-all duration-200"
                >
            </div>
            
            <!-- Type Filter -->
            <div class="relative">
                <select wire:model.live="filterType" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100 transition-all duration-200">
                    <option value="all">🏷️ Tüm Türler</option>
                    <option value="general">📝 Genel</option>
                    <option value="medical">🩺 Tıbbi</option>
                    <option value="reminder">⏰ Hatırlatma</option>
                    <option value="important">⚠️ Önemli</option>
                    <option value="follow_up">📋 Takip</option>
                </select>
            </div>
            
            <!-- Privacy Filter -->
            <div class="relative">
                <select wire:model.live="filterPrivacy" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100 transition-all duration-200">
                    <option value="all">👁️ Tüm Notlar</option>
                    <option value="private">🔒 Özel Notlar</option>
                    <option value="public">🌐 Genel Notlar</option>
                </select>
            </div>
        </div>
    </div>
    
    <!-- Notes Grid -->
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse($currentNotes as $note)
                <div class="relative 
                    @if($note->type === 'doctor') bg-yellow-100 border-yellow-200 text-yellow-800
                    @elseif($note->type === 'patient') bg-blue-50 border-blue-200 text-blue-800
                    @elseif($note->type === 'operation') bg-orange-50 border-orange-200 text-orange-800
                    @elseif($note->type === 'appointment') bg-purple-50 border-purple-200 text-purple-800
                    @endif 
                    p-4 rounded-lg shadow-md transform rotate-1 hover:rotate-0 transition-transform duration-200 border-l-4 group cursor-pointer"
                    wire:click="openNoteModal({{ $note->id }}, '{{ $note->type }}')">
                        
                        <!-- Post-it Header -->
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center space-x-2">
                                <i class="fas 
                                    @if($note->type === 'doctor') fa-user-md
                                    @elseif($note->type === 'patient') fa-user-injured
                                    @elseif($note->type === 'operation') fa-procedures
                                    @elseif($note->type === 'appointment') fa-calendar-check
                                    @endif text-sm"></i>
                                <span class="text-xs font-medium uppercase tracking-wide">
                                    @if($note->type === 'doctor') Doktor Notu
                                    @elseif($note->type === 'patient') Hasta Notu
                                    @elseif($note->type === 'operation') Operasyon Notu
                                    @elseif($note->type === 'appointment') Randevu Notu
                                    @endif
                                </span>
                                @if($note->is_private)
                                    <i class="fas fa-lock text-xs" title="Özel Not"></i>
                                @endif
                            </div>
                            <div class="flex items-center space-x-1">
                                @if($this->canEdit($note))
                                    <button wire:click.stop="edit({{ $note->id }})" class="text-gray-600 hover:text-gray-800 p-1 rounded" title="Düzenle">
                                        <i class="fas fa-edit text-xs"></i>
                                    </button>
                                @endif
                                @if($this->canDelete($note))
                                    <button wire:click.stop="delete({{ $note->id }})" wire:confirm="Bu notu silmek istediğinizden emin misiniz?" class="text-red-600 hover:text-red-800 p-1 rounded" title="Sil">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Note Title -->
                        @if($note->title)
                            <h3 class="font-semibold mb-2 text-sm leading-tight">
                                {{ $note->title }}
                            </h3>
                        @endif
                        
                        <!-- Note Content -->
                        <div class="text-sm mb-3 line-clamp-4">
                            {{ $note->content }}
                        </div>
                        
                        <!-- Related Info -->
                        @if(isset($note->related_info))
                            <div class="text-xs opacity-75 mb-2">
                                {{ $note->related_info }}
                            </div>
                        @endif
                        
                        <!-- Note Footer -->
                        <div class="text-xs opacity-60 border-t pt-2 mt-3">
                            <div class="flex items-center justify-between">
                                <span>{{ $note->user->name ?? 'Bilinmeyen' }}</span>
                                <span>{{ $note->created_at->format('d.m.Y H:i') }}</span>
                            </div>
                        </div>
                    </div>
            @empty
                <div class="col-span-full text-center py-16">
                    <div class="bg-gradient-to-br from-yellow-400 to-orange-500 rounded-xl p-8 shadow-xl transform rotate-2 mx-auto max-w-sm border border-white/20">
                        <div class="text-white mb-4">
                            <i class="fas fa-sticky-note text-5xl mb-4 opacity-80"></i>
                            <h3 class="text-xl font-bold mb-2">Henüz not bulunmuyor</h3>
                            <p class="text-white/90 mb-6">İlk notunuzu oluşturarak başlayın!</p>
                        </div>
                        <button wire:click="openModal" class="bg-white/20 hover:bg-white/30 text-white px-6 py-3 rounded-lg transition-all duration-200 font-medium backdrop-blur-sm border border-white/20">
                            <i class="fas fa-plus mr-2"></i>
                            İlk Notunu Oluştur
                        </button>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Create/Edit Modal -->
    @if($showModal)
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden">
                <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-indigo-600 to-purple-600">
                    <h3 class="text-xl font-semibold text-white flex items-center">
                        <i class="fas {{ $editingNote ? 'fa-edit' : 'fa-plus' }} mr-3"></i>
                        {{ $editingNote ? 'Not Düzenle' : 'Yeni Not Ekle' }}
                    </h3>
                    <button wire:click="closeModal" class="text-white/80 hover:text-white transition-colors p-2 hover:bg-white/10 rounded-lg">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <form wire:submit.prevent="{{ $editingNote ? 'update' : 'create' }}" class="p-6 space-y-6 overflow-y-auto max-h-[calc(90vh-120px)]">
                    <!-- Note Type Selection -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <button type="button" wire:click="$set('noteType', 'doctor')" 
                                class="p-4 rounded-xl border-2 transition-all duration-200 {{ $noteType === 'doctor' ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-indigo-300' }}">
                            <i class="fas fa-user-md text-2xl mb-2 {{ $noteType === 'doctor' ? 'text-indigo-600' : 'text-gray-400' }}"></i>
                            <div class="text-sm font-medium {{ $noteType === 'doctor' ? 'text-indigo-600' : 'text-gray-600 dark:text-gray-400' }}">Doktor Notu</div>
                        </button>
                        
                        <button type="button" wire:click="$set('noteType', 'patient')" 
                                class="p-4 rounded-xl border-2 transition-all duration-200 {{ $noteType === 'patient' ? 'border-emerald-500 bg-emerald-50 dark:bg-emerald-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-emerald-300' }}">
                            <i class="fas fa-user-injured text-2xl mb-2 {{ $noteType === 'patient' ? 'text-emerald-600' : 'text-gray-400' }}"></i>
                            <div class="text-sm font-medium {{ $noteType === 'patient' ? 'text-emerald-600' : 'text-gray-600 dark:text-gray-400' }}">Hasta Notu</div>
                        </button>
                        
                        <button type="button" wire:click="$set('noteType', 'operation')" 
                                class="p-4 rounded-xl border-2 transition-all duration-200 {{ $noteType === 'operation' ? 'border-orange-500 bg-orange-50 dark:bg-orange-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-orange-300' }}">
                            <i class="fas fa-procedures text-2xl mb-2 {{ $noteType === 'operation' ? 'text-orange-600' : 'text-gray-400' }}"></i>
                            <div class="text-sm font-medium {{ $noteType === 'operation' ? 'text-orange-600' : 'text-gray-600 dark:text-gray-400' }}">Operasyon Notu</div>
                        </button>
                        
                        <button type="button" wire:click="$set('noteType', 'appointment')" 
                                class="p-4 rounded-xl border-2 transition-all duration-200 {{ $noteType === 'appointment' ? 'border-purple-500 bg-purple-50 dark:bg-purple-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-purple-300' }}">
                            <i class="fas fa-calendar-check text-2xl mb-2 {{ $noteType === 'appointment' ? 'text-purple-600' : 'text-gray-400' }}"></i>
                            <div class="text-sm font-medium {{ $noteType === 'appointment' ? 'text-purple-600' : 'text-gray-600 dark:text-gray-400' }}">Randevu Notu</div>
                        </button>
                    </div>
                    
                    <!-- Related Record Selection -->
                    @if($noteType === 'patient')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-user-injured mr-2 text-emerald-600"></i>
                                Hasta Seçin
                            </label>
                            <select wire:model="selectedPatient" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 dark:bg-gray-700 dark:text-gray-100">
                                <option value="">Hasta seçin...</option>
                                @foreach($patients as $patient)
                                    <option value="{{ $patient->id }}">{{ $patient->name }}</option>
                                @endforeach
                            </select>
                            @error('selectedPatient') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>
                    @elseif($noteType === 'operation')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-procedures mr-2 text-orange-600"></i>
                                Operasyon Seçin
                            </label>
                            <select wire:model="selectedOperation" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:text-gray-100">
                                <option value="">Operasyon seçin...</option>
                                @foreach($operations as $operation)
                                    <option value="{{ $operation->id }}">{{ $operation->patient->name }} - {{ $operation->operation_type }}</option>
                                @endforeach
                            </select>
                            @error('selectedOperation') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>
                    @elseif($noteType === 'appointment')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-calendar-check mr-2 text-purple-600"></i>
                                Randevu Seçin
                            </label>
                            <select wire:model="selectedAppointment" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:text-gray-100">
                                <option value="">Randevu seçin...</option>
                                @foreach($appointments as $appointment)
                                    <option value="{{ $appointment->id }}">{{ $appointment->patient->name }} - {{ $appointment->appointment_date->format('d.m.Y H:i') }}</option>
                                @endforeach
                            </select>
                            @error('selectedAppointment') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>
                    @endif
                    
                    <!-- Title (for doctor notes) -->
                    @if($noteType === 'doctor')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-heading mr-2 text-indigo-600"></i>
                                Başlık (İsteğe bağlı)
                            </label>
                            <input type="text" wire:model="newNote.title" 
                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100" 
                                   placeholder="Not başlığı...">
                            @error('newNote.title') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>
                    @endif
                    
                    <!-- Note Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-tag mr-2 text-gray-600"></i>
                            Not Türü
                        </label>
                        <select wire:model="newNote.note_type" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100">
                            <option value="general">📝 Genel</option>
                            <option value="medical">🩺 Tıbbi</option>
                            <option value="reminder">⏰ Hatırlatma</option>
                            <option value="important">⚠️ Önemli</option>
                            <option value="follow_up">📋 Takip</option>
                        </select>
                        @error('newNote.note_type') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                    </div>
                    
                    <!-- Content -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-file-alt mr-2 text-gray-600"></i>
                            Not İçeriği
                        </label>
                        <textarea wire:model="newNote.content" rows="6" 
                                  class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100" 
                                  placeholder="Not içeriğinizi buraya yazın..."></textarea>
                        @error('newNote.content') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                    </div>
                    
                    <!-- Privacy Setting -->
                    <div class="flex items-center space-x-3 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <input type="checkbox" wire:model="newNote.is_private" id="is_private" 
                               class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        <label for="is_private" class="flex items-center text-sm font-medium text-gray-700 dark:text-gray-300">
                            <i class="fas fa-lock mr-2 text-gray-500"></i>
                            Bu not özel olsun (sadece ben görebilirim)
                        </label>
                    </div>
                    
                    <!-- Form Actions -->
                    <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <button type="button" wire:click="closeModal" 
                                class="px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <i class="fas fa-times mr-2"></i>
                            İptal
                        </button>
                        <button type="submit" 
                                class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white rounded-lg transition-all duration-200 shadow-lg hover:shadow-xl">
                            <i class="fas {{ $editingNote ? 'fa-save' : 'fa-plus' }} mr-2"></i>
                            {{ $editingNote ? 'Güncelle' : 'Kaydet' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Note Detail Modal -->
    @if($showNoteModal && $selectedNote)
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden">
                <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r {{ $this->getNoteColor($selectedNote->note_type) }}">
                    <h3 class="text-xl font-semibold text-white flex items-center">
                        <i class="fas fa-sticky-note mr-3"></i>
                        {{ $selectedNote->title ?? 'Not Detayı' }}
                    </h3>
                    <button wire:click="closeNoteModal" class="text-white/80 hover:text-white transition-colors p-2 hover:bg-white/10 rounded-lg">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
                    <!-- Note Info -->
                    <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="font-medium text-gray-600 dark:text-gray-400">Tür:</span>
                                <span class="ml-2 text-gray-900 dark:text-gray-100">{{ $this->getNoteTypeLabel($selectedNote->note_type) }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-600 dark:text-gray-400">Tarih:</span>
                                <span class="ml-2 text-gray-900 dark:text-gray-100">{{ $selectedNote->created_at->format('d.m.Y H:i') }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-600 dark:text-gray-400">Yazan:</span>
                                <span class="ml-2 text-gray-900 dark:text-gray-100">{{ $selectedNote->user->name }} ({{ $selectedNote->user->getRoleDisplayName() }})</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-600 dark:text-gray-400">Gizlilik:</span>
                                <span class="ml-2 text-gray-900 dark:text-gray-100">
                                    @if($selectedNote->is_private)
                                        <i class="fas fa-lock text-red-500 mr-1"></i> Özel
                                    @else
                                        <i class="fas fa-globe text-green-500 mr-1"></i> Genel
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Note Content -->
                    <div class="prose dark:prose-invert max-w-none">
                        <div class="whitespace-pre-wrap text-gray-700 dark:text-gray-300 leading-relaxed">
                            {{ $selectedNote->content }}
                        </div>
                    </div>
                    
                    <!-- Related Information -->
                    @if($selectedNote->patient_id || $selectedNote->operation_id || $selectedNote->appointment_id)
                        <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-700">
                            <h4 class="font-medium text-blue-900 dark:text-blue-100 mb-2">
                                <i class="fas fa-link mr-2"></i>
                                İlgili Kayıt
                            </h4>
                            @if($selectedNote->patient_id && isset($selectedNote->patient))
                                <p class="text-blue-800 dark:text-blue-200">Hasta: {{ $selectedNote->patient->first_name }} {{ $selectedNote->patient->last_name }}</p>
                            @endif
                            @if($selectedNote->operation_id && isset($selectedNote->operation))
                                <p class="text-blue-800 dark:text-blue-200">Operasyon: 
                                    @if($selectedNote->operation->patient)
                                        {{ $selectedNote->operation->patient->first_name }} {{ $selectedNote->operation->patient->last_name }} - 
                                    @endif
                                    {{ $selectedNote->operation->process ?? $selectedNote->operation->operation_type }}
                                </p>
                            @endif
                            @if($selectedNote->appointment_id && isset($selectedNote->appointment))
                                <p class="text-blue-800 dark:text-blue-200">Randevu: {{ $selectedNote->appointment->appointment_date->format('d.m.Y') }} {{ $selectedNote->appointment->appointment_time }}</p>
                            @endif
                        </div>
                    @endif
                </div>
                
                <!-- Modal Actions -->
                <div class="flex justify-end space-x-3 p-6 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
                    @if($this->canEdit($selectedNote))
                        <button wire:click="edit({{ $selectedNote->id }})" 
                                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors">
                            <i class="fas fa-edit mr-2"></i>
                            Düzenle
                        </button>
                    @endif
                    <button wire:click="closeNoteModal" 
                            class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                        <i class="fas fa-times mr-2"></i>
                        Kapat
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
