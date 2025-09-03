<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="csrf-token" content="{{ csrf_token() }}" />

<title>{{ $title ?? config('app.name') }}</title>

<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!-- Tailwind CSS v4 CDN -->
<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

@fluxAppearance

<!-- Livewire Scripts -->
@livewireStyles

<!-- Alpine.js CDN -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.9/dist/cdn.min.js"></script>

<!-- Custom Styles for Doctor Panel -->
<style>
    .sidebar {
        transition: all 0.3s;
    }
    .sidebar-collapsed {
        width: 80px;
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
    }
    .content-expanded {
        margin-left: 80px;
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
            z-index: 50;
            transform: translateX(-100%);
        }
        .sidebar-open {
            transform: translateX(0);
        }
        .content-area {
            margin-left: 0 !important;
        }
        #main-content {
            margin-left: 0 !important;
        }
    }
    .gradient-bg {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .card-shadow {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
</style>
