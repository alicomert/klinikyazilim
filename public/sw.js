// KlinikGo Service Worker
const CACHE_NAME = 'klinikgo-v1.0.0';
const OFFLINE_URL = '/offline.html';

// Cache edilecek statik dosyalar
const STATIC_CACHE_URLS = [
  '/',
  '/dashboard',
  '/patients',
  '/appointments', 
  '/operations',
  '/doctor-panel',
  '/manifest.json',
  '/favicon.svg',
  '/favicon.ico',
  '/klinikgo.png',
  '/apple-touch-icon.png',
  OFFLINE_URL
];

// CSS ve JS dosyaları için dinamik cache
const DYNAMIC_CACHE_URLS = [
  '/build/',
  '/css/',
  '/js/'
];

// Service Worker kurulumu
self.addEventListener('install', event => {
  console.log('KlinikGo Service Worker: Installing...');
  
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        console.log('KlinikGo Service Worker: Caching static files');
        return cache.addAll(STATIC_CACHE_URLS);
      })
      .then(() => {
        console.log('KlinikGo Service Worker: Installation complete');
        return self.skipWaiting();
      })
      .catch(error => {
        console.error('KlinikGo Service Worker: Installation failed', error);
      })
  );
});

// Service Worker aktivasyonu
self.addEventListener('activate', event => {
  console.log('KlinikGo Service Worker: Activating...');
  
  event.waitUntil(
    caches.keys()
      .then(cacheNames => {
        return Promise.all(
          cacheNames.map(cacheName => {
            if (cacheName !== CACHE_NAME) {
              console.log('KlinikGo Service Worker: Deleting old cache', cacheName);
              return caches.delete(cacheName);
            }
          })
        );
      })
      .then(() => {
        console.log('KlinikGo Service Worker: Activation complete');
        return self.clients.claim();
      })
  );
});

// Fetch event handler - Network First with Cache Fallback
self.addEventListener('fetch', event => {
  const { request } = event;
  const url = new URL(request.url);
  
  // Sadece GET isteklerini handle et
  if (request.method !== 'GET') {
    return;
  }
  
  // API istekleri için Network First stratejisi
  if (url.pathname.startsWith('/api/') || url.pathname.startsWith('/livewire/')) {
    event.respondWith(
      fetch(request)
        .then(response => {
          // Başarılı response'u cache'le
          if (response.status === 200) {
            const responseClone = response.clone();
            caches.open(CACHE_NAME)
              .then(cache => {
                cache.put(request, responseClone);
              });
          }
          return response;
        })
        .catch(() => {
          // Network başarısız, cache'den dön
          return caches.match(request)
            .then(cachedResponse => {
              if (cachedResponse) {
                return cachedResponse;
              }
              // Cache'de de yoksa offline sayfasını göster
              return caches.match(OFFLINE_URL);
            });
        })
    );
    return;
  }
  
  // Statik dosyalar için Cache First stratejisi
  if (isStaticAsset(url.pathname)) {
    event.respondWith(
      caches.match(request)
        .then(cachedResponse => {
          if (cachedResponse) {
            return cachedResponse;
          }
          
          return fetch(request)
            .then(response => {
              if (response.status === 200) {
                const responseClone = response.clone();
                caches.open(CACHE_NAME)
                  .then(cache => {
                    cache.put(request, responseClone);
                  });
              }
              return response;
            })
            .catch(() => {
              return caches.match(OFFLINE_URL);
            });
        })
    );
    return;
  }
  
  // HTML sayfaları için Network First with Cache Fallback
  if (request.headers.get('accept').includes('text/html')) {
    event.respondWith(
      fetch(request)
        .then(response => {
          if (response.status === 200) {
            const responseClone = response.clone();
            caches.open(CACHE_NAME)
              .then(cache => {
                cache.put(request, responseClone);
              });
          }
          return response;
        })
        .catch(() => {
          return caches.match(request)
            .then(cachedResponse => {
              if (cachedResponse) {
                return cachedResponse;
              }
              return caches.match(OFFLINE_URL);
            });
        })
    );
  }
});

// Statik asset kontrolü
function isStaticAsset(pathname) {
  const staticExtensions = ['.css', '.js', '.png', '.jpg', '.jpeg', '.gif', '.svg', '.ico', '.woff', '.woff2', '.ttf', '.eot'];
  return staticExtensions.some(ext => pathname.endsWith(ext)) || 
         DYNAMIC_CACHE_URLS.some(path => pathname.startsWith(path));
}

// Background Sync için
self.addEventListener('sync', event => {
  console.log('KlinikGo Service Worker: Background sync triggered', event.tag);
  
  if (event.tag === 'background-sync') {
    event.waitUntil(
      // Offline sırasında yapılan işlemleri senkronize et
      syncOfflineData()
    );
  }
});

// Push notification handler
self.addEventListener('push', event => {
  console.log('KlinikGo Service Worker: Push notification received');
  
  const options = {
    body: event.data ? event.data.text() : 'KlinikGo bildirim',
    icon: '/klinikgo.png',
    badge: '/favicon.svg',
    vibrate: [100, 50, 100],
    data: {
      dateOfArrival: Date.now(),
      primaryKey: 1
    },
    actions: [
      {
        action: 'explore',
        title: 'Görüntüle',
        icon: '/favicon.svg'
      },
      {
        action: 'close',
        title: 'Kapat',
        icon: '/favicon.svg'
      }
    ]
  };
  
  event.waitUntil(
    self.registration.showNotification('KlinikGo', options)
  );
});

// Notification click handler
self.addEventListener('notificationclick', event => {
  console.log('KlinikGo Service Worker: Notification clicked');
  
  event.notification.close();
  
  if (event.action === 'explore') {
    event.waitUntil(
      clients.openWindow('/dashboard')
    );
  } else if (event.action === 'close') {
    // Notification kapatıldı
  } else {
    // Varsayılan action
    event.waitUntil(
      clients.openWindow('/')
    );
  }
});

// Offline data sync fonksiyonu
async function syncOfflineData() {
  try {
    // Offline sırasında kaydedilen verileri senkronize et
    const offlineData = await getOfflineData();
    
    if (offlineData && offlineData.length > 0) {
      for (const data of offlineData) {
        try {
          await fetch(data.url, {
            method: data.method,
            headers: data.headers,
            body: data.body
          });
          
          // Başarılı sync sonrası offline data'yı temizle
          await removeOfflineData(data.id);
        } catch (error) {
          console.error('KlinikGo Service Worker: Sync failed for', data.url, error);
        }
      }
    }
  } catch (error) {
    console.error('KlinikGo Service Worker: Background sync failed', error);
  }
}

// Offline data yönetimi
async function getOfflineData() {
  try {
    const cache = await caches.open('klinikgo-offline-data');
    const response = await cache.match('/offline-data');
    if (response) {
      return await response.json();
    }
    return [];
  } catch (error) {
    console.error('KlinikGo Service Worker: Failed to get offline data', error);
    return [];
  }
}

async function removeOfflineData(id) {
  try {
    const offlineData = await getOfflineData();
    const updatedData = offlineData.filter(item => item.id !== id);
    
    const cache = await caches.open('klinikgo-offline-data');
    await cache.put('/offline-data', new Response(JSON.stringify(updatedData)));
  } catch (error) {
    console.error('KlinikGo Service Worker: Failed to remove offline data', error);
  }
}

// Cache temizleme
self.addEventListener('message', event => {
  if (event.data && event.data.type === 'CLEAR_CACHE') {
    event.waitUntil(
      caches.keys().then(cacheNames => {
        return Promise.all(
          cacheNames.map(cacheName => {
            return caches.delete(cacheName);
          })
        );
      })
    );
  }
  
  if (event.data && event.data.type === 'SKIP_WAITING') {
    self.skipWaiting();
  }
});

console.log('KlinikGo Service Worker: Loaded successfully');