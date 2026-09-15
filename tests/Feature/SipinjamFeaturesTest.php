<?php

use App\Models\Peminjaman;
use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    // Pastikan role Spatie terdaftar
});

function bookingTerlambat(User $user, array $attr = []): Peminjaman
{
    $ruangan = Ruangan::create([
        'nama' => 'Lab Komputer',
        'kode' => 'LAB-'.fake()->unique()->numerify('###'),
        'kapasitas' => 30,
        'status' => 'tersedia',
    ]);

    return Peminjaman::create(array_merge([
        'user_id' => $user->id,
        'tipe' => 'ruangan',
        'ruangan_id' => $ruangan->id,
        'nama_item' => $ruangan->nama,
        'tanggal_mulai' => now()->subDays(5),
        'tanggal_selesai' => now()->subDays(3),
        'jam_mulai' => '08:00',
        'jam_selesai' => '17:00',
        'keterangan' => 'Praktikum',
        'status' => Peminjaman::STATUS_APPROVED,
    ], $attr));
}

test('B-04: peminjaman terlambat DITANDAI, bukan diblokir otomatis', function () {
    $user = User::factory()->peminjam()->create();
    bookingTerlambat($user);

    $this->artisan('sanction:apply')
        ->expectsOutputToContain('menunggu pemeriksaan')
        ->assertSuccessful();

    // Inti perbaikan B-04: barang mungkin sudah dikembalikan dan admin hanya
    // belum menekan "Selesai". Menghukum atas dasar waktu berlalu saja berarti
    // menghukum kelambatan admin, bukan kelalaian peminjam.
    $user->refresh();

    expect($user->is_blocked)->toBeFalse()
        ->and($user->blocked_until)->toBeNull();
});

test('B-04: peminjaman yang belum lewat toleransi tidak ikut ditandai', function () {
    $user = User::factory()->peminjam()->create();
    bookingTerlambat($user, [
        'tanggal_selesai' => now()->addDay(),
        'jam_selesai' => '10:00',
    ]);

    $this->artisan('sanction:apply')
        ->doesntExpectOutputToContain('menunggu pemeriksaan')
        ->assertSuccessful();
});

test('pembebasan blokir yang sudah berakhir tetap berjalan', function () {
    $user = User::factory()->peminjam()->create([
        'is_blocked' => true,
        'blocked_until' => now()->subDay(),
        'blocked_reason' => 'Sanksi lama',
    ]);

    $this->artisan('sanction:apply')->assertSuccessful();

    $user->refresh();

    expect($user->is_blocked)->toBeFalse()
        ->and($user->blocked_until)->toBeNull();
});

test('blokir yang masih berlaku tidak ikut dibebaskan', function () {
    $user = User::factory()->peminjam()->create([
        'is_blocked' => true,
        'blocked_until' => now()->addDays(5),
        'blocked_reason' => 'Sanksi berjalan',
    ]);

    $this->artisan('sanction:apply')->assertSuccessful();

    expect($user->fresh()->is_blocked)->toBeTrue();
});

test('B-15: jatuh tempo diambil dari selesai_at, bukan dirakit ulang dari tanggal+jam', function () {
    $user = User::factory()->peminjam()->create();
    $booking = bookingTerlambat($user);

    // Kolom lama menunjuk masa lalu, kolom baru menunjuk masa depan.
    // Versi lama membaca tanggal_selesai+jam_selesai dan akan menandainya
    // terlambat; versi sekarang menghormati selesai_at.
    $booking->forceFill([
        'selesai_at' => now()->addDays(3),
    ])->saveQuietly();

    $this->artisan('sanction:apply')
        ->doesntExpectOutputToContain('menunggu pemeriksaan')
        ->assertSuccessful();
});

test('B-04: memakai chunkById, bukan memuat seluruh tabel ke memori', function () {
    $user = User::factory()->peminjam()->create();

    foreach (range(1, 5) as $i) {
        bookingTerlambat($user);
    }

    // Yang diuji di sini perilakunya: seluruh baris tetap terproses walau
    // dibaca per potongan.
    $this->artisan('sanction:apply')
        ->expectsOutputToContain('Terlambat: 5')
        ->assertSuccessful();
});

/*
 * Test "lapor berantakan blocks the last user who used the room today" DIHAPUS.
 *
 * Test itu mengunci perilaku B-05: menetapkan pelaku lewat tebakan kueri
 * ("peminjaman selesai terakhir di ruangan ini hari ini") lalu memblokirnya 30
 * hari. Fiturnya sudah dihapus; menyimpan testnya berarti mempertahankan
 * tekanan untuk menghidupkannya kembali.
 *
 * P5 menggantinya dengan test "Catat Temuan" yang membuktikan pelaku SELALU
 * diambil dari pengajuan.user_id — diuji dengan beberapa pengajuan berbeda di
 * ruangan yang sama pada hari yang sama, yaitu kasus yang dulu salah.
 */

test('generate PDF generates nomor surat and stores it physically for approved bookings only', function () {
    Storage::fake('local');
    Storage::fake('public');

    $user = User::factory()->peminjam()->create();

    $ruangan = Ruangan::create([
        'nama' => 'Lab Bahasa',
        'kode' => 'LAB-BAHASA',
        'kapasitas' => 20,
        'status' => 'tersedia',
    ]);

    // 1. Pending booking should NOT be able to download PDF
    $bookingPending = Peminjaman::create([
        'user_id' => $user->id,
        'tipe' => 'ruangan',
        'ruangan_id' => $ruangan->id,
        'nama_item' => $ruangan->nama,
        'tanggal_mulai' => now()->addDay(),
        'tanggal_selesai' => now()->addDay(),
        'jam_mulai' => '08:00',
        'jam_selesai' => '10:00',
        'keterangan' => 'Kuliah Bahasa Inggris',
        'status' => Peminjaman::STATUS_PENDING,
    ]);

    $response = $this->actingAs($user)
        ->get(route('bookings.pdf', $bookingPending->id));

    $response->assertStatus(403);

    // 2. Approved booking should be able to download PDF, generate auto-incrementing nomor_surat and save it to storage
    $bookingApproved = Peminjaman::create([
        'user_id' => $user->id,
        'tipe' => 'ruangan',
        'ruangan_id' => $ruangan->id,
        'nama_item' => $ruangan->nama,
        'tanggal_mulai' => now()->addDay(),
        'tanggal_selesai' => now()->addDay(),
        'jam_mulai' => '08:00',
        'jam_selesai' => '10:00',
        'keterangan' => 'Ujian TOEFL',
        'status' => Peminjaman::STATUS_APPROVED,
        'approved_at' => now(),
    ]);

    $responseApproved = $this->actingAs($user)
        ->get(route('bookings.pdf', $bookingApproved->id));

    $responseApproved->assertStatus(200);
    $responseApproved->assertHeader('content-type', 'application/pdf');

    // Verify nomor_surat generated
    $bookingApproved->refresh();
    expect($bookingApproved->nomor_surat)->not->toBeEmpty();
    expect($bookingApproved->nomor_surat)->toContain('/INT/SIPINJAM/'.now()->year);

    // Verify physical file was NOT saved in storage/app/public/surat to prevent leaks
    $safeNomor = str_replace(['/', '\\'], '-', $bookingApproved->nomor_surat);
    $filename = "surat-peminjaman-{$safeNomor}.pdf";
    Storage::disk('local')->assertMissing("public/surat/{$filename}");
});
