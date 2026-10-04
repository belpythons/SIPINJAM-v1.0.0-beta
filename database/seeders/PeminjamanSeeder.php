<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\Peminjaman;
use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class PeminjamanSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Truncate existing bookings
        Peminjaman::query()->delete();

        // 2. Fetch Master Data
        $users = User::where('role', 'user')->where('is_blocked', false)->get();
        $blockedRoomUser = User::where('email', '202312010@stitek.ac.id')->firstOrFail();
        $blockedItemUser = User::where('email', '202312020@stitek.ac.id')->firstOrFail();

        $ruangs = Ruangan::all();
        $barangs = Barang::all();

        $proyektor = Barang::where('nama', 'Proyektor')->firstOrFail();
        $labMultimedia = Ruangan::where('nama', 'Lab Multimedia')->firstOrFail();

        $today = Carbon::today();

        // ── 3. Seed Specific Target States for Today ─────────────────

        // Target A: Lab Multimedia active "sedang_dipinjam" spanning the entirety of today
        Peminjaman::create([
            'user_id'         => $users->random()->id,
            'tipe'            => 'ruangan',
            'ruangan_id'      => $labMultimedia->id,
            'nama_item'       => $labMultimedia->nama,
            'jumlah'          => 1,
            'tanggal'         => $today->toDateString(),
            'tanggal_mulai'   => $today->toDateString(),
            'tanggal_selesai' => $today->toDateString(),
            'jam_mulai'       => '00:00',
            'jam_selesai'     => '23:59',
            'keterangan'      => 'Penyelenggaraan Expo Riset dan Karya Kreatif Mahasiswa',
            'status'          => Peminjaman::STATUS_APPROVED,
            'nomor_surat'     => Peminjaman::generateNomorSurat(),
            'approved_at'     => $today->copy()->subDay()->setTime(9, 0),
        ]);

        // Target B: Proyektor fully booked today (sum of jumlah = 4)
        $proyektorBookings = [
            [
                'user'   => $users->get(0) ?? $users->random(),
                'jumlah' => 1,
                'start'  => '08:00',
                'end'    => '12:00',
            ],
            [
                'user'   => $users->get(1) ?? $users->random(),
                'jumlah' => 1,
                'start'  => '09:00',
                'end'    => '13:00',
            ],
            [
                'user'   => $users->get(2) ?? $users->random(),
                'jumlah' => 2,
                'start'  => '10:00',
                'end'    => '14:00',
            ],
        ];

        foreach ($proyektorBookings as $pb) {
            Peminjaman::create([
                'user_id'         => $pb['user']->id,
                'tipe'            => 'barang',
                'barang_id'       => $proyektor->id,
                'nama_item'       => $proyektor->nama,
                'jumlah'          => $pb['jumlah'],
                'tanggal'         => $today->toDateString(),
                'tanggal_mulai'   => $today->toDateString(),
                'tanggal_selesai' => $today->toDateString(),
                'jam_mulai'       => $pb['start'],
                'jam_selesai'     => $pb['end'],
                'keterangan'      => 'Kebutuhan presentasi sidang kelompok dan praktikum terintegrasi',
                'status'          => Peminjaman::STATUS_APPROVED,
                'nomor_surat'     => Peminjaman::generateNomorSurat(),
                'approved_at'     => $today->copy()->subDay()->setTime(10, 0),
            ]);
        }

        // ── 4. Seed Blocked User History ─────────────────────────────

        // User 10 (Room violation in Lab Multimedia)
        Peminjaman::create([
            'user_id'         => $blockedRoomUser->id,
            'tipe'            => 'ruangan',
            'ruangan_id'      => $labMultimedia->id,
            'nama_item'       => $labMultimedia->nama,
            'jumlah'          => 1,
            'tanggal'         => $today->copy()->subDays(5)->toDateString(),
            'tanggal_mulai'   => $today->copy()->subDays(5)->toDateString(),
            'tanggal_selesai' => $today->copy()->subDays(5)->toDateString(),
            'jam_mulai'       => '08:00',
            'jam_selesai'     => '12:00',
            'keterangan'      => 'Praktikum Desain Grafis Mandiri - Menyebabkan pelanggaran kebersihan/kerusakan.',
            'status'          => Peminjaman::STATUS_DONE,
            'nomor_surat'     => Peminjaman::generateNomorSurat(),
            'approved_at'     => $today->copy()->subDays(6)->setTime(8, 30),
            'completed_at'    => $today->copy()->subDays(5)->setTime(12, 0),
        ]);

        // User 20 (Item violation - Proyektor returned 7 days late)
        Peminjaman::create([
            'user_id'         => $blockedItemUser->id,
            'tipe'            => 'barang',
            'barang_id'       => $proyektor->id,
            'nama_item'       => $proyektor->nama,
            'jumlah'          => 1,
            'tanggal'         => $today->copy()->subDays(10)->toDateString(),
            'tanggal_mulai'   => $today->copy()->subDays(10)->toDateString(),
            'tanggal_selesai' => $today->copy()->subDays(8)->toDateString(),
            'jam_mulai'       => '08:00',
            'jam_selesai'     => '17:00',
            'keterangan'      => 'Peminjaman Proyektor untuk Lomba Eksternal (Dikembalikan terlambat 7 hari).',
            'status'          => Peminjaman::STATUS_DONE,
            'nomor_surat'     => Peminjaman::generateNomorSurat(),
            'approved_at'     => $today->copy()->subDays(11)->setTime(14, 0),
            'completed_at'    => $today->copy()->subDays(1)->setTime(17, 0), // 8th to 1st = 7 days late!
        ]);

        // ── 5. Generate Massive Booking History ────────────────────────

        $startDate = $today->copy()->subMonths(2);
        $endDate = $today->copy()->addMonth();

        $period = CarbonPeriod::create($startDate, $endDate);

        // Keep track of room bookings by date to prevent overlap when seeding
        $roomBookingsPerDay = [];

        foreach ($period as $date) {
            $dateString = $date->toDateString();

            // Skip today to prevent conflicts with the explicit targets
            if ($dateString === $today->toDateString()) {
                continue;
            }

            // Skip dates of blocked histories for those specific users
            if ($dateString === $today->copy()->subDays(5)->toDateString() ||
                $dateString === $today->copy()->subDays(10)->toDateString()) {
                continue;
            }

            // Seed 1-2 random bookings for this day
            $numBookings = rand(1, 2);

            for ($k = 0; $k < $numBookings; $k++) {
                $user = $users->random();
                $type = rand(0, 1) === 0 ? 'ruangan' : 'barang';

                if ($type === 'ruangan' && $ruangs->count() > 0) {
                    $ruang = $ruangs->random();

                    $duration = rand(0, 7);
                    $tanggalSelesai = $date->copy()->addDays($duration);
                    $tanggalSelesaiString = $tanggalSelesai->toDateString();

                    // Check for overlap within the range of dates to avoid room conflicts
                    $hasConflict = false;
                    $current = $date->copy();
                    while ($current->lte($tanggalSelesai)) {
                        $currString = $current->toDateString();
                        if ($currString === $today->toDateString() ||
                            $currString === $today->copy()->subDays(5)->toDateString() ||
                            $currString === $today->copy()->subDays(10)->toDateString() ||
                            isset($roomBookingsPerDay[$currString][$ruang->id])) {
                            $hasConflict = true;
                            break;
                        }
                        $current->addDay();
                    }

                    if ($hasConflict) {
                        continue; // Skip overlap
                    }

                    // Mark all dates in range as booked
                    $current = $date->copy();
                    while ($current->lte($tanggalSelesai)) {
                        $roomBookingsPerDay[$current->toDateString()][$ruang->id] = true;
                        $current->addDay();
                    }

                    $isPast = $tanggalSelesai->lt($today);
                    $isActive = $date->lt($today) && $tanggalSelesai->gte($today);

                    if ($isPast) {
                        $status = rand(1, 10) <= 8 ? Peminjaman::STATUS_DONE : Peminjaman::STATUS_REJECTED;
                    } elseif ($isActive) {
                        $status = Peminjaman::STATUS_APPROVED;
                    } else {
                        $status = Peminjaman::STATUS_PENDING;
                    }

                    $approvedAt = null;
                    $completedAt = null;
                    if ($status === Peminjaman::STATUS_DONE) {
                        $approvedAt = $date->copy()->subDay()->setTime(9, 0);
                        $completedAt = $tanggalSelesai->copy()->setTime(12, 0);
                    } elseif ($status === Peminjaman::STATUS_APPROVED) {
                        $approvedAt = $date->copy()->subDay()->setTime(9, 0);
                    }

                    Peminjaman::create([
                        'user_id'         => $user->id,
                        'tipe'            => 'ruangan',
                        'ruangan_id'      => $ruang->id,
                        'nama_item'       => $ruang->nama,
                        'jumlah'          => 1,
                        'tanggal'         => $dateString,
                        'tanggal_mulai'   => $dateString,
                        'tanggal_selesai' => $tanggalSelesaiString,
                        'jam_mulai'       => '08:00',
                        'jam_selesai'     => '12:00',
                        'keterangan'      => 'Diskusi Kelompok dan Belajar Bersama Mahasiswa',
                        'status'          => $status,
                        'nomor_surat'     => in_array($status, [Peminjaman::STATUS_DONE, Peminjaman::STATUS_APPROVED]) ? Peminjaman::generateNomorSurat() : null,
                        'approved_at'     => $approvedAt,
                        'completed_at'    => $completedAt,
                    ]);
                } else if ($type === 'barang' && $barangs->count() > 0) {
                    $barang = $barangs->random();

                    $duration = rand(0, 7);
                    $tanggalSelesai = $date->copy()->addDays($duration);
                    $tanggalSelesaiString = $tanggalSelesai->toDateString();

                    // Skip Proyektor on today (we already handled it)
                    if ($barang->nama === 'Proyektor' && 
                        ($dateString === $today->toDateString() || $tanggalSelesaiString === $today->toDateString())) {
                        continue;
                    }

                    $isPast = $tanggalSelesai->lt($today);
                    $isActive = $date->lt($today) && $tanggalSelesai->gte($today);

                    if ($isPast) {
                        $status = rand(1, 10) <= 9 ? Peminjaman::STATUS_DONE : Peminjaman::STATUS_REJECTED;
                    } elseif ($isActive) {
                        $status = Peminjaman::STATUS_APPROVED;
                    } else {
                        $status = Peminjaman::STATUS_PENDING;
                    }

                    $jumlah = rand(1, min(2, $barang->stok_total));

                    $approvedAt = null;
                    $completedAt = null;
                    if ($status === Peminjaman::STATUS_DONE) {
                        $approvedAt = $date->copy()->subDay()->setTime(10, 0);
                        $completedAt = $tanggalSelesai->copy()->setTime(17, 0);
                    } elseif ($status === Peminjaman::STATUS_APPROVED) {
                        $approvedAt = $date->copy()->subDay()->setTime(10, 0);
                    }

                    Peminjaman::create([
                        'user_id'         => $user->id,
                        'tipe'            => 'barang',
                        'barang_id'       => $barang->id,
                        'nama_item'       => $barang->nama,
                        'jumlah'          => $jumlah,
                        'tanggal'         => $dateString,
                        'tanggal_mulai'   => $dateString,
                        'tanggal_selesai' => $tanggalSelesaiString,
                        'jam_mulai'       => '08:00',
                        'jam_selesai'     => '17:00',
                        'keterangan'      => 'Penunjang kegiatan akademik dan kemahasiswaan Stitek.',
                        'status'          => $status,
                        'nomor_surat'     => in_array($status, [Peminjaman::STATUS_DONE, Peminjaman::STATUS_APPROVED]) ? Peminjaman::generateNomorSurat() : null,
                        'approved_at'     => $approvedAt,
                        'completed_at'    => $completedAt,
                    ]);
                }
            }
        }

        // ── 6. Dynamically Adjust available stocks ────────────────────
        foreach ($barangs as $b) {
            $activeTodayCount = Peminjaman::where('barang_id', $b->id)
                ->where('status', Peminjaman::STATUS_APPROVED)
                ->where('tanggal_mulai', '<=', $today->toDateString())
                ->where('tanggal_selesai', '>=', $today->toDateString())
                ->sum('jumlah');

            $newAvailable = max(0, $b->stok_total - $activeTodayCount);
            if ($b->nama === 'Proyektor') {
                $newAvailable = 0; // Strictly ensure Proyektor is fully booked
            }
            $b->update(['stok_tersedia' => $newAvailable]);
        }
    }
}
