@extends('layouts.app')

@section('title', 'Ayarlar')

@section('content')
<div x-data="settingsData()" x-init="loadSettings()">
    <!-- Ayar Kategorileri -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Sol Menü -->
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 card-shadow">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Ayar Kategorileri</h3>
                <nav class="space-y-2">
                    <button @click="activeTab = 'profile'" 
                            :class="activeTab === 'profile' ? 'bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-600 text-blue-600 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700'"
                            class="w-full text-left p-3 rounded-lg transition-colors">
                        <i class="fas fa-user-circle mr-3"></i>Profil Ayarları
                    </button>
                    <button @click="activeTab = 'theme'" 
                            :class="activeTab === 'theme' ? 'bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-600 text-blue-600 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700'"
                            class="w-full text-left p-3 rounded-lg transition-colors">
                        <i class="fas fa-palette mr-3"></i>Tema Ayarları
                    </button>
                    <button @click="activeTab = 'clinic'" 
                            :class="activeTab === 'clinic' ? 'bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-600 text-blue-600 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700'"
                            class="w-full text-left p-3 rounded-lg transition-colors">
                        <i class="fas fa-clinic-medical mr-3"></i>Klinik Bilgileri
                    </button>
                    <button @click="activeTab = 'notifications'" 
                            :class="activeTab === 'notifications' ? 'bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-600 text-blue-600 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700'"
                            class="w-full text-left p-3 rounded-lg transition-colors">
                        <i class="fas fa-bell mr-3"></i>Bildirimler
                    </button>
                    <button @click="activeTab = 'security'" 
                            :class="activeTab === 'security' ? 'bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-600 text-blue-600 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700'"
                            class="w-full text-left p-3 rounded-lg transition-colors">
                        <i class="fas fa-shield-alt mr-3"></i>Güvenlik
                    </button>
                </nav>
            </div>
        </div>

        <!-- Sağ İçerik -->
        <div class="lg:col-span-3">
            <!-- Profil Ayarları -->
            <div x-show="activeTab === 'profile'" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 card-shadow">
                <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-6">Profil Ayarları</h3>
                
                <div class="space-y-6">
                    <!-- Profil Fotoğrafı -->
                    <div class="flex items-center space-x-6">
                        <div class="w-24 h-24 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                            <i class="fas fa-user-md text-blue-600 dark:text-blue-400 text-3xl"></i>
                        </div>
                        <div>
                            <h4 class="text-lg font-medium text-gray-800 dark:text-white">Profil Fotoğrafı</h4>
                            <p class="text-gray-600 dark:text-gray-400 text-sm mb-3">JPG, PNG formatında maksimum 2MB</p>
                            <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                                <i class="fas fa-upload mr-2"></i>Fotoğraf Yükle
                            </button>
                        </div>
                    </div>

                    <!-- Kişisel Bilgiler -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Ad Soyad</label>
                            <input type="text" x-model="profile.name" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-800 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Uzmanlık</label>
                            <input type="text" x-model="profile.specialty" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-800 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">E-posta</label>
                            <input type="email" x-model="profile.email" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-800 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Telefon</label>
                            <input type="tel" x-model="profile.phone" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-800 dark:text-white">
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button @click="saveProfile()" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg transition-colors">
                            <i class="fas fa-save mr-2"></i>Profil Bilgilerini Kaydet
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tema Ayarları -->
            <div x-show="activeTab === 'theme'" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 card-shadow">
                <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-6">Tema Özelleştirme</h3>
                
                <div class="space-y-8">
                    <!-- Mode Selection -->
                    <div>
                        <h4 class="text-lg font-medium text-gray-800 dark:text-white mb-4">Düzenleme Modu</h4>
                        <div class="flex space-x-2">
                            <button 
                                @click="editMode = 'light'"
                                class="px-4 py-2 rounded-lg border transition-all"
                                :class="editMode === 'light' ? 'bg-blue-500 text-white border-blue-500' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-600'"
                            >
                                <i class="fas fa-sun mr-2"></i>Aydınlık Mod
                            </button>
                            <button 
                                @click="editMode = 'dark'"
                                class="px-4 py-2 rounded-lg border transition-all"
                                :class="editMode === 'dark' ? 'bg-blue-500 text-white border-blue-500' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-600'"
                            >
                                <i class="fas fa-moon mr-2"></i>Karanlık Mod
                            </button>
                        </div>
                    </div>

                    <!-- Kart Renkleri -->
                    <div>
                        <h4 class="text-lg font-medium text-gray-800 dark:text-white mb-4">
                            Kart Renkleri (<span x-text="editMode === 'light' ? 'Aydınlık' : 'Karanlık'"></span> Mod)
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Kart Arka Plan Rengi</label>
                                <div class="flex items-center space-x-3">
                                    <input type="color" x-model="getCurrentTheme().cardBackground" class="w-12 h-10 border border-gray-300 dark:border-gray-600 rounded cursor-pointer">
                                    <input type="text" x-model="getCurrentTheme().cardBackground" class="flex-1 border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-800 dark:text-white">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Kart Kenarlık Rengi</label>
                                <div class="flex items-center space-x-3">
                                    <input type="color" x-model="getCurrentTheme().cardBorder" class="w-12 h-10 border border-gray-300 dark:border-gray-600 rounded cursor-pointer">
                                    <input type="text" x-model="getCurrentTheme().cardBorder" class="flex-1 border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-800 dark:text-white">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Vurgu Renkleri -->
                    <div>
                        <h4 class="text-lg font-medium text-gray-800 dark:text-white mb-4">
                            Vurgu Renkleri (<span x-text="editMode === 'light' ? 'Aydınlık' : 'Karanlık'"></span> Mod)
                        </h4>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Birincil</label>
                                <div class="flex items-center space-x-2">
                                    <input type="color" x-model="getCurrentTheme().primaryColor" class="w-10 h-8 border border-gray-300 dark:border-gray-600 rounded cursor-pointer">
                                    <input type="text" x-model="getCurrentTheme().primaryColor" class="flex-1 text-xs border border-gray-300 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-700 text-gray-800 dark:text-white">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Başarı</label>
                                <div class="flex items-center space-x-2">
                                    <input type="color" x-model="getCurrentTheme().successColor" class="w-10 h-8 border border-gray-300 dark:border-gray-600 rounded cursor-pointer">
                                    <input type="text" x-model="getCurrentTheme().successColor" class="flex-1 text-xs border border-gray-300 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-700 text-gray-800 dark:text-white">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Uyarı</label>
                                <div class="flex items-center space-x-2">
                                    <input type="color" x-model="getCurrentTheme().warningColor" class="w-10 h-8 border border-gray-300 dark:border-gray-600 rounded cursor-pointer">
                                    <input type="text" x-model="getCurrentTheme().warningColor" class="flex-1 text-xs border border-gray-300 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-700 text-gray-800 dark:text-white">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tehlike</label>
                                <div class="flex items-center space-x-2">
                                    <input type="color" x-model="getCurrentTheme().dangerColor" class="w-10 h-8 border border-gray-300 dark:border-gray-600 rounded cursor-pointer">
                                    <input type="text" x-model="getCurrentTheme().dangerColor" class="flex-1 text-xs border border-gray-300 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-700 text-gray-800 dark:text-white">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Önizleme -->
                    <div>
                        <h4 class="text-lg font-medium text-gray-800 dark:text-white mb-4">
                            Önizleme (<span x-text="editMode === 'light' ? 'Aydınlık' : 'Karanlık'"></span> Mod)
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="p-4 rounded-lg border-2 transition-all" 
                                 :style="'background-color: ' + getCurrentTheme().cardBackground + '; border-color: ' + getCurrentTheme().cardBorder">
                                <div class="flex items-center justify-between mb-3">
                                    <h5 class="font-semibold text-gray-800 dark:text-white">Örnek Kart</h5>
                                    <i class="fas fa-heart" :style="'color: ' + getCurrentTheme().dangerColor"></i>
                                </div>
                                <p class="text-gray-600 dark:text-gray-400 text-sm mb-3">Bu bir örnek kart içeriğidir.</p>
                                <div class="flex space-x-2">
                                    <button class="px-3 py-1 rounded text-sm text-white" :style="'background-color: ' + getCurrentTheme().primaryColor">Birincil</button>
                                    <button class="px-3 py-1 rounded text-sm text-white" :style="'background-color: ' + getCurrentTheme().successColor">Başarı</button>
                                    <button class="px-3 py-1 rounded text-sm text-white" :style="'background-color: ' + getCurrentTheme().warningColor">Uyarı</button>
                                </div>
                            </div>
                            <div class="p-4 rounded-lg border-2 transition-all" 
                                 :style="'background-color: ' + getCurrentTheme().cardBackground + '; border-color: ' + getCurrentTheme().cardBorder">
                                <div class="flex items-center space-x-3 mb-3">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center" :style="'background-color: ' + getCurrentTheme().primaryColor">
                                        <i class="fas fa-user text-white"></i>
                                    </div>
                                    <div>
                                        <h6 class="font-medium text-gray-800 dark:text-white">Hasta Bilgisi</h6>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Örnek hasta</p>
                                    </div>
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Son randevu: Bugün</div>
                            </div>
                        </div>
                    </div>

                    <!-- Hazır Temalar -->
                    <div>
                        <h4 class="text-lg font-medium text-gray-800 dark:text-white mb-4">Hazır Temalar</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Hazır temalar hem aydınlık hem karanlık mod için renkleri ayarlar.</p>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <button @click="applyPresetTheme('default')" class="p-4 border-2 border-gray-200 dark:border-gray-600 rounded-lg hover:border-blue-500 transition-colors">
                                <div class="flex space-x-1 mb-2">
                                    <div class="w-4 h-4 bg-blue-500 rounded"></div>
                                    <div class="w-4 h-4 bg-green-500 rounded"></div>
                                    <div class="w-4 h-4 bg-yellow-500 rounded"></div>
                                    <div class="w-4 h-4 bg-red-500 rounded"></div>
                                </div>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Varsayılan</span>
                            </button>
                            <button @click="applyPresetTheme('ocean')" class="p-4 border-2 border-gray-200 dark:border-gray-600 rounded-lg hover:border-blue-500 transition-colors">
                                <div class="flex space-x-1 mb-2">
                                    <div class="w-4 h-4 bg-cyan-500 rounded"></div>
                                    <div class="w-4 h-4 bg-teal-500 rounded"></div>
                                    <div class="w-4 h-4 bg-blue-500 rounded"></div>
                                    <div class="w-4 h-4 bg-indigo-500 rounded"></div>
                                </div>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Okyanus</span>
                            </button>
                            <button @click="applyPresetTheme('forest')" class="p-4 border-2 border-gray-200 dark:border-gray-600 rounded-lg hover:border-blue-500 transition-colors">
                                <div class="flex space-x-1 mb-2">
                                    <div class="w-4 h-4 bg-green-600 rounded"></div>
                                    <div class="w-4 h-4 bg-emerald-500 rounded"></div>
                                    <div class="w-4 h-4 bg-lime-500 rounded"></div>
                                    <div class="w-4 h-4 bg-yellow-500 rounded"></div>
                                </div>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Orman</span>
                            </button>
                            <button @click="applyPresetTheme('sunset')" class="p-4 border-2 border-gray-200 dark:border-gray-600 rounded-lg hover:border-blue-500 transition-colors">
                                <div class="flex space-x-1 mb-2">
                                    <div class="w-4 h-4 bg-orange-500 rounded"></div>
                                    <div class="w-4 h-4 bg-red-500 rounded"></div>
                                    <div class="w-4 h-4 bg-pink-500 rounded"></div>
                                    <div class="w-4 h-4 bg-purple-500 rounded"></div>
                                </div>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Gün Batımı</span>
                            </button>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button @click="resetTheme()" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg transition-colors">
                            <i class="fas fa-undo mr-2"></i>Sıfırla
                        </button>
                        <button @click="saveTheme()" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg transition-colors">
                            <i class="fas fa-save mr-2"></i>Tema Ayarlarını Kaydet
                        </button>
                    </div>
                </div>
            </div>

            <!-- Diğer ayar sekmeleri için placeholder'lar -->
            <div x-show="activeTab === 'clinic'" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 card-shadow">
                <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-6">Klinik Bilgileri</h3>
                <p class="text-gray-600 dark:text-gray-400">Klinik bilgileri ayarları burada yer alacak...</p>
            </div>

            <div x-show="activeTab === 'notifications'" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 card-shadow">
                <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-6">Bildirim Ayarları</h3>
                <p class="text-gray-600 dark:text-gray-400">Bildirim ayarları burada yer alacak...</p>
            </div>

            <div x-show="activeTab === 'security'" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 card-shadow">
                <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-6">Güvenlik Ayarları</h3>
                <p class="text-gray-600 dark:text-gray-400">Güvenlik ayarları burada yer alacak...</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function settingsData() {
    return {
        activeTab: 'profile',
        editMode: 'light',
        profile: {
            name: 'Dr. Mehmet Yılmaz',
            specialty: 'Plastik Cerrah',
            email: 'dr.mehmet@klinik.com',
            phone: '+90 532 123 4567'
        },
        theme: {
            light: {
                cardBackground: '#ffffff',
                cardBorder: '#e5e7eb',
                primaryColor: '#3b82f6',
                successColor: '#10b981',
                warningColor: '#f59e0b',
                dangerColor: '#ef4444'
            },
            dark: {
                cardBackground: '#1f2937',
                cardBorder: '#374151',
                primaryColor: '#60a5fa',
                successColor: '#34d399',
                warningColor: '#fbbf24',
                dangerColor: '#f87171'
            }
        },
        
        getCurrentTheme() {
             return this.theme[this.editMode];
         },
         
         getCurrentPrimaryColor() {
             return this.theme[this.editMode].primaryColor;
         },
         
         getCurrentSuccessColor() {
             return this.theme[this.editMode].successColor;
         },
         
         getCurrentWarningColor() {
             return this.theme[this.editMode].warningColor;
         },
         
         getCurrentDangerColor() {
             return this.theme[this.editMode].dangerColor;
         },
         
         getCurrentCardStyle() {
             return this.theme[this.editMode].cardBackground;
         },
         
         setPrimaryColor(color) {
             this.theme[this.editMode].primaryColor = color;
             this.applyTheme();
         },
         
         setSuccessColor(color) {
             this.theme[this.editMode].successColor = color;
             this.applyTheme();
         },
         
         setWarningColor(color) {
             this.theme[this.editMode].warningColor = color;
             this.applyTheme();
         },
         
         setDangerColor(color) {
             this.theme[this.editMode].dangerColor = color;
             this.applyTheme();
         },
         
         setCardStyle(style) {
             this.theme[this.editMode].cardBackground = style;
             this.applyTheme();
         },
         
         primaryColors: [
             { name: 'Mavi', value: '#3b82f6' },
             { name: 'Yeşil', value: '#10b981' },
             { name: 'Mor', value: '#8b5cf6' },
             { name: 'Kırmızı', value: '#ef4444' },
             { name: 'Turuncu', value: '#f97316' },
             { name: 'Pembe', value: '#ec4899' }
         ],
         
         successColors: [
             { name: 'Yeşil', value: '#10b981' },
             { name: 'Açık Yeşil', value: '#22c55e' },
             { name: 'Koyu Yeşil', value: '#16a34a' },
             { name: 'Teal', value: '#14b8a6' }
         ],
         
         warningColors: [
             { name: 'Sarı', value: '#f59e0b' },
             { name: 'Turuncu', value: '#f97316' },
             { name: 'Amber', value: '#d97706' },
             { name: 'Açık Sarı', value: '#eab308' }
         ],
         
         dangerColors: [
             { name: 'Kırmızı', value: '#ef4444' },
             { name: 'Koyu Kırmızı', value: '#dc2626' },
             { name: 'Rose', value: '#f43f5e' },
             { name: 'Pembe', value: '#ec4899' }
         ],
         
         cardStyles: [
             { name: 'Beyaz', value: '#ffffff' },
             { name: 'Açık Gri', value: '#f9fafb' },
             { name: 'Mavi Ton', value: '#f0f9ff' },
             { name: 'Yeşil Ton', value: '#f0fdf4' }
         ]
        
        loadSettings() {
            // Profil ayarlarını yükle
            const savedProfile = localStorage.getItem('clinic_profile');
            if (savedProfile) {
                this.profile = { ...this.profile, ...JSON.parse(savedProfile) };
            }
            
            // Tema ayarlarını yükle
            const savedTheme = localStorage.getItem('clinic_theme');
            if (savedTheme) {
                this.theme = { ...this.theme, ...JSON.parse(savedTheme) };
                this.applyThemeToPage();
            }
        },
        
        saveProfile() {
            localStorage.setItem('clinic_profile', JSON.stringify(this.profile));
            this.showNotification('Profil bilgileri başarıyla kaydedildi!', 'success');
        },
        
        saveTheme() {
            localStorage.setItem('clinic_theme', JSON.stringify(this.theme));
            this.applyThemeToPage();
            this.showNotification('Tema ayarları başarıyla kaydedildi!', 'success');
        },
        
        applyThemeToPage() {
            // CSS custom properties ile tema renklerini uygula
            const root = document.documentElement;
            const isDark = document.documentElement.classList.contains('dark');
            
            // Mevcut mod için tema renklerini uygula
            if (isDark) {
                root.style.setProperty('--custom-card-bg', this.theme.dark.cardBackground);
                root.style.setProperty('--custom-card-border', this.theme.dark.cardBorder);
                root.style.setProperty('--custom-primary', this.theme.dark.primaryColor);
                root.style.setProperty('--custom-success', this.theme.dark.successColor);
                root.style.setProperty('--custom-warning', this.theme.dark.warningColor);
                root.style.setProperty('--custom-danger', this.theme.dark.dangerColor);
            } else {
                root.style.setProperty('--custom-card-bg', this.theme.light.cardBackground);
                root.style.setProperty('--custom-card-border', this.theme.light.cardBorder);
                root.style.setProperty('--custom-primary', this.theme.light.primaryColor);
                root.style.setProperty('--custom-success', this.theme.light.successColor);
                root.style.setProperty('--custom-warning', this.theme.light.warningColor);
                root.style.setProperty('--custom-danger', this.theme.light.dangerColor);
            }
            
            // Global tema değişikliği eventi gönder
            window.dispatchEvent(new CustomEvent('themeChanged', {
                detail: {
                    theme: this.theme,
                    isDark: isDark
                }
            }));
        },
        
        // Dark mode değişikliklerini dinle
        initThemeWatcher() {
            const observer = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                        this.applyThemeToPage();
                    }
                });
            });
            
            observer.observe(document.documentElement, {
                attributes: true,
                attributeFilter: ['class']
            });
        },
        
        resetTheme() {
            this.theme = {
                light: {
                    cardBackground: '#ffffff',
                    cardBorder: '#e5e7eb',
                    primaryColor: '#3b82f6',
                    successColor: '#10b981',
                    warningColor: '#f59e0b',
                    dangerColor: '#ef4444'
                },
                dark: {
                    cardBackground: '#1f2937',
                    cardBorder: '#374151',
                    primaryColor: '#60a5fa',
                    successColor: '#34d399',
                    warningColor: '#fbbf24',
                    dangerColor: '#f87171'
                }
            };
        },
        
        applyPresetTheme(preset) {
            const presets = {
                default: {
                    light: {
                        cardBackground: '#ffffff',
                        cardBorder: '#e5e7eb',
                        primaryColor: '#3b82f6',
                        successColor: '#10b981',
                        warningColor: '#f59e0b',
                        dangerColor: '#ef4444'
                    },
                    dark: {
                        cardBackground: '#1f2937',
                        cardBorder: '#374151',
                        primaryColor: '#60a5fa',
                        successColor: '#34d399',
                        warningColor: '#fbbf24',
                        dangerColor: '#f87171'
                    }
                },
                ocean: {
                    light: {
                        cardBackground: '#f0f9ff',
                        cardBorder: '#0ea5e9',
                        primaryColor: '#0891b2',
                        successColor: '#14b8a6',
                        warningColor: '#0ea5e9',
                        dangerColor: '#6366f1'
                    },
                    dark: {
                        cardBackground: '#0c4a6e',
                        cardBorder: '#0284c7',
                        primaryColor: '#38bdf8',
                        successColor: '#2dd4bf',
                        warningColor: '#38bdf8',
                        dangerColor: '#818cf8'
                    }
                },
                forest: {
                    light: {
                        cardBackground: '#f0fdf4',
                        cardBorder: '#16a34a',
                        primaryColor: '#16a34a',
                        successColor: '#10b981',
                        warningColor: '#84cc16',
                        dangerColor: '#eab308'
                    },
                    dark: {
                        cardBackground: '#14532d',
                        cardBorder: '#15803d',
                        primaryColor: '#22c55e',
                        successColor: '#34d399',
                        warningColor: '#a3e635',
                        dangerColor: '#facc15'
                    }
                },
                sunset: {
                    light: {
                        cardBackground: '#fff7ed',
                        cardBorder: '#f97316',
                        primaryColor: '#f97316',
                        successColor: '#ef4444',
                        warningColor: '#ec4899',
                        dangerColor: '#a855f7'
                    },
                    dark: {
                        cardBackground: '#7c2d12',
                        cardBorder: '#ea580c',
                        primaryColor: '#fb923c',
                        successColor: '#f87171',
                        warningColor: '#f472b6',
                        dangerColor: '#c084fc'
                    }
                }
            };
            
            if (presets[preset]) {
                this.theme = { ...presets[preset] };
            }
        },
        
        showNotification(message, type = 'info') {
            // Basit bir bildirim sistemi
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg text-white transition-all duration-300 ${
                type === 'success' ? 'bg-green-500' : 
                type === 'error' ? 'bg-red-500' : 
                type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500'
            }`;
            notification.innerHTML = `
                <div class="flex items-center space-x-2">
                    <i class="fas ${
                        type === 'success' ? 'fa-check-circle' : 
                        type === 'error' ? 'fa-exclamation-circle' : 
                        type === 'warning' ? 'fa-exclamation-triangle' : 'fa-info-circle'
                    }"></i>
                    <span>${message}</span>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.style.opacity = '0';
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    document.body.removeChild(notification);
                }, 300);
            }, 3000);
        },
        
        // Sayfa yüklendiğinde tema watcher'ı başlat
        init() {
            this.loadSettings();
            this.initThemeWatcher();
        }
    }
}

