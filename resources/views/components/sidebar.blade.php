<!-- Sidebar -->
<div 
    class="sidebar bg-blue-800 text-white h-screen fixed top-0 left-0 shadow-lg flex flex-col transition-all duration-300 z-40"
    :class="{
        'w-64': !sidebarCollapsed,
        'w-16': sidebarCollapsed,
        'sidebar-open': !sidebarCollapsed
    }"
    x-data="{
        menuItems: [
            { id: 'dashboard', name: 'Dashboard', icon: 'fas fa-tachometer-alt', route: '/' },
            { id: 'patients', name: 'Hasta Kayıtları', icon: 'fas fa-users', route: '/patients' },
            { id: 'operations', name: 'Operasyonlar', icon: 'fas fa-procedures', route: '/operations' },
            { id: 'settings', name: 'Ayarlar', icon: 'fas fa-cog', route: '/settings' },
            { id: 'reports', name: 'Raporlar', icon: 'fas fa-chart-bar', route: '/reports' },
            { id: 'doctor-panel', name: 'Doktor Paneli', icon: 'fas fa-user-md', route: '/doctor-panel' },
            { id: 'messages', name: 'Hasta Mesajları', icon: 'fas fa-comments', route: '/messages', badge: 3 }
        ]
    }"
>
    <!-- Logo -->
    <div class="p-4 flex items-center space-x-2 border-b border-blue-700">
        <div class="bg-white p-2 rounded-lg">
            <i class="fas fa-hospital text-blue-600 text-xl"></i>
        </div>
        <span 
            class="logo-text font-bold text-xl transition-opacity duration-300"
            :class="{ 'opacity-0': sidebarCollapsed }"
            x-show="!sidebarCollapsed"
        >
            EstetikLine
        </span>
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
            <div class="font-semibold">Dr. Ahmet Yılmaz</div>
            <div class="text-xs text-blue-200">Plastik Cerrahi Uzmanı</div>
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