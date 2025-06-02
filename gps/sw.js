// sw.js - Service Worker para GPX Navigator PWA (Versión Localhost)
const CACHE_NAME = 'gpx-navigator-v1.0.0';
const STATIC_CACHE_NAME = 'gpx-navigator-static-v1.0.0';
const DYNAMIC_CACHE_NAME = 'gpx-navigator-dynamic-v1.0.0';

// Recursos que se cachean inmediatamente al instalar
const STATIC_ASSETS = [
    './',
    './index.html',
    'https://unpkg.com/leaflet@1.7.1/dist/leaflet.css',
    'https://unpkg.com/leaflet@1.7.1/dist/leaflet.js'
];

// Recursos de mapas que se cachean dinámicamente
const MAP_TILE_PATTERNS = [
    /^https:\/\/[abc]\.tile\.openstreetmap\.org/,
    /^https:\/\/server\.arcgisonline\.com\/ArcGIS\/rest\/services\/World_Imagery/,
    /^https:\/\/[abc]\.tile\.opentopomap\.org/
];

// Rutas que requieren conexión (APIs externas)
const NETWORK_ONLY_PATTERNS = [
    /^https:\/\/router\.project-osrm\.org/,
    /nominatim\.openstreetmap\.org/
];

// Instalación del Service Worker
self.addEventListener('install', (event) => {
    console.log('🔧 Service Worker: Instalando...');
    
    event.waitUntil(
        caches.open(STATIC_CACHE_NAME).then((cache) => {
            console.log('📦 Service Worker: Cacheando recursos estáticos');
            return cache.addAll(STATIC_ASSETS).catch((error) => {
                console.error('Error cacheando recursos estáticos:', error);
                // Continuar aun si algunos recursos fallan
                return Promise.resolve();
            });
        }).then(() => {
            console.log('✅ Service Worker instalado correctamente');
            return self.skipWaiting();
        }).catch((error) => {
            console.error('Error en instalación de Service Worker:', error);
        })
    );
});

// Activación del Service Worker
self.addEventListener('activate', (event) => {
    console.log('✅ Service Worker: Activando...');
    
    event.waitUntil(
        Promise.all([
            // Limpiar caches antiguos
            caches.keys().then((cacheNames) => {
                return Promise.all(
                    cacheNames.map((cacheName) => {
                        if (cacheName !== STATIC_CACHE_NAME && 
                            cacheName !== DYNAMIC_CACHE_NAME &&
                            cacheName.startsWith('gpx-navigator-')) {
                            console.log('🗑️ Service Worker: Eliminando cache antiguo:', cacheName);
                            return caches.delete(cacheName);
                        }
                    })
                );
            }),
            
            // Tomar control inmediato
            self.clients.claim()
        ]).then(() => {
            console.log('✅ Service Worker activado y en control');
        }).catch((error) => {
            console.error('Error en activación de Service Worker:', error);
        })
    );
});

// Interceptar requests
self.addEventListener('fetch', (event) => {
    const request = event.request;
    const url = new URL(request.url);
    
    // Solo manejar requests GET
    if (request.method !== 'GET') {
        return;
    }
    
    // Ignorar chrome-extension y otras URLs especiales
    if (url.protocol === 'chrome-extension:' || 
        url.protocol === 'moz-extension:' || 
        url.protocol === 'safari-extension:' ||
        url.hostname === 'localhost' && url.pathname.includes('favicon.ico')) {
        return;
    }
    
    // Network-only para APIs externas críticas
    if (NETWORK_ONLY_PATTERNS.some(pattern => pattern.test(request.url))) {
        event.respondWith(
            fetch(request).catch((error) => {
                console.log('Network request failed:', request.url, error);
                // Fallback para routing offline
                if (request.url.includes('router.project-osrm.org')) {
                    return new Response(JSON.stringify({
                        error: 'Routing no disponible offline',
                        code: 'NoRouting'
                    }), {
                        headers: { 'Content-Type': 'application/json' },
                        status: 503
                    });
                }
                throw error;
            })
        );
        return;
    }
    
    // Cache First para recursos estáticos y tiles de mapa
    if (isStaticAsset(request.url) || isMapTile(request.url)) {
        event.respondWith(cacheFirstStrategy(request));
        return;
    }
    
    // Network First para todo lo demás
    event.respondWith(networkFirstStrategy(request));
});

// Estrategia Cache First (para recursos estáticos y tiles)
async function cacheFirstStrategy(request) {
    try {
        const cacheName = isStaticAsset(request.url) ? STATIC_CACHE_NAME : DYNAMIC_CACHE_NAME;
        const cache = await caches.open(cacheName);
        
        const cachedResponse = await cache.match(request);
        
        if (cachedResponse) {
            // Actualizar en background para tiles de mapa
            if (isMapTile(request.url)) {
                updateMapTileInBackground(request, cache);
            }
            return cachedResponse;
        }
        
        // Si no está en cache, fetchear y cachear
        const networkResponse = await fetch(request);
        
        if (networkResponse.ok) {
            // Solo cachear respuestas exitosas
            await cache.put(request, networkResponse.clone());
        }
        
        return networkResponse;
        
    } catch (error) {
        console.error('Error en Cache First:', error);
        
        // Fallback para tiles de mapa
        if (isMapTile(request.url)) {
            return createOfflineMapTile();
        }
        
        // Para otros recursos, intentar red directamente
        return fetch(request);
    }
}

