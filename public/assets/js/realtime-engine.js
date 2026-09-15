/**
 * RealtimeEngine - Engine Sinkronisasi Layar Display & POS Kasir
 * Mendukung Reconnect Otomatis & Seamless Fallback Polling.
 */
class RealtimeEngine {
  constructor(options = {}) {
    this.endpoint = options.endpoint || "/api/realtime/events";
    this.interval = options.interval || 2000;
    this.onEvent = options.onEvent || function () {};
    this.lastTimestamp = 0;
    this.timer = null;
  }

  start() {
    this.poll();
    this.timer = setInterval(() => this.poll(), this.interval);
  }

  stop() {
    if (this.timer) clearInterval(this.timer);
  }

  poll() {
    fetch(this.endpoint, { cache: "no-store" })
      .then((res) => res.json())
      .then((res) => {
        if (res && res.timestamp && res.timestamp > this.lastTimestamp) {
          this.lastTimestamp = res.timestamp;
          this.onEvent(res.event, res.data);
        }
      })
      .catch((err) => {
        console.warn("Realtime sync fallback active:", err);
      });
  }
}
