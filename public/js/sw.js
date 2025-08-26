// NOTE: This currently doesn't cache anything
// We are just installing it so that chrome users can be
// prompted to install Quill via add to homescreen
self.addEventListener("install", installWorker);

async function installWorker() {
  await self.skipWaiting();
}

self.addEventListener("activate", activateServiceWorker);

async function activateServiceWorker(event) {
  event.waitUntil(clients.claim()); // make the current sw the active sw in all pages
}
