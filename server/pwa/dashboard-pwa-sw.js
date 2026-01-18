const OFFLINE_URL = "./offline.html";
const CACHE_NAME = "dashboardpwa";

self.addEventListener("install", event => {
  event.waitUntil(
    caches.open(CACHE_NAME).then(cache => cache.add(OFFLINE_URL))
  );
  self.skipWaiting();
});

self.addEventListener("fetch", event => {
  event.respondWith(
    fetch(event.request).catch(() =>
      caches.match(event.request).then(res => res || caches.match(OFFLINE_URL))
    )
  );
});

self.addEventListener("activate", event => {
  event.waitUntil(clients.claim());
});
