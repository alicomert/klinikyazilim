@extends('layouts.app')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">{{ Auth::user()->getRoleDisplayName() }} Paneli</h1>
    
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-100 dark:border-gray-700 card-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Bugünkü Hastalar</p>
                    <p class="text-3xl font-bold text-gray-800 dark:text-gray-200 mt-1">12</p>
                    <p class="text-green-600 dark:text-green-400 text-sm mt-1">
                        <i class="fas fa-arrow-up"></i> 2 yeni hasta
                    </p>
                </div>
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user-injured text-blue-600 dark:text-blue-400 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-100 dark:border-gray-700 card-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Bugünkü Randevular</p>
                    <p class="text-3xl font-bold text-gray-800 dark:text-gray-200 mt-1">8</p>
                    <p class="text-blue-600 dark:text-blue-400 text-sm mt-1">
                        <i class="fas fa-clock"></i> 3 beklemede
                    </p>
                </div>
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar-check text-green-600 dark:text-green-400 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-100 dark:border-gray-700 card-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Planlanan Operasyonlar</p>
                    <p class="text-3xl font-bold text-gray-800 dark:text-gray-200 mt-1">3</p>
                    <p class="text-orange-600 dark:text-orange-400 text-sm mt-1">
                        <i class="fas fa-procedures"></i> Bu hafta
                    </p>
                </div>
                <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900 rounded-lg flex items-center justify-center">
                    <i class="fas fa-procedures text-orange-600 dark:text-orange-400 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-100 dark:border-gray-700 card-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Yeni Mesajlar</p>
                    <p class="text-3xl font-bold text-gray-800 dark:text-gray-200 mt-1">5</p>
                    <p class="text-purple-600 dark:text-purple-400 text-sm mt-1">
                        <i class="fas fa-envelope"></i> Sekreterden
                    </p>
                </div>
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                    <i class="fas fa-comments text-purple-600 dark:text-purple-400 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Bugünkü Program ve Hızlı İşlemler Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-8">
        <!-- Bugünkü Program (3/4 genişlik) -->
        <div class="lg:col-span-3">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 h-[600px] flex flex-col">
                <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex-shrink-0">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200">Bugünkü Program</h2>
                        <button class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">
                            <i class="fas fa-plus mr-2"></i>Yeni Randevu
                        </button>
                    </div>
                </div>
                <div class="p-6 flex-1 overflow-y-auto">
                    <div class="space-y-4">
                        <div class="flex items-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-100 dark:border-blue-800">
                            <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center text-white font-bold">
                                09:00
                            </div>
                            <div class="ml-4 flex-1">
                                <h3 class="font-semibold text-gray-800 dark:text-gray-200">Ayşe Demir - Konsültasyon</h3>
                                <p class="text-gray-600 dark:text-gray-400 text-sm">Rinoplasti değerlendirmesi</p>
                            </div>
                            <div class="flex space-x-2">
                                <button class="px-3 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-full text-sm font-medium">
                                    <i class="fas fa-check mr-1"></i>Tamamla
                                </button>
                                <button class="px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full text-sm font-medium">
                                    <i class="fas fa-edit mr-1"></i>Düzenle
                                </button>
                            </div>
                        </div>

                        <div class="flex items-center p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-100 dark:border-yellow-800">
                            <div class="w-12 h-12 bg-yellow-600 rounded-lg flex items-center justify-center text-white font-bold">
                                10:30
                            </div>
                            <div class="ml-4 flex-1">
                                <h3 class="font-semibold text-gray-800 dark:text-gray-200">Mehmet Kaya - Kontrol</h3>
                                <p class="text-gray-600 dark:text-gray-400 text-sm">Post-op kontrol (7. gün)</p>
                            </div>
                            <div class="flex space-x-2">
                                <button class="px-3 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 rounded-full text-sm font-medium">
                                    <i class="fas fa-clock mr-1"></i>Beklemede
                                </button>
                                <button class="px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full text-sm font-medium">
                                    <i class="fas fa-edit mr-1"></i>Düzenle
                                </button>
                            </div>
                        </div>

                        <div class="flex items-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-100 dark:border-green-800">
                            <div class="w-12 h-12 bg-green-600 rounded-lg flex items-center justify-center text-white font-bold">
                                14:00
                            </div>
                            <div class="ml-4 flex-1">
                                <h3 class="font-semibold text-gray-800 dark:text-gray-200">Fatma Özkan - Operasyon</h3>
                                <p class="text-gray-600 dark:text-gray-400 text-sm">Meme büyütme ameliyatı</p>
                            </div>
                            <div class="flex space-x-2">
                                <button class="px-3 py-1 bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400 rounded-full text-sm font-medium">
                                    <i class="fas fa-procedures mr-1"></i>Operasyon
                                </button>
                                <button class="px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full text-sm font-medium">
                                    <i class="fas fa-file-medical mr-1"></i>Dosya
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hızlı İşlemler (1/4 genişlik) -->
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 h-[600px] flex flex-col">
                <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex-shrink-0">
                    <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200">Hızlı İşlemler</h2>
                </div>
                <div class="p-6 flex-1 overflow-y-auto">
                    <div class="space-y-4">
                        <button class="w-full flex items-center p-4 bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/30 rounded-lg border border-blue-100 dark:border-blue-800 transition-colors">
                            <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                                <i class="fas fa-user-plus text-white"></i>
                            </div>
                            <div class="ml-3 text-left">
                                <p class="font-semibold text-gray-800 dark:text-gray-200">Yeni Hasta Kaydı</p>
                                <p class="text-gray-600 dark:text-gray-400 text-sm">Hasta bilgilerini ekle</p>
                            </div>
                        </button>

                        <button class="w-full flex items-center p-4 bg-green-50 dark:bg-green-900/20 hover:bg-green-100 dark:hover:bg-green-900/30 rounded-lg border border-green-100 dark:border-green-800 transition-colors">
                            <div class="w-10 h-10 bg-green-600 rounded-lg flex items-center justify-center">
                                <i class="fas fa-calendar-plus text-white"></i>
                            </div>
                            <div class="ml-3 text-left">
                                <p class="font-semibold text-gray-800 dark:text-gray-200">Randevu Planla</p>
                                <p class="text-gray-600 dark:text-gray-400 text-sm">Yeni randevu oluştur</p>
                            </div>
                        </button>

                        <button class="w-full flex items-center p-4 bg-orange-50 dark:bg-orange-900/20 hover:bg-orange-100 dark:hover:bg-orange-900/30 rounded-lg border border-orange-100 dark:border-orange-800 transition-colors">
                            <div class="w-10 h-10 bg-orange-600 rounded-lg flex items-center justify-center">
                                <i class="fas fa-procedures text-white"></i>
                            </div>
                            <div class="ml-3 text-left">
                                <p class="font-semibold text-gray-800 dark:text-gray-200">Operasyon Planla</p>
                                <p class="text-gray-600 dark:text-gray-400 text-sm">Ameliyat programı</p>
                            </div>
                        </button>

                        <button class="w-full flex items-center p-4 bg-purple-50 dark:bg-purple-900/20 hover:bg-purple-100 dark:hover:bg-purple-900/30 rounded-lg border border-purple-100 dark:border-purple-800 transition-colors">
                            <div class="w-10 h-10 bg-purple-600 rounded-lg flex items-center justify-center">
                                <i class="fas fa-file-medical text-white"></i>
                            </div>
                            <div class="ml-3 text-left">
                                <p class="font-semibold text-gray-800 dark:text-gray-200">Hasta Dosyası</p>
                                <p class="text-gray-600 dark:text-gray-400 text-sm">Tıbbi kayıtlar</p>
                            </div>
                        </button>

                        <button class="w-full flex items-center p-4 bg-indigo-50 dark:bg-indigo-900/20 hover:bg-indigo-100 dark:hover:bg-indigo-900/30 rounded-lg border border-indigo-100 dark:border-indigo-800 transition-colors">
                            <div class="w-10 h-10 bg-indigo-600 rounded-lg flex items-center justify-center">
                                <i class="fas fa-chart-line text-white"></i>
                            </div>
                            <div class="ml-3 text-left">
                                <p class="font-semibold text-gray-800 dark:text-gray-200">Raporlar</p>
                                <p class="text-gray-600 dark:text-gray-400 text-sm">İstatistikler ve analiz</p>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Son Hastalar -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
        <div class="p-6 border-b border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200">Son Hastalar</h2>
                <a href="{{ route('patients') }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">
                    Tümünü Görüntüle <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Hasta</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Son Randevu</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Prosedür</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Durum</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-blue-600 dark:text-blue-400"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">Meltem Karaca</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">35 yaş</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 dark:text-white">02.10.2023</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">09:00</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">Burun Estetiği</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">Kontrol</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            <button class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 mr-3" title="Detayları Görüntüle">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300 mr-3" title="Randevu Ver">
                                <i class="fas fa-calendar-plus"></i>
                            </button>
                            <button class="text-purple-600 dark:text-purple-400 hover:text-purple-900 dark:hover:text-purple-300" title="Dosya">
                                <i class="fas fa-file-medical"></i>
                            </button>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-green-600 dark:text-green-400"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">Can Burak</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">28 yaş</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 dark:text-white">01.10.2023</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">11:30</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">İlk Muayene</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200">Beklemede</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            <button class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 mr-3" title="Detayları Görüntüle">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300 mr-3" title="Randevu Ver">
                                <i class="fas fa-calendar-plus"></i>
                            </button>
                            <button class="text-purple-600 dark:text-purple-400 hover:text-purple-900 dark:hover:text-purple-300" title="Dosya">
                                <i class="fas fa-file-medical"></i>
                            </button>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-purple-600 dark:text-purple-400"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">Sema Tekin</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">42 yaş</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 dark:text-white">03.10.2023</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">10:15</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200">Dudak Dolgusu</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">Tamamlandı</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            <button class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 mr-3" title="Detayları Görüntüle">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300 mr-3" title="Randevu Ver">
                                <i class="fas fa-calendar-plus"></i>
                            </button>
                            <button class="text-purple-600 dark:text-purple-400 hover:text-purple-900 dark:hover:text-purple-300" title="Dosya">
                                <i class="fas fa-file-medical"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection