@extends('layouts.app')

@section('content')
<!-- Content -->
<div class="p-6">
    <!-- Rapor Türü Seçimi -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6 no-print">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Rapor Türü Seçin</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <button onclick="showReport('patient', this)" class="report-btn bg-blue-50 border-2 border-blue-200 p-4 rounded-lg hover:bg-blue-100 transition-colors">
                <i class="fas fa-user-injured text-blue-600 text-2xl mb-2"></i>
                <h4 class="font-semibold text-blue-800">Hasta Raporu</h4>
                <p class="text-sm text-blue-600">Hasta istatistikleri</p>
            </button>
            <button onclick="showReport('operation', this)" class="report-btn bg-purple-50 border-2 border-purple-200 p-4 rounded-lg hover:bg-purple-100 transition-colors">
                <i class="fas fa-procedures text-purple-600 text-2xl mb-2"></i>
                <h4 class="font-semibold text-purple-800">Operasyon Raporu</h4>
                <p class="text-sm text-purple-600">Operasyon analizleri</p>
            </button>
            <button onclick="showReport('performance', this)" class="report-btn bg-orange-50 border-2 border-orange-200 p-4 rounded-lg hover:bg-orange-100 transition-colors">
                <i class="fas fa-chart-line text-orange-600 text-2xl mb-2"></i>
                <h4 class="font-semibold text-orange-800">Performans Raporu</h4>
                <p class="text-sm text-orange-600">Klinik performansı</p>
            </button>
        </div>
    </div>

    <!-- Tarih Aralığı Seçimi -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6 no-print">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Tarih Aralığı</h3>
        <div class="flex flex-wrap items-center gap-4">
            <div class="flex items-center space-x-2">
                <label class="text-sm font-medium text-gray-700">Başlangıç:</label>
                <input type="date" id="start-date" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div class="flex items-center space-x-2">
                <label class="text-sm font-medium text-gray-700">Bitiş:</label>
                <input type="date" id="end-date" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div class="flex space-x-2">
                <button onclick="setDateRange('today')" class="px-3 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg">Bugün</button>
                <button onclick="setDateRange('week')" class="px-3 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg">Bu Hafta</button>
                <button onclick="setDateRange('month')" class="px-3 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg">Bu Ay</button>
                <button onclick="setDateRange('year')" class="px-3 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg">Bu Yıl</button>
            </div>
            <button onclick="generateReport()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-chart-bar mr-2"></i>Rapor Oluştur
            </button>
        </div>
    </div>

    <!-- Hasta Raporu -->
    <div id="patient-report" class="report-section hidden">
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h3 class="text-xl font-bold text-gray-800 mb-6">Hasta Raporu</h3>
            
            <!-- Hasta İstatistikleri -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-6 rounded-lg text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-100 text-sm">Toplam Hasta</p>
                            <p class="text-2xl font-bold">1,247</p>
                        </div>
                        <i class="fas fa-users text-3xl text-blue-200"></i>
                    </div>
                    <p class="text-blue-100 text-sm mt-2">+8% geçen aya göre</p>
                </div>
                
                <div class="bg-gradient-to-r from-green-500 to-green-600 p-6 rounded-lg text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-100 text-sm">Yeni Hasta</p>
                            <p class="text-2xl font-bold">89</p>
                        </div>
                        <i class="fas fa-user-plus text-3xl text-green-200"></i>
                    </div>
                    <p class="text-green-100 text-sm mt-2">+15% geçen aya göre</p>
                </div>
                
                <div class="bg-gradient-to-r from-orange-500 to-orange-600 p-6 rounded-lg text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-orange-100 text-sm">Aktif Tedavi</p>
                            <p class="text-2xl font-bold">156</p>
                        </div>
                        <i class="fas fa-heartbeat text-3xl text-orange-200"></i>
                    </div>
                    <p class="text-orange-100 text-sm mt-2">+3% geçen aya göre</p>
                </div>
                
                <div class="bg-gradient-to-r from-purple-500 to-purple-600 p-6 rounded-lg text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-purple-100 text-sm">Memnuniyet</p>
                            <p class="text-2xl font-bold">4.8/5</p>
                        </div>
                        <i class="fas fa-star text-3xl text-purple-200"></i>
                    </div>
                    <p class="text-purple-100 text-sm mt-2">+0.2 geçen aya göre</p>
                </div>
            </div>

            <!-- Hasta Yaş ve Cinsiyet Dağılımı -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="text-lg font-semibold text-gray-800 mb-4">Yaş Dağılımı</h4>
                    <canvas id="ageDistributionChart" width="300" height="300"></canvas>
                </div>
                <div>
                    <h4 class="text-lg font-semibold text-gray-800 mb-4">Cinsiyet Dağılımı</h4>
                    <canvas id="genderDistributionChart" width="300" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Operasyon Raporu -->
    <div id="operation-report" class="report-section hidden">
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h3 class="text-xl font-bold text-gray-800 mb-6">Operasyon Raporu</h3>
            
            <!-- Operasyon İstatistikleri -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 p-6 rounded-lg text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-indigo-100 text-sm">Toplam Operasyon</p>
                            <p class="text-2xl font-bold">342</p>
                        </div>
                        <i class="fas fa-procedures text-3xl text-indigo-200"></i>
                    </div>
                    <p class="text-indigo-100 text-sm mt-2">+12% geçen aya göre</p>
                </div>
                
                <div class="bg-gradient-to-r from-green-500 to-green-600 p-6 rounded-lg text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-100 text-sm">Başarı Oranı</p>
                            <p class="text-2xl font-bold">98.5%</p>
                        </div>
                        <i class="fas fa-check-circle text-3xl text-green-200"></i>
                    </div>
                    <p class="text-green-100 text-sm mt-2">+0.3% geçen aya göre</p>
                </div>
                
                <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 p-6 rounded-lg text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-yellow-100 text-sm">Ortalama Süre</p>
                            <p class="text-2xl font-bold">2.5 saat</p>
                        </div>
                        <i class="fas fa-clock text-3xl text-yellow-200"></i>
                    </div>
                    <p class="text-yellow-100 text-sm mt-2">-15 dk geçen aya göre</p>
                </div>
                
                <div class="bg-gradient-to-r from-red-500 to-red-600 p-6 rounded-lg text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-red-100 text-sm">Komplikasyon</p>
                            <p class="text-2xl font-bold">1.5%</p>
                        </div>
                        <i class="fas fa-exclamation-triangle text-3xl text-red-200"></i>
                    </div>
                    <p class="text-red-100 text-sm mt-2">-0.2% geçen aya göre</p>
                </div>
            </div>

            <!-- Operasyon Türü Dağılımı ve Aylık Trend -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="text-lg font-semibold text-gray-800 mb-4">Operasyon Türü Dağılımı</h4>
                    <canvas id="operationTypeChart" width="300" height="300"></canvas>
                </div>
                <div>
                    <h4 class="text-lg font-semibold text-gray-800 mb-4">Aylık Operasyon Trendi</h4>
                    <canvas id="monthlyOperationChart" width="300" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Performans Raporu -->
    <div id="performance-report" class="report-section hidden">
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h3 class="text-xl font-bold text-gray-800 mb-6">Performans Raporu</h3>
            
            <!-- Performans Kartları -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-gradient-to-r from-teal-500 to-teal-600 p-6 rounded-lg text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-teal-100 text-sm">Randevu Doluluk</p>
                            <p class="text-2xl font-bold">87%</p>
                        </div>
                        <i class="fas fa-calendar-check text-3xl text-teal-200"></i>
                    </div>
                    <p class="text-teal-100 text-sm mt-2">+5% geçen aya göre</p>
                </div>
                
                <div class="bg-gradient-to-r from-pink-500 to-pink-600 p-6 rounded-lg text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-pink-100 text-sm">Hasta Sadakati</p>
                            <p class="text-2xl font-bold">92%</p>
                        </div>
                        <i class="fas fa-heart text-3xl text-pink-200"></i>
                    </div>
                    <p class="text-pink-100 text-sm mt-2">+3% geçen aya göre</p>
                </div>
                
                <div class="bg-gradient-to-r from-cyan-500 to-cyan-600 p-6 rounded-lg text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-cyan-100 text-sm">Ortalama Bekleme</p>
                            <p class="text-2xl font-bold">12 dk</p>
                        </div>
                        <i class="fas fa-hourglass-half text-3xl text-cyan-200"></i>
                    </div>
                    <p class="text-cyan-100 text-sm mt-2">-3 dk geçen aya göre</p>
                </div>
                
                <div class="bg-gradient-to-r from-lime-500 to-lime-600 p-6 rounded-lg text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-lime-100 text-sm">Tekrar Ziyaret</p>
                            <p class="text-2xl font-bold">78%</p>
                        </div>
                        <i class="fas fa-redo text-3xl text-lime-200"></i>
                    </div>
                    <p class="text-lime-100 text-sm mt-2">+7% geçen aya göre</p>
                </div>
            </div>

            <!-- Performans Grafikleri -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="text-lg font-semibold text-gray-800 mb-4">Aylık Performans Trendi</h4>
                    <canvas id="monthlyPerformanceChart" width="300" height="300"></canvas>
                </div>
                <div>
                    <h4 class="text-lg font-semibold text-gray-800 mb-4">Hasta Memnuniyet Skorları</h4>
                    <canvas id="satisfactionScoreChart" width="300" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.print-hidden {
    display: none;
}
@media print {
    .no-print {
        display: none !important;
    }
    .print-hidden {
        display: block !important;
    }
    body {
        background: white !important;
    }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Date setting
function setCurrentDate() {
    const now = new Date();
    const today = now.toISOString().split('T')[0];
    const firstDayOfMonth = new Date(now.getFullYear(), now.getMonth(), 1).toISOString().split('T')[0];
    
    document.getElementById('start-date').value = firstDayOfMonth;
    document.getElementById('end-date').value = today;
    document.getElementById('current-date').textContent = now.toLocaleDateString('tr-TR');
}

// Date range setting
function setDateRange(period) {
    const now = new Date();
    const endDate = now.toISOString().split('T')[0];
    let startDate;
    
    switch(period) {
        case 'today':
            startDate = endDate;
            break;
        case 'week':
            const weekAgo = new Date(now.getTime() - 7 * 24 * 60 * 60 * 1000);
            startDate = weekAgo.toISOString().split('T')[0];
            break;
        case 'month':
            startDate = new Date(now.getFullYear(), now.getMonth(), 1).toISOString().split('T')[0];
            break;
        case 'year':
            startDate = new Date(now.getFullYear(), 0, 1).toISOString().split('T')[0];
            break;
    }
    
    document.getElementById('start-date').value = startDate;
    document.getElementById('end-date').value = endDate;
}

// Show report
function showReport(reportType, button) {
    document.querySelectorAll('.report-section').forEach(section => {
        section.classList.add('hidden');
    });
    
    document.querySelectorAll('.report-btn').forEach(btn => {
        btn.classList.remove('ring-2', 'ring-blue-500');
    });
    
    document.getElementById(reportType + '-report').classList.remove('hidden');
    button.classList.add('ring-2', 'ring-blue-500');
    
    setTimeout(() => {
        initializeCharts(reportType);
    }, 100);
}

// Generate report
function generateReport() {
    const startDate = document.getElementById('start-date').value;
    const endDate = document.getElementById('end-date').value;
    
    if (!startDate || !endDate) {
        alert('Lütfen tarih aralığını seçin.');
        return;
    }
    
    console.log('Rapor oluşturuluyor:', { startDate, endDate });
    
    const activeReport = document.querySelector('.report-section:not(.hidden)');
    if (activeReport) {
        const reportType = activeReport.id.replace('-report', '');
        initializeCharts(reportType);
    }
}

// Initialize charts
function initializeCharts(reportType) {
    switch(reportType) {
        case 'patient':
            initializePatientCharts();
            break;
        case 'operation':
            initializeOperationCharts();
            break;
        case 'performance':
            initializePerformanceCharts();
            break;
    }
}

// Patient report charts
function initializePatientCharts() {
    const ageDistributionCtx = document.getElementById('ageDistributionChart');
    if (ageDistributionCtx) {
        new Chart(ageDistributionCtx, {
            type: 'bar',
            data: {
                labels: ['18-25', '26-35', '36-45', '46-55', '56-65', '65+'],
                datasets: [{
                    label: 'Hasta Sayısı',
                    data: [120, 280, 350, 280, 150, 67],
                    backgroundColor: 'rgba(59, 130, 246, 0.8)'
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
    
    const genderDistributionCtx = document.getElementById('genderDistributionChart');
    if (genderDistributionCtx) {
        new Chart(genderDistributionCtx, {
            type: 'pie',
            data: {
                labels: ['Kadın', 'Erkek'],
                datasets: [{
                    data: [68, 32],
                    backgroundColor: [
                        'rgb(236, 72, 153)',
                        'rgb(59, 130, 246)'
                    ]
                }]
            },
            options: {
                responsive: true
            }
        });
    }
}

// Operation report charts
function initializeOperationCharts() {
    const operationTypeCtx = document.getElementById('operationTypeChart');
    if (operationTypeCtx) {
        new Chart(operationTypeCtx, {
            type: 'doughnut',
            data: {
                labels: ['Rinoplasti', 'Liposuction', 'Meme Büyütme', 'Botoks', 'Diğer'],
                datasets: [{
                    data: [25, 20, 18, 22, 15],
                    backgroundColor: [
                        'rgb(99, 102, 241)',
                        'rgb(34, 197, 94)',
                        'rgb(249, 115, 22)',
                        'rgb(236, 72, 153)',
                        'rgb(156, 163, 175)'
                    ]
                }]
            },
            options: {
                responsive: true
            }
        });
    }
    
    const monthlyOperationCtx = document.getElementById('monthlyOperationChart');
    if (monthlyOperationCtx) {
        new Chart(monthlyOperationCtx, {
            type: 'line',
            data: {
                labels: ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran'],
                datasets: [{
                    label: 'Operasyon Sayısı',
                    data: [45, 52, 48, 61, 55, 67],
                    borderColor: 'rgb(139, 92, 246)',
                    backgroundColor: 'rgba(139, 92, 246, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
}

// Performance report charts
function initializePerformanceCharts() {
    const monthlyPerformanceCtx = document.getElementById('monthlyPerformanceChart');
    if (monthlyPerformanceCtx) {
        new Chart(monthlyPerformanceCtx, {
            type: 'radar',
            data: {
                labels: ['Randevu Doluluk', 'Hasta Sadakati', 'Memnuniyet', 'Tekrar Ziyaret', 'Zamanında Başlama'],
                datasets: [{
                    label: 'Bu Ay',
                    data: [87, 92, 4.8*20, 78, 95],
                    borderColor: 'rgb(6, 182, 212)',
                    backgroundColor: 'rgba(6, 182, 212, 0.2)'
                }, {
                    label: 'Geçen Ay',
                    data: [82, 89, 4.6*20, 71, 92],
                    borderColor: 'rgb(156, 163, 175)',
                    backgroundColor: 'rgba(156, 163, 175, 0.2)'
                }]
            },
            options: {
                responsive: true,
                scales: {
                    r: {
                        beginAtZero: true,
                        max: 100
                    }
                }
            }
        });
    }
    
    const satisfactionScoreCtx = document.getElementById('satisfactionScoreChart');
    if (satisfactionScoreCtx) {
        new Chart(satisfactionScoreCtx, {
            type: 'bar',
            data: {
                labels: ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran'],
                datasets: [{
                    label: 'Memnuniyet Skoru',
                    data: [4.5, 4.6, 4.7, 4.6, 4.8, 4.9],
                    backgroundColor: 'rgba(34, 197, 94, 0.8)'
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 5
                    }
                }
            }
        });
    }
}

// On page load
document.addEventListener('DOMContentLoaded', function() {
    setCurrentDate();
    const firstReportBtn = document.querySelector('.report-btn');
    if (firstReportBtn) {
        showReport('patient', firstReportBtn);
    }
});
</script>
@endsection