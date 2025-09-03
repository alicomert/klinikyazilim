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
            <p class="text-blue-100">Bugün 8 randevunuz ve 2 operasyonunuz bulunmaktadır.</p>
        </div>
        <div class="text-right">
            <div class="text-3xl font-bold">₺45,280</div>
            <div class="text-blue-100">Bu Ay Gelir</div>
        </div>
    </div>
</div>

<!-- Quick Stats -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <div class="stat-card bg-white rounded-lg shadow-sm p-6 card-shadow">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-gray-500 text-sm font-medium">Bugünkü Randevular</div>
                <div class="text-3xl font-bold text-blue-600 mt-2">8</div>
                <div class="text-green-500 text-sm mt-1">
                    <i class="fas fa-arrow-up"></i> %12 artış
                </div>
            </div>
            <div class="bg-blue-100 p-4 rounded-full">
                <i class="fas fa-calendar-day text-blue-600 text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="stat-card bg-white rounded-lg shadow-sm p-6 card-shadow">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-gray-500 text-sm font-medium">Toplam Hasta</div>
                <div class="text-3xl font-bold text-green-600 mt-2">1,247</div>
                <div class="text-green-500 text-sm mt-1">
                    <i class="fas fa-arrow-up"></i> %8 artış
                </div>
            </div>
            <div class="bg-green-100 p-4 rounded-full">
                <i class="fas fa-users text-green-600 text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="stat-card bg-white rounded-lg shadow-sm p-6 card-shadow">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-gray-500 text-sm font-medium">Bu Ay Operasyon</div>
                <div class="text-3xl font-bold text-purple-600 mt-2">24</div>
                <div class="text-green-500 text-sm mt-1">
                    <i class="fas fa-arrow-up"></i> %15 artış
                </div>
            </div>
            <div class="bg-purple-100 p-4 rounded-full">
                <i class="fas fa-procedures text-purple-600 text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="stat-card bg-white rounded-lg shadow-sm p-6 card-shadow">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-gray-500 text-sm font-medium">Aylık Gelir</div>
                <div class="text-3xl font-bold text-yellow-600 mt-2">₺45,280</div>
                <div class="text-green-500 text-sm mt-1">
                    <i class="fas fa-arrow-up"></i> %22 artış
                </div>
            </div>
            <div class="bg-yellow-100 p-4 rounded-full">
                <i class="fas fa-money-bill-wave text-yellow-600 text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Revenue Chart -->
    <div class="bg-white rounded-lg shadow-sm p-6 card-shadow">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Aylık Gelir Trendi</h3>
            <select class="text-sm border rounded-lg px-3 py-1">
                <option>Son 6 Ay</option>
                <option>Son 12 Ay</option>
            </select>
        </div>
        <div class="chart-container">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    <!-- Procedure Distribution -->
    <div class="bg-white rounded-lg shadow-sm p-6 card-shadow">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Prosedür Dağılımı</h3>
            <select class="text-sm border rounded-lg px-3 py-1">
                <option>Bu Ay</option>
                <option>Son 3 Ay</option>
            </select>
        </div>
        <div class="chart-container">
            <canvas id="procedureChart"></canvas>
        </div>
    </div>
</div>

