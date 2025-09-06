<div>
    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-3 gap-6 mb-6">
        <div class="stat-card bg-white rounded-lg shadow-sm p-6 card-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-gray-500 text-sm font-medium">Bu Ay Operasyon</div>
                    <div class="text-3xl font-bold text-blue-600 mt-2">{{ number_format($this->stats['this_month_operations']) }}</div>
                    @if($this->stats['monthly_percentage_change'] !== 0)
                        <div class="text-{{ $this->stats['monthly_percentage_change'] >= 0 ? 'green' : 'red' }}-500 text-sm mt-1">
                            <i class="fas fa-arrow-{{ $this->stats['monthly_percentage_change'] >= 0 ? 'up' : 'down' }}"></i> 
                            %{{ abs($this->stats['monthly_percentage_change']) }} geçen aya göre
                        </div>
                    @else
                        <div class="text-gray-500 text-sm mt-1">
                            <i class="fas fa-calendar-check"></i> {{ $this->stats['current_month'] }}
                        </div>
                    @endif
                </div>
                <div class="bg-blue-100 p-4 rounded-full">
                    <i class="fas fa-calendar-check text-blue-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="stat-card bg-white rounded-lg shadow-sm p-6 card-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-gray-500 text-sm font-medium">Toplam Hasta</div>
                    <div class="text-3xl font-bold text-green-600 mt-2">{{ number_format($this->stats['total_patients']) }}</div>
                    <div class="text-green-500 text-sm mt-1">
                        <i class="fas fa-users"></i> Kayıtlı hasta
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
                    <div class="text-gray-500 text-sm font-medium">Toplam Operasyon</div>
                    <div class="text-3xl font-bold text-purple-600 mt-2">{{ number_format($this->stats['total_operations']) }}</div>
                    @if($this->stats['yearly_percentage_change'] !== 0)
                        <div class="text-{{ $this->stats['yearly_percentage_change'] >= 0 ? 'green' : 'red' }}-500 text-sm mt-1">
                            <i class="fas fa-arrow-{{ $this->stats['yearly_percentage_change'] >= 0 ? 'up' : 'down' }}"></i> 
                            %{{ abs($this->stats['yearly_percentage_change']) }} geçen yıla göre
                        </div>
                    @else
                        <div class="text-purple-500 text-sm mt-1">
                            <i class="fas fa-procedures"></i> {{ $this->stats['current_year'] }} yılı
                        </div>
                    @endif
                </div>
                <div class="bg-purple-100 p-4 rounded-full">
                    <i class="fas fa-procedures text-purple-600 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Monthly Operation Trend -->
        <div class="bg-white rounded-lg shadow-sm p-6 card-shadow">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Aylık Operasyon Trendi</h3>
                <select wire:model.live="operationTrendPeriod" class="text-sm border rounded-lg px-3 py-1">
                    <option value="6months">Son 6 Ay</option>
                    <option value="12months">Son 12 Ay</option>
                </select>
            </div>
            <div class="chart-container h-64">
                <canvas id="dashboardOperationTrendChart"></canvas>
            </div>
        </div>

        <!-- Procedure Distribution -->
        <div class="bg-white rounded-lg shadow-sm p-6 card-shadow">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Prosedür Dağılımı</h3>
                <select wire:model.live="procedurePeriod" class="text-sm border rounded-lg px-3 py-1">
                    <option value="current_month">Bu Ay</option>
                    <option value="last_3_months">Son 3 Ay</option>
                </select>
            </div>
            <div class="chart-container h-64">
                <canvas id="dashboardProcedureChart"></canvas>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Activities Section -->
        <div class="bg-white rounded-lg shadow-sm p-6 card-shadow">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Son Aktiviteler</h3>
            </div>
            <div class="space-y-4">
                @forelse($this->recentActivities as $activity)
                    <div class="flex items-start space-x-3">
                        <div class="bg-{{ $activity['color'] }}-100 p-2 rounded-full">
                            <i class="{{ $activity['icon'] }} text-{{ $activity['color'] }}-600"></i>
                        </div>
                        <div class="flex-1">
                            <div class="text-sm font-medium">{{ $this->getActivityTitle($activity['type']) }}</div>
                            <div class="text-xs text-gray-500">{{ $activity['patient_name'] }} - {{ \Illuminate\Support\Str::limit($activity['description'], 50) }}</div>
                            <div class="text-xs text-gray-400">{{ $activity['time_ago'] }}</div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <div class="text-gray-400">
                            <i class="fas fa-history text-3xl mb-3"></i>
                            <p class="text-sm">Henüz aktivite bulunmuyor</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Today's Schedule -->
        <div class="bg-white rounded-lg shadow-sm p-6 card-shadow">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Yaklaşan Randevular</h3>
                <a href="{{ route('clinic') }}" class="text-blue-600 text-sm hover:underline">Tümünü Gör</a>
            </div>
            <div class="space-y-4">
                @forelse($this->todayAppointments as $appointment)
                    <div class="flex items-center space-x-4 p-3 bg-{{ $appointment['bg_color'] }} rounded-lg">
                        <div class="text-{{ $appointment['color'] }}-600 font-semibold">{{ $appointment['time'] }}</div>
                        <div class="flex-1">
                            <div class="font-medium">{{ $appointment['patient_name'] }}</div>
                            <div class="text-sm text-gray-500">{{ $appointment['appointment_type_text'] }}</div>
                        </div>
                        <div class="bg-{{ $appointment['color'] }}-100 text-{{ $appointment['color'] }}-800 px-2 py-1 rounded text-xs">
                            {{ $appointment['appointment_type_text'] }}
                        </div>
                        @if($appointment['status'] === 'completed')
                            <div class="text-green-600 text-sm">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        @elseif($appointment['status'] === 'no_show')
                            <div class="text-red-600 text-sm">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-8">
                        <div class="text-gray-400 mb-2">
                            <i class="fas fa-calendar-day text-3xl"></i>
                        </div>
                        <p class="text-gray-500">Yaklaşan randevu bulunmuyor.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
    <script>
        // Global değişkenleri window objesinde kontrol et
        if (typeof window.dashboardOperationChart === 'undefined') {
            window.dashboardOperationChart = null;
        }
        if (typeof window.dashboardProcedureChart === 'undefined') {
            window.dashboardProcedureChart = null;
        }
        
        // Livewire navigasyon event'lerini dinle
        document.addEventListener('livewire:navigated', function() {
            setTimeout(function() {
                initializeDashboardCharts();
            }, 2000);
        });
        
        document.addEventListener('livewire:load', function() {
            setTimeout(function() {
                initializeDashboardCharts();
            }, 2000);
        });
        
        document.addEventListener('DOMContentLoaded', function() {
            initializeDashboardCharts();
        });
        
        // Sayfa görünürlük değişikliklerini dinle
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                setTimeout(function() {
                    initializeDashboardCharts();
                }, 500);
            }
        });
        
        // Window focus event'ini dinle
        window.addEventListener('focus', function() {
            setTimeout(function() {
                initializeDashboardCharts();
            }, 500);
        });
        
        // Livewire component güncellendiğinde
        Livewire.on('refreshCharts', function() {
            setTimeout(function() {
                initializeDashboardCharts();
            }, 100);
        });
        
        function initializeDashboardCharts() {
            // Destroy existing charts
            if (window.dashboardOperationChart && typeof window.dashboardOperationChart.destroy === 'function') {
                window.dashboardOperationChart.destroy();
                window.dashboardOperationChart = null;
            }
            if (window.dashboardProcedureChart && typeof window.dashboardProcedureChart.destroy === 'function') {
                window.dashboardProcedureChart.destroy();
                window.dashboardProcedureChart = null;
            }
            
            // Operation Trend Chart
            const operationTrendCtx = document.getElementById('dashboardOperationTrendChart');
            if (operationTrendCtx) {
                const trendData = @json($this->monthlyOperationTrend);
                
                window.dashboardOperationChart = new Chart(operationTrendCtx, {
                    type: 'line',
                    data: {
                        labels: trendData.labels,
                        datasets: [{
                            label: 'Operasyon Sayısı',
                            data: trendData.data,
                            borderColor: '#3B82F6',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#3B82F6',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            pointRadius: 5
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
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });
            }

            // Procedure Distribution Chart
            const procedureCtx = document.getElementById('dashboardProcedureChart');
            if (procedureCtx) {
                const procedureData = @json($this->procedureDistribution);
                
                window.dashboardProcedureChart = new Chart(procedureCtx, {
                    type: 'doughnut',
                    data: {
                        labels: procedureData.labels,
                        datasets: [{
                            data: procedureData.data,
                            backgroundColor: [
                                '#3B82F6',
                                '#10B981',
                                '#8B5CF6',
                                '#F59E0B'
                            ],
                            borderWidth: 2,
                            borderColor: '#ffffff'
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
                                    usePointStyle: true,
                                    font: {
                                        size: 12
                                    }
                                }
                            }
                        }
                    }
                });
            }
        }
    </script>
</div>