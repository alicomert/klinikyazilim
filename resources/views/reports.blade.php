@extends('layouts.app')

@section('content')
<!-- Content -->
<div class="p-6">
    <!-- Rapor Türü Seçimi -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6 no-print">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Rapor Türü Seçin</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
           <!-- <button onclick="showReport('performance', this)" class="report-btn bg-orange-50 border-2 border-orange-200 p-4 rounded-lg hover:bg-orange-100 transition-colors">
                <i class="fas fa-chart-line text-orange-600 text-2xl mb-2"></i>
                <h4 class="font-semibold text-orange-800">Performans Raporu</h4>
                <p class="text-sm text-orange-600">Klinik performansı</p>
            </button> -->
        </div>
    </div>

    <!-- 
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
    </div> Tarih Aralığı Seçimi -->

    <!-- Hasta Raporu -->
    <div id="patient-report" class="report-section hidden">
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h3 class="text-xl font-bold text-gray-800 mb-6">Hasta Raporu</h3>
            
            <!-- Hasta İstatistikleri -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-6 rounded-lg text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-100 text-sm">Toplam Hasta</p>
                            <p class="text-2xl font-bold" id="total-patients">-</p>
                        </div>
                        <i class="fas fa-users text-3xl text-blue-200"></i>
                    </div>
                    <p class="text-blue-100 text-sm mt-2" id="total-patients-change">Hesaplanıyor...</p>
                </div>
                
                <div class="bg-gradient-to-r from-green-500 to-green-600 p-6 rounded-lg text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-100 text-sm">Bu Ay Yeni Hasta</p>
                            <p class="text-2xl font-bold" id="new-patients">-</p>
                        </div>
                        <i class="fas fa-user-plus text-3xl text-green-200"></i>
                    </div>
                    <p class="text-green-100 text-sm mt-2" id="new-patients-change">Hesaplanıyor...</p>
                </div>
            </div>

            <!-- Hasta Yaş Dağılımı ve Aylık Kayıt Grafiği -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="text-lg font-semibold text-gray-800 mb-4">Yaş Dağılımı</h4>
                    <canvas id="ageDistributionChart" width="300" height="300"></canvas>
                </div>
                <div>
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="text-lg font-semibold text-gray-800">Hasta Kayıt Trendi</h4>
                        <select id="patient-period-select" class="border rounded px-3 py-1 text-sm">
                            <option value="monthly" selected>Aylık</option>
                            <option value="quarterly">3 Aylık</option>
                            <option value="yearly">Yıllık</option>
                        </select>
                    </div>
                    <canvas id="monthlyPatientChart" width="300" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Operasyon Raporu -->
    <div id="operation-report" class="report-section hidden">
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h3 class="text-xl font-bold text-gray-800 mb-6">Operasyon Raporu</h3>
            
            <!-- Operasyon İstatistikleri -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 p-6 rounded-lg text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-indigo-100 text-sm">Toplam Operasyon</p>
                            <p id="total-operations" class="text-2xl font-bold">-</p>
                        </div>
                        <i class="fas fa-procedures text-3xl text-indigo-200"></i>
                    </div>
                    <p id="total-operations-change" class="text-indigo-100 text-sm mt-2">-</p>
                </div>
                
                <div class="bg-gradient-to-r from-green-500 to-green-600 p-6 rounded-lg text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-100 text-sm">En Çok Yapılan Operasyon</p>
                            <div class="mt-1">
                                <p id="most-operation-type" class="text-2xl font-bold">-</p>
                                <p id="most-operation-count" class="text-lg text-green-100">- işlem</p>
                            </div>
                        </div>
                        <i class="fas fa-check-circle text-3xl text-green-200"></i>
                    </div>
                    <p id="most-operation-change" class="text-green-100 text-sm mt-2">-</p>
                </div>
                
                <div class="bg-gradient-to-r from-red-500 to-red-600 p-6 rounded-lg text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-red-100 text-sm">En Az Yapılan Operasyon</p>
                            <div class="mt-1">
                                <p id="least-operation-type" class="text-2xl font-bold">-</p>
                                <p id="least-operation-count" class="text-lg text-red-100">- işlem</p>
                            </div>
                        </div>
                        <i class="fas fa-exclamation-triangle text-3xl text-red-200"></i>
                    </div>
                    <p id="least-operation-change" class="text-red-100 text-sm mt-2">-</p>
                </div>
            </div>

            <!-- Operasyon Türü Dağılımı ve Aylık Trend -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="text-lg font-semibold text-gray-800">Operasyon Türü Dağılımı</h4>
                        <select id="operation-period-select" class="border rounded px-3 py-1 text-sm">
                            <option value="monthly">Bu Ay</option>
                            <option value="yearly">Bu Yıl</option>
                            <option value="all">Tüm Zamanlar</option>
                            <option value="custom">Özel Tarih</option>
                        </select>
                    </div>
                    <div id="custom-month-select" class="mb-4 hidden">
                        <input type="month" id="selected-month" class="border rounded px-3 py-1 text-sm">
                    </div>
                    <canvas id="operationTypeChart" width="300" height="300"></canvas>
                </div>
                <div>
                    <h4 class="text-lg font-semibold text-gray-800 mb-4">Aylık Operasyon Trendi</h4>
                    <canvas id="monthlyOperationChart" width="300" height="300"></canvas>
                </div>
            </div>

            <!-- İşlem Türü (Process Type) Analizi -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="text-lg font-semibold text-gray-800">İşlem Türü Dağılımı</h4>
                        <select id="process-type-period-select" class="border rounded px-3 py-1 text-sm">
                            <option value="monthly">Bu Ay</option>
                            <option value="yearly">Bu Yıl</option>
                            <option value="all">Tüm Zamanlar</option>
                        </select>
                    </div>
                    <canvas id="processTypeChart" width="300" height="300"></canvas>
                </div>
                <div>
                    <h4 class="text-lg font-semibold text-gray-800 mb-4">İşlem Türü İstatistikleri</h4>
                    <div class="space-y-4">
                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-4 rounded-lg text-white">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-blue-100 text-sm">En Çok Yapılan İşlem Türü</p>
                                    <p id="most-process-type" class="text-xl font-bold">-</p>
                                    <p id="most-process-type-count" class="text-sm text-blue-100">- işlem</p>
                                </div>
                                <i class="fas fa-star text-2xl text-blue-200"></i>
                            </div>
                        </div>
                        <div class="bg-gradient-to-r from-orange-500 to-orange-600 p-4 rounded-lg text-white">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-orange-100 text-sm">Toplam İşlem Türü</p>
                                    <p id="total-process-types" class="text-xl font-bold">-</p>
                                    <p class="text-sm text-orange-100">Farklı türde işlem</p>
                                </div>
                                <i class="fas fa-list text-2xl text-orange-200"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- İşlem Bazlı Detay Tablosu -->
            <div class="bg-gray-50 rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h4 class="text-lg font-semibold text-gray-800">İşlem Bazlı Detay Analizi</h4>
                    <div class="flex space-x-2">
                        <select id="table-period-select" class="border rounded px-3 py-1 text-sm">
                            <option value="monthly">Bu Ay</option>
                            <option value="yearly">Bu Yıl</option>
                            <option value="all">Tüm Zamanlar</option>
                        </select>
                        <button onclick="exportTableData()" class="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700 mr-2">
                            <i class="fas fa-download mr-1"></i>Excel'e Aktar
                        </button>
                        <button onclick="exportTableDataPDF()" class="bg-red-600 text-white px-3 py-1 rounded text-sm hover:bg-red-700">
                            <i class="fas fa-file-pdf mr-1"></i>PDF'e Aktar
                        </button>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200" id="operations-detail-table">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Operasyon Adı
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        İşlem Türü
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Toplam İşlem
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Bu Ay
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Geçen Ay
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Değişim
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="operations-table-body" class="bg-white divide-y divide-gray-200">
                                <!-- Veriler JavaScript ile yüklenecek -->
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                        <i class="fas fa-spinner fa-spin mr-2"></i>Veriler yükleniyor...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Sayfalama -->
                    <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                        <div class="flex-1 flex justify-between sm:hidden">
                            <button id="prev-page-mobile" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Önceki
                            </button>
                            <button id="next-page-mobile" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Sonraki
                            </button>
                        </div>
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700">
                                    Toplam <span id="total-records" class="font-medium">0</span> kayıttan 
                                    <span id="showing-from" class="font-medium">0</span> - <span id="showing-to" class="font-medium">0</span> arası gösteriliyor
                                </p>
                            </div>
                            <div>
                                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" id="pagination-nav">
                                    <!-- Sayfalama butonları JavaScript ile oluşturulacak -->
                                </nav>
                            </div>
                        </div>
                    </div>
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
                labels: ['18-25', '26-35', '36-45', '46-55', '55+'],
                datasets: [{
                    label: 'Hasta Sayısı',
                    data: [0, 0, 0, 0, 0], // Backend'den gelecek
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
    
    const monthlyPatientCtx = document.getElementById('monthlyPatientChart');
    if (monthlyPatientCtx) {
        new Chart(monthlyPatientCtx, {
            type: 'line',
            data: {
                labels: ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'],
                datasets: [{
                    label: 'Yeni Hasta Sayısı',
                    data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0], // Backend'den gelecek
                    borderColor: 'rgb(34, 197, 94)',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
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
    
    // Dönem seçimi event listener
    const periodSelect = document.getElementById('patient-period-select');
    if (periodSelect) {
        periodSelect.addEventListener('change', function() {
            loadPatientData(this.value);
        });
    }
    
    // İlk yükleme
    loadPatientData('monthly');
}

// Hasta verilerini backend'den yükle
function loadPatientData(period = 'monthly') {
    const params = new URLSearchParams({
        period: period
    });
    
    // Toplam hasta sayısı ve bu ay yeni hasta sayısını hesapla
    fetch(`/api/patient-stats?${params}`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        // Toplam hasta sayısı
        document.getElementById('total-patients').textContent = data.total_patients || 0;
        
        // Bu ay yeni hasta sayısı
        document.getElementById('new-patients').textContent = data.new_patients_this_month || 0;
        
        // Yüzdelik değişimler
        const totalChange = data.total_patients_change || 0;
        const newChange = data.new_patients_change || 0;
        
        document.getElementById('total-patients-change').textContent = 
            (totalChange >= 0 ? '+' : '') + totalChange + '% geçen aya göre';
        
        document.getElementById('new-patients-change').textContent = 
            (newChange >= 0 ? '+' : '') + newChange + '% geçen aya göre';
        
        // Yaş dağılımı grafiğini güncelle
        updateAgeDistributionChart(data.age_distribution || {});
        
        // Aylık hasta kayıt grafiğini güncelle
        updateMonthlyPatientChart(data.monthly_trend || {});
    })
    .catch(error => {
        console.error('Hasta verileri yüklenirken hata:', error);
        document.getElementById('total-patients').textContent = '0';
        document.getElementById('new-patients').textContent = '0';
        document.getElementById('total-patients-change').textContent = 'Veri yüklenemedi';
        document.getElementById('new-patients-change').textContent = 'Veri yüklenemedi';
    });
}

// Yaş dağılımı grafiğini güncelle
function updateAgeDistributionChart(ageData) {
    const chart = Chart.getChart('ageDistributionChart');
    if (chart && ageData.labels && ageData.data) {
        chart.data.labels = ageData.labels;
        chart.data.datasets[0].data = ageData.data;
        chart.update();
    }
}

// Aylık hasta kayıt grafiğini güncelle
function updateMonthlyPatientChart(monthlyData) {
    const chart = Chart.getChart('monthlyPatientChart');
    if (chart && monthlyData.labels && monthlyData.data) {
        chart.data.labels = monthlyData.labels;
        chart.data.datasets[0].data = monthlyData.data;
        chart.update();
    }
}

// Operation report charts
let operationTypeChart = null;
let monthlyOperationChart = null;
let processTypeChart = null;

function initializeOperationCharts() {
    const operationTypeCtx = document.getElementById('operationTypeChart');
    if (operationTypeCtx) {
        operationTypeChart = new Chart(operationTypeCtx, {
            type: 'doughnut',
            data: {
                labels: [],
                datasets: [{
                    data: [],
                    backgroundColor: [
                        'rgb(99, 102, 241)',
                        'rgb(34, 197, 94)',
                        'rgb(249, 115, 22)',
                        'rgb(236, 72, 153)'
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
        monthlyOperationChart = new Chart(monthlyOperationCtx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Operasyon Sayısı',
                    data: [],
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
    
    // Dönem seçimi event listener
    const periodSelect = document.getElementById('operation-period-select');
    const customMonthDiv = document.getElementById('custom-month-select');
    const selectedMonth = document.getElementById('selected-month');
    
    if (periodSelect) {
        periodSelect.addEventListener('change', function() {
            if (this.value === 'custom') {
                customMonthDiv.classList.remove('hidden');
            } else {
                customMonthDiv.classList.add('hidden');
                loadOperationData(this.value);
            }
        });
    }
    
    if (selectedMonth) {
        selectedMonth.addEventListener('change', function() {
            loadOperationData('custom', this.value);
        });
    }
    
    // Process Type Chart
    const processTypeCtx = document.getElementById('processTypeChart');
    if (processTypeCtx) {
        processTypeChart = new Chart(processTypeCtx, {
            type: 'pie',
            data: {
                labels: [],
                datasets: [{
                    data: [],
                    backgroundColor: [
                        'rgb(59, 130, 246)',
                        'rgb(16, 185, 129)',
                        'rgb(245, 101, 101)',
                        'rgb(251, 191, 36)',
                        'rgb(139, 92, 246)',
                        'rgb(236, 72, 153)'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }

    // Process Type Period Select Event Listener
    const processTypePeriodSelect = document.getElementById('process-type-period-select');
    if (processTypePeriodSelect) {
        processTypePeriodSelect.addEventListener('change', function() {
            loadProcessTypeData(this.value);
        });
    }

    // Table Period Select Event Listener
    const tablePeriodSelect = document.getElementById('table-period-select');
    if (tablePeriodSelect) {
        tablePeriodSelect.addEventListener('change', function() {
            loadOperationsDetailTable(this.value);
        });
    }

    // İlk yükleme
    loadOperationData('monthly');
    loadProcessTypeData('monthly');
    loadOperationsDetailTable('monthly');
}

// Operasyon verilerini backend'den al
function loadOperationData(period = 'monthly', customMonth = null) {
    const params = new URLSearchParams({
        period: period
    });
    
    if (customMonth) {
        params.append('month', customMonth);
    }
    
    fetch(`/api/operation-stats?${params}`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        updateOperationStats(data);
        updateOperationCharts(data);
    })
    .catch(error => {
        console.error('Operasyon verileri yüklenirken hata:', error);
    });
}

// Operasyon istatistiklerini güncelle
function updateOperationStats(data) {
    document.getElementById('total-operations').textContent = data.total_operations || 0;
    document.getElementById('total-operations-change').textContent = data.total_operations_change || '-';
    
    document.getElementById('most-operation-type').textContent = data.most_operation_type || '-';
    document.getElementById('most-operation-count').textContent = (data.most_operation_count || 0) + ' işlem';
    document.getElementById('most-operation-change').textContent = data.most_operation_change || '-';
    
    document.getElementById('least-operation-type').textContent = data.least_operation_type || '-';
    document.getElementById('least-operation-count').textContent = (data.least_operation_count || 0) + ' işlem';
    document.getElementById('least-operation-change').textContent = data.least_operation_change || '-';
}

// Operasyon grafiklerini güncelle
function updateOperationCharts(data) {
    // Operasyon türü dağılımı grafiği
    if (operationTypeChart && data.operation_types) {
        operationTypeChart.data.labels = data.operation_types.labels;
        operationTypeChart.data.datasets[0].data = data.operation_types.data;
        operationTypeChart.update();
    }
    
    // Aylık operasyon trendi grafiği
    if (monthlyOperationChart && data.monthly_trend) {
        monthlyOperationChart.data.labels = data.monthly_trend.labels;
        monthlyOperationChart.data.datasets[0].data = data.monthly_trend.data;
        monthlyOperationChart.update();
    }
}

// Process Type verilerini backend'den al
function loadProcessTypeData(period = 'monthly') {
    const params = new URLSearchParams({
        period: period
    });
    
    fetch(`/api/process-type-stats?${params}`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        updateProcessTypeStats(data);
        updateProcessTypeChart(data);
    })
    .catch(error => {
        console.error('Process type verileri yüklenirken hata:', error);
        // Hata durumunda varsayılan değerler
        document.getElementById('most-process-type').textContent = 'Veri yok';
        document.getElementById('most-process-type-count').textContent = '0 işlem';
        document.getElementById('total-process-types').textContent = '0';
    });
}

// Process Type istatistiklerini güncelle
function updateProcessTypeStats(data) {
    document.getElementById('most-process-type').textContent = data.most_process_type || 'Veri yok';
    document.getElementById('most-process-type-count').textContent = (data.most_process_type_count || 0) + ' işlem';
    document.getElementById('total-process-types').textContent = data.total_process_types || 0;
}

// Process Type grafiğini güncelle
function updateProcessTypeChart(data) {
    if (processTypeChart && data.process_types) {
        processTypeChart.data.labels = data.process_types.labels || [];
        processTypeChart.data.datasets[0].data = data.process_types.data || [];
        processTypeChart.update();
    }
}

// İşlem Bazlı Detay Tablosu verilerini yükle
let currentPage = 1;
let totalPages = 1;
const itemsPerPage = 10;

function loadOperationsDetailTable(period = 'monthly', page = 1) {
    const params = new URLSearchParams({
        period: period,
        page: page,
        per_page: itemsPerPage
    });
    
    // Loading state
    const tableBody = document.getElementById('operations-table-body');
    tableBody.innerHTML = `
        <tr>
            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                <i class="fas fa-spinner fa-spin mr-2"></i>Veriler yükleniyor...
            </td>
        </tr>
    `;
    
    fetch(`/api/operations-detail?${params}`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        updateOperationsDetailTable(data);
        updatePagination(data.pagination);
    })
    .catch(error => {
        console.error('İşlem detay verileri yüklenirken hata:', error);
        tableBody.innerHTML = `
            <tr>
                <td colspan="6" class="px-6 py-4 text-center text-red-500">
                    <i class="fas fa-exclamation-triangle mr-2"></i>Veriler yüklenirken hata oluştu
                </td>
            </tr>
        `;
    });
}

// İşlem Bazlı Detay Tablosunu güncelle
function updateOperationsDetailTable(data) {
    const tableBody = document.getElementById('operations-table-body');
    
    if (!data.operations || data.operations.length === 0) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                    <i class="fas fa-info-circle mr-2"></i>Bu dönemde işlem bulunamadı
                </td>
            </tr>
        `;
        return;
    }
    
    let tableHTML = '';
    data.operations.forEach(operation => {
        const changeClass = operation.change > 0 ? 'text-green-600' : operation.change < 0 ? 'text-red-600' : 'text-gray-600';
        const changeIcon = operation.change > 0 ? 'fa-arrow-up' : operation.change < 0 ? 'fa-arrow-down' : 'fa-minus';
        
        tableHTML += `
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">${operation.operation_name}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        ${operation.process_type}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    ${operation.total_count}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    ${operation.current_month}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    ${operation.previous_month}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm ${changeClass}">
                    <i class="fas ${changeIcon} mr-1"></i>
                    ${Math.abs(operation.change)}%
                </td>
            </tr>
        `;
    });
    
    tableBody.innerHTML = tableHTML;
}

// Sayfalama güncelle
function updatePagination(pagination) {
    currentPage = pagination.current_page;
    totalPages = pagination.last_page;
    
    // Sayfa bilgilerini güncelle
    document.getElementById('total-records').textContent = pagination.total;
    document.getElementById('showing-from').textContent = pagination.from || 0;
    document.getElementById('showing-to').textContent = pagination.to || 0;
    
    // Sayfalama butonlarını oluştur
    const paginationNav = document.getElementById('pagination-nav');
    let paginationHTML = '';
    
    // Önceki sayfa butonu
    if (currentPage > 1) {
        paginationHTML += `
            <button onclick="changePage(${currentPage - 1})" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                <i class="fas fa-chevron-left"></i>
            </button>
        `;
    }
    
    // Sayfa numaraları
    for (let i = Math.max(1, currentPage - 2); i <= Math.min(totalPages, currentPage + 2); i++) {
        const isActive = i === currentPage;
        paginationHTML += `
            <button onclick="changePage(${i})" class="relative inline-flex items-center px-4 py-2 border text-sm font-medium ${
                isActive 
                    ? 'z-10 bg-blue-50 border-blue-500 text-blue-600' 
                    : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'
            }">
                ${i}
            </button>
        `;
    }
    
    // Sonraki sayfa butonu
    if (currentPage < totalPages) {
        paginationHTML += `
            <button onclick="changePage(${currentPage + 1})" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                <i class="fas fa-chevron-right"></i>
            </button>
        `;
    }
    
    paginationNav.innerHTML = paginationHTML;
}

// Sayfa değiştir
function changePage(page) {
    if (page >= 1 && page <= totalPages) {
        const period = document.getElementById('table-period-select').value;
        loadOperationsDetailTable(period, page);
    }
}

// Excel'e aktar
function exportTableData() {
    const period = document.getElementById('table-period-select').value;
    window.open(`/api/operations-detail/export?period=${period}`, '_blank');
}

// PDF'e aktar
function exportTableDataPDF() {
    const period = document.getElementById('table-period-select').value;
    window.open(`/api/operations-detail/pdf?period=${period}`, '_blank');
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
    // setCurrentDate(); // Tarih input elementleri yorum satırında olduğu için devre dışı
    const firstReportBtn = document.querySelector('.report-btn');
    if (firstReportBtn) {
        showReport('patient', firstReportBtn);
    }
});
</script>
@endsection