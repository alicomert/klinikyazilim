# Genel Sistem Kuralları - Livewire CRUD İşlemleri

## 1. Livewire Component Yapısı

### Temel Component Özellikleri
```php
class ExampleList extends Component
{
    // Public properties - Blade'de kullanılabilir
    public $items = [];
    public $newItem = [];
    public $editingItem = null;
    public $showModal = false;
    
    // Validation rules
    protected $rules = [
        'newItem.name' => 'required|string|max:255',
        'newItem.description' => 'nullable|string'
    ];
    
    // Mount method - Component yüklendiğinde çalışır
    public function mount()
    {
        $this->loadItems();
        $this->resetForm();
    }
}
```

## 2. CRUD İşlemleri - Kayıt Ekleme (Create)

### Livewire Component Method
```php
public function create()
{
    $this->validate();
    
    // Yeni kayıt oluştur
    ExampleModel::create([
        'name' => $this->newItem['name'],
        'description' => $this->newItem['description'],
        'user_id' => auth()->id(),
        'created_at' => now()
    ]);
    
    // Form'u temizle
    $this->resetForm();
    
    // Listeyi yenile
    $this->loadItems();
    
    // Modal'ı kapat
    $this->showModal = false;
    
    // Başarı mesajı
    session()->flash('message', 'Kayıt başarıyla eklendi.');
}

public function resetForm()
{
    $this->newItem = [
        'name' => '',
        'description' => ''
    ];
}
```

### Blade Template (Create)
```html
<!-- Ekleme Butonu -->
<button wire:click="$set('showModal', true)" class="bg-blue-500 text-white px-4 py-2 rounded">
    Yeni Ekle
</button>

<!-- Modal Form -->
@if($showModal)
<div class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center">
    <div class="bg-white p-6 rounded-lg w-96">
        <h3 class="text-lg font-semibold mb-4">Yeni Kayıt Ekle</h3>
        
        <form wire:submit.prevent="create">
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">İsim</label>
                <input type="text" wire:model="newItem.name" class="w-full border rounded px-3 py-2">
                @error('newItem.name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Açıklama</label>
                <textarea wire:model="newItem.description" class="w-full border rounded px-3 py-2"></textarea>
            </div>
            
            <div class="flex justify-end space-x-2">
                <button type="button" wire:click="$set('showModal', false)" class="bg-gray-500 text-white px-4 py-2 rounded">
                    İptal
                </button>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">
                    Kaydet
                </button>
            </div>
        </form>
    </div>
</div>
@endif
```

## 3. CRUD İşlemleri - Kayıt Güncelleme (Update)

### Livewire Component Method
```php
public function edit($itemId)
{
    $item = ExampleModel::findOrFail($itemId);
    
    // Düzenleme moduna geç
    $this->editingItem = $item->id;
    $this->newItem = [
        'name' => $item->name,
        'description' => $item->description
    ];
    $this->showModal = true;
}

public function update()
{
    $this->validate();
    
    $item = ExampleModel::findOrFail($this->editingItem);
    
    // Yetki kontrolü
    if (!$this->canEdit($item)) {
        session()->flash('error', 'Bu kaydı düzenleme yetkiniz yok.');
        return;
    }
    
    // Kaydı güncelle
    $item->update([
        'name' => $this->newItem['name'],
        'description' => $this->newItem['description'],
        'updated_at' => now()
    ]);
    
    // Form'u temizle
    $this->resetEditForm();
    
    // Listeyi yenile
    $this->loadItems();
    
    session()->flash('message', 'Kayıt başarıyla güncellendi.');
}

public function resetEditForm()
{
    $this->editingItem = null;
    $this->newItem = [];
    $this->showModal = false;
}
```

### Blade Template (Update)
```html
<!-- Düzenleme Butonu -->
<button wire:click="edit({{ $item->id }})" class="bg-yellow-500 text-white px-3 py-1 rounded text-sm">
    Düzenle
</button>

<!-- Modal Form (Aynı form, başlık değişir) -->
<h3 class="text-lg font-semibold mb-4">
    {{ $editingItem ? 'Kayıt Düzenle' : 'Yeni Kayıt Ekle' }}
</h3>

<!-- Submit butonu -->
<button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">
    {{ $editingItem ? 'Güncelle' : 'Kaydet' }}
</button>
```

## 4. CRUD İşlemleri - Kayıt Silme (Delete)

### Livewire Component Method
```php
public function delete($itemId)
{
    $item = ExampleModel::findOrFail($itemId);
    
    // Yetki kontrolü
    if (!$this->canDelete($item)) {
        session()->flash('error', 'Bu kaydı silme yetkiniz yok.');
        return;
    }
    
    // Kaydı sil
    $item->delete();
    
    // Listeyi yenile
    $this->loadItems();
    
    session()->flash('message', 'Kayıt başarıyla silindi.');
}

// Yetki kontrol methodları
public function canEdit($item)
{
    $user = auth()->user();
    
    // Admin her şeyi düzenleyebilir
    if ($user->role === 'admin') {
        return true;
    }
    
    // Kullanıcı sadece kendi kayıtlarını düzenleyebilir
    return $item->user_id === $user->id;
}

public function canDelete($item)
{
    $user = auth()->user();
    
    // Admin her şeyi silebilir
    if ($user->role === 'admin') {
        return true;
    }
    
    // Kullanıcı sadece kendi kayıtlarını silebilir
    return $item->user_id === $user->id;
}
```