<!-- Today's Schedule & Recent Activities -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Today's Schedule -->
    <div class="bg-white rounded-lg shadow-sm p-6 card-shadow">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Bugünkü Program</h3>
            <a href="{{ route('clinic') }}" class="text-blue-600 text-sm hover:underline">Tümünü Gör</a>
        </div>
        <div class="space-y-4">
            <div class="flex items-center space-x-4 p-3 bg-blue-50 rounded-lg">
                <div class="text-blue-600 font-semibold">09:00</div>
                <div class="flex-1">
                    <div class="font-medium">Meltem Karaca</div>
                    <div class="text-sm text-gray-500">Burun Estetiği Kontrol</div>
                </div>
                <div class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">Kontrol</div>
            </div>
            <div class="flex items-center space-x-4 p-3 bg-green-50 rounded-lg">
                <div class="text-green-600 font-semibold">10:30</div>
                <div class="flex-1">
                    <div class="font-medium">Ayşe Demir</div>
                    <div class="text-sm text-gray-500">Yüz Germe Operasyonu</div>
                </div>
                <div class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs">Operasyon</div>
            </div>
            <div class="flex items-center space-x-4 p-3 bg-purple-50 rounded-lg">
                <div class="text-purple-600 font-semibold">14:00</div>
                <div class="flex-1">
                    <div class="font-medium">Mehmet Özkan</div>
                    <div class="text-sm text-gray-500">Botoks Uygulaması</div>
                </div>
                <div class="bg-purple-100 text-purple-800 px-2 py-1 rounded text-xs">Estetik</div>
            </div>
            <div class="flex items-center space-x-4 p-3 bg-yellow-50 rounded-lg">
                <div class="text-yellow-600 font-semibold">16:00</div>
                <div class="flex-1">
                    <div class="font-medium">Zeynep Yılmaz</div>
                    <div class="text-sm text-gray-500">İlk Muayene</div>
                </div>
                <div class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Muayene</div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="bg-white rounded-lg shadow-sm p-6 card-shadow">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Son Aktiviteler</h3>
            <button class="text-blue-600 text-sm hover:underline">Tümünü Gör</button>
        </div>
        <div class="space-y-4">
            <div class="flex items-start space-x-3">
                <div class="bg-green-100 p-2 rounded-full">
                    <i class="fas fa-check text-green-600"></i>
                </div>
                <div class="flex-1">
                    <div class="text-sm font-medium">Operasyon tamamlandı</div>
                    <div class="text-xs text-gray-500">Sema Tekin - Dudak Dolgusu</div>
                    <div class="text-xs text-gray-400">2 saat önce</div>
                </div>
            </div>
            <div class="flex items-start space-x-3">
                <div class="bg-blue-100 p-2 rounded-full">
                    <i class="fas fa-calendar-plus text-blue-600"></i>
                </div>
                <div class="flex-1">
                    <div class="text-sm font-medium">Yeni randevu eklendi</div>
                    <div class="text-xs text-gray-500">Can Burak - 15.10.2023</div>
                    <div class="text-xs text-gray-400">3 saat önce</div>
                </div>
            </div>
            <div class="flex items-start space-x-3">
                <div class="bg-yellow-100 p-2 rounded-full">
                    <i class="fas fa-money-bill text-yellow-600"></i>
                </div>
                <div class="flex-1">
                    <div class="text-sm font-medium">Ödeme alındı</div>
                    <div class="text-xs text-gray-500">Meltem Karaca - ₺3,500</div>
                    <div class="text-xs text-gray-400">5 saat önce</div>
                </div>
            </div>
            <div class="flex items-start space-x-3">
                <div class="bg-purple-100 p-2 rounded-full">
                    <i class="fas fa-user-plus text-purple-600"></i>
                </div>
                <div class="flex-1">
                    <div class="text-sm font-medium">Yeni hasta kaydı</div>
                    <div class="text-xs text-gray-500">Eren Demir - 25 yaş</div>
                    <div class="text-xs text-gray-400">1 gün önce</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Performance Metrics -->
<div class="bg-white rounded-lg shadow-sm p-6 card-shadow mb-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-6">Performans Metrikleri</h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-600">Hasta Memnuniyeti</span>
                <span class="text-sm font-bold text-green-600">96%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-green-500 h-2 rounded-full progress-bar" style="width: 96%"></div>
            </div>
        </div>
        <div>
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-600">Randevu Doluluk Oranı</span>
                <span class="text-sm font-bold text-blue-600">87%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-blue-500 h-2 rounded-full progress-bar" style="width: 87%"></div>
            </div>
        </div>
        <div>
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-600">Operasyon Başarı Oranı</span>
                <span class="text-sm font-bold text-purple-600">98%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-purple-500 h-2 rounded-full progress-bar" style="width: 98%"></div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="bg-white rounded-lg shadow-sm p-6 card-shadow">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Hızlı İşlemler</h3>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
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
        <button class="flex flex-col items-center p-4 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition-colors">
            <i class="fas fa-money-bill-wave text-yellow-600 text-2xl mb-2"></i>
            <span class="text-sm font-medium text-yellow-800">Ödeme Al</span>
        </button>
    </div>
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
