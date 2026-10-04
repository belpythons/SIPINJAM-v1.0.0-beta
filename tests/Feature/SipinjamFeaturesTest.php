<?php

use App\Models\Barang;
use App\Models\Peminjaman;
use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    // Pastikan role Spatie terdaftar
    Role::findOrCreate('user');
    Role::findOrCreate('admin');
});

test('auto sanction blocks overtime users and unblocks them when expired', function () {
    // 1. Create a user with approved booking that is overtime
    $user = User::factory()->create(['role' => 'user']);
    $user->assignRole('user');

    $ruangan = Ruangan::create([
        'nama' => 'Lab Komputer',
        'kode' => 'LAB-KOMP',
        'kapasitas' => 30,
        'status' => 'tersedia',
    ]);

    // Booking overtime (selesai kemarin)
    $booking = Peminjaman::create([
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
    ]);

    // Jalankan artisan command untuk memblokir
    $this->artisan('sanction:apply')
        ->assertSuccessful();

    // Verify user is blocked
    $user->refresh();
    expect($user->is_blocked)->toBeTrue();
    expect($user->blocked_until)->not->toBeNull();

    // Mark booking as done so it's no longer overtime, allowing unblock to succeed
    $booking->update(['status' => Peminjaman::STATUS_DONE]);

    // 2. Set user's block expiration to the past to test unblocking
    $user->update([
        'blocked_until' => now()->subDay(),
    ]);

    // Jalankan artisan command untuk unblock
    $this->artisan('sanction:apply')
        ->assertSuccessful();

    // Verify user is unblocked
    $user->refresh();
    expect($user->is_blocked)->toBeFalse();
    expect($user->blocked_until)->toBeNull();
});

test('lapor berantakan blocks the last user who used the room today', function () {
    Storage::fake('public');

    // Create admin & user
    $admin = User::factory()->create(['role' => 'admin']);
    $admin->assignRole('admin');

    $user = User::factory()->create(['role' => 'user']);
    $user->assignRole('user');

    $ruangan = Ruangan::create([
        'nama' => 'Aula Utama',
        'kode' => 'AULA-01',
        'kapasitas' => 100,
        'status' => 'tersedia',
    ]);

    // Booking yang selesai hari ini
    $booking = Peminjaman::create([
        'user_id' => $user->id,
        'tipe' => 'ruangan',
        'ruangan_id' => $ruangan->id,
        'nama_item' => $ruangan->nama,
        'tanggal_mulai' => now(),
        'tanggal_selesai' => now(),
        'jam_mulai' => '08:00',
        'jam_selesai' => '10:00',
        'keterangan' => 'Seminar',
        'status' => Peminjaman::STATUS_DONE,
        'completed_at' => now(),
    ]);

    // Panggil route admin untuk melaporkan ruangan berantakan
    $response = $this->actingAs($admin)
        ->withoutMiddleware()
        ->post(route('admin.ruangan.lapor_berantakan', $ruangan->id), [
            'feedback' => 'Ruangan sangat kotor dan kursi berantakan setelah acara selesai.',
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    // Verify user is blocked
    $user->refresh();
    expect($user->is_blocked)->toBeTrue();
    expect($user->blocked_until)->not->toBeNull();
});

test('generate PDF generates nomor surat and stores it physically for approved bookings only', function () {
    Storage::fake('local');
    Storage::fake('public');

    $user = User::factory()->create(['role' => 'user']);
    $user->assignRole('user');

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
    expect($bookingApproved->nomor_surat)->toContain('/INT/SIPINJAM/' . now()->year);

    // Verify physical file was NOT saved in storage/app/public/surat to prevent leaks
    $safeNomor = str_replace(['/', '\\'], '-', $bookingApproved->nomor_surat);
    $filename = "surat-peminjaman-{$safeNomor}.pdf";
    Storage::disk('local')->assertMissing("public/surat/{$filename}");
});
