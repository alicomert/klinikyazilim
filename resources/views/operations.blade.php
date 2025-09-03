@extends('layouts.app')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Operasyonlar</h1>
    
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-sm p-6 card-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-gray-500 text-sm font-medium">Bu Ay Operasyon</div>
                    <div class="text-3xl font-bold text-blue-600 mt-2">23</div>
                    <div class="text-green-500 text-sm mt-1">
                        <i class="fas fa-arrow-up"></i> %12 artış
                    </div>
                </div>
                <div class="bg-blue-100 p-4 rounded-full">
                    <i class="fas fa-procedures text-blue-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 card-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-gray-500 text-sm font-medium">Bugün Planlanan</div>
                    <div class="text-3xl font-bold text-green-600 mt-2">3</div>
                    <div class="text-gray-500 text-sm mt-1">
                        <i class="fas fa-clock"></i> 1 devam ediyor
                    </div>
                </div>
                <div class="bg-green-100 p-4 rounded-full">
                    <i class="fas fa-calendar-day text-green-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 card-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-gray-500 text-sm font-medium">Başarı Oranı</div>
                    <div class="text-3xl font-bold text-purple-600 mt-2">98%</div>
                    <div class="text-green-500 text-sm mt-1">
                        <i class="fas fa-arrow-up"></i> %2 artış
                    </div>
                </div>
                <div class="bg-purple-100 p-4 rounded-full">
                    <i class="fas fa-chart-line text-purple-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 card-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-gray-500 text-sm font-medium">Ortalama Süre</div>
                    <div class="text-3xl font-bold text-yellow-600 mt-2">2.5h</div>
                    <div class="text-red-500 text-sm mt-1">
                        <i class="fas fa-arrow-down"></i> %5 azalış
                    </div>
                </div>
                <div class="bg-yellow-100 p-4 rounded-full">
                    <i class="fas fa-stopwatch text-yellow-600 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6 card-shadow">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex-1 max-w-md">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <input type="text" id="searchInput" placeholder="Hasta adı, operasyon türü ile ara..." 
                           class="search-input w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <select class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                    <option>Tüm Durumlar</option>
                    <option>Planlandı</option>
                    <option>Devam Ediyor</option>
                    <option>Tamamlandı</option>
                    <option>İptal Edildi</option>
                </select>
                <select class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                    <option>Tüm Operasyonlar</option>
                    <option>Burun Estetiği</option>
                    <option>Göğüs Estetiği</option>
                    <option>Liposuction</option>
                    <option>Yüz Germe</option>
                    <option>Botoks</option>
                </select>
                <input type="date" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                <button id="addOperationBtn" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center space-x-2">
                    <i class="fas fa-plus"></i>
                    <span>Yeni Operasyon</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Operations Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden card-shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Operasyon Listesi</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hasta & Operasyon</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarih & Saat</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Süre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durum</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ekip</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="operationsTableBody">
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-12 w-12 bg-blue-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-blue-600"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">Meltem Karaca</div>
                                    <div class="text-sm text-gray-500">Burun Estetiği (Rinoplasti)</div>
                                    <div class="text-xs text-gray-400">OP-2023-001</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">05.10.2023</div>
                            <div class="text-sm text-gray-500">09:00 - 12:00</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">3 saat</div>
                            <div class="text-sm text-gray-500">Planlanan: 3h</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full status-completed">Tamamlandı</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">Dr. Ahmet Yılmaz</div>
                            <div class="text-sm text-gray-500">Hemşire: Ayşe K.</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <button class="text-blue-600 hover:text-blue-900 mr-3" title="Detayları Görüntüle">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="text-green-600 hover:text-green-900 mr-3" title="Rapor Oluştur">
                                <i class="fas fa-file-medical-alt"></i>
                            </button>
                            <button class="text-purple-600 hover:text-purple-900 mr-3" title="Fotoğraflar">
                                <i class="fas fa-camera"></i>
                            </button>
                            <button class="text-yellow-600 hover:text-yellow-900" title="Düzenle">
                                <i class="fas fa-edit"></i>
                            </button>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-12 w-12 bg-green-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-green-600"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">Sema Tekin</div>
                                    <div class="text-sm text-gray-500">Göğüs Estetiği (Augmentasyon)</div>
                                    <div class="text-xs text-gray-400">OP-2023-002</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">06.10.2023</div>
                            <div class="text-sm text-gray-500">10:00 - 13:30</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">2.5 saat</div>
                            <div class="text-sm text-gray-500">Planlanan: 3h</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full status-in-progress">Devam Ediyor</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">Dr. Ahmet Yılmaz</div>
                            <div class="text-sm text-gray-500">Hemşire: Fatma D.</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <button class="text-blue-600 hover:text-blue-900 mr-3" title="Detayları Görüntüle">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="text-red-600 hover:text-red-900 mr-3" title="Operasyonu Durdur">
                                <i class="fas fa-stop"></i>
                            </button>
                            <button class="text-purple-600 hover:text-purple-900 mr-3" title="Notlar">
                                <i class="fas fa-sticky-note"></i>
                            </button>
                            <button class="text-yellow-600 hover:text-yellow-900" title="Düzenle">
                                <i class="fas fa-edit"></i>
                            </button>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-12 w-12 bg-yellow-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-yellow-600"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">Ayşe Demir</div>
                                    <div class="text-sm text-gray-500">Liposuction (Karın Bölgesi)</div>
                                    <div class="text-xs text-gray-400">OP-2023-003</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">07.10.2023</div>
                            <div class="text-sm text-gray-500">08:30 - 11:00</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">2.5 saat</div>
                            <div class="text-sm text-gray-500">Planlanan: 2.5h</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full status-scheduled">Planlandı</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">Dr. Ahmet Yılmaz</div>
                            <div class="text-sm text-gray-500">Hemşire: Zeynep A.</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <button class="text-blue-600 hover:text-blue-900 mr-3" title="Detayları Görüntüle">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="text-green-600 hover:text-green-900 mr-3" title="Operasyonu Başlat">
                                <i class="fas fa-play"></i>
                            </button>
                            <button class="text-purple-600 hover:text-purple-900 mr-3" title="Ön Hazırlık">
                                <i class="fas fa-clipboard-check"></i>
                            </button>
                            <button class="text-yellow-600 hover:text-yellow-900" title="Düzenle">
                                <i class="fas fa-edit"></i>
                            </button>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-12 w-12 bg-purple-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-purple-600"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">Mehmet Özkan</div>
                                    <div class="text-sm text-gray-500">Yüz Germe (Facelift)</div>
                                    <div class="text-xs text-gray-400">OP-2023-004</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">08.10.2023</div>
                            <div class="text-sm text-gray-500">09:00 - 14:00</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">5 saat</div>
                            <div class="text-sm text-gray-500">Planlanan: 4.5h</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full status-scheduled">Planlandı</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">Dr. Ahmet Yılmaz</div>
                            <div class="text-sm text-gray-500">Hemşire: Elif S.</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <button class="text-blue-600 hover:text-blue-900 mr-3" title="Detayları Görüntüle">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="text-green-600 hover:text-green-900 mr-3" title="Operasyonu Başlat">
                                <i class="fas fa-play"></i>
                            </button>
                            <button class="text-purple-600 hover:text-purple-900 mr-3" title="Ön Hazırlık">
                                <i class="fas fa-clipboard-check"></i>
                            </button>
                            <button class="text-yellow-600 hover:text-yellow-900" title="Düzenle">
                                <i class="fas fa-edit"></i>
                            </button>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-12 w-12 bg-red-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-red-600"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">Can Burak</div>
                                    <div class="text-sm text-gray-500">Burun Estetiği (Revizyon)</div>
                                    <div class="text-xs text-gray-400">OP-2023-005</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">04.10.2023</div>
                            <div class="text-sm text-gray-500">10:00 - 13:00</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">-</div>
                            <div class="text-sm text-gray-500">Planlanan: 3h</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full status-cancelled">İptal Edildi</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">Dr. Ahmet Yılmaz</div>
                            <div class="text-sm text-gray-500">-</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <button class="text-blue-600 hover:text-blue-900 mr-3" title="Detayları Görüntüle">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="text-green-600 hover:text-green-900 mr-3" title="Yeniden Planla">
                                <i class="fas fa-redo"></i>
                            </button>
                            <button class="text-purple-600 hover:text-purple-900 mr-3" title="İptal Nedeni">
                                <i class="fas fa-info-circle"></i>
                            </button>
                            <button class="text-red-600 hover:text-red-900" title="Sil">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
            <div class="flex-1 flex justify-between sm:hidden">
                <button class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Önceki
                </button>
                <button class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Sonraki
                </button>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700">
                        <span class="font-medium">1</span> - <span class="font-medium">5</span> arası, toplam <span class="font-medium">156</span> operasyon
                    </p>
                </div>
                <div>
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                        <button class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <button class="bg-blue-50 border-blue-500 text-blue-600 relative inline-flex items-center px-4 py-2 border text-sm font-medium">1</button>
                        <button class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">2</button>
                        <button class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">3</button>
                        <button class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Operation Modal -->
<div id="addOperationModal" class="modal fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-2/3 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Yeni Operasyon Planla</h3>
                <button id="closeModal" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Hasta Seç *</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option>Hasta Seçin</option>
                            <option>Meltem Karaca</option>
                            <option>Sema Tekin</option>
                            <option>Ayşe Demir</option>
                            <option>Mehmet Özkan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Operasyon Türü *</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option>Operasyon Seçin</option>
                            <option>Burun Estetiği (Rinoplasti)</option>
                            <option>Göğüs Estetiği (Augmentasyon)</option>
                            <option>Liposuction</option>
                            <option>Yüz Germe (Facelift)</option>
                            <option>Karın Germe (Abdominoplasti)</option>
                            <option>Botoks</option>
                            <option>Dolgu</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Operasyon Tarihi *</label>
                        <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Başlangıç Saati *</label>
                        <input type="time" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tahmini Süre (saat) *</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option>Süre Seçin</option>
                            <option>1 saat</option>
                            <option>1.5 saat</option>
                            <option>2 saat</option>
                            <option>2.5 saat</option>
                            <option>3 saat</option>
                            <option>3.5 saat</option>
                            <option>4 saat</option>
                            <option>4.5 saat</option>
                            <option>5 saat</option>
                            <option>5+ saat</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ameliyathane</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option>Ameliyathane 1</option>
                            <option>Ameliyathane 2</option>
                            <option>Ameliyathane 3</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Anestezi Türü</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option>Genel Anestezi</option>
                            <option>Lokal Anestezi</option>
                            <option>Sedasyon</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Hemşire</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option>Hemşire Seçin</option>
                            <option>Ayşe Kaya</option>
                            <option>Fatma Demir</option>
                            <option>Zeynep Arslan</option>
                            <option>Elif Şahin</option>
                        </select>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Operasyon Detayları</label>
                    <textarea rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Operasyon hakkında detaylı bilgi"></textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ön Hazırlık Notları</label>
                    <textarea rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Operasyon öncesi hazırlık notları"></textarea>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" id="urgentOperation" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="urgentOperation" class="ml-2 block text-sm text-gray-900">Acil Operasyon</label>
                </div>
                
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" id="cancelBtn" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                        İptal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700">
                        Operasyon Planla
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Modal functionality
    const addOperationBtn = document.getElementById('addOperationBtn');
    const addOperationModal = document.getElementById('addOperationModal');
    const closeModal = document.getElementById('closeModal');
    const cancelBtn = document.getElementById('cancelBtn');

    addOperationBtn.addEventListener('click', () => {
        addOperationModal.classList.remove('hidden');
    });

    closeModal.addEventListener('click', () => {
        addOperationModal.classList.add('hidden');
    });

    cancelBtn.addEventListener('click', () => {
        addOperationModal.classList.add('hidden');
    });

    // Close modal when clicking outside
    addOperationModal.addEventListener('click', (e) => {
        if (e.target === addOperationModal) {
            addOperationModal.classList.add('hidden');
        }
    });

    // Search functionality
    const searchInput = document.getElementById('searchInput');
    const operationsTableBody = document.getElementById('operationsTableBody');

    searchInput.addEventListener('input', (e) => {
        const searchTerm = e.target.value.toLowerCase();
        const rows = operationsTableBody.getElementsByTagName('tr');

        Array.from(rows).forEach(row => {
            const text = row.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
</script>
@endsection