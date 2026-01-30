// Kalau kamu update file CSS/HTML, ubah: const CACHE_NAME = 'sakurupiah-v3'; // dari v2 → v10 dll
const CACHE_NAME = 'sakurupiah-v7';
const CACHE_ASSETS = [
  '/',
  'https://sakurupiah.my.id/index.php',
  'https://sakurupiah.my.id/offline.html',
  'https://sakurupiah.my.id/assets/css/sakurupiah.css',
  'https://sakurupiah.my.id/assets/img/web/logo-dompet-sakurupiah512x512.png',
  'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
  'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css',
  'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css',
  'https://cdn.jsdelivr.net/npm/sweetalert2@11'
];

// Install: cache aset statis
self.addEventListener('install', event => {
  console.log('[ServiceWorker] Installing...');
  event.waitUntil(
    caches.open(CACHE_NAME).then(cache => {
      console.log('[ServiceWorker] Caching static files...');
      return cache.addAll(CACHE_ASSETS).catch(err => {
        console.error('[ServiceWorker] Cache error:', err);
      });
    })
  );
});

// Activate: hapus cache lama jika ada
self.addEventListener('activate', event => {
  console.log('[ServiceWorker] Activating...');
  event.waitUntil(
    caches.keys().then(keys => {
      return Promise.all(
        keys.map(key => {
          if (key !== CACHE_NAME) {
            console.log('[ServiceWorker] Deleting old cache:', key);
            return caches.delete(key);
          }
        })
      );
    })
  );
});

// Fetch: cache-first, lalu network. Cache halaman order/ dinamis
self.addEventListener('fetch', event => {
  const request = event.request;
  const url = new URL(request.url);

  // Hanya untuk domain sendiri
  if (url.origin === location.origin) {
    event.respondWith(
      caches.match(request).then(cachedResponse => {
        if (cachedResponse) {
          return cachedResponse;
        }

        return fetch(request).then(networkResponse => {
          // Cache halaman dalam /order/ saat diakses
          if (request.url.includes('/order/')) {
            return caches.open(CACHE_NAME).then(cache => {
              cache.put(request, networkResponse.clone());
              return networkResponse;
            });
          } else {
            return networkResponse;
          }
        }).catch(() => {
          // Fallback jika offline dan tidak ada cache
          return caches.match('/offline.html');
        });
      })
    );
  }
});
