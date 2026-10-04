import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import { resolve } from 'path';
import { VitePWA } from 'vite-plugin-pwa';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        VitePWA({
            buildBase: '/build/',
            scope: '/',
            base: '/build/',
            registerType: 'autoUpdate',
            outDir: 'public',
            injectRegister: false,
            manifest: {
                id: '/',
                name: 'SIPINJAM - Sistem Peminjaman Sarpras STITEK',
                short_name: 'SiPinjam',
                description: 'Sistem Peminjaman Ruangan dan Barang Kampus STITEK Bontang yang Modern dan Efisien.',
                theme_color: '#2563eb',
                background_color: '#ffffff',
                display: 'standalone',
                orientation: 'portrait-primary',
                start_url: '/',
                scope: '/',
                icons: [
                    {
                        src: '/pwa-192x192.png',
                        sizes: '192x192',
                        type: 'image/png',
                        purpose: 'any'
                    },
                    {
                        src: '/pwa-512x512.png',
                        sizes: '512x512',
                        type: 'image/png',
                        purpose: 'any'
                    },
                    {
                        src: '/pwa-maskable-512x512.png',
                        sizes: '512x512',
                        type: 'image/png',
                        purpose: 'maskable'
                    }
                ],
                shortcuts: [
                    {
                        name: 'Mulai Pinjam',
                        short_name: 'Pinjam',
                        description: 'Ajukan peminjaman ruangan atau barang',
                        url: '/dashboard',
                        icons: [{ src: '/pwa-192x192.png', sizes: '192x192' }]
                    },
                    {
                        name: 'Riwayat Peminjaman',
                        short_name: 'Riwayat',
                        description: 'Lihat status pengajuan peminjaman',
                        url: '/bookings',
                        icons: [{ src: '/pwa-192x192.png', sizes: '192x192' }]
                    },
                    {
                        name: 'Tata Tertib',
                        short_name: 'Aturan',
                        description: 'Baca regulasi dan sanksi peminjaman',
                        url: '/tata_tertib',
                        icons: [{ src: '/pwa-192x192.png', sizes: '192x192' }]
                    }
                ]
            },
            workbox: {
                globPatterns: ['**/*.{js,css,html,ico,png,svg,woff2,woff}'],
                navigateFallback: '/offline.html',
                navigateFallbackDenylist: [/^\/admin/, /^\/login/, /^\/logout/, /^\/api/],
                runtimeCaching: [
                    {
                        urlPattern: /^https:\/\/fonts\.googleapis\.com\/.*/i,
                        handler: 'CacheFirst',
                        options: {
                            cacheName: 'google-fonts-cache',
                            expiration: {
                                maxEntries: 10,
                                maxAgeSeconds: 60 * 60 * 24 * 365
                            },
                            cacheableResponse: {
                                statuses: [0, 200]
                            }
                        }
                    },
                    {
                        urlPattern: /\.(?:png|jpg|jpeg|svg|gif|webp)$/i,
                        handler: 'CacheFirst',
                        options: {
                            cacheName: 'images-cache',
                            expiration: {
                                maxEntries: 60,
                                maxAgeSeconds: 60 * 60 * 24 * 30
                            }
                        }
                    },
                    {
                        urlPattern: /\/tata_tertib$/i,
                        handler: 'StaleWhileRevalidate',
                        options: {
                            cacheName: 'tata-tertib-cache',
                            expiration: {
                                maxEntries: 5,
                                maxAgeSeconds: 60 * 60 * 24 * 7
                            }
                        }
                    },
                    {
                        urlPattern: /\/kalender$/i,
                        handler: 'StaleWhileRevalidate',
                        options: {
                            cacheName: 'kalender-cache',
                            expiration: {
                                maxEntries: 5,
                                maxAgeSeconds: 60 * 60 * 24 * 3
                            }
                        }
                    },
                    {
                        urlPattern: /\/(?:ruangan|barang)$/i,
                        handler: 'NetworkFirst',
                        options: {
                            cacheName: 'katalog-sarpras-cache',
                            networkTimeoutSeconds: 3,
                            expiration: {
                                maxEntries: 10,
                                maxAgeSeconds: 60 * 60 * 24 * 2
                            }
                        }
                    }
                ]
            }
        }),
    ],
    resolve: {
        alias: {
            '@': resolve(__dirname, 'resources/js'),
            '@images': resolve(__dirname, 'resources/images'),
        },
    },
    build: {
        chunkSizeWarningLimit: 800,
        rollupOptions: {
            output: {
                manualChunks(id) {
                    if (id.includes('node_modules')) {
                        if (id.includes('vue') || id.includes('@vue')) {
                            return 'vendor-core';
                        }
                        if (id.includes('@inertiajs')) {
                            return 'vendor-inertia';
                        }
                        if (id.includes('lucide') || id.includes('@lucide')) {
                            return 'vendor-icons';
                        }
                        return 'vendor-others';
                    }
                }
            }
        }
    }
});
