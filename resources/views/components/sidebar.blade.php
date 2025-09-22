<!-- Desktop Sidebar -->
<div 
    class="sidebar bg-gradient-to-b from-blue-800 to-blue-900 text-white h-screen fixed top-0 left-0 shadow-xl flex flex-col transition-all duration-300 z-50 hidden md:flex border-r border-blue-700"
    :class="{
        'w-64': !sidebarCollapsed,
        'w-16': sidebarCollapsed
    }"
    x-data="{
        menuItems: [
            @if(Auth::user()->isDoctor())
                { id: 'doctor-panel', name: 'Doktor Paneli', icon: 'fas fa-user-md', route: '/doctor-panel', color: 'text-green-300' },
            @endif
            { id: 'dashboard', name: 'Anasayfa', icon: 'fas fa-tachometer-alt', route: '/', color: 'text-blue-300' },
            { id: 'clinic', name: 'Randevular', icon: 'fas fa-calendar-alt', route: '/clinic', color: 'text-purple-300' },
            { id: 'patients', name: 'Hasta Kayıtları', icon: 'fas fa-users', route: '/patients', color: 'text-yellow-300' },
            { id: 'operations', name: 'Operasyonlar', icon: 'fas fa-procedures', route: '/operations', color: 'text-red-300' },
            { id: 'whatsapp', name: 'Mesaj Otomasyon', icon: 'fab fa-whatsapp', route: '/whatsapp', color: 'text-green-400' },
            { id: 'payment-reports', name: 'Ücret Raporları', icon: 'fas fa-money-bill-wave', route: '/payment-reports', color: 'text-emerald-300' },
            { id: 'reports', name: 'Raporlar', icon: 'fas fa-chart-bar', route: '/reports', color: 'text-indigo-300' }
        ]
    }"
>
    <!-- Logo Section -->
    <div class="p-4 border-b border-blue-700/50" :class="sidebarCollapsed ? 'flex justify-center' : 'flex items-center justify-center'">
        <div class="flex items-center" :class="sidebarCollapsed ? 'space-x-0' : 'space-x-3'">
            <div class="bg-white p-2 rounded-xl shadow-lg flex-shrink-0">
                <img src="{{ asset('klinikgo.png') }}" alt="KlinikGo Logo" class="h-6 w-auto">
            </div>
            <span 
                class="text-white font-bold text-xl tracking-wide transition-all duration-300"
                x-show="!sidebarCollapsed"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform scale-100"
                x-transition:leave-end="opacity-0 transform scale-95"
            >
                KlinikGo
            </span>
        </div>
    </div>

    <!-- User Info -->
    <div class="p-4 border-b border-blue-700/50" :class="sidebarCollapsed ? 'flex justify-center' : 'flex items-center space-x-3'">
        <div class="h-12 w-12 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center flex-shrink-0 shadow-lg ring-2 ring-blue-400/30">
            <i class="fas fa-user-md text-white text-lg"></i>
        </div>
        <div 
            class="sidebar-text transition-all duration-300 min-w-0"
            x-show="!sidebarCollapsed"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-x-4"
            x-transition:enter-end="opacity-100 transform translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform translate-x-0"
            x-transition:leave-end="opacity-0 transform translate-x-4"
        >
            <div class="font-semibold text-white truncate">{{ Auth::user()->name }}</div>
            <div class="text-xs text-blue-200 truncate">{{ Auth::user()->getRoleDisplayName() }}</div>
        </div>
    </div>

    <!-- Main Menu -->
    <nav class="flex-1 overflow-y-auto py-2 px-2">
        <ul class="space-y-1">
            <template x-for="item in menuItems" :key="item.id">
                <li>
                    <a 
                        :href="item.route"
                        class="menu-item group flex items-center rounded-xl transition-all duration-200 w-full relative overflow-hidden"
                        :class="{
                            'bg-blue-600/50 shadow-lg ring-1 ring-blue-400/30': currentPage === item.id,
                            'hover:bg-blue-700/50 hover:shadow-md': currentPage !== item.id,
                            'justify-center p-3': sidebarCollapsed,
                            'space-x-3 px-4 py-3': !sidebarCollapsed
                        }"
                    >
                        <!-- Icon with dynamic color -->
                        <i 
                            :class="item.icon + ' text-lg transition-all duration-200 ' + (currentPage === item.id ? 'text-white' : item.color)"
                            class="w-6 text-center flex-shrink-0"
                        ></i>
                        
                        <!-- Menu Text -->
                        <span 
                            class="sidebar-text font-medium transition-all duration-300 flex-1 min-w-0"
                            :class="currentPage === item.id ? 'text-white' : 'text-blue-100'"
                            x-show="!sidebarCollapsed"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 transform translate-x-4"
                            x-transition:enter-end="opacity-100 transform translate-x-0"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 transform translate-x-0"
                            x-transition:leave-end="opacity-0 transform translate-x-4"
                            x-text="item.name"
                        ></span>
                        
                        <!-- Badge -->
                        <span 
                            x-show="item.badge && !sidebarCollapsed" 
                            class="bg-red-500 text-white text-xs px-2 py-1 rounded-full font-semibold shadow-sm"
                            x-text="item.badge"
                        ></span>
                        
                        <!-- Active indicator -->
                        <div 
                            x-show="currentPage === item.id"
                            class="absolute right-0 top-0 bottom-0 w-1 bg-white rounded-l-full"
                        ></div>
                        
                        <!-- Tooltip for collapsed state -->
                        <div 
                            x-show="sidebarCollapsed"
                            class="absolute left-full ml-2 px-3 py-2 bg-gray-900 text-white text-sm rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none whitespace-nowrap z-50"
                            x-text="item.name"
                        ></div>
                    </a>
                </li>
            </template>
        </ul>
    </nav>

    <!-- Bottom Controls -->
    <div class="p-4 border-t border-blue-700/50 mt-auto">
        <!-- Collapse Button -->
        <button 
            @click="toggleSidebar()"
            class="group flex items-center w-full rounded-xl hover:bg-blue-700/50 transition-all duration-200 relative overflow-hidden"
            :class="{
                'justify-center p-3': sidebarCollapsed,
                'space-x-3 px-4 py-3': !sidebarCollapsed
            }"
        >
            <i 
                :class="sidebarCollapsed ? 'fas fa-chevron-right' : 'fas fa-chevron-left'"
                class="w-6 text-center text-blue-300 text-lg transition-all duration-300 flex-shrink-0"
            ></i>
            <span 
                class="sidebar-text font-medium text-blue-100 transition-all duration-300 flex-1 text-left"
                x-show="!sidebarCollapsed"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform translate-x-4"
                x-transition:enter-end="opacity-100 transform translate-x-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform translate-x-0"
                x-transition:leave-end="opacity-0 transform translate-x-4"
            >
                Daralt
            </span>
            
            <!-- Tooltip for collapsed state -->
            <div 
                x-show="sidebarCollapsed"
                class="absolute left-full ml-2 px-3 py-2 bg-gray-900 text-white text-sm rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none whitespace-nowrap z-50"
            >
                Daralt/Genişlet
            </div>
        </button>
    </div>
