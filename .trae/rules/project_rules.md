# Klinik Yazılım Proje Kuralları

## 1. Framework Versiyonu ve Bağımlılıklar

### Laravel 12 Gereksinimleri
- **Laravel Framework**: ^12.0 (Şubat 2025 sürümü)
- **PHP Minimum Versiyonu**: 8.2 - 8.4
- **Composer Bağımlılıkları**:
  - `laravel/framework: ^12.0`
  - `phpunit/phpunit: ^11.0`
  - `pestphp/pest: ^3.0`

### Frontend Teknolojileri
- **CSS Framework**: Tailwind CSS (en son stabil versiyon)
- **JavaScript Framework**: Alpine.js v3.14.9 (CDN üzerinden)
- **Alpine.js CDN**: `https://cdn.jsdelivr.net/npm/alpinejs@3.14.9/dist/cdn.min.js`
- **Tailwind CSS**: PostCSS ile entegre edilmiş olarak kullanılacak

### Starter Kit Seçimi
- Laravel 12'nin yeni Livewire starter kit'i kullanılacak
- Flux UI component library ile Tailwind entegrasyonu
- Laravel Volt desteği aktif olacak

## 2. Test Framework Detayları

### Test Araçları
- **Unit Testing**: PHPUnit 11.0+
- **Feature Testing**: Laravel'in built-in test suite
- **Browser Testing**: Laravel Dusk (gerektiğinde)
- **API Testing**: Pest 3.0+ ile API endpoint testleri

### Test Standartları
- Her yeni özellik için mutlaka test yazılacak
- Test coverage minimum %80 olacak
- Feature testleri database transactions kullanacak
- Mock'lar sadece external API'lar için kullanılacak

## 3. Kaçınılması Gereken API'lar ve Uygulamalar

### Yasaklı Laravel Özellikleri
- **Log Sistemi**: Hiçbir şekilde log kaydı yapılmayacak
  - `Log::info()`, `Log::error()`, `Log::debug()` kullanımı yasak
  - `storage/logs` klasörü boş kalacak
  - `.env` dosyasında `LOG_CHANNEL=null` olacak

### Yasaklı Dosya Oluşturma
- **README Dosyaları**: Hiçbir özellik için README.md oluşturulmayacak
- **Dokümantasyon Dosyaları**: .md uzantılı dosyalar yasaklı
- **CHANGELOG**: Versiyon geçmişi dosyaları oluşturulmayacak

### Güvenlik ve Performance
- **Debug Modu**: Production'da mutlaka kapalı olacak
- **Query Logging**: Database query logları devre dışı
- **Session Logging**: Session aktiviteleri loglanmayacak
- **Error Reporting**: Sadece geliştirme ortamında aktif

## 4. Kod Kalitesi ve Hata Azaltma Kuralları

### Laravel 12 Best Practices
- **Dependency Injection**: PHP 8+ property promotion kullanılacak
- **Query Builder**: Laravel 12'nin yeni nested query metodları kullanılacak
- **Caching**: Asynchronous caching mechanisms tercih edilecek
- **UUIDs**: UUIDv7 (ordered UUIDs) kullanılacak

### Alpine.js Entegrasyonu
- **CDN Kullanımı**: `<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.9/dist/cdn.min.js"></script>`
- **x-data Direktifi**: Her Alpine component'i için zorunlu
- **Defer Attribute**: Script tag'inde mutlaka defer kullanılacak
- **Global Alpine**: `window.Alpine` erişimi için ayarlanacak

### Tailwind CSS Optimizasyonu
- **JIT Mode**: Just-In-Time compilation aktif olacak
- **Purge CSS**: Kullanılmayan CSS'ler otomatik temizlenecek
- **Component Classes**: @apply direktifi ile custom component'ler
- **Responsive Design**: Mobile-first yaklaşım benimsenecek

## 5. Sistem Anlayışı ve Güncellemeler

### Otomatik Sistem Tanıma
- **Migration Patterns**: Consistent naming convention
- **Model Relationships**: Explicit relationship tanımları
- **Controller Structure**: Resource controller pattern
- **Route Organization**: API ve web route'ları ayrı dosyalarda

### Yeni Özellik Ekleme Protokolü
1. **Feature Branch**: Her yeni özellik için ayrı branch
2. **Test-Driven Development**: Önce test, sonra implementation
3. **Code Review**: Peer review zorunlu
4. **Performance Check**: Her özellik performance impact analizi

### Güncelleme Stratejisi
- **Semantic Versioning**: Laravel'in version constraint'leri takip edilecek
- **Dependency Updates**: Haftalık bağımlılık kontrolü
- **Security Patches**: Güvenlik güncellemeleri öncelikli
- **Breaking Changes**: Major version güncellemelerinde dikkatli analiz

## 6. Geliştirme Ortamı Kuralları

### Zorunlu Konfigürasyonlar
```env
LOG_CHANNEL=null
LOG_LEVEL=emergency
DB_CONNECTION=mysql
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

### Yasaklı Middleware
- Logging middleware'leri kullanılmayacak
- Debug bar production'da kapalı olacak
- Telescope sadece local environment'ta aktif

### Performance Optimizasyonları
- **OPcache**: PHP OPcache aktif olacak
- **Redis**: Caching ve session için Redis kullanılacak
- **Queue Workers**: Background job'lar için queue system
- **Database Indexing**: Kritik query'ler için index optimizasyonu

Bu kurallar, Laravel 12 ile kusursuz, hızlı ve güvenli bir klinik yönetim sistemi geliştirmek için tasarlanmıştır.