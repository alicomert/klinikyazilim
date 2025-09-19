<!-- Top Bar -->
<header class="bg-white shadow-sm py-4 px-6 flex items-center justify-between transition-colors duration-300">
    <div class="flex items-center space-x-4">
        <h1 class="text-2xl font-bold text-gray-800" x-text="getPageTitle(currentPage)"></h1>
        <div class="text-sm text-gray-500 hidden sm:block">
            <i class="fas fa-calendar-alt mr-1"></i>
            <span x-text="getCurrentDate()"></span>
        </div>
    </div>
    
    <div class="flex items-center space-x-4">
        <!-- Notifications -->
        <div class="relative">
            <button class="p-2 rounded-full hover:bg-gray-100 transition-colors duration-200">
                <i class="fas fa-bell text-gray-500"></i>
                <span class="absolute top-0 right-0 h-2 w-2 rounded-full bg-red-500"></span>
            </button>
        </div>

        
        <!-- User Profile -->
        <div class="border-l border-gray-200 pl-4">
            <div class="flex items-center space-x-2" x-data="{ profileOpen: false }">
                <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                    <i class="fas fa-user text-blue-600"></i>
                </div>
                <div class="hidden sm:block">
                    <span class="text-sm font-medium text-gray-800">{{ Auth::user()->name }}</span>
                </div>
                
                <!-- Profile Dropdown -->
                <div class="relative">
                    <button 
                        @click="profileOpen = !profileOpen"
                        class="p-1 rounded-full hover:bg-gray-100 transition-colors duration-200"
                    >
                        <i class="fas fa-chevron-down text-gray-500 text-xs"></i>
                    </button>
                    
                    <div 
                        x-show="profileOpen"
                        @click.away="profileOpen = false"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 border border-gray-200"
                        style="display: none;"
                    >
                      <!--  <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-user mr-2"></i>
                            Profil
                        </a>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-cog mr-2"></i>
                            Ayarlar
                        </a> -->
                        <div class="border-t border-gray-200"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-sign-out-alt mr-2"></i>
                                Çıkış Yap
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
    function getPageTitle(page) {
        const titles = {
            'dashboard': 'Anasayfa',
            'patients': 'Hasta Kayıtları',
            'operations': 'Operasyonlar',
            'settings': 'Ayarlar',
            'reports': 'Raporlar',
            'doctor-panel': 'Doktor Paneli',
            'messages': 'Hasta Mesajları'
        };
        return titles[page] || 'Dashboard';
    }
    
    function getCurrentDate() {
        return new Date().toLocaleDateString('tr-TR', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    }
</script>