</div>

<!-- Mobile Bottom Navbar -->
<div class="md:hidden fixed bottom-0 left-0 right-0 bg-gradient-to-r from-blue-800 to-blue-900 shadow-2xl z-50 border-t border-blue-700" 
     x-data="{
        mobileMenuOpen: false,
        mainMenuItems: [
            { id: 'dashboard', name: 'Anasayfa', icon: 'fas fa-tachometer-alt', route: '/', color: 'text-blue-300' },
            { id: 'clinic', name: 'Randevular', icon: 'fas fa-calendar-alt', route: '/clinic', color: 'text-purple-300' },
            { id: 'patients', name: 'Hasta Kayıtları', icon: 'fas fa-users', route: '/patients', color: 'text-yellow-300' },
            { id: 'operations', name: 'Operasyonlar', icon: 'fas fa-procedures', route: '/operations', color: 'text-red-300' }
        ],
        hiddenMenuItems: [
            @if(Auth::user()->isDoctor())
                { id: 'doctor-panel', name: 'Doktor Paneli', icon: 'fas fa-user-md', route: '/doctor-panel', color: 'text-green-300' },
            @endif
            { id: 'whatsapp', name: 'Mesaj Otomasyon', icon: 'fab fa-whatsapp', route: '/whatsapp', color: 'text-green-400' },
            { id: 'payment-reports', name: 'Ücret Raporları', icon: 'fas fa-money-bill-wave', route: '/payment-reports', color: 'text-emerald-300' },
            { id: 'reports', name: 'Raporlar', icon: 'fas fa-chart-bar', route: '/reports', color: 'text-indigo-300' }
        ]
     }">
    
    <!-- Hidden Menu Items (Slide up from bottom) -->
    <div class="absolute bottom-full left-0 right-0 bg-gradient-to-b from-blue-800 to-blue-900 transition-all duration-300 ease-in-out rounded-t-2xl shadow-2xl border-t border-blue-700"
         :class="mobileMenuOpen ? 'translate-y-0 opacity-100' : 'translate-y-full opacity-0'"
         x-show="mobileMenuOpen"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-full"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform translate-y-full">
        
        <!-- User Info Header -->
        <div class="px-4 py-4 border-b border-blue-700/50">
            <div class="flex items-center space-x-3">
                <div class="h-12 w-12 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-lg ring-2 ring-blue-400/30">
                    <i class="fas fa-user-md text-white text-lg"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="font-semibold text-white truncate">{{ Auth::user()->name }}</div>
                    <div class="text-xs text-blue-200 truncate">{{ Auth::user()->getRoleDisplayName() }}</div>
                </div>
                <div class="flex items-center space-x-1">
                    <div class="w-2 h-2 bg-green-400 rounded-full"></div>
                    <span class="text-xs text-green-300">Aktif</span>
                </div>
            </div>
        </div>
        
        <!-- Menu Items -->
        <div class="py-2 px-2">
            <template x-for="item in hiddenMenuItems" :key="item.id">
                <a :href="item.route" 
                   class="flex items-center px-4 py-3 mx-2 rounded-xl hover:bg-blue-700/50 transition-all duration-200 group"
                   @click="mobileMenuOpen = false">
                    <i :class="item.icon + ' ' + item.color" class="w-6 text-center mr-3 text-lg transition-colors duration-200"></i>
                    <span x-text="item.name" class="text-sm font-medium text-blue-100 group-hover:text-white transition-colors duration-200"></span>
                </a>
            </template>
        </div>
    </div>
    
    <!-- Main Bottom Navigation -->
    <div class="flex items-center justify-between px-4 py-3 h-16">
        <!-- Left Buttons -->
        <div class="flex space-x-2">
            <a href="/" 
               class="flex items-center justify-center w-12 h-12 rounded-xl transition-all duration-200 relative"
               :class="currentPage === 'dashboard' ? 'bg-blue-600 shadow-lg' : 'hover:bg-blue-700/50'">
                <i class="fas fa-tachometer-alt text-lg transition-colors duration-200"
                   :class="currentPage === 'dashboard' ? 'text-white' : 'text-blue-300'"></i>
                <div x-show="currentPage === 'dashboard'" class="absolute -top-1 -right-1 w-3 h-3 bg-white rounded-full"></div>
            </a>
            <a href="/clinic" 
               class="flex items-center justify-center w-12 h-12 rounded-xl transition-all duration-200 relative"
               :class="currentPage === 'clinic' ? 'bg-blue-600 shadow-lg' : 'hover:bg-blue-700/50'">
                <i class="fas fa-calendar-alt text-lg transition-colors duration-200"
                   :class="currentPage === 'clinic' ? 'text-white' : 'text-purple-300'"></i>
                <div x-show="currentPage === 'clinic'" class="absolute -top-1 -right-1 w-3 h-3 bg-white rounded-full"></div>
            </a>
        </div>
        
        <!-- Center Hamburger Button -->
        <button @click="mobileMenuOpen = !mobileMenuOpen" 
                class="flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 hover:from-blue-400 hover:to-blue-500 transition-all duration-200 shadow-xl ring-2 ring-blue-400/30 transform"
                :class="{ 'scale-95 shadow-lg': mobileMenuOpen }">
            <i class="fas fa-bars text-white text-xl transition-transform duration-300" 
               :class="{ 'rotate-90': mobileMenuOpen }"></i>
        </button>
        
        <!-- Right Buttons -->
        <div class="flex space-x-2">
            <a href="/patients" 
               class="flex items-center justify-center w-12 h-12 rounded-xl transition-all duration-200 relative"
               :class="currentPage === 'patients' ? 'bg-blue-600 shadow-lg' : 'hover:bg-blue-700/50'">
                <i class="fas fa-users text-lg transition-colors duration-200"
                   :class="currentPage === 'patients' ? 'text-white' : 'text-yellow-300'"></i>
                <div x-show="currentPage === 'patients'" class="absolute -top-1 -right-1 w-3 h-3 bg-white rounded-full"></div>
            </a>
            <a href="/operations" 
               class="flex items-center justify-center w-12 h-12 rounded-xl transition-all duration-200 relative"
               :class="currentPage === 'operations' ? 'bg-blue-600 shadow-lg' : 'hover:bg-blue-700/50'">
                <i class="fas fa-procedures text-lg transition-colors duration-200"
                   :class="currentPage === 'operations' ? 'text-white' : 'text-red-300'"></i>
                <div x-show="currentPage === 'operations'" class="absolute -top-1 -right-1 w-3 h-3 bg-white rounded-full"></div>
            </a>
        </div>
    </div>
    
    <!-- Overlay for closing menu -->
    <div x-show="mobileMenuOpen" 
         @click="mobileMenuOpen = false"
         class="fixed inset-0 bg-black bg-opacity-25 z-[-1]"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
    </div>
</div>