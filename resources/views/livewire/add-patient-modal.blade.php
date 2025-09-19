<div x-data="{ showModal: @entangle('showModal').live }">
    <!-- Trigger Button -->

    
    <!-- Modal Overlay -->
    <div x-show="showModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-2 sm:p-4" 
         style="display: none;"
         @keydown.escape.window="$wire.closeModal()">
        
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" 
             @click="$wire.closeModal()"></div>
        
        <!-- Modal panel -->
        <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-4xl max-h-[95vh] sm:max-h-[90vh] flex flex-col transform transition-all overflow-hidden">
            <form wire:submit.prevent="save" class="flex flex-col h-full max-h-[95vh] sm:max-h-[90vh]">
                <!-- Modal Header -->
                <div class="flex-shrink-0 bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4 rounded-t-xl">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-bold text-white flex items-center">
                            <i class="fas {{ $isEditMode ? 'fa-user-edit' : 'fa-user-plus' }} mr-3"></i>
                            {{ $isEditMode ? 'Hasta Bilgilerini Düzenle' : 'Yeni Hasta Kaydı' }}
                        </h3>
                        <button type="button" 
                                @click="$wire.closeModal()" 
                                class="text-white hover:text-gray-200 transition-colors p-2 hover:bg-white hover:bg-opacity-20 rounded-lg">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Modal Body - Scrollable -->
                <div class="flex-1 overflow-y-auto px-6 py-6 bg-gray-50 min-h-0">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Temel Bilgiler -->
                        <div class="bg-white rounded-lg p-6 shadow-sm">
                            <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center border-b border-gray-200 pb-3">
                                <i class="fas fa-user text-blue-600 mr-3"></i>
                                    Temel Bilgiler
                                </h4>
                                
                            <!-- Ad -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Ad *</label>
                                <input type="text" 
                                       wire:model="first_name" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('first_name') border-red-500 ring-2 ring-red-200 @enderror"
                                       placeholder="Hastanın adı">
                                @error('first_name') 
                                    <span class="text-red-500 text-sm mt-1 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        {{ $message }}
                                    </span> 
                                @enderror
                            </div>
                            
                            <!-- Soyad -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Soyad *</label>
                                <input type="text" 
                                       wire:model="last_name" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('last_name') border-red-500 ring-2 ring-red-200 @enderror"
                                       placeholder="Hastanın soyadı">
                                @error('last_name') 
                                    <span class="text-red-500 text-sm mt-1 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        {{ $message }}
                                    </span> 
                                @enderror
                            </div>
                            
                            <!-- TC Kimlik -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">TC Kimlik No *</label>
                                <input type="text" 
                                           wire:model="tc_identity" 
                                           maxlength="11"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('tc_identity') border-red-500 @enderror"
                                           placeholder="12345678901">
                                    @error('tc_identity') 
                                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span> 
                                    @enderror
                                </div>
                                
                                <!-- Doğum Tarihi -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Doğum Tarihi *</label>
                                    <input type="date" 
                                           wire:model="birth_date" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('birth_date') border-red-500 @enderror">
                                    @error('birth_date') 
                                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span> 
                                    @enderror
                                </div>
                                
                                <!-- Telefon -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Telefon *</label>
                                    <input type="tel" 
                                           wire:model="phone" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('phone') border-red-500 @enderror"
                                           placeholder="0532 123 45 67">
                                    @error('phone') 
                                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span> 
                                    @enderror
                                </div>
                                
                                <!-- Adres -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Adres</label>
                                    <textarea wire:model="address" 
                                              rows="3"
                                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                              placeholder="Hastanın adresi"></textarea>
                                </div>
                                
                                <!-- Kayıt Tarihi -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Kayıt Tarihi *</label>
                                    <input type="datetime-local" 
                                           wire:model="registration_date" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('registration_date') border-red-500 @enderror">
                                    @error('registration_date') 
                                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span> 
                                    @enderror
                                </div>
                                
                                <!-- Alınacak Ücret -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Alınacak Ücret (₺)</label>
                                    <input type="number" 
                                           step="0.01"
                                           min="0"
                                           wire:model="needs_paid" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('needs_paid') border-red-500 @enderror"
                                           placeholder="0.00">
                                    @error('needs_paid') 
                                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span> 
                                    @enderror
                                </div>
                            </div>
                            
                        <!-- Tıbbi Bilgiler -->
                        <div class="bg-white rounded-lg p-6 shadow-sm">
                            <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center border-b border-gray-200 pb-3">
                                <i class="fas fa-stethoscope text-green-600 mr-3"></i>
                                Tıbbi Bilgiler
                            </h4>
                            <div class="space-y-4">
                                
                                <!-- İlaç Kullanımı -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">İlaç Kullanımı</label>
                                    <textarea wire:model="medications" 
                                              rows="2"
                                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                              placeholder="Kullandığı ilaçlar"></textarea>
                                </div>
                                
                                <!-- Alerjik Durum -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Alerjik Durum</label>
                                    <textarea wire:model="allergies" 
                                              rows="2"
                                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                              placeholder="Bilinen alerjiler"></textarea>
                                </div>
                                
                                <!-- Kronik Rahatsızlığı -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Kronik Rahatsızlığı</label>
                                    <textarea wire:model="chronic_conditions" 
                                              rows="2"
                                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                              placeholder="Kronik hastalıklar"></textarea>
                                </div>
                                
                                <!-- Geçirilen Operasyonlar -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Geçirilen Operasyonlar</label>
                                    <textarea wire:model="previous_operations" 
                                              rows="2"
                                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                              placeholder="Daha önce geçirilen operasyonlar"></textarea>
                                </div>
                                
                                <!-- Şikayetleri -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Şikayetleri</label>
                                    <textarea wire:model="complaints" 
                                              rows="2"
                                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                              placeholder="Hastanın şikayetleri"></textarea>
                                </div>
                                
                                <!-- Anamnez -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Anamnez</label>
                                    <textarea wire:model="anamnesis" 
                                              rows="2"
                                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                              placeholder="Anamnez bilgileri"></textarea>
                                </div>
                                
                                <!-- Fiziki Muayene -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Fiziki Muayene</label>
                                    <textarea wire:model="physical_examination" 
                                              rows="2"
                                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                              placeholder="Fiziki muayene bulguları"></textarea>
                                </div>
                                
                                <!-- Karar Verilen Operasyon -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Karar Verilen Operasyon</label>
                                    <textarea wire:model="planned_operation" 
                                              rows="2"
                                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                              placeholder="Planlanmış operasyon"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Ödeme Yönetimi -->
                    <div class="bg-white rounded-lg p-6 shadow-sm mt-6">
                        <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center border-b border-gray-200 pb-3">
                            <i class="fas fa-money-bill-wave text-green-600 mr-3"></i>
                            Ödeme Yönetimi
                        </h4>
                        
                        <!-- Ödeme Özeti -->
                        @if($needs_paid > 0)
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                <div class="text-center">
                                    <div class="text-blue-600 font-semibold">Alınacak Ücret</div>
                                    <div class="text-lg font-bold text-blue-800">₺{{ number_format($needs_paid, 2) }}</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-green-600 font-semibold">Alınan Toplam</div>
                                    <div class="text-lg font-bold text-green-800">₺{{ number_format($this->totalPaid, 2) }}</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-red-600 font-semibold">Kalan Tutar</div>
                                    <div class="text-lg font-bold text-red-800">₺{{ number_format($this->remainingAmount, 2) }}</div>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        <!-- Mevcut Ödemeler -->
                        @if(count($payments) > 0)
                        <div class="mb-4">
                            <h5 class="text-md font-semibold text-gray-700 mb-3">Yapılan Ödemeler</h5>
                            <div class="space-y-2">
                                @foreach($payments as $index => $payment)
                                <div class="flex items-center justify-between bg-gray-50 p-3 rounded-lg">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-4">
                                            <span class="font-semibold text-green-600">₺{{ number_format($payment['paid_amount'], 2) }}</span>
                                            <span class="text-sm text-gray-600">
                                                @switch($payment['payment_method'])
                                                    @case('nakit') Nakit @break
                                                    @case('kredi_karti') Kredi Kartı @break
                                                    @case('banka_havalesi') Banka Havalesi @break
                                                    @case('pos') POS @break
                                                    @case('diger') Diğer @break
                                                @endswitch
                                            </span>
                                            @if($payment['notes'])
                                                <span class="text-sm text-gray-500">- {{ $payment['notes'] }}</span>
                                            @endif
                                            @if(isset($payment['created_at']))
                                                <span class="text-xs text-gray-400">{{ $payment['created_at'] }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    @if(!isset($payment['id']))
                                    <button type="button" 
                                            wire:click="removePayment({{ $index }})"
                                            class="text-red-500 hover:text-red-700 p-1">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                        
                        <!-- Yeni Ödeme Ekleme -->
                        <div class="border-t border-gray-200 pt-4">
                            <h5 class="text-md font-semibold text-gray-700 mb-3">Yeni Ödeme Ekle</h5>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Ödeme Yöntemi</label>
                                    <select wire:model="newPayment.payment_method" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="nakit">Nakit</option>
                                        <option value="kredi_karti">Kredi Kartı</option>
                                        <option value="banka_havalesi">Banka Havalesi</option>
                                        <option value="pos">POS</option>
                                        <option value="diger">Diğer</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tutar (₺)</label>
                                    <input type="number" 
                                           step="0.01"
                                           min="0.01"
                                           wire:model="newPayment.paid_amount" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                           placeholder="0.00">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Not</label>
                                    <input type="text" 
                                           wire:model="newPayment.notes" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                           placeholder="Ödeme notu">
                                </div>
                                <div class="flex items-end">
                                    <button type="button" 
                                            wire:click="addPayment"
                                            class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 transition-all duration-200">
                                        <i class="fas fa-plus mr-2"></i>
                                        Ekle
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Modal Footer -->
                <div class="flex-shrink-0 bg-white border-t border-gray-200 px-6 py-4 rounded-b-xl">
                    <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-3">
                        <button type="button" 
                                @click="$wire.closeModal()" 
                                wire:loading.attr="disabled"
                                class="w-full sm:w-auto px-6 py-3 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:ring-2 focus:ring-gray-500 transition-all duration-200 font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="fas fa-times mr-2"></i>
                            İptal
                        </button>
                        <button type="submit" 
                                wire:loading.attr="disabled"
                                wire:target="save"
                                class="w-full sm:w-auto px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 transition-all duration-200 font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                            <span wire:loading.remove wire:target="save">
                                <i class="fas fa-save mr-2"></i>
                                {{ $isEditMode ? 'Güncelle' : 'Kaydet' }}
                            </span>
                            <span wire:loading wire:target="save">
                                <i class="fas fa-spinner fa-spin mr-2"></i>
                                {{ $isEditMode ? 'Güncelleniyor...' : 'Kaydediliyor...' }}
                            </span>
                        </button>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>
