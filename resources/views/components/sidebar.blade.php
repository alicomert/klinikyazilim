<!-- Desktop Sidebar -->
<div 
    class="sidebar bg-blue-800 text-white h-screen fixed top-0 left-0 shadow-lg flex flex-col transition-all duration-300 z-40 hidden md:flex"
    :class="{
        'w-64': !sidebarCollapsed,
        'w-16': sidebarCollapsed,
        'sidebar-open': !sidebarCollapsed
    }"
    x-data="{
        menuItems: [
            @if(Auth::user()->isDoctor())
                { id: 'doctor-panel', name: 'Doktor Paneli', icon: 'fas fa-user-md', route: '/doctor-panel' },
            @endif
            { id: 'dashboard', name: 'Anasayfa', icon: 'fas fa-tachometer-alt', route: '/' },
            { id: 'clinic', name: 'Randevular', icon: 'fas fa-calendar-alt', route: '/clinic' },
            { id: 'patients', name: 'Hasta Kayıtları', icon: 'fas fa-users', route: '/patients' },
            { id: 'operations', name: 'Operasyonlar', icon: 'fas fa-procedures', route: '/operations' },
            { id: 'payment-reports', name: 'Ücret Raporları', icon: 'fas fa-money-bill-wave', route: '/payment-reports' },
            { id: 'reports', name: 'Raporlar', icon: 'fas fa-chart-bar', route: '/reports' }
        ]
    }"
>
    <!--             { id: 'settings', name: 'Ayarlar', icon: 'fas fa-cog', route: '/settings' }, bir de messages kapattım. 
 -->
    <div class="p-4 flex items-center justify-center border-b border-blue-700">
        <div class="flex items-center space-x-2">
            <div class="bg-white p-2 rounded-lg">
                <img src="{{ asset('klinikgo.png') }}" alt="KlinikGo Logo" class="h-6 w-auto">
            </div>
            <span class="text-white font-semibold text-lg">KlinikGo</span>
        </div>
    </div>

    <!-- Doctor Info -->
    <div class="p-4 flex items-center space-x-3 border-b border-blue-700">
        <div class="h-12 w-12 rounded-full bg-blue-600 flex items-center justify-center flex-shrink-0">
            <i class="fas fa-user-md text-white"></i>
        </div>
        <div 
            class="sidebar-text transition-opacity duration-300"
            :class="{ 'opacity-0': sidebarCollapsed }"
            x-show="!sidebarCollapsed"
        >
            <div class="font-semibold">{{ Auth::user()->name }}</div>
            <div class="text-xs text-blue-200">{{ Auth::user()->getRoleDisplayName() }}</div>
        </div>
    </div>

    <!-- Main Menu -->
    <nav class="flex-1 overflow-y-auto py-4">
        <ul>
            <template x-for="item in menuItems" :key="item.id">
                <li>
                    <a 
                        :href="item.route"
                        class="menu-item flex items-center p-3 hover:bg-blue-700 text-blue-100 transition-colors duration-200 w-full"
                        :class="{
                            'bg-blue-700': currentPage === item.id,
                            'justify-center': sidebarCollapsed,
                            'space-x-3': !sidebarCollapsed
                        }"
                    >
                        <i :class="item.icon + ' w-6 text-center'"></i>
                        <span 
                            class="sidebar-text transition-opacity duration-300"
                            :class="{ 'opacity-0': sidebarCollapsed }"
                            x-show="!sidebarCollapsed"
                            x-text="item.name"
                        ></span>
                        <span 
                            x-show="item.badge && !sidebarCollapsed" 
                            class="ml-auto bg-red-500 text-white text-xs px-2 py-1 rounded-full"
                            x-text="item.badge"
                        ></span>
                    </a>
                </li>
            </template>
        </ul>
    </nav>

    <!-- Bottom Controls -->
    <div class="p-4 border-t border-blue-700 space-y-2">
        <!-- Collapse Button -->
        <button 
            @click="toggleSidebar()"
            class="menu-item flex items-center p-2 hover:bg-blue-700 rounded-lg text-blue-100 w-full transition-colors duration-200"
            :class="{
                'justify-center': sidebarCollapsed,
                'space-x-3': !sidebarCollapsed
            }"
        >
            <i 
                :class="sidebarCollapsed ? 'fas fa-chevron-right' : 'fas fa-chevron-left'"
                class="w-6 text-center transition-transform duration-300"
            ></i>
            <span 
                class="sidebar-text transition-opacity duration-300"
                :class="{ 'opacity-0': sidebarCollapsed }"
                x-show="!sidebarCollapsed"
            >
                Daralt
            </span>
        </button>
    </div>
