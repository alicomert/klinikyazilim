# Hasta Not Sistemi - Genel Kurallar ve Standartlar

## 1. Veritabanı Yapısı Kuralları

### PatientNote Model Zorunlu Alanları
```php
// ZORUNLU ALANLAR - Asla kaldırılmamalı
'patient_id' => 'required|exists:patients,id',
'user_id' => 'required|exists:users,id',
'content' => 'required|string|min:1|max:5000',
'note_type' => 'required|in:general,medical,appointment,treatment',
'is_private' => 'boolean'

// YASAKLI ALANLAR - Asla eklenmemeli
'title' => 'YASAK - Kaldırıldı',
'priority' => 'YASAK - Kaldırıldı'
```

### Migration Kuralları
- `note_type` enum değerleri: `['general', 'medical', 'appointment', 'treatment']`
- `is_private` boolean default false
- `priority` alanı tamamen kaldırıldı
- `title` alanı tamamen kaldırıldı

## 2. Role-Based Authorization Kuralları

### Doktor Yetkileri
```php
// Doktorlar SADECE kendi notlarını yönetebilir
if ($user->role === 'doctor') {
    // ✅ Kendi notlarını düzenleyebilir/silebilir
    return $note->user_id === $user->id;
    // ✅ is_private özelliğini kullanabilir
    // ❌ Başkalarının notlarına dokunamaz
}
```

### Hemşire/Sekreter Yetkileri
```php
// Hemşire ve sekreterler birbirlerinin notlarını yönetebilir
if ($user->role === 'nurse' || $user->role === 'secretary') {
    // ✅ Birbirlerinin notlarını düzenleyebilir/silebilir
    // ❌ Doktor notlarına dokunamaz
    if ($note->user->role === 'doctor') {
        return false;
    }
    return true;
    // ❌ is_private özelliğini kullanamaz
}
```

### Private Not Kuralları
```php
// Private notları sadece sahibi görebilir
->where(function($query) use ($user) {
    $query->where('is_private', false)
        ->orWhere(function($subQuery) use ($user) {
            $subQuery->where('is_private', true)
                    ->where('user_id', $user->id);
        });
})
```

## 3. Livewire Component Kuralları

### Zorunlu Properties
```php
public $patientNotes = [];
public $selectedPatientForNotes;
public $showNotesModal = false;
public $newNote = [
    'content' => '',
    'note_type' => 'general',
    'is_private' => false
];
public $editingNote = null;
```

### Validation Kuralları
```php
$this->validate([
    'newNote.content' => 'required|string|min:1|max:5000',
    'newNote.note_type' => 'required|in:general,medical,appointment,treatment',
    'newNote.is_private' => 'boolean'
]);
```

### Zorunlu Metodlar
```php
// Bu metodlar mutlaka bulunmalı
public function loadPatientNotes($patientId)
public function saveNote()
public function editNote($noteId)
public function deleteNote($noteId)
public function canEditNote($note)
public function canDeleteNote($note)
public function getNoteTypeIcon($type)
public function getNoteTypeText($type)
public function resetNoteForm()
```

## 4. Blade Template Kuralları

### Note Type Gösterimi
```blade
<!-- ✅ DOĞRU KULLANIM -->
<span>{{ $this->getNoteTypeText($note->note_type) }}</span>
<i class="{{ $this->getNoteTypeIcon($note->note_type) }}"></i>

<!-- ❌ YANLIŞ KULLANIM -->
<span>{{ ucfirst($note->note_type) }}</span>
<span>{{ $note->priority }}</span> <!-- YASAK -->
```

### Private Note Checkbox
```blade
<!-- ✅ DOĞRU KULLANIM - Sadece doktorlar için -->
@if(Auth::user()->role === 'doctor')
<div class="flex items-center">
    <input type="checkbox" wire:model="newNote.is_private" id="is_private">
    <label for="is_private">Özel not (sadece ben görebilirim)</label>
</div>
@endif
```

### Authorization Kontrolleri
```blade
<!-- ✅ Edit/Delete butonları için yetki kontrolü -->
@if($this->canEditNote($note))
    <button wire:click="editNote({{ $note->id }})">Düzenle</button>
@endif

@if($this->canDeleteNote($note))
    <button wire:click="deleteNote({{ $note->id }})">Sil</button>
@endif
```

## 5. Note Type Türkçeleştirme Kuralları

### Enum Değerleri ve Türkçe Karşılıkları
```php
'general' => 'Genel',
'medical' => 'Tıbbi',
'appointment' => 'Randevu',
'treatment' => 'Tedavi'
```

