<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SiPinjam Admin</title>
    <link rel="icon" type="image/png" href="{{ asset('image/logo-sp.png') }}">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        :root {
            --bs-brand: #ea580c;
            --bs-brand-dark: #c2410c;
            --bs-brand-light: #f97316;
            --bs-gray-50: #f8f9fa;
        }

        body {
            background-color: var(--bs-gray-50);
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }

        /* Teks & Background Custom */
        .text-brand { color: var(--bs-brand) !important; }
        .bg-brand { background-color: var(--bs-brand) !important; }

        /* Sidebar & Layouting */
        .sidebar-wrapper {
            width: 260px;
            /* Modifikasi: Gradasi dari gelap ke terang */
            background: linear-gradient(180deg, var(--bs-brand-dark) 0%, var(--bs-brand-light) 100%);
            z-index: 1040;
        }
        
        .main-wrapper {
            margin-left: 260px;
            height: 100vh;
        }

        @media (max-width: 991.98px) {
            .main-wrapper { margin-left: 0; }
        }

        /* Navigasi Sidebar */
        .nav-link-custom {
            color: rgba(255, 255, 255, 0.9);
            padding: 0.8rem 1.25rem;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            transition: all 0.2s ease-in-out;
        }
        .nav-link-custom:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: #fff;
        }
        .nav-link-custom.active {
            background-color: rgba(255, 255, 255, 0.2);
            color: #fff;
            font-weight: 500;
        }
        .nav-icon { width: 24px; text-align: center; font-size: 1.1rem; }

        /* Banner Gradasi */
        .banner-gradient {
            background: linear-gradient(to right, var(--bs-brand), var(--bs-brand-light));
        }

        /* Utilities */
        .rounded-xl { border-radius: 1rem; }
        .rounded-2xl { border-radius: 1.25rem; }
        
        .icon-circle {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        /* Warna Custom untuk Card Icons */
        .bg-blue-50 { background-color: #eff6ff; color: #3b82f6; }
        .bg-orange-50 { background-color: #fff7ed; color: #f97316; }
        .bg-yellow-50 { background-color: #fefce8; color: #eab308; }
        .bg-green-50 { background-color: #f0fdf4; color: #22c55e; }
        
        /* Aksi Cepat hover effect */
        .btn-quick-action {
            border: 1px solid #dee2e6;
            color: #6c757d;
            transition: all 0.2s;
        }
        .btn-quick-action:hover {
            border-color: var(--bs-brand);
            color: var(--bs-brand);
            background-color: #fff;
        }

        .scrollable-content {
            overflow-y: auto;
            height: calc(100vh - 70px); /* Adjust based on header height */
        }
    </style>
</head>
<body class="overflow-hidden">

    @include('admin.partials.sidebar', ['activePage' => 'dashboard'])

    <!-- Main Content -->
    <main class="main-wrapper d-flex flex-column">

        @include('admin.partials.navbar')

        <!-- Scrollable Content -->
        <div class="scrollable-content p-4 p-md-5 w-100 flex-grow-1">
            
            <!-- Welcome Banner -->
            <div class="banner-gradient rounded-2xl p-4 p-md-5 mb-4 text-white shadow d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <h2 class="fw-bold mb-2 d-flex align-items-center gap-2">
                        <i class="fas fa-sparkles"></i> Selamat Malam, Admin SIPINJAM!
                    </h2>
                    <p class="text-white-50 mb-3">{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, DD MMMM YYYY') }}</p>
                    <p class="mb-0 text-light">Kelola sistem peminjaman dengan efisien. 3 peminjaman menunggu persetujuan Anda.</p>
                </div>
                <div>
                    <a href="{{ route('admin.kelola_peminjaman') }}" class="btn btn-light text-brand fw-medium shadow-sm d-inline-flex align-items-center gap-2">
                        <i class="far fa-calendar-alt"></i> Kelola Peminjaman
                    </a>
                </div>
            </div>

            <!-- Stat Cards -->
            <div class="row g-4 mb-4">
                <!-- Card 1 -->
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="card border-0 shadow-sm rounded-xl h-100">
                        <div class="card-body p-4 d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted small mb-1">Total User</p>
                                <h3 class="fw-bold m-0 text-dark">6</h3>
                            </div>
                            <div class="icon-circle bg-blue-50">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Card 2 -->
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="card border-0 shadow-sm rounded-xl h-100">
                        <div class="card-body p-4 d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted small mb-1">Total Peminjaman</p>
                                <h3 class="fw-bold m-0 text-dark">6</h3>
                            </div>
                            <div class="icon-circle bg-orange-50">
                                <i class="far fa-calendar-check"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Card 3 -->
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="card border-0 shadow-sm rounded-xl h-100">
                        <div class="card-body p-4 d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted small mb-1">Menunggu</p>
                                <h3 class="fw-bold m-0 text-brand">3</h3>
                            </div>
                            <div class="icon-circle bg-yellow-50">
                                <i class="far fa-clock"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Card 4 -->
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="card border-0 shadow-sm rounded-xl h-100">
                        <div class="card-body p-4 d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted small mb-1">Disetujui</p>
                                <h3 class="fw-bold m-0 text-success">1</h3>
                            </div>
                            <div class="icon-circle bg-green-50">
                                <i class="far fa-check-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="row g-4 mb-4">
                <!-- Line Chart -->
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded-2xl p-4 h-100">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h5 class="fw-bold text-dark m-0">Tren Peminjaman</h5>
                                <p class="text-muted small m-0">6 bulan terakhir</p>
                            </div>
                            <i class="fas fa-arrow-trend-up text-brand fs-5"></i>
                        </div>
                        <div class="position-relative" style="height: 250px;">
                            <canvas id="trendChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Doughnut Chart -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm rounded-2xl p-4 h-100">
                        <h5 class="fw-bold text-dark mb-4">Status Peminjaman</h5>
                        <div class="position-relative d-flex justify-content-center" style="height: 180px;">
                            <canvas id="statusChart"></canvas>
                        </div>
                        <!-- Custom Legend -->
                        <div class="mt-4 d-flex flex-column gap-2">
                            <div class="d-flex justify-content-between align-items-center small">
                                <div class="d-flex align-items-center gap-2"><span class="rounded-circle bg-brand" style="width:12px; height:12px;"></span> Pending</div>
                                <span class="fw-bold">3</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center small">
                                <div class="d-flex align-items-center gap-2"><span class="rounded-circle" style="background-color: #f97316; width:12px; height:12px;"></span> Disetujui</div>
                                <span class="fw-bold">1</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center small">
                                <div class="d-flex align-items-center gap-2"><span class="rounded-circle" style="background-color: #fdba74; width:12px; height:12px;"></span> Ditolak</div>
                                <span class="fw-bold">1</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center small">
                                <div class="d-flex align-items-center gap-2"><span class="rounded-circle" style="background-color: #fed7aa; width:12px; height:12px;"></span> Selesai</div>
                                <span class="fw-bold">1</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Section -->
            <div class="row g-4 pb-4">
                <!-- Aksi Cepat -->
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm rounded-2xl p-4 h-100">
                        <h5 class="fw-bold text-dark mb-4">Aksi Cepat</h5>
                        <div class="row g-3">
                            <div class="col-6">
                                <a href="{{ route('admin.kelola_user') }}" class="btn btn-quick-action w-100 py-4 rounded-xl d-flex flex-column align-items-center gap-2 bg-transparent text-decoration-none">
                                    <i class="fas fa-user-plus text-brand fs-4"></i>
                                    <span class="fw-medium">Kelola User</span>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('admin.kelola_ruangan') }}" class="btn btn-quick-action w-100 py-4 rounded-xl d-flex flex-column align-items-center gap-2 bg-transparent text-decoration-none">
                                    <i class="fas fa-door-open text-primary fs-4"></i>
                                    <span class="fw-medium">Kelola Ruangan</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sumber Daya -->
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm rounded-2xl p-4 h-100">
                        <h5 class="fw-bold text-dark mb-4">Sumber Daya</h5>
                        <div class="d-flex flex-column gap-3">
                            <div class="d-flex align-items-center justify-content-between p-3 rounded-xl border" style="background-color: #f0f7ff; border-color: #cce3fd !important;">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="icon-circle bg-white text-primary shadow-sm rounded-3" style="width: 40px; height: 40px;">
                                        <i class="fas fa-door-closed"></i>
                                    </div>
                                    <div>
                                        <h6 class="m-0 fw-bold text-dark">Ruangan</h6>
                                        <p class="m-0 small text-muted">3 / 5 tersedia</p>
                                    </div>
                                </div>
                                <i class="fas fa-chevron-right text-muted"></i>
                            </div>
                            <div class="d-flex align-items-center justify-content-between p-3 rounded-xl border" style="background-color: #f0fdf4; border-color: #dcfce7 !important;">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="icon-circle bg-white text-success shadow-sm rounded-3" style="width: 40px; height: 40px;">
                                        <i class="fas fa-box"></i>
                                    </div>
                                    <div>
                                        <h6 class="m-0 fw-bold text-dark">Barang</h6>
                                        <p class="m-0 small text-muted">5 / 5 tersedia</p>
                                    </div>
                                </div>
                                <i class="fas fa-chevron-right text-muted"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <footer class="pt-3 pb-2 border-top d-flex flex-column flex-sm-row justify-content-between align-items-center small text-muted">
                <p class="mb-2 mb-sm-0">&copy; 2026 SIPINJAM - Sistem Informasi Peminjaman Ruangan dan Barang</p>
                <p class="mb-0">Built with Laravel 11 & Bootstrap 5</p>
            </footer>

        </div>
    </main>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Chart Configuration Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Konfigurasi Area Chart (Tren Peminjaman)
            const ctxTrend = document.getElementById('trendChart').getContext('2d');
            
            // Membuat gradient warna untuk line chart
            let gradientFill = ctxTrend.createLinearGradient(0, 0, 0, 300);
            gradientFill.addColorStop(0, 'rgba(234, 88, 12, 0.2)'); // Brand light transparent
            gradientFill.addColorStop(1, 'rgba(234, 88, 12, 0)');

            new Chart(ctxTrend, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
                    datasets: [{
                        label: 'Total Peminjaman',
                        data: [45, 52, 48, 62, 55, 68],
                        borderColor: '#ea580c', // brand color
                        backgroundColor: gradientFill,
                        borderWidth: 2,
                        pointRadius: 0,
                        pointHoverRadius: 4,
                        fill: true,
                        tension: 0.4 // Membuat garis melengkung (smooth)
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { 
                            beginAtZero: true, 
                            max: 80,
                            grid: { borderDash: [4, 4], color: '#f3f4f6' },
                            border: { display: false }
                        },
                        x: { 
                            grid: { display: false },
                            border: { display: true, color: '#e5e7eb' }
                        }
                    }
                }
            });

            // Konfigurasi Doughnut Chart (Status Peminjaman)
            const ctxStatus = document.getElementById('statusChart').getContext('2d');
            new Chart(ctxStatus, {
                type: 'doughnut',
                data: {
                    labels: ['Pending', 'Disetujui', 'Ditolak', 'Selesai'],
                    datasets: [{
                        data: [50, 16.6, 16.6, 16.6], // Representasi visual proporsional
                        backgroundColor: [
                            '#ea580c', // brand
                            '#f97316', // brand-light
                            '#fdba74', // orange-300
                            '#fed7aa'  // orange-200
                        ],
                        borderWidth: 4,
                        borderColor: '#ffffff',
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '75%', // Ketebalan cincin
                    plugins: { legend: { display: false } } // Legend di-handle secara kustom di HTML
                }
            });
        });
    </script>
</body>
</html>