<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
    <title>{{ $title ?? config('app.name') }}</title>
</head>
<body class="bg-gray-100" x-data="appData()">
    <div class="flex h-screen">
        <!-- Sidebar -->
        @include('components.sidebar')
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col content-area" id="main-content">
            <!-- Header -->
            @include('components.header')
            
            <!-- Content -->
            <main class="flex-1 p-6 overflow-y-auto">
                {{ $slot }}
            </main>
        </div>
    </div>

    @stack('scripts')
    
    <!-- Alpine.js -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.9/dist/cdn.min.js"></script>
    
    <!-- Alpine.js initialization -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Alpine.js is ready
        });
    </script>
    
    <script>
        function appData() {
            return {
                sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true',
                currentPage: 'dashboard',
                loading: false,
                
                init() {
                    this.currentPage = this.pathToPageId(window.location.pathname);
                    this.initRouter();
                    
                    this.$watch('sidebarCollapsed', value => {
                        localStorage.setItem('sidebarCollapsed', value);
                    });
                    
                    window.addEventListener('popstate', () => {
                        this.navigate(window.location.pathname, true);
                    });
                },
                
                toggleSidebar() {
                    this.sidebarCollapsed = !this.sidebarCollapsed;
                },
                
                pathToPageId(path) {
                    const routes = {
                        '/': 'dashboard',
                        '/dashboard': 'dashboard',
                        '/patients': 'patients',
                        '/appointments': 'appointments',
                        '/operations': 'operations',
                        '/reports': 'reports',
                        '/settings': 'settings'
                    };
                    return routes[path] || 'dashboard';
                },
                
                initRouter() {
                    // Router initialization
                },
                
                navigate(page, skipHistory = false) {
                    this.loading = true;
                    this.currentPage = typeof page === 'string' ? this.pathToPageId(page) : page;
                    
                    if (!skipHistory) {
                        history.pushState({}, '', page);
                    }
                    
                    setTimeout(() => {
                        this.loading = false;
                    }, 100);
                }
            }
        }
        
        function getCurrentDate() {
            return new Date().toLocaleDateString('tr-TR', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        }
        
        function getPageTitle(page) {
            const titles = {
                'dashboard': 'Dashboard',
                'patients': 'Hastalar',
                'appointments': 'Randevular',
                'treatments': 'Tedaviler',
                'reports': 'Raporlar',
                'settings': 'Ayarlar'
            };
            return titles[page] || 'Dashboard';
        }
    </script>
</body>
</html>
