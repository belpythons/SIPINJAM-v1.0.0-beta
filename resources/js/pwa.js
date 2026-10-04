import { ref } from 'vue';
import { Workbox } from 'workbox-window';

export const isPwaInstallable = ref(false);
export const isAppInstalled = ref(false);
export const deferredPrompt = ref(null);
export const needRefresh = ref(false);
export const updateServiceWorker = ref(null);

// Detect if already in standalone PWA mode
if (typeof window !== 'undefined') {
  if (window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true) {
    isAppInstalled.value = true;
  }

  // Listen for browser beforeinstallprompt event
  window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredPrompt.value = e;
    isPwaInstallable.value = true;
  });

  // Listen for appinstalled event
  window.addEventListener('appinstalled', () => {
    deferredPrompt.value = null;
    isPwaInstallable.value = false;
    isAppInstalled.value = true;
    console.log('[SIPINJAM PWA] Aplikasi berhasil dipasang.');
  });
}

// Function to trigger native browser install prompt
export async function promptPwaInstall() {
  if (!deferredPrompt.value) return false;
  
  deferredPrompt.value.prompt();
  const choiceResult = await deferredPrompt.value.userChoice;
  
  if (choiceResult.outcome === 'accepted') {
    console.log('[SIPINJAM PWA] Pengguna menyetujui instalasi.');
    isPwaInstallable.value = false;
    deferredPrompt.value = null;
    return true;
  } else {
    console.log('[SIPINJAM PWA] Pengguna membatalkan instalasi.');
    return false;
  }
}

// Register Service Worker
export function registerPwaServiceWorker() {
  if (typeof window === 'undefined' || !('serviceWorker' in navigator)) return;

  const swUrl = '/sw.js';
  const wb = new Workbox(swUrl);

  wb.addEventListener('waiting', () => {
    needRefresh.value = true;
  });

  updateServiceWorker.value = async () => {
    wb.addEventListener('controlling', () => {
      window.location.reload();
    });
    await wb.messageSkipWaiting();
  };

  wb.register().catch((err) => {
    console.warn('[SIPINJAM PWA] Service Worker registration failed:', err);
  });
}