// Estrategia Network First (para contenido dinámico)
async function networkFirstStrategy(request) {
    try {
        const networkResponse = await fetch(request);
        
        if (networkResponse.ok && networkResponse.status < 400) {
            const cache = await caches.open(DYNAMIC_CACHE_NAME);
            try {
                await cache.put(request, networkResponse.clone());
            } catch (error) {
                console.log('Error cacheando respuesta:', error);
                // Continuar sin cachear
            }
        }
        
        return networkResponse;
        
    } catch (error) {
        console.log('Network failed, intentando cache para:', request.url);
        
        const cache = await caches.open(DYNAMIC_CACHE_NAME);
        const cachedResponse = await cache.match(request);
        
        if (cachedResponse) {
            return cachedResponse;
        }
        
        // Fallback para la página principal
        if (request.mode === 'navigate') {
            const staticCache = await caches.open(STATIC_CACHE_NAME);
            const fallbackResponse = await staticCache.match('./index.html') || 
                                   await staticCache.match('./');
            if (fallbackResponse) {
                return fallbackResponse;
            }
        }
        
        throw error;
    }
}

// Actualizar tiles de mapa en background
async function updateMapTileInBackground(request, cache) {
    try {
        const networkResponse = await fetch(request);
        if (networkResponse.ok) {
            await cache.put(request, networkResponse.clone());
        }
    } catch (error) {
        // Silenciar errores de background updates
        console.log('Background update failed for:', request.url);
    }
}

// Crear tile offline placeholder
async function createOfflineMapTile() {
    try {
        // Crear un canvas simple para el tile offline
        const svg = `
            <svg width="256" height="256" xmlns="http://www.w3.org/2000/svg">
                <rect width="256" height="256" fill="#e0e0e0"/>
                <text x="128" y="120" text-anchor="middle" font-family="Arial" font-size="16" fill="#666">Sin conexión</text>
                <text x="128" y="140" text-anchor="middle" font-family="Arial" font-size="20" fill="#666">🚫</text>
            </svg>
        `;
        
        const blob = new Blob([svg], { type: 'image/svg+xml' });
        
        return new Response(blob, {
            headers: { 
                'Content-Type': 'image/svg+xml',
                'Cache-Control': 'no-cache'
            }
        });
    } catch (error) {
        console.error('Error creando tile offline:', error);
        return new Response('', { status: 503 });
    }
}

// Verificar si es un recurso estático
function isStaticAsset(url) {
    return STATIC_ASSETS.some(asset => url.includes(asset.replace('./', ''))) ||
           url.includes('leaflet') ||
           url.endsWith('.css') ||
           url.endsWith('.js') ||
           url.includes('cdnjs.cloudflare.com') ||
           url.includes('unpkg.com');
}

// Verificar si es un tile de mapa
function isMapTile(url) {
    return MAP_TILE_PATTERNS.some(pattern => pattern.test(url));
}

// Manejar mensajes desde la app principal
self.addEventListener('message', (event) => {
    if (!event.data) return;
    
    const { type, data } = event.data;
    
    switch (type) {
        case 'SKIP_WAITING':
            self.skipWaiting();
            break;
            
        case 'CACHE_GPX_ROUTE':
            cacheGPXRoute(data);
            break;
            
        case 'CLEAR_CACHE':
            clearCache(data?.cacheType);
            break;
            
        case 'GET_CACHE_SIZE':
            getCacheSize().then(size => {
                if (event.ports && event.ports[0]) {
                    event.ports[0].postMessage({ size });
                }
            });
            break;
    }
});

// Cachear datos de ruta GPX
async function cacheGPXRoute(routeData) {
    try {
        const cache = await caches.open(DYNAMIC_CACHE_NAME);
        const response = new Response(JSON.stringify(routeData), {
            headers: { 'Content-Type': 'application/json' }
        });
        
        await cache.put('./current-route', response);
        console.log('✅ Ruta GPX cacheada para uso offline');
        
    } catch (error) {
        console.error('Error cacheando ruta GPX:', error);
    }
}

// Limpiar cache específico
async function clearCache(cacheType = 'all') {
    try {
        const cacheNames = await caches.keys();
        
        const cachesToDelete = cacheNames.filter(name => {
            if (cacheType === 'all') return name.startsWith('gpx-navigator-');
            if (cacheType === 'dynamic') return name === DYNAMIC_CACHE_NAME;
            if (cacheType === 'static') return name === STATIC_CACHE_NAME;
            return false;
        });
        
        await Promise.all(
            cachesToDelete.map(cacheName => caches.delete(cacheName))
        );
        
        console.log('🗑️ Cache limpiado:', cachesToDelete);
        
    } catch (error) {
        console.error('Error limpiando cache:', error);
    }
}

// Obtener tamaño total del cache
async function getCacheSize() {
    try {
        const cacheNames = await caches.keys();
        let totalSize = 0;
        
        for (const cacheName of cacheNames) {
            if (cacheName.startsWith('gpx-navigator-')) {
                const cache = await caches.open(cacheName);
                const keys = await cache.keys();
                
                for (const request of keys) {
                    try {
                        const response = await cache.match(request);
                        if (response && response.headers.get('content-length')) {
                            totalSize += parseInt(response.headers.get('content-length'));
                        }
                    } catch (error) {
                        // Ignorar errores individuales
                        continue;
                    }
                }
            }
        }
        
        return totalSize;
        
    } catch (error) {
        console.error('Error calculando tamaño de cache:', error);
        return 0;
    }
}

// Manejo de errores globales
self.addEventListener('error', (event) => {
    console.error('Service Worker Error:', event.error);
});

self.addEventListener('unhandledrejection', (event) => {
    console.log('Service Worker Unhandled Promise Rejection:', event.reason);
    // No mostrar como error crítico, solo log
    event.preventDefault();
});

console.log('🚀 Service Worker GPX Navigator cargado correctamente');