// Global tema fonksiyonları
window.themeManager = {
    // Kaydedilmiş temayı yükle
    loadSavedTheme() {
        const savedTheme = localStorage.getItem('clinic_theme');
        if (savedTheme) {
            const themeData = JSON.parse(savedTheme);
            this.applyTheme(themeData);
        }
    },
    
    // Temayı uygula
    applyTheme(themeData) {
        const root = document.documentElement;
        const isDark = document.documentElement.classList.contains('dark');
        
        if (isDark && themeData.dark) {
            root.style.setProperty('--custom-card-bg', themeData.dark.cardBackground);
            root.style.setProperty('--custom-card-border', themeData.dark.cardBorder);
            root.style.setProperty('--custom-primary', themeData.dark.primaryColor);
            root.style.setProperty('--custom-success', themeData.dark.successColor);
            root.style.setProperty('--custom-warning', themeData.dark.warningColor);
            root.style.setProperty('--custom-danger', themeData.dark.dangerColor);
        } else if (!isDark && themeData.light) {
            root.style.setProperty('--custom-card-bg', themeData.light.cardBackground);
            root.style.setProperty('--custom-card-border', themeData.light.cardBorder);
            root.style.setProperty('--custom-primary', themeData.light.primaryColor);
            root.style.setProperty('--custom-success', themeData.light.successColor);
            root.style.setProperty('--custom-warning', themeData.light.warningColor);
            root.style.setProperty('--custom-danger', themeData.light.dangerColor);
        }
    },
    
    // Dark mode değişikliklerini dinle
    initGlobalWatcher() {
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                    this.loadSavedTheme();
                }
            });
        });
        
        observer.observe(document.documentElement, {
            attributes: true,
            attributeFilter: ['class']
        });
    }
};

// Sayfa yüklendiğinde global tema yöneticisini başlat
document.addEventListener('DOMContentLoaded', function() {
    window.themeManager.loadSavedTheme();
    window.themeManager.initGlobalWatcher();
});

// Tema değişikliği eventini dinle
window.addEventListener('themeChanged', function(event) {
    window.themeManager.applyTheme(event.detail.theme);
});
</script>
@endpush