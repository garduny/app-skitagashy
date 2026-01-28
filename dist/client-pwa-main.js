let clientinstallEvent = null;
const clientinstallButton = document.getElementById("installapp");
const clienturl = clientinstallButton?.getAttribute("data-url");

if (clientinstallButton) {
  startPwa(true);

  if (localStorage["client-pwa-enabled"]) {
    startPwa();
  }

  clientinstallButton.addEventListener("click", () => {
    if (clientinstallEvent) clientinstallEvent.prompt();
  });
}

function startPwa(firstStart) {
  localStorage["client-pwa-enabled"] = true;

  window.addEventListener("load", () => {
    // Always use absolute path to avoid redirect error
    navigator.serviceWorker
      .register("./dist/client-pwa-sw.js")
      .then((registration) => {
        // console.log("Service Worker registered", registration);
      })
      .catch((err) => {
        console.error("Service Worker registration failed:", err);
      });
  });

  window.addEventListener("beforeinstallprompt", (e) => {
    e.preventDefault();
    clientinstallEvent = e;
    if (clientinstallButton) clientinstallButton.style.display = "block";
  });

  // Cache all links after short delay
  setTimeout(() => {
    caches.open("clientpwa").then((cache) => {
      const clientlinksFound = Array.from(document.querySelectorAll("a")).map(
        (a) => a.href
      );
      // cache.addAll(clientlinksFound); // Uncomment if you want to pre-cache all links
    });
  }, 500);
}
