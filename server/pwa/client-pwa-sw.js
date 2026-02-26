self.addEventListener("install", (event) => {
  // console.log("Service Worker installed");

  event.waitUntil(
    caches.open("clientpwa").then((cache) => {
      console.log("Cache opened:", cache);
      // Pre-cache assets here if needed
      // return cache.addAll(["/"]);
    }),
    caches.open("offline-only").then(cache => cache.add(OFFLINE_URL))
  );
  // Activate immediately without waiting
  self.skipWaiting();
});

self.addEventListener("fetch", (event) => {
  event.respondWith(
    fetch(event.request).catch(() => caches.match(OFFLINE_URL)),
    caches.open("clientpwa").then((cache) => {
      return cache.match(event.request).then((response) => {
        if (response) {
          return response; // Serve from cache
        }
        return fetch(event.request).then((networkResponse) => {
          // Optionally cache the new resource:
          // cache.put(event.request, networkResponse.clone());
          return networkResponse;
        });
      });
    })
  );
});

self.addEventListener("activate", (event) => {
  // Cleanup old caches if needed
  event.waitUntil(clients.claim());
});