### Blade Template (Delete)
```html
<!-- Silme Butonu (Onay ile) -->
<button 
    wire:click="delete({{ $item->id }})" 
    wire:confirm="Bu kaydı silmek istediğinizden emin misiniz?"
    class="bg-red-500 text-white px-3 py-1 rounded text-sm"
>
    Sil
</button>

<!-- Yetki kontrolü ile gösterme -->
@if($this->canDelete($item))
    <button wire:click="delete({{ $item->id }})" wire:confirm="Silmek istediğinizden emin misiniz?">
        Sil
    </button>
@endif
```

## 5. Veri Yükleme ve Listeleme

### Livewire Component Method
```php
public function loadItems()
{
    $query = ExampleModel::query();
    
    // Kullanıcı rolüne göre filtreleme
    if (auth()->user()->role !== 'admin') {
        $query->where('user_id', auth()->id());
    }
    
    // Sıralama
    $query->orderBy('created_at', 'desc');
    
    $this->items = $query->get();
}

// Computed property olarak da kullanılabilir
public function getItemsProperty()
{
    return ExampleModel::when(auth()->user()->role !== 'admin', function($query) {
        $query->where('user_id', auth()->id());
    })->orderBy('created_at', 'desc')->get();
}
```

### Blade Template (Listeleme)
```html
<!-- Liste Tablosu -->
<div class="overflow-x-auto">
    <table class="min-w-full bg-white border border-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">İsim</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Açıklama</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tarih</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">İşlemler</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($items as $item)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->name }}</td>
                    <td class="px-6 py-4 text-sm text-gray-900">{{ $item->description }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->created_at->format('d.m.Y H:i') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                        @if($this->canEdit($item))
                            <button wire:click="edit({{ $item->id }})" class="bg-yellow-500 text-white px-3 py-1 rounded text-sm">
                                Düzenle
                            </button>
                        @endif
                        
                        @if($this->canDelete($item))
                            <button wire:click="delete({{ $item->id }})" wire:confirm="Silmek istediğinizden emin misiniz?" class="bg-red-500 text-white px-3 py-1 rounded text-sm">
                                Sil
                            </button>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                        Henüz kayıt bulunmuyor.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
```

## 6. Rol Tabanlı Yetkilendirme Sistemi

### Yetki Kontrol Methodları
```php
// Genel yetki kontrol trait'i
trait HasPermissions
{
    public function canCreate()
    {
        $user = auth()->user();
        return in_array($user->role, ['admin', 'doctor', 'nurse']);
    }
    
    public function canEdit($model)
    {
        $user = auth()->user();
        
        // Admin her şeyi yapabilir
        if ($user->role === 'admin') {
            return true;
        }
        
        // Doktor sadece kendi kayıtlarını düzenleyebilir
        if ($user->role === 'doctor') {
            return $model->user_id === $user->id;
        }
        
        // Hemşire ve sekreter birbirlerinin kayıtlarını düzenleyebilir ama doktorunkini değil
        if (in_array($user->role, ['nurse', 'secretary'])) {
            $modelOwner = User::find($model->user_id);
            return $modelOwner->role !== 'doctor';
        }
        
        return false;
    }
    
    public function canDelete($model)
    {
        // Silme yetkileri düzenleme ile aynı
        return $this->canEdit($model);
    }
    
    public function canView($model)
    {
        $user = auth()->user();
        
        // Özel kayıtlar sadece sahibi tarafından görülebilir
        if (isset($model->is_private) && $model->is_private) {
            return $model->user_id === $user->id;
        }
        
        // Genel kayıtlar herkese açık
        return true;
    }
}
```

## 7. Validation ve Error Handling

### Validation Rules
```php
protected $rules = [
    'newItem.name' => 'required|string|max:255',
    'newItem.email' => 'required|email|unique:users,email',
    'newItem.phone' => 'nullable|string|max:20',
    'newItem.date' => 'required|date|after_or_equal:today'
];

// Real-time validation
protected $validationAttributes = [
    'newItem.name' => 'isim',
    'newItem.email' => 'e-posta',
    'newItem.phone' => 'telefon'
];

// Custom validation messages
protected $messages = [
    'newItem.name.required' => 'İsim alanı zorunludur.',
    'newItem.email.unique' => 'Bu e-posta adresi zaten kullanılıyor.'
];
```

### Error Handling
```php
public function create()
{
    try {
        $this->validate();
        
        ExampleModel::create($this->newItem);
        
        session()->flash('message', 'Kayıt başarıyla eklendi.');
        $this->resetForm();
        
    } catch (ValidationException $e) {
        // Validation hataları otomatik olarak gösterilir
        throw $e;
        
    } catch (Exception $e) {
        session()->flash('error', 'Bir hata oluştu: ' . $e->getMessage());
    }
}
```

## 8. Performance Optimizasyonu

### Lazy Loading ve Caching
```php
// Lazy loading için
public function loadItems()
{
    $this->items = ExampleModel::with(['user', 'category'])
        ->when($this->search, function($query) {
            $query->where('name', 'like', '%' . $this->search . '%');
        })
        ->orderBy('created_at', 'desc')
        ->get();
}

// Computed property ile caching
public function getItemsProperty()
{
    return once(function () {
        return ExampleModel::with('user')->get();
    });
}
```

### Wire:key Kullanımı
```html
<!-- Liste elemanları için unique key -->
@foreach($items as $item)
    <tr wire:key="item-{{ $item->id }}">
        <!-- İçerik -->
    </tr>
@endforeach
```

Bu kurallar tüm Livewire component'leri için geçerlidir ve hasta notları sisteminde kullanılan yapının aynısıdır.