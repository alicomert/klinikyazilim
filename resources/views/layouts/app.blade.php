<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'EstetikLine') }} - @yield('title', 'Dashboard')</title>

    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#0c3779">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="KlinikGo">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="msapplication-TileColor" content="#0c3779">
    <meta name="msapplication-tap-highlight" content="no">
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    
    <!-- PWA Icons -->
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('klinikgo.png') }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="apple-touch-icon" href="{{ asset('klinikgo.png') }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('klinikgo.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('klinikgo.png') }}">
    <link rel="apple-touch-icon" sizes="167x167" href="{{ asset('klinikgo.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Tailwind CSS v3 CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js CDN - Completely disable for Livewire pages to prevent conflicts -->
    @php
        $currentPath = request()->path();
        $isLivewirePage = (substr($currentPath, 0, 8) === 'whatsapp') || 
                         $currentPath === 'patients' || 
                         $currentPath === 'payment-reports' || 
                         $currentPath === 'operations' || 
                         $currentPath === 'doctor-panel';
    @endphp

    @if(!$isLivewirePage)
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @endif
    
    @livewireStyles
    
    <style>
        .sidebar {
            transition: all 0.3s;
            z-index: 9999 !important;
            position: fixed !important;
        }
        .sidebar-collapsed {
            width: 70px;
        }
        .sidebar-collapsed .sidebar-text {
            display: none;
        }
        .sidebar-collapsed .logo-text {
            display: none;
        }
        .sidebar-collapsed .menu-item {
            justify-content: center;
        }
        .content-area {
            transition: all 0.3s;
            position: relative;
            z-index: 1;
        }
        .content-expanded {
            margin-left: 70px;
        }
        .chart-container {
            height: 300px;
        }
        .stat-card {
            transition: all 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .progress-bar {
            transition: width 1s ease-in-out;
        }
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                z-index: 9999 !important;
                transform: translateX(-100%);
            }
            .sidebar-open {
                transform: translateX(0);
            }
            .content-area {
                margin-left: 0 !important;
            }
        }
        
        /* Mobile bottom navbar z-index */
        .md\\:hidden.fixed.bottom-0 {
            z-index: 9998 !important;
        }
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .card-shadow {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        
        /* Dark mode styles */
        .dark .bg-gray-50 {
            background-color: #1f2937 !important;
        }
        .dark .bg-white {
            background-color: #374151 !important;
        }
        .dark .bg-gray-100 {
            background-color: #4b5563 !important;
        }
        .dark .text-gray-800 {
            color: #f9fafb !important;
        }
        .dark .text-gray-500 {
            color: #d1d5db !important;
        }
        .dark .text-gray-600 {
            color: #d1d5db !important;
        }
        .dark .text-gray-700 {
            color: #e5e7eb !important;
        }
        .dark .text-gray-900 {
            color: #f9fafb !important;
        }
        .dark .border-gray-200 {
            border-color: #4b5563 !important;
        }
        .dark .border-gray-100 {
            border-color: #4b5563 !important;
        }
        .dark .shadow-sm {
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.3) !important;
        }
        .dark .hover\:bg-gray-50:hover {
            background-color: #4b5563 !important;
        }
        .dark .hover\:bg-gray-100:hover {
            background-color: #6b7280 !important;
        }
        .dark .bg-blue-50 {
            background-color: #1e3a8a !important;
        }
        .dark .bg-green-50 {
            background-color: #14532d !important;
        }
        .dark .bg-yellow-50 {
            background-color: #451a03 !important;
        }
        .dark .bg-red-50 {
            background-color: #7f1d1d !important;
        }
        .dark .bg-purple-50 {
            background-color: #581c87 !important;
        }
        .dark .bg-orange-50 {
            background-color: #7c2d12 !important;
        }
        .dark .bg-indigo-50 {
            background-color: #312e81 !important;
        }
        .dark .border-blue-100 {
            border-color: #1e40af !important;
        }
        .dark .border-green-100 {
            border-color: #166534 !important;
        }
        .dark .border-yellow-100 {
            border-color: #92400e !important;
        }
        .dark .border-red-100 {
            border-color: #991b1b !important;
        }
        .dark .border-purple-100 {
            border-color: #7c3aed !important;
        }
        .dark .border-orange-100 {
            border-color: #ea580c !important;
        }
        .dark .border-indigo-100 {
            border-color: #4338ca !important;
        }
    </style>
    
    @stack('styles')
</head>
<body class="bg-gray-50 transition-colors duration-300" x-data="appData()" x-init="init()">
    <!-- Mobile Menu Button -->
    <button 
        @click="sidebarCollapsed = !sidebarCollapsed" 
        class="md:hidden fixed top-4 left-4 z-[10000] bg-white dark:bg-gray-800 p-2 rounded-lg shadow-lg"
    >
        <i class="fas fa-bars text-blue-600 dark:text-blue-400"></i>
    </button>

    <!-- Sidebar Component -->
    @include('components.sidebar')

    <!-- Main Content -->
    <div 
        class="content-area min-h-screen transition-all duration-300 ml-64"
        :class="{
            'ml-64': !sidebarCollapsed,
            'ml-16': sidebarCollapsed
        }"
    >
        <!-- Header Component -->
        @include('components.header')

        <!-- Main Content -->
        <main id="spa-container" class="p-6 pt-20">
            <div class="max-w-7xl mx-auto">
                @yield('content')
            </div>
        </main>
    </div>

    @livewireScripts
    
    @stack('scripts')
    
    <!-- Alpine.js configuration - Only for Livewire pages -->
    @if($isLivewirePage)
    <script>
        document.addEventListener('livewire:init', () => {
            // Alpine.js is now available via Livewire - no conflicts
            console.log('Livewire Alpine.js initialized');
        });
    </script>
    @endif
    
    <script>
        // Global appData function - Compatible with both CDN and Livewire Alpine.js
        window.appData = function() {
            return {
                sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true',
                currentPage: 'dashboard',
                loading: false,
                
                init() {
                    // Wait for Alpine to be ready
                    this.$nextTick(() => {
                        this.currentPage = this.pathToPageId(window.location.pathname);
                        this.initRouter();
                        
                        this.$watch('sidebarCollapsed', value => {
                            localStorage.setItem('sidebarCollapsed', value);
                        });
                        
                        window.addEventListener('popstate', () => {
                            this.navigate(window.location.pathname, true);
                        });
                    });
                },
                

                
                toggleSidebar() {
                    this.sidebarCollapsed = !this.sidebarCollapsed;
                },
                


                // SPA Router
                initRouter() {
                    document.addEventListener('click', (e) => {
                        const anchor = e.target.closest('a.menu-item');
                        if (!anchor) return;
                        const href = anchor.getAttribute('href');
                        if (!href || href.startsWith('http') || href.startsWith('#')) return;
                        e.preventDefault();
                        this.navigate(href);
                    });
                },

                navigate(path, replace = false) {
                    if (replace) {
                        window.history.replaceState({}, '', path);
                    } else {
                        window.history.pushState({}, '', path);
                    }
                    this.setCurrentPageFromPath(path);
                    this.loadPage(path);
                },

                setCurrentPageFromPath(path) {
                    this.currentPage = this.pathToPageId(path);
                },

                pathToPageId(path) {
                    switch (path) {
                        case '/':
                        case '/dashboard':
                            return 'dashboard';
                        case '/clinic':
                            return 'clinic';
                        case '/patients':
                            return 'patients';
                        case '/operations':
                            return 'operations';
                        case '/settings':
                            return 'settings';
                        case '/reports':
                            return 'reports';
                        case '/doctor-panel':
                            return 'doctor-panel';
                        case '/messages':
                            return 'messages';
                        case '/whatsapp':
                        case '/whatsapp/dashboard':
                        case '/whatsapp/configs':
                        case '/whatsapp/templates':
                        case '/whatsapp/messages':
                        case '/whatsapp/reports':
                            return 'whatsapp';
                        default:
                            return 'dashboard';
                    }
                },

                navigateTo(pageId) {
                    const map = {
                        'dashboard': '/',
                        'clinic': '/clinic',
                        'patients': '/patients',
                        'operations': '/operations',
                        'settings': '/settings',
                        'reports': '/reports',
                        'doctor-panel': '/doctor-panel',
                        'messages': '/messages'
                    };
                    const path = map[pageId] || '/';
                    this.navigate(path);
                },

                async loadPage(path) {
                    const container = document.getElementById('spa-container');
                    if (!container) return;
                    this.loading = true;
                    container.classList.add('opacity-50');
                    try {
                        const res = await fetch(path, { credentials: 'same-origin' });
                        const html = await res.text();
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const incoming = doc.querySelector('#spa-container') || doc.querySelector('main');
                        if (incoming) {
                            // Extract and execute inline scripts from incoming content
                            const scripts = Array.from(incoming.querySelectorAll('script'));
                            // Set HTML first
                            container.innerHTML = incoming.innerHTML;
                            // Execute scripts
                            for (const oldScript of scripts) {
                                const s = document.createElement('script');
                                if (oldScript.src) {
                                    s.src = oldScript.src;
                                    s.defer = oldScript.defer || false;
                                } else {
                                    s.textContent = oldScript.textContent;
                                }
                                document.body.appendChild(s);
                                s.remove();
                            }
                            // Re-init Alpine for new content
                            if (window.Alpine && typeof window.Alpine.initTree === 'function') {
                                window.Alpine.initTree(container);
                            }
                        }
                        window.scrollTo(0, 0);
                    } catch (e) {
                        // Fail silently
                    } finally {
                        this.loading = false;
                        container.classList.remove('opacity-50');
                    }
                }
            }
        }
        
        // Global dashboard data function
        function dashboardData() {
            return {
                stats: [
                    {
                        id: 1,
                        title: 'Bugünkü Randevular',
                        value: '8',
                        change: '%12 artış',
                        color: 'text-blue-600 dark:text-blue-400',
                        icon: 'fas fa-calendar-day',
                        iconColor: 'text-blue-600',
                        bgColor: 'bg-blue-100 dark:bg-blue-900'
                    },
                    {
                        id: 2,
                        title: 'Toplam Hasta',
                        value: '1,247',
                        change: '%8 artış',
                        color: 'text-green-600 dark:text-green-400',
                        icon: 'fas fa-users',
                        iconColor: 'text-green-600',
                        bgColor: 'bg-green-100 dark:bg-green-900'
                    },
                    {
                        id: 3,
                        title: 'Bu Ay Operasyon',
                        value: '24',
                        change: '%15 artış',
                        color: 'text-purple-600 dark:text-purple-400',
                        icon: 'fas fa-procedures',
                        iconColor: 'text-purple-600',
                        bgColor: 'bg-purple-100 dark:bg-purple-900'
                    }
                ],
                todaySchedule: [
                    {
                        id: 1,
                        time: '09:00',
                        patient: 'Meltem Karaca',
                        procedure: 'Burun Estetiği Kontrol',
                        type: 'Kontrol',
                        timeColor: 'text-blue-600',
                        bgClass: 'bg-blue-50 dark:bg-blue-900/20',
                        badgeClass: 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-200'
                    },
                    {
                        id: 2,
                        time: '10:30',
                        patient: 'Ayşe Demir',
                        procedure: 'Yüz Germe Operasyonu',
                        type: 'Operasyon',
                        timeColor: 'text-green-600',
                        bgClass: 'bg-green-50 dark:bg-green-900/20',
                        badgeClass: 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-200'
                    },
                    {
                        id: 3,
                        time: '14:00',
                        patient: 'Mehmet Özkan',
                        procedure: 'Botoks Uygulaması',
                        type: 'Estetik',
                        timeColor: 'text-purple-600',
                        bgClass: 'bg-purple-50 dark:bg-purple-900/20',
                        badgeClass: 'bg-purple-100 text-purple-800 dark:bg-purple-800 dark:text-purple-200'
                    },
                    {
                        id: 4,
                        time: '16:00',
                        patient: 'Zeynep Yılmaz',
                        procedure: 'İlk Muayene',
                        type: 'Muayene',
                        timeColor: 'text-yellow-600',
                        bgClass: 'bg-yellow-50 dark:bg-yellow-900/20',
                        badgeClass: 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200'
                    }
                ],
                recentActivities: [
                    {
                        id: 1,
                        title: 'Operasyon tamamlandı',
                        description: 'Sema Tekin - Dudak Dolgusu',
                        time: '2 saat önce',
                        icon: 'fas fa-check',
                        iconColor: 'text-green-600',
                        iconBg: 'bg-green-100 dark:bg-green-900'
                    },
                    {
                        id: 4,
                        title: 'Yeni hasta kaydı',
                        description: 'Eren Demir - 25 yaş',
                        time: '1 gün önce',
                        icon: 'fas fa-user-plus',
                        iconColor: 'text-purple-600',
                        iconBg: 'bg-purple-100 dark:bg-purple-900'
                    }
                ],
                performanceMetrics: [
                    {
                        id: 1,
                        name: 'Hasta Memnuniyeti',
                        value: 96,
                        color: 'text-green-600 dark:text-green-400',
                        barColor: 'bg-green-500'
                    },
                    {
                        id: 2,
                        name: 'Randevu Doluluk Oranı',
                        value: 87,
                        color: 'text-blue-600 dark:text-blue-400',
                        barColor: 'bg-blue-500'
                    },
                    {
                        id: 3,
                        name: 'Operasyon Başarı Oranı',
                        value: 98,
                        color: 'text-purple-600 dark:text-purple-400',
                        barColor: 'bg-purple-500'
                    }
                ],
                quickActions: [
                    {
                        id: 'add-patient',
                        name: 'Hasta Ekle',
                        icon: 'fas fa-user-plus',
                        iconColor: 'text-green-600',
                        textColor: 'text-green-800 dark:text-green-200',
                        bgClass: 'bg-green-50 dark:bg-green-900/20 hover:bg-green-100 dark:hover:bg-green-900/30'
                    },
                    {
                        id: 'create-report',
                        name: 'Rapor Oluştur',
                        icon: 'fas fa-file-medical',
                        iconColor: 'text-purple-600',
                        textColor: 'text-purple-800 dark:text-purple-200',
                        bgClass: 'bg-purple-50 dark:bg-purple-900/20 hover:bg-purple-100 dark:hover:bg-purple-900/30'
                    }
                ],
                
                initCharts() {
                    this.$nextTick(() => {
                        this.createRevenueChart();
                    });
                },
                
                createRevenueChart() {
                    const ctx = document.getElementById('revenueChart');
                    if (ctx) {
                        new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: ['Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim'],
                                datasets: [{
                                    label: 'Gelir (₺)',
                                    data: [32000, 38000, 42000, 39000, 44000, 45280],
                                    borderColor: '#3B82F6',
                                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                    borderWidth: 3,
                                    fill: true,
                                    tension: 0.4
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        display: false
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            callback: function(value) {
                                                return '₺' + value.toLocaleString();
                                            }
                                        }
                                    }
                                }
                            }
                        });
                    }
                },
                

                handleQuickAction(actionId) {
                    switch(actionId) {
                        case 'add-patient':
                            this.navigateTo('patients');
                            break;
                        case 'create-report':
                            this.navigateTo('reports');
                            break;
                    }
                }
            }
        }
    </script>
    
    <!-- PWA JavaScript - Enhanced for Livewire compatibility -->
    <script>
        // Ensure PWA.js loads after Alpine.js is ready
        @if($isLivewirePage)
        document.addEventListener('livewire:init', () => {
            // Load PWA after Livewire Alpine is ready
            loadPWAScript();
        });
        @else
        document.addEventListener('alpine:init', () => {
            // Load PWA after CDN Alpine is ready
            loadPWAScript();
        });
        @endif
        
        function loadPWAScript() {
            if (!window.pwaLoaded) {
                window.pwaLoaded = true;
                const script = document.createElement('script');
                script.src = "{{ asset('pwa.js') }}";
                script.onload = () => {
                    console.log('PWA.js loaded successfully');
                };
                script.onerror = () => {
                    console.error('PWA.js failed to load');
                };
                document.head.appendChild(script);
            }
        }
    </script>
</body>
</html>