<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tata Tertib & Kalender - SIPINJAM</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    animation: {
                        'fade-in': 'fadeIn 0.4s ease forwards',
                        'slide-up': 'slideUp 0.5s ease forwards',
                    },
                    keyframes: {
                        fadeIn: { '0%': { opacity: '0' }, '100%': { opacity: '1' } },
                        slideUp: { '0%': { transform: 'translateY(20px)', opacity: '0' }, '100%': { transform: 'translateY(0)', opacity: '1' } },
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 text-gray-900 font-sans antialiased">

<div class="flex h-screen overflow-hidden">

    @include('user.partials.sidebar', ['activePage' => 'tata_tertib'])

    {{-- MAIN CONTENT --}}
    <main class="flex-1 flex flex-col h-screen overflow-y-auto lg:ml-64 bg-gray-50 relative">

        @include('user.partials.navbar')

        <div class="p-4 sm:p-6 lg:p-8 flex-1 animate-fade-in max-w-7xl mx-auto w-full">

            {{-- Header Section --}}
            <div class="mb-6 animate-slide-up">
                <h1 class="text-3xl font-bold text-gray-900">Tata Tertib & Kalender</h1>
                <p class="text-sm text-gray-500 mt-1">Peraturan peminjaman dan kalender akademik kampus</p>
            </div>

            {{-- Tab Switcher --}}
            <div class="bg-gray-200/50 backdrop-blur-sm p-1.5 rounded-2xl flex w-fit gap-1 mb-8 animate-slide-up" style="animation-delay: 0.1s;">
                <button id="tab-btn-peraturan" onclick="switchTab('peraturan')" class="flex items-center gap-2 px-8 py-2.5 rounded-xl text-sm font-medium transition-all bg-white text-gray-900 shadow-sm">
                    <i data-lucide="book-open" class="w-4 h-4"></i> Peraturan
                </button>
                <button id="tab-btn-kalender" onclick="switchTab('kalender')" class="flex items-center gap-2 px-8 py-2.5 rounded-xl text-sm font-medium transition-all text-gray-500 hover:text-gray-700">
                    <i data-lucide="calendar" class="w-4 h-4"></i> Kalender
                </button>
            </div>

            {{-- ================= TAB 1: PERATURAN ================= --}}
            <div id="content-peraturan" class="space-y-4 animate-fade-in">
                 <div class="bg-white border border-gray-100 shadow-sm rounded-xl p-5 flex gap-4 mb-6">
                    <div class="mt-0.5"><i data-lucide="book-open" class="w-5 h-5 text-gray-600"></i></div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">Informasi Penting</h3>
                        <p class="text-sm text-gray-500 mt-1">Harap membaca dan memahami semua peraturan sebelum melakukan peminjaman. Pelanggaran akan dikenakan sanksi sesuai ketentuan yang berlaku.</p>
                    </div>
                </div>
                <h2 class="text-xl font-bold text-gray-900 mb-4">Peraturan Peminjaman</h2>
                
                @php
                    $rules = [
                        ['id' => 1, 'icon' => 'clock', 'title' => 'Waktu Peminjaman', 'desc' => 'Peminjaman ruangan dan barang harus dilakukan minimal 3 hari sebelum tanggal penggunaan. Peminjaman maksimal 14 hari ke depan.'],
                        ['id' => 2, 'icon' => 'user', 'title' => 'Syarat Peminjaman', 'desc' => 'Peminjam harus mahasiswa/staff aktif dengan KTM/ID Card yang masih berlaku. Satu peminjam maksimal 2 item aktif bersamaan.'],
                        ['id' => 3, 'icon' => 'info', 'title' => 'Persetujuan Admin', 'desc' => 'Semua peminjaman memerlukan persetujuan admin. Notifikasi persetujuan akan dikirim via email maksimal 2x24 jam.'],
                        ['id' => 4, 'icon' => 'shield', 'title' => 'Tanggung Jawab Peminjam', 'desc' => 'Peminjam bertanggung jawab penuh atas kondisi barang/ruangan. Kerusakan atau kehilangan akan dikenakan sanksi sesuai ketentuan.'],
                        ['id' => 5, 'icon' => 'x-circle', 'title' => 'Pembatalan Peminjaman', 'desc' => 'Pembatalan dapat dilakukan maksimal 1 hari sebelum waktu peminjaman. Pembatalan mendadak tanpa alasan akan dicatat dalam sistem.'],
                        ['id' => 6, 'icon' => 'alert-triangle', 'title' => 'Sanksi Pelanggaran', 'desc' => 'Pelanggaran akan dikenakan sanksi berupa: (1) Teguran tertulis, (2) Suspend akun 1-3 bulan, (3) Blacklist permanen untuk pelanggaran berat.']
                    ];
                @endphp

                @foreach($rules as $rule)
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-start gap-5">
                    <div class="bg-blue-50 p-3 rounded-xl flex-shrink-0">
                        <i data-lucide="{{ $rule['icon'] }}" class="w-6 h-6 text-blue-600"></i>
                    </div>
                    <div>
                        <h3 class="flex items-center gap-2 text-base font-bold text-gray-900">
                            <span class="bg-blue-600 text-white w-6 h-6 rounded-full flex items-center justify-center text-xs">{{ $rule['id'] }}</span>
                            {{ $rule['title'] }}
                        </h3>
                        <p class="text-sm text-gray-500 mt-2 leading-relaxed">{{ $rule['desc'] }}</p>
                    </div>
                </div>
                @endforeach

                <div class="mt-8 bg-red-50/50 border border-red-100 p-4 rounded-xl flex gap-3">
                    <i data-lucide="alert-triangle" class="w-5 h-5 text-red-500 mt-0.5"></i>
                    <div>
                        <h4 class="text-sm font-semibold text-red-600">Sanksi Pelanggaran</h4>
                        <p class="text-sm text-red-500 mt-1">Pelanggaran berulang dapat mengakibatkan suspend atau blacklist permanen dari sistem peminjaman. Pastikan untuk mengembalikan barang tepat waktu dan dalam kondisi baik.</p>
                    </div>
                </div>
            </div>

            {{-- ================= TAB 2: KALENDER ================= --}}
            <div id="content-kalender" class="hidden animate-fade-in space-y-8">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 uppercase">KALENDER AKADEMIK TA. 2025/2026</h2>
                        <p class="text-sm text-gray-500">Sekolah Tinggi Teknologi Bontang</p>
                    </div>
                    <button class="flex items-center gap-2 border border-gray-300 bg-white/70 backdrop-blur-sm text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-white transition">
                        <i data-lucide="download" class="w-4 h-4"></i> Unduh Kalender
                    </button>
                </div>

                {{-- Keterangan Card - Glassmorphism --}}
                <div class="bg-white/50 backdrop-blur-md p-6 rounded-2xl border border-white/60 shadow-sm">
                    <h4 class="text-sm font-bold text-gray-900 mb-4">Keterangan:</h4>
                    <div class="flex flex-wrap gap-8">
                        <div class="flex items-center gap-3"><div class="w-4 h-4 rounded bg-red-100 border border-red-200"></div><span class="text-sm text-gray-600">Libur/Hari Besar</span></div>
                        <div class="flex items-center gap-3"><div class="w-4 h-4 rounded bg-orange-100 border border-orange-200"></div><span class="text-sm text-gray-600">Periode Ujian</span></div>
                        <div class="flex items-center gap-3"><div class="w-4 h-4 rounded bg-blue-100 border border-blue-200"></div><span class="text-sm text-gray-600">Acara Kampus</span></div>
                        <div class="flex items-center gap-3"><div class="w-4 h-4 rounded bg-green-100 border border-green-200"></div><span class="text-sm text-gray-600">Periode Kuliah</span></div>
                    </div>
                </div>

                {{-- Kalender Bulanan Cards - Glassmorphism --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    
                    <div class="bg-white/50 backdrop-blur-md rounded-2xl border border-white/60 shadow-sm hover:bg-white/70 transition-colors duration-300 p-5 text-center">
                        <h4 class="font-bold text-gray-900 mb-4">SEPTEMBER 2025</h4>
                        <div class="grid grid-cols-7 gap-1 text-[11px] font-bold text-gray-500 mb-2 text-center">
                            <div>MIN</div><div>SEN</div><div>SEL</div><div>RAB</div><div>KAM</div><div>JUM</div><div>SAB</div>
                        </div>
                        <div class="grid grid-cols-7 gap-y-3 text-sm text-gray-700 text-center">
                            <div class="text-gray-300">31</div><div>1</div><div>2</div><div>3</div><div>4</div><div>5</div><div>6</div>
                            <div>7</div><div>8</div><div>9</div><div>10</div><div>11</div><div>12</div><div>13</div>
                            <div>14</div><div>15</div><div>16</div><div>17</div><div>18</div><div>19</div><div>20</div>
                            <div>21</div><div>22</div><div>23</div><div>24</div><div>25</div><div>26</div><div>27</div>
                            <div>28</div><div>29</div><div>30</div><div class="text-gray-300">1</div><div class="text-gray-300">2</div><div class="text-gray-300">3</div><div class="text-gray-300">4</div>
                        </div>
                    </div>

                    <div class="bg-white/50 backdrop-blur-md rounded-2xl border border-white/60 shadow-sm hover:bg-white/70 transition-colors duration-300 p-5 text-center">
                        <h4 class="font-bold text-gray-900 mb-4">OKTOBER 2025</h4>
                        <div class="grid grid-cols-7 gap-1 text-[11px] font-bold text-gray-500 mb-2 text-center">
                            <div>MIN</div><div>SEN</div><div>SEL</div><div>RAB</div><div>KAM</div><div>JUM</div><div>SAB</div>
                        </div>
                        <div class="grid grid-cols-7 gap-y-3 text-sm text-gray-700 text-center">
                            <div class="text-gray-300">28</div><div class="text-gray-300">29</div><div class="text-gray-300">30</div><div>1</div><div>2</div><div>3</div><div>4</div>
                            <div>5</div><div>6</div><div>7</div><div>8</div><div>9</div><div>10</div><div>11</div>
                            <div>12</div><div>13</div><div>14</div><div>15</div><div>16</div><div>17</div><div>18</div>
                            <div>19</div><div>20</div><div>21</div><div>22</div><div>23</div><div>24</div><div>25</div>
                            <div>26</div><div>27</div><div>28</div><div>29</div><div>30</div><div>31</div><div class="text-gray-300">1</div>
                        </div>
                    </div>

                    <div class="bg-white/50 backdrop-blur-md rounded-2xl border border-white/60 shadow-sm hover:bg-white/70 transition-colors duration-300 p-5 text-center">
                        <h4 class="font-bold text-gray-900 mb-4">NOVEMBER 2025</h4>
                        <div class="grid grid-cols-7 gap-1 text-[11px] font-bold text-gray-500 mb-2 text-center">
                            <div>MIN</div><div>SEN</div><div>SEL</div><div>RAB</div><div>KAM</div><div>JUM</div><div>SAB</div>
                        </div>
                        <div class="grid grid-cols-7 gap-y-3 text-sm text-gray-700 text-center">
                            <div class="text-gray-300">26</div><div class="text-gray-300">27</div><div class="text-gray-300">28</div><div class="text-gray-300">29</div><div class="text-gray-300">30</div><div class="text-gray-300">31</div><div>1</div>
                            <div>2</div><div>3</div><div>4</div><div>5</div><div>6</div><div>7</div><div>8</div>
                            <div>9</div><div>10</div><div>11</div><div>12</div><div>13</div><div>14</div><div>15</div>
                            <div>16</div><div>17</div><div>18</div><div>19</div><div>20</div><div>21</div><div>22</div>
                            <div>23</div><div>24</div><div>25</div><div>26</div><div>27</div><div>28</div><div>29</div>
                            <div>30</div><div class="text-gray-300">1</div><div class="text-gray-300">2</div><div class="text-gray-300">3</div><div class="text-gray-300">4</div><div class="text-gray-300">5</div><div class="text-gray-300">6</div>
                        </div>
                    </div>

                    <div class="bg-white/50 backdrop-blur-md rounded-2xl border border-white/60 shadow-sm hover:bg-white/70 transition-colors duration-300 p-5 text-center">
                        <h4 class="font-bold text-gray-900 mb-4">DESEMBER 2025</h4>
                        <div class="grid grid-cols-7 gap-1 text-[11px] font-bold text-gray-500 mb-2 text-center">
                            <div>MIN</div><div>SEN</div><div>SEL</div><div>RAB</div><div>KAM</div><div>JUM</div><div>SAB</div>
                        </div>
                        <div class="grid grid-cols-7 gap-y-3 text-sm text-gray-700 text-center">
                            <div class="text-gray-300">30</div><div>1</div><div>2</div><div>3</div><div>4</div><div>5</div><div>6</div>
                            <div>7</div><div>8</div><div>9</div><div>10</div><div>11</div><div>12</div><div>13</div>
                            <div>14</div><div>15</div><div>16</div><div>17</div><div>18</div><div>19</div><div>20</div>
                            <div>21</div><div>22</div><div>23</div><div>24</div><div>25</div><div>26</div><div>27</div>
                            <div>28</div><div>29</div><div>30</div><div>31</div><div class="text-gray-300">1</div><div class="text-gray-300">2</div><div class="text-gray-300">3</div>
                        </div>
                    </div>


                    <div class="bg-white/50 backdrop-blur-md rounded-2xl border border-white/60 shadow-sm hover:bg-white/70 transition-colors duration-300 p-5 text-center">
                        <h4 class="font-bold text-gray-900 mb-4">JANUARI 2026</h4>
                        <div class="grid grid-cols-7 gap-1 text-[11px] font-bold text-gray-500 mb-2 text-center">
                            <div>MIN</div><div>SEN</div><div>SEL</div><div>RAB</div><div>KAM</div><div>JUM</div><div>SAB</div>
                        </div>
                        <div class="grid grid-cols-7 gap-y-3 text-sm text-gray-700 text-center">
                            <div class="text-gray-300">28</div><div class="text-gray-300">29</div><div class="text-gray-300">30</div><div class="text-gray-300">31</div><div>1</div><div>2</div><div>3</div>
                            <div>4</div><div>5</div><div>6</div><div>7</div><div>8</div><div>9</div><div>10</div>
                            <div>11</div><div>12</div><div>13</div><div>14</div><div>15</div><div>16</div><div>17</div>
                            <div>18</div><div>19</div><div>20</div><div>21</div><div>22</div><div>23</div><div>24</div>
                            <div>25</div><div>26</div><div>27</div><div>28</div><div>29</div><div>30</div><div>31</div>
                        </div>
                    </div>

                    <div class="bg-white/50 backdrop-blur-md rounded-2xl border border-white/60 shadow-sm hover:bg-white/70 transition-colors duration-300 p-5 text-center">
                        <h4 class="font-bold text-gray-900 mb-4">FEBRUARI 2026</h4>
                        <div class="grid grid-cols-7 gap-1 text-[11px] font-bold text-gray-500 mb-2 text-center">
                            <div>MIN</div><div>SEN</div><div>SEL</div><div>RAB</div><div>KAM</div><div>JUM</div><div>SAB</div>
                        </div>
                        <div class="grid grid-cols-7 gap-y-3 text-sm text-gray-700 text-center">
                            <div>1</div><div>2</div><div>3</div><div>4</div><div>5</div><div>6</div><div>7</div>
                            <div>8</div><div>9</div><div>10</div><div>11</div><div>12</div><div>13</div><div>14</div>
                            <div>15</div><div>16</div><div>17</div><div>18</div><div>19</div><div>20</div><div>21</div>
                            <div>22</div><div>23</div><div>24</div><div>25</div><div>26</div><div>27</div><div>28</div>
                            <div class="text-gray-300">1</div><div class="text-gray-300">2</div><div class="text-gray-300">3</div><div class="text-gray-300">4</div><div class="text-gray-300">5</div><div class="text-gray-300">6</div><div class="text-gray-300">7</div>
                        </div>
                    </div>

                    <div class="bg-white/50 backdrop-blur-md rounded-2xl border border-white/60 shadow-sm hover:bg-white/70 transition-colors duration-300 p-5 text-center">
                        <h4 class="font-bold text-gray-900 mb-4">MARET 2026</h4>
                        <div class="grid grid-cols-7 gap-1 text-[11px] font-bold text-gray-500 mb-2 text-center">
                            <div>MIN</div><div>SEN</div><div>SEL</div><div>RAB</div><div>KAM</div><div>JUM</div><div>SAB</div>
                        </div>
                        <div class="grid grid-cols-7 gap-y-3 text-sm text-gray-700 text-center">
                            <div>1</div><div>2</div><div>3</div><div>4</div><div>5</div><div>6</div><div>7</div>
                            <div>8</div><div>9</div><div>10</div><div>11</div><div>12</div><div>13</div><div>14</div>
                            <div>15</div><div>16</div><div>17</div><div>18</div><div>19</div><div>20</div><div>21</div>
                            <div>22</div><div>23</div><div>24</div><div>25</div><div>26</div><div>27</div><div>28</div>
                            <div>29</div><div>30</div><div>31</div><div class="text-gray-300">1</div><div class="text-gray-300">2</div><div class="text-gray-300">3</div><div class="text-gray-300">4</div>
                        </div>
                    </div>

                    <div class="bg-white/50 backdrop-blur-md rounded-2xl border border-white/60 shadow-sm hover:bg-white/70 transition-colors duration-300 p-5 text-center">
                        <h4 class="font-bold text-gray-900 mb-4">APRIL 2026</h4>
                        <div class="grid grid-cols-7 gap-1 text-[11px] font-bold text-gray-500 mb-2 text-center">
                            <div>MIN</div><div>SEN</div><div>SEL</div><div>RAB</div><div>KAM</div><div>JUM</div><div>SAB</div>
                        </div>
                        <div class="grid grid-cols-7 gap-y-3 text-sm text-gray-700 text-center">
                            <div class="text-gray-300">29</div><div class="text-gray-300">30</div><div class="text-gray-300">31</div><div>1</div><div>2</div><div>3</div><div>4</div>
                            <div>5</div><div>6</div><div>7</div><div>8</div><div>9</div><div>10</div><div>11</div>
                            <div>12</div><div>13</div><div>14</div><div>15</div><div>16</div><div>17</div><div>18</div>
                            <div>19</div><div>20</div><div>21</div><div>22</div><div>23</div><div>24</div><div>25</div>
                            <div>26</div><div>27</div><div>28</div><div>29</div><div>30</div><div class="text-gray-300">1</div><div class="text-gray-300">2</div>
                        </div>
                    </div>

                    <div class="bg-white/50 backdrop-blur-md rounded-2xl border border-white/60 shadow-sm hover:bg-white/70 transition-colors duration-300 p-5 text-center">
                        <h4 class="font-bold text-gray-900 mb-4">MEI 2026</h4>
                        <div class="grid grid-cols-7 gap-1 text-[11px] font-bold text-gray-500 mb-2 text-center">
                            <div>MIN</div><div>SEN</div><div>SEL</div><div>RAB</div><div>KAM</div><div>JUM</div><div>SAB</div>
                        </div>
                        <div class="grid grid-cols-7 gap-y-3 text-sm text-gray-700 text-center">
                            <div class="text-gray-300">26</div><div class="text-gray-300">27</div><div class="text-gray-300">28</div><div class="text-gray-300">29</div><div class="text-gray-300">30</div><div>1</div><div>2</div>
                            <div>3</div><div>4</div><div>5</div><div>6</div><div>7</div><div>8</div><div>9</div>
                            <div>10</div><div>11</div><div>12</div><div>13</div><div>14</div><div>15</div><div>16</div>
                            <div>17</div><div>18</div><div>19</div><div>20</div><div>21</div><div>22</div><div>23</div>
                            <div>24</div><div>25</div><div>26</div><div>27</div><div>28</div><div>29</div><div>30</div>
                            <div>31</div>
                        </div>
                    </div>

                    <div class="bg-white/50 backdrop-blur-md rounded-2xl border border-white/60 shadow-sm hover:bg-white/70 transition-colors duration-300 p-5 text-center">
                        <h4 class="font-bold text-gray-900 mb-4">JUNI 2026</h4>
                        <div class="grid grid-cols-7 gap-1 text-[11px] font-bold text-gray-500 mb-2 text-center">
                            <div>MIN</div><div>SEN</div><div>SEL</div><div>RAB</div><div>KAM</div><div>JUM</div><div>SAB</div>
                        </div>
                        <div class="grid grid-cols-7 gap-y-3 text-sm text-gray-700 text-center">
                            <div class="text-gray-300">31</div><div>1</div><div>2</div><div>3</div><div>4</div><div>5</div><div>6</div>
                            <div>7</div><div>8</div><div>9</div><div>10</div><div>11</div><div>12</div><div>13</div>
                            <div>14</div><div>15</div><div>16</div><div>17</div><div>18</div><div>19</div><div>20</div>
                            <div>21</div><div>22</div><div>23</div><div>24</div><div>25</div><div>26</div><div>27</div>
                            <div>28</div><div>29</div><div>30</div><div class="text-gray-300">1</div><div class="text-gray-300">2</div><div class="text-gray-300">3</div><div class="text-gray-300">4</div>
                        </div>
                    </div>

                    <div class="bg-white/50 backdrop-blur-md rounded-2xl border border-white/60 shadow-sm hover:bg-white/70 transition-colors duration-300 p-5 text-center">
                        <h4 class="font-bold text-gray-900 mb-4">JULI 2026</h4>
                        <div class="grid grid-cols-7 gap-1 text-[11px] font-bold text-gray-500 mb-2 text-center">
                            <div>MIN</div><div>SEN</div><div>SEL</div><div>RAB</div><div>KAM</div><div>JUM</div><div>SAB</div>
                        </div>
                        <div class="grid grid-cols-7 gap-y-3 text-sm text-gray-700 text-center">
                            <div class="text-gray-300">28</div><div class="text-gray-300">29</div><div class="text-gray-300">30</div><div>1</div><div>2</div><div>3</div><div>4</div>
                            <div>5</div><div>6</div><div>7</div><div>8</div><div>9</div><div>10</div><div>11</div>
                            <div>12</div><div>13</div><div>14</div><div>15</div><div>16</div><div>17</div><div>18</div>
                            <div>19</div><div>20</div><div>21</div><div>22</div><div>23</div><div>24</div><div>25</div>
                            <div>26</div><div>27</div><div>28</div><div>29</div><div>30</div><div>31</div><div class="text-gray-300">1</div>
                        </div>
                    </div>

                    <div class="bg-white/50 backdrop-blur-md rounded-2xl border border-white/60 shadow-sm hover:bg-white/70 transition-colors duration-300 p-5 text-center">
                        <h4 class="font-bold text-gray-900 mb-4">AGUSTUS 2026</h4>
                        <div class="grid grid-cols-7 gap-1 text-[11px] font-bold text-gray-500 mb-2 text-center">
                            <div>MIN</div><div>SEN</div><div>SEL</div><div>RAB</div><div>KAM</div><div>JUM</div><div>SAB</div>
                        </div>
                        <div class="grid grid-cols-7 gap-y-3 text-sm text-gray-700 text-center">
                            <div class="text-gray-300">26</div><div class="text-gray-300">27</div><div class="text-gray-300">28</div><div class="text-gray-300">29</div><div class="text-gray-300">30</div><div class="text-gray-300">31</div><div>1</div>
                            <div>2</div><div>3</div><div>4</div><div>5</div><div>6</div><div>7</div><div>8</div>
                            <div>9</div><div>10</div><div>11</div><div>12</div><div>13</div><div>14</div><div>15</div>
                            <div>16</div><div>17</div><div>18</div><div>19</div><div>20</div><div>21</div><div>22</div>
                            <div>23</div><div>24</div><div>25</div><div>26</div><div>27</div><div>28</div><div>29</div>
                            <div>30</div><div>31</div><div class="text-gray-300">1</div><div class="text-gray-300">2</div><div class="text-gray-300">3</div><div class="text-gray-300">4</div><div class="text-gray-300">5</div>
                        </div>
                    </div>

                    <div class="bg-white/50 backdrop-blur-md rounded-2xl border border-white/60 shadow-sm hover:bg-white/70 transition-colors duration-300 p-5 text-center">
                        <h4 class="font-bold text-gray-900 mb-4">SEPTEMBER 2026</h4>
                        <div class="grid grid-cols-7 gap-1 text-[11px] font-bold text-gray-500 mb-2 text-center">
                            <div>MIN</div><div>SEN</div><div>SEL</div><div>RAB</div><div>KAM</div><div>JUM</div><div>SAB</div>
                        </div>
                        <div class="grid grid-cols-7 gap-y-3 text-sm text-gray-700 text-center">
                            <div class="text-gray-300">30</div><div class="text-gray-300">31</div><div>1</div><div>2</div><div>3</div><div>4</div><div>5</div>
                            <div>6</div><div>7</div><div>8</div><div>9</div><div>10</div><div>11</div><div>12</div>
                            <div>13</div><div>14</div><div>15</div><div>16</div><div>17</div><div>18</div><div>19</div>
                            <div>20</div><div>21</div><div>22</div><div>23</div><div>24</div><div>25</div><div>26</div>
                            <div>27</div><div>28</div><div>29</div><div>30</div><div class="text-gray-300">1</div><div class="text-gray-300">2</div><div class="text-gray-300">3</div>
                        </div>
                    </div>

                    <div class="bg-white/50 backdrop-blur-md rounded-2xl border border-white/60 shadow-sm hover:bg-white/70 transition-colors duration-300 p-5 text-center">
                        <h4 class="font-bold text-gray-900 mb-4">OKTOBER 2026</h4>
                        <div class="grid grid-cols-7 gap-1 text-[11px] font-bold text-gray-500 mb-2 text-center">
                            <div>MIN</div><div>SEN</div><div>SEL</div><div>RAB</div><div>KAM</div><div>JUM</div><div>SAB</div>
                        </div>
                        <div class="grid grid-cols-7 gap-y-3 text-sm text-gray-700 text-center">
                            <div class="text-gray-300">27</div><div class="text-gray-300">28</div><div class="text-gray-300">29</div><div class="text-gray-300">30</div><div>1</div><div>2</div><div>3</div>
                            <div>4</div><div>5</div><div>6</div><div>7</div><div>8</div><div>9</div><div>10</div>
                            <div>11</div><div>12</div><div>13</div><div>14</div><div>15</div><div>16</div><div>17</div>
                            <div>18</div><div>19</div><div>20</div><div>21</div><div>22</div><div>23</div><div>24</div>
                            <div>25</div><div>26</div><div>27</div><div>28</div><div>29</div><div>30</div><div>31</div>
                        </div>
                    </div>

                    <div class="bg-white/50 backdrop-blur-md rounded-2xl border border-white/60 shadow-sm hover:bg-white/70 transition-colors duration-300 p-5 text-center">
                        <h4 class="font-bold text-gray-900 mb-4">NOVEMBER 2026</h4>
                        <div class="grid grid-cols-7 gap-1 text-[11px] font-bold text-gray-500 mb-2 text-center">
                            <div>MIN</div><div>SEN</div><div>SEL</div><div>RAB</div><div>KAM</div><div>JUM</div><div>SAB</div>
                        </div>
                        <div class="grid grid-cols-7 gap-y-3 text-sm text-gray-700 text-center">
                            <div>1</div><div>2</div><div>3</div><div>4</div><div>5</div><div>6</div><div>7</div>
                            <div>8</div><div>9</div><div>10</div><div>11</div><div>12</div><div>13</div><div>14</div>
                            <div>15</div><div>16</div><div>17</div><div>18</div><div>19</div><div>20</div><div>21</div>
                            <div>22</div><div>23</div><div>24</div><div>25</div><div>26</div><div>27</div><div>28</div>
                            <div>29</div><div>30</div><div class="text-gray-300">1</div><div class="text-gray-300">2</div><div class="text-gray-300">3</div><div class="text-gray-300">4</div><div class="text-gray-300">5</div>
                        </div>
                    </div>

                    <div class="bg-white/50 backdrop-blur-md rounded-2xl border border-white/60 shadow-sm hover:bg-white/70 transition-colors duration-300 p-5 text-center">
                        <h4 class="font-bold text-gray-900 mb-4">DESEMBER 2026</h4>
                        <div class="grid grid-cols-7 gap-1 text-[11px] font-bold text-gray-500 mb-2 text-center">
                            <div>MIN</div><div>SEN</div><div>SEL</div><div>RAB</div><div>KAM</div><div>JUM</div><div>SAB</div>
                        </div>
                        <div class="grid grid-cols-7 gap-y-3 text-sm text-gray-700 text-center">
                            <div class="text-gray-300">29</div><div class="text-gray-300">30</div><div>1</div><div>2</div><div>3</div><div>4</div><div>5</div>
                            <div>6</div><div>7</div><div>8</div><div>9</div><div>10</div><div>11</div><div>12</div>
                            <div>13</div><div>14</div><div>15</div><div>16</div><div>17</div><div>18</div><div>19</div>
                            <div>20</div><div>21</div><div>22</div><div>23</div><div>24</div><div>25</div><div>26</div>
                            <div>27</div><div>28</div><div>29</div><div>30</div><div>31</div><div class="text-gray-300">1</div><div class="text-gray-300">2</div>
                        </div>
                    </div>

                </div>

                {{-- Semester Schedule Cards - Glassmorphism --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4">
                    <div class="bg-white/50 backdrop-blur-md rounded-2xl border border-white/60 shadow-sm p-6">
                        <h4 class="text-base font-bold text-gray-900 mb-6">SEMESTER GANJIL 2025/2026</h4>
                        <div class="space-y-4 text-sm">
                            <div class="flex justify-between"><span class="text-gray-600">Her Registrasi Maba:</span><span class="font-medium text-gray-900">1 – 4 Sept 2025</span></div>
                            <div class="flex justify-between"><span class="text-gray-600">Basic Study Skills Maba:</span><span class="font-medium text-gray-900">4 – 6 Sept 2025</span></div>
                            <div class="flex justify-between"><span class="text-gray-600">Perkuliahan:</span><span class="font-medium text-gray-900">15 Sept – 26 Des 2025</span></div>
                            <div class="flex justify-between"><span class="text-gray-600">UTS:</span><span class="font-medium text-gray-900">3 – 7 Nov 2025</span></div>
                            <div class="flex justify-between"><span class="text-gray-600">UAS:</span><span class="font-medium text-gray-900">5 – 9 Jan 2026</span></div>
                            <div class="flex justify-between"><span class="text-gray-600">Wisuda:</span><span class="font-medium text-gray-900">November 2025</span></div>
                        </div>
                    </div>
                    <div class="bg-white/50 backdrop-blur-md rounded-2xl border border-white/60 shadow-sm p-6">
                        <h4 class="text-base font-bold text-gray-900 mb-6">SEMESTER GENAP 2025/2026</h4>
                        <div class="space-y-4 text-sm">
                            <div class="flex justify-between"><span class="text-gray-600">Her Registrasi:</span><span class="font-medium text-gray-900">2 Feb – 6 Feb 2026</span></div>
                            <div class="flex justify-between"><span class="text-gray-600">Perkuliahan Tahap 1:</span><span class="font-medium text-gray-900">9 Feb – 13 Feb 2026</span></div>
                            <div class="flex justify-between"><span class="text-gray-600">Libur Ramadhan:</span><span class="font-medium text-gray-900">16 Feb – 3 Apr 2026</span></div>
                            <div class="flex justify-between"><span class="text-gray-600">Perkuliahan Tahap 2:</span><span class="font-medium text-gray-900">6 Apr – 10 Jul 2026</span></div>
                            <div class="flex justify-between"><span class="text-gray-600">UTS:</span><span class="font-medium text-gray-900">18 – 22 Mei 2026</span></div>
                            <div class="flex justify-between"><span class="text-gray-600">UAS:</span><span class="font-medium text-gray-900">20 – 24 Jul 2026</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <footer class="mt-auto px-8 py-5 border-t border-gray-200 text-xs text-gray-400 flex justify-between bg-white/80 backdrop-blur-md flex-shrink-0">
            <span>© 2026 SIPINJAM - Sistem Informasi Peminjaman Ruangan dan Barang</span>
            <span>Built with Next.js</span>
        </footer>
    </main>
</div>

<script>
    lucide.createIcons();
    function switchTab(tabName) {
        const contentPeraturan = document.getElementById('content-peraturan');
        const contentKalender = document.getElementById('content-kalender');
        const btnPeraturan = document.getElementById('tab-btn-peraturan');
        const btnKalender = document.getElementById('tab-btn-kalender');

        if(tabName === 'peraturan') {
            btnPeraturan.className = "flex items-center gap-2 px-8 py-2.5 rounded-xl text-sm font-medium transition-all bg-white text-gray-900 shadow-sm";
            btnKalender.className = "flex items-center gap-2 px-8 py-2.5 rounded-xl text-sm font-medium transition-all text-gray-500 hover:text-gray-700";
            contentPeraturan.classList.remove('hidden');
            contentKalender.classList.add('hidden');
        } else {
            btnKalender.className = "flex items-center gap-2 px-8 py-2.5 rounded-xl text-sm font-medium transition-all bg-white text-gray-900 shadow-sm";
            btnPeraturan.className = "flex items-center gap-2 px-8 py-2.5 rounded-xl text-sm font-medium transition-all text-gray-500 hover:text-gray-700";
            contentKalender.classList.remove('hidden');
            contentPeraturan.classList.add('hidden');
        }
    }
</script>
</body>
</html>