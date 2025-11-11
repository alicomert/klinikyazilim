<div>
    @if($showModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full" style="z-index: 999999;" wire:click="closeModal">
            <div class="relative top-6 mx-auto p-6 border w-11/12 md:w-3/4 lg:max-w-6xl shadow-2xl rounded-2xl bg-white" style="z-index: 1000000;" wire:click.stop>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Toplu Hasta & İşlem Ekle</h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <div class="mb-3 flex items-center gap-2">
                    <button wire:click="saveAll" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">
                        <i class="fas fa-save mr-2"></i>Kaydet
                    </button>
                    <div class="text-xs text-gray-500 ml-auto">
                        Maksimum 10 satır hazır gelir. Enter: bir alt satır aynı kolona geçiş • Tab: bir sonraki alana geçiş • Boş satırlar kaydedilmez.
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-600">Ad</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-600">Soyad</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-600">TC</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-600">Telefon</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-600">Doğum Tarihi</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-600">Kayıt Tarihi</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-600">Adres</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-600">Operasyon(lar)</th>
                                <th class="px-3 py-2"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($rows as $rIdx => $row)
                                <tr>
                                    <td class="px-3 py-2">
                                        <input type="text" class="w-full border rounded px-2 py-1" wire:model.defer="rows.{{ $rIdx }}.first_name" placeholder="Ad">
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="text" class="w-full border rounded px-2 py-1" wire:model.defer="rows.{{ $rIdx }}.last_name" placeholder="Soyad">
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="text" class="w-full border rounded px-2 py-1" wire:model.defer="rows.{{ $rIdx }}.tc_identity" placeholder="11 hane">
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="text" class="w-full border rounded px-2 py-1" wire:model.defer="rows.{{ $rIdx }}.phone" placeholder="05xxxxxxxxx">
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="date" class="w-full border rounded px-2 py-1" wire:model.defer="rows.{{ $rIdx }}.birth_date">
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="date" class="w-full border rounded px-2 py-1" wire:model.defer="rows.{{ $rIdx }}.registration_date">
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="text" class="w-full border rounded px-2 py-1" wire:model.defer="rows.{{ $rIdx }}.address" placeholder="Adres">
                                    </td>
                                    <td class="px-3 py-2">
                                        <div class="space-y-2">
                                            <div class="flex items-center gap-2">
                                                <label class="inline-flex items-center">
                                                    <input type="checkbox" wire:model="rows.{{ $rIdx }}.add_operation" class="mr-2">
                                                    <span class="text-xs text-gray-600">Operasyon ekle</span>
                                                </label>
                                                <button type="button" class="bg-indigo-600 hover:bg-indigo-700 text-white px-2 py-1 rounded text-xs" wire:click="addOperation({{ $rIdx }})" @class(['hidden' => !($row['add_operation'] ?? false)])>
                                                    <i class="fas fa-plus mr-1"></i>Operasyon
                                                </button>
                                            </div>
                                            @if(!empty($row['operations']))
                                                @foreach(($row['operations'] ?? []) as $oIdx => $op)
                                                    <div class="grid grid-cols-1 md:grid-cols-5 gap-2">
                                                        <div>
                                                            <select class="w-full border rounded px-2 py-1" wire:model.defer="rows.{{ $rIdx }}.operations.{{ $oIdx }}.process">
                                                                <option value="">İşlem Süreci</option>
                                                                <option value="ameliyat">Ameliyat</option>
                                                                <option value="mezoterapi">Mezoterapi</option>
                                                                <option value="dolgu">Dolgu</option>
                                                                <option value="botoks">Botoks</option>
                                                            </select>
                                                        </div>
                                                        <div>
                                                            <input type="text" class="w-full border rounded px-2 py-1" wire:model.defer="rows.{{ $rIdx }}.operations.{{ $oIdx }}.type_name" placeholder="İşlem Tipi">
                                                        </div>
                                                        <div class="md:col-span-2">
                                                            <input type="text" class="w-full border rounded px-2 py-1" wire:model.defer="rows.{{ $rIdx }}.operations.{{ $oIdx }}.process_detail" placeholder="Detay">
                                                        </div>
                                                        <div class="flex items-center gap-2">
                                                            <input type="month" class="border rounded px-2 py-1" wire:model.defer="rows.{{ $rIdx }}.operations.{{ $oIdx }}.registration_period">
                                                            <input type="date" class="border rounded px-2 py-1" wire:model.defer="rows.{{ $rIdx }}.operations.{{ $oIdx }}.process_date">
                                                            <button type="button" class="text-red-600 hover:text-red-800" wire:click="removeOperation({{ $rIdx }}, {{ $oIdx }})">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-3 py-2 text-right">
                                        <button type="button" class="text-red-600 hover:text-red-800" wire:click="removeRow({{ $rIdx }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Datalist for operation types per process -->
                <datalist id="dlist-surgery">
                    @foreach(($operationTypesMap['surgery'] ?? []) as $name)
                        <option value="{{ $name }}"></option>
                    @endforeach
                </datalist>
                <datalist id="dlist-mesotherapy">
                    @foreach(($operationTypesMap['mesotherapy'] ?? []) as $name)
                        <option value="{{ $name }}"></option>
                    @endforeach
                </datalist>
                <datalist id="dlist-botox">
                    @foreach(($operationTypesMap['botox'] ?? []) as $name)
                        <option value="{{ $name }}"></option>
                    @endforeach
                </datalist>
                <datalist id="dlist-filler">
                    @foreach(($operationTypesMap['filler'] ?? []) as $name)
                        <option value="{{ $name }}"></option>
                    @endforeach
                </datalist>
            </div>
        </div>
    @endif
</div>