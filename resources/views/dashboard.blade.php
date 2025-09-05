@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<style>
    .sidebar {
        transition: all 0.3s;
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
            z-index: 50;
            transform: translateX(-100%);
        }
        .sidebar-open {
            transform: translateX(0);
        }
        .content-area {
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

<!-- Welcome Section -->
<div class="gradient-bg rounded-lg p-6 mb-6 text-white">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold mb-2">Hoş Geldiniz, Dr. Serhat Atalay EVİŞ</h2>
        </div>
        <div class="text-right">
            <div class="text-3xl font-bold">128</div>
            <div class="text-blue-100">Bu Ay Toplam İşlem</div>
        </div>
    </div>
</div>
@livewire('dashboard')


<!-- Quick Actions -->
<div class="bg-white rounded-lg shadow-sm p-6 card-shadow mt-4">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Hızlı İşlemler</h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <button class="flex flex-col items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
            <i class="fas fa-calendar-plus text-blue-600 text-2xl mb-2"></i>
            <span class="text-sm font-medium text-blue-800">Yeni Randevu</span>
        </button>
        <button class="flex flex-col items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
            <i class="fas fa-user-plus text-green-600 text-2xl mb-2"></i>
            <span class="text-sm font-medium text-green-800">Hasta Ekle</span>
        </button>
        <button class="flex flex-col items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
            <i class="fas fa-file-medical text-purple-600 text-2xl mb-2"></i>
            <span class="text-sm font-medium text-purple-800">Rapor Oluştur</span>
        </button>
    </div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Set current date
    document.addEventListener('DOMContentLoaded', function() {
        // Revenue Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        new Chart(revenueCtx, {
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

        // Procedure Chart
        const procedureCtx = document.getElementById('procedureChart').getContext('2d');
        new Chart(procedureCtx, {
            type: 'doughnut',
            data: {
                labels: ['Burun Estetiği', 'Botoks', 'Dudak Dolgusu', 'Yüz Germe', 'Diğer'],
                datasets: [{
                    data: [35, 25, 20, 15, 5],
                    backgroundColor: [
                        '#3B82F6',
                        '#10B981',
                        '#8B5CF6',
                        '#F59E0B',
                        '#EF4444'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true
                        }
                    }
                }
            }
        });

        // Animate progress bars on load
        const progressBars = document.querySelectorAll('.progress-bar');
        progressBars.forEach(bar => {
            const width = bar.style.width;
            bar.style.width = '0%';
            setTimeout(() => {
                bar.style.width = width;
            }, 500);
        });
    });
</script>
@endsection