### Icon Mapping
```php
'medical' => 'fas fa-stethoscope',
'appointment' => 'fas fa-calendar',
'treatment' => 'fas fa-pills',
'general' => 'fas fa-sticky-note'
```

## 6. Hata Önleme Kuralları

### YASAKLI İşlemler
```php
// ❌ Bu alanları asla kullanma
$note->title = 'something'; // YASAK
$note->priority = 'high'; // YASAK

// ❌ Bu validasyonları asla ekleme
'newNote.title' => 'required', // YASAK
'newNote.priority' => 'required', // YASAK

// ❌ Bu metodları asla kullanma
$this->getPriorityColor($priority); // YASAK - Kaldırıldı
scopeHighPriority(); // YASAK - Kaldırıldı
```

### Zorunlu Kontroller
```php
// ✅ Her CRUD işleminde yetki kontrolü yap
if (!$this->canEditNote($note)) {
    throw new UnauthorizedException();
}

// ✅ Private not filtrelemesi her zaman aktif
$this->loadPatientNotes($patientId); // Otomatik filtreleme içerir

// ✅ Form reset işleminde tüm alanları temizle
public function resetNoteForm()
{
    $this->newNote = [
        'content' => '',
        'note_type' => 'general',
        'is_private' => false
    ];
    $this->editingNote = null;
}
```

## 7. Migration Güvenlik Kuralları

### Rollback Stratejisi
```bash
# Değişiklik öncesi her zaman rollback yap
php artisan migrate:rollback --step=5
php artisan migrate
```

### Enum Değişiklikleri
```php
// ✅ DOĞRU enum tanımı
$table->enum('note_type', ['general', 'medical', 'appointment', 'treatment'])
      ->default('general');

// ❌ YANLIŞ - Eski değerler
$table->enum('note_type', ['doctor', 'staff']); // YASAK
```

## 8. Test Kuralları

### Zorunlu Test Senaryoları
```php
// Role-based authorization testleri
test('doctor can only edit own notes');
test('nurse cannot edit doctor notes');
test('private notes only visible to owner');

// CRUD operation testleri
test('note creation with valid data');
test('note update with authorization');
test('note deletion with authorization');

// Validation testleri
test('note_type must be valid enum value');
test('content is required');
test('is_private is boolean');
```

## 9. Performans Kuralları

### Query Optimizasyonu
```php
// ✅ Her zaman eager loading kullan
PatientNote::with('user')->where('patient_id', $patientId)

// ✅ Index kullanımı
// patient_id, user_id, note_type, is_private alanları indexli

// ✅ Pagination kullan
$notes = PatientNote::paginate(10);
```

## 10. Güvenlik Kuralları

### Encryption
```php
// ✅ Hassas alanlar şifreli
protected $encryptable = ['content'];

// ✅ Mass assignment koruması
protected $fillable = [
    'patient_id', 'user_id', 'content', 
    'note_type', 'is_private'
];
```

### Input Sanitization
```php
// ✅ Her zaman validate et
'content' => 'required|string|min:1|max:5000',
'note_type' => 'required|in:general,medical,appointment,treatment'
```

## 11. Hata Ayıklama Kuralları

### Debug Bilgileri
```php
// ✅ Development ortamında log kullan
if (app()->environment('local')) {
    \Log::info('Note operation', ['user_id' => $user->id, 'note_id' => $note->id]);
}

// ❌ Production'da log kullanma (proje kuralları gereği)
```

### Error Handling
```php
try {
    $note->save();
    $this->dispatch('note-saved', ['message' => 'Not başarıyla kaydedildi']);
} catch (\Exception $e) {
    $this->dispatch('note-error', ['message' => 'Not kaydedilirken hata oluştu']);
}
```

## 12. Kod Kalitesi Kuralları

### Naming Conventions
```php
// ✅ Method isimleri açık ve net
canEditNote(), canDeleteNote(), loadPatientNotes()
getNoteTypeText(), getNoteTypeIcon()

// ✅ Variable isimleri anlamlı
$patientNotes, $selectedPatientForNotes, $editingNote
```

### Code Organization
```php
// ✅ Metodlar mantıklı sırada
// 1. Properties
// 2. Mount/Render methods
// 3. CRUD operations
// 4. Helper methods
// 5. Authorization methods
```

Bu kurallar, hasta not sisteminin tutarlı, güvenli ve hatasız çalışmasını sağlar. Her değişiklik öncesi bu kurallara uygunluk kontrol edilmelidir.