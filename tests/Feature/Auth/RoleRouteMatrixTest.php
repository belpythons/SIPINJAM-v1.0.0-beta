<?php

use App\Models\User;

/**
 * Matriks peran × rute.
 *
 * Tanpa test ini, kegagalan otorisasi muncul sebagai pengalihan/403 tanpa pesan
 * dan terlihat seperti bug UI, bukan bug izin.
 *
 * Peran & permission disemai otomatis untuk seluruh test Feature lewat
 * RolePermissionSeeder (lihat tests/Pest.php).
 */

// Rute admin representatif — mewakili tiap kelompok CRUD di /admin/*.
dataset('rute admin', [
    'dashboard admin' => '/admin/dashboard',
    'kelola user' => '/admin/kelola-user',
    'kelola peminjaman' => '/admin/kelola-peminjaman',
    'kelola ruangan' => '/admin/kelola-ruangan',
    'kelola barang' => '/admin/kelola-barang',
    'laporan admin' => '/admin/laporan',
]);

// Rute pengguna terautentikasi.
dataset('rute pengguna', [
    'dashboard' => '/dashboard',
    'ruangan' => '/ruangan',
    'barang' => '/barang',
    'riwayat' => '/bookings',
    'tata tertib' => '/tata_tertib',
    'kalender' => '/kalender',
    'profil' => '/profile',
]);

// ── admin ────────────────────────────────────────────────────────────────────

test('admin: rute admin -> 200', function (string $uri) {
    $this->actingAs(User::factory()->admin()->create())
        ->get($uri)
        ->assertOk();
})->with('rute admin');

test('admin: rute pengguna -> 200 (atau diarahkan ke admin dashboard jika rute dashboard)', function (string $uri) {
    $response = $this->actingAs(User::factory()->admin()->create())
        ->get($uri);

    if ($uri === '/dashboard') {
        $response->assertRedirect(route('admin.dashboard'));
    } else {
        $response->assertOk();
    }
})->with('rute pengguna');

// ── peminjam ──────────────────────────────────────────────────────────

test('peminjam: rute admin ditolak', function (string $uri) {
    // Catatan kontrak: IsAdmin MENGALIHKAN ke /dashboard, bukan melempar 403.
    // Diasersi apa adanya; lihat T-24 bila 403 yang diinginkan.
    $this->actingAs(User::factory()->peminjam()->create())
        ->get($uri)
        ->assertRedirect('/dashboard');
})->with('rute admin');

test('peminjam: rute pengguna -> 200', function (string $uri) {
    $this->actingAs(User::factory()->peminjam()->create())
        ->get($uri)
        ->assertOk();
})->with('rute pengguna');

// ── tamu ─────────────────────────────────────────────────────────────────────

test('tamu: rute admin diarahkan ke login', function (string $uri) {
    // IsAdmin memulangkan ke /dashboard, lalu middleware auth memulangkan ke /login.
    $this->get($uri)->assertRedirect();
    $this->assertGuest();
})->with('rute admin');

test('tamu: rute pengguna diarahkan ke login', function (string $uri) {
    $this->get($uri)->assertRedirect(route('login'));
})->with('rute pengguna');

// ── Permission, bukan sekadar peran, yang menjadi penentu ────────────────────
//
// Delapan FormRequest kini memakai can('master.aset.manage'). Karena IsAdmin sudah
// menghadang non-admin lebih dulu, satu-satunya cara membuktikan permission itu
// benar-benar dievaluasi adalah dengan mencabutnya dari seorang admin.

test('admin dengan master.aset.manage boleh menyimpan master data', function () {
    $admin = User::factory()->admin()->create();

    expect($admin->can('master.aset.manage'))->toBeTrue();

    $this->actingAs($admin)
        ->post('/admin/kelola-barang', [
            'nama' => 'Proyektor Baru',
            'kode' => 'PRJ-BARU',
            'stok_total' => 2,
            'stok_tersedia' => 2,
            'kategori' => 'Elektronik',
            'status' => 'tersedia',
        ])
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('barangs', ['kode' => 'PRJ-BARU']);
});

// ── Matriks 6 peran (P1) ─────────────────────────────────────────────────────

dataset('peran non admin', ['mahasiswa', 'dosen', 'staff', 'staf_aset', 'pimpinan']);

test('hanya admin yang menembus middleware admin', function (string $peran) {
    $u = User::factory()->create();
    $u->assignRole($peran);

    $this->actingAs($u)->get('/admin/dashboard')->assertRedirect('/dashboard');
})->with('peran non admin');

test('setiap peran dapat membuka rute pengguna', function (string $peran) {
    $u = User::factory()->create();
    $u->assignRole($peran);

    $this->actingAs($u)->get('/dashboard')->assertOk();
})->with('peran non admin');

test('permission tiap peran sesuai matriks §5.3.1', function () {
    $harapan = [
        'mahasiswa' => ['pengajuan.create'],
        'dosen' => ['pengajuan.create'],
        'staff' => ['pengajuan.create'],
        'staf_aset' => ['pengajuan.verify', 'serahterima.create', 'pelanggaran.create', 'master.aset.manage'],
        'pimpinan' => ['pengajuan.approve'],
    ];

    foreach ($harapan as $peran => $izinWajib) {
        $u = User::factory()->create();
        $u->assignRole($peran);

        foreach ($izinWajib as $izin) {
            expect($u->can($izin))->toBeTrue("peran {$peran} seharusnya punya {$izin}");
        }
    }
});

test('pemisahan kewenangan: yang memverifikasi bukan yang menyetujui', function () {
    $stafAset = User::factory()->create();
    $stafAset->assignRole('staf_aset');

    $pimpinan = User::factory()->create();
    $pimpinan->assignRole('pimpinan');

    // Inti SOP kampus: satu tombol "Setujui" tidak boleh merangkap keduanya.
    expect($stafAset->can('pengajuan.verify'))->toBeTrue()
        ->and($stafAset->can('pengajuan.approve'))->toBeFalse()
        ->and($pimpinan->can('pengajuan.approve'))->toBeTrue()
        ->and($pimpinan->can('pengajuan.verify'))->toBeFalse();
});

test('staf_aset mengelola aset tetapi tidak menyentuh konten', function () {
    $u = User::factory()->create();
    $u->assignRole('staf_aset');

    expect($u->can('master.aset.manage'))->toBeTrue()
        ->and($u->can('master.konten.manage'))->toBeFalse();
});

test('peran peminjam pada config cocok dengan yang punya pengajuan.create', function () {
    foreach (config('sipinjam.peran.peminjam') as $peran) {
        $u = User::factory()->create();
        $u->assignRole($peran);

        expect($u->can('pengajuan.create'))->toBeTrue("peran peminjam {$peran} harus bisa mengajukan");
    }
});

test('admin yang master.aset.manage-nya dicabut -> 403 dari FormRequest', function () {
    $admin = User::factory()->admin()->create();

    // Cabut dari perannya, bukan dari usernya — inilah yang akan dilakukan P1
    // saat memisahkan kewenangan staf_aset dari admin.
    $admin->roles()->first()->revokePermissionTo('master.aset.manage');
    $admin->forgetCachedPermissions();

    expect($admin->fresh()->can('master.aset.manage'))->toBeFalse();

    $this->actingAs($admin->fresh())
        ->post('/admin/kelola-barang', [
            'nama' => 'Proyektor Ditolak',
            'kode' => 'PRJ-TOLAK',
            'stok_total' => 2,
            'kategori' => 'Elektronik',
            'status' => 'tersedia',
        ])
        ->assertForbidden();

    $this->assertDatabaseMissing('barangs', ['kode' => 'PRJ-TOLAK']);
});

test('peminjam tidak punya master.aset.manage', function () {
    expect(User::factory()->peminjam()->create()->can('master.aset.manage'))->toBeFalse();
});

// ── Regresi: kolom users.role benar-benar hilang ─────────────────────────────

test('kolom users.role sudah tidak ada', function () {
    expect(Schema::hasColumn('users', 'role'))->toBeFalse();
});

test('role tidak dapat di-mass-assign', function () {
    $user = User::create([
        'name' => 'Coba Eskalasi',
        'email' => 'eskalasi@stitek.ac.id',
        'password' => bcrypt('password'),
        'role' => 'admin',        // harus diabaikan diam-diam oleh $fillable
    ]);

    expect($user->hasRole('admin'))->toBeFalse()
        ->and($user->getRoleNames())->toBeEmpty();
});