</div>

<!-- Mobile Bottom Navbar -->
<div class="md:hidden fixed bottom-0 left-0 right-0 bg-blue-800 shadow-lg z-50" 
     x-data="{
        mobileMenuOpen: false,
        mainMenuItems: [
            { id: 'dashboard', name: 'Anasayfa', icon: 'fas fa-tachometer-alt', route: '/' },
            { id: 'clinic', name: 'Randevular', icon: 'fas fa-calendar-alt', route: '/clinic' },
            { id: 'patients', name: 'Hasta Kayıtları', icon: 'fas fa-users', route: '/patients' },
            { id: 'operations', name: 'Operasyonlar', icon: 'fas fa-procedures', route: '/operations' }
        ],
        hiddenMenuItems: [
            @if(Auth::user()->isDoctor())
                { id: 'doctor-panel', name: 'Doktor Paneli', icon: 'fas fa-user-md', route: '/doctor-panel' },
            @endif
            { id: 'payment-reports', name: 'Ücret Raporları', icon: 'fas fa-money-bill-wave', route: '/payment-reports' },
            { id: 'reports', name: 'Raporlar', icon: 'fas fa-chart-bar', route: '/reports' }
        ]
     }">
    
    <!-- Hidden Menu Items (Slide up from bottom) -->
    <div class="absolute bottom-full left-0 right-0 bg-blue-800 transition-all duration-300 ease-in-out"
         :class="mobileMenuOpen ? 'translate-y-0 opacity-100' : 'translate-y-full opacity-0'"
         x-show="mobileMenuOpen"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-full"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform translate-y-full">
        <div class="px-4 py-3 border-b border-blue-700">
            <div class="flex items-center space-x-3">
                <div class="h-10 w-10 rounded-full bg-blue-600 flex items-center justify-center">
                    <i class="fas fa-user-md text-white text-sm"></i>
                </div>
                <div>
                    <div class="font-semibold text-sm">{{ Auth::user()->name }}</div>
                    <div class="text-xs text-blue-200">{{ Auth::user()->getRoleDisplayName() }}</div>
                </div>
            </div>
        </div>
        <div class="py-2">
            <template x-for="item in hiddenMenuItems" :key="item.id">
                <a :href="item.route" 
                   class="flex items-center px-4 py-3 hover:bg-blue-700 transition-colors duration-200"
                   @click="mobileMenuOpen = false">
                    <i :class="item.icon" class="w-6 text-center mr-3"></i>
                    <span x-text="item.name" class="text-sm"></span>
                </a>
            </template>
        </div>
    </div>
    
    <!-- Main Bottom Navigation -->
    <div class="flex items-center justify-between px-4 py-2 h-16">
        <!-- Left Buttons -->
        <div class="flex space-x-4">
            <a href="/" class="flex items-center justify-center w-12 h-12 rounded-lg hover:bg-blue-700 transition-colors duration-200">
                <i class="fas fa-tachometer-alt text-white text-lg"></i>
            </a>
            <a href="/clinic" class="flex items-center justify-center w-12 h-12 rounded-lg hover:bg-blue-700 transition-colors duration-200">
                <i class="fas fa-calendar-alt text-white text-lg"></i>
            </a>
        </div>
        
        <!-- Center Hamburger Button -->
        <button @click="mobileMenuOpen = !mobileMenuOpen" 
                class="flex items-center justify-center w-14 h-14 rounded-full bg-blue-600 hover:bg-blue-500 transition-all duration-200 shadow-lg"
                :class="{ 'bg-blue-500': mobileMenuOpen }">
            <i class="fas fa-bars text-white text-xl transition-transform duration-200" 
               :class="{ 'rotate-90': mobileMenuOpen }"></i>
        </button>
        
        <!-- Right Buttons -->
        <div class="flex space-x-4">
            <a href="/patients" class="flex items-center justify-center w-12 h-12 rounded-lg hover:bg-blue-700 transition-colors duration-200">
                <i class="fas fa-users text-white text-lg"></i>
            </a>
            <a href="/operations" class="flex items-center justify-center w-12 h-12 rounded-lg hover:bg-blue-700 transition-colors duration-200">
                <i class="fas fa-procedures text-white text-lg"></i>
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