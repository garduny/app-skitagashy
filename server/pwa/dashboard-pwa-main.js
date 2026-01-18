let dashboardinstallEvent = null;
const dashboardinstallButton = document.getElementById("installapp");
const dashboardurl = dashboardinstallButton?.getAttribute("data-url");

if (dashboardinstallButton) {
  startPwa(true);

  if (localStorage["dashboard-pwa-enabled"]) {
    startPwa();
  }

  dashboardinstallButton.addEventListener("click", () => {
    if (dashboardinstallEvent) dashboardinstallEvent.prompt();
  });
}

function startPwa(firstStart) {
  localStorage["dashboard-pwa-enabled"] = true;

  window.addEventListener("load", () => {
    // Always use absolute path from root to avoid redirect error
    navigator.serviceWorker
      .register("../dist/dashboard-pwa-sw.js")
      .then((registration) => {
        // console.log("Service Worker registered", registration);
      })
      .catch((err) => {
        console.error("Service Worker registration failed:", err);
      });
  });

  window.addEventListener("beforeinstallprompt", (e) => {
    e.preventDefault();
    dashboardinstallEvent = e;
    if (dashboardinstallButton) dashboardinstallButton.style.display = "block";
  });

  // Cache all links after short delay
  setTimeout(() => {
    caches.open("dashboardpwa").then((cache) => {
      const dashboardlinksFound = Array.from(
        document.querySelectorAll("a")
      ).map((a) => a.href);
      // cache.addAll(dashboardlinksFound); // Uncomment if you want to pre-cache all links
    });
  }, 500);
}
