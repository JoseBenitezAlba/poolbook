// Service worker mínimo: PoolBook necesita servidor y base de datos,
// así que no guarda nada en caché. Todo va directo a la red.
self.addEventListener('install', () => self.skipWaiting());
self.addEventListener('activate', (event) => event.waitUntil(self.clients.claim()));
self.addEventListener('fetch', () => {});
