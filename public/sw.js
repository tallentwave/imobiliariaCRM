// Service Worker da Nova Imóveis — cache mínimo de assets estáticos + fallback offline.
// Nunca armazena em cache páginas HTML dinâmicas (evita CSRF/token expirados) nem
// requisições que não sejam GET.

const CACHE_VERSION = 'nova-imoveis-v1';
const STATIC_CACHE = `${CACHE_VERSION}-static`;
const OFFLINE_URL = '/offline.html';

const PRECACHE_URLS = [
    OFFLINE_URL,
    '/icons/icon-192.png',
    '/icons/icon-512.png',
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(STATIC_CACHE).then((cache) => cache.addAll(PRECACHE_URLS))
    );
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) => Promise.all(
            keys
                .filter((key) => key.startsWith('nova-imoveis-') && key !== STATIC_CACHE)
                .map((key) => caches.delete(key))
        ))
    );
    self.clients.claim();
});

self.addEventListener('fetch', (event) => {
    const { request } = event;

    if (request.method !== 'GET') {
        return;
    }

    const url = new URL(request.url);

    if (url.origin !== self.location.origin) {
        return;
    }

    // Assets compilados e ícones: cache-first (nomes com hash, seguro cachear por muito tempo)
    if (url.pathname.startsWith('/build/') || url.pathname.startsWith('/icons/')) {
        event.respondWith(
            caches.open(STATIC_CACHE).then((cache) =>
                cache.match(request).then((cached) => cached || fetch(request).then((response) => {
                    cache.put(request, response.clone());
                    return response;
                }))
            )
        );
        return;
    }

    // Navegação (páginas HTML): sempre busca da rede; se falhar (offline), mostra fallback.
    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request).catch(() => caches.match(OFFLINE_URL))
        );
    }
});
