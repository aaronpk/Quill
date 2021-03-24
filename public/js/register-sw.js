(function registerSW() {
  if ("serviceWorker" in navigator) {
    navigator.serviceWorker.register("/js/sw.js").catch((e) => {
      console.log("Registration fail: ", e);
    });
  }
})();
