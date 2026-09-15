<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Satu-satunya tempat peran & permission Spatie dibuat.
 *
 * Idempoten — aman dijalankan berulang kali, dan dipanggil di setiap test
 * lewat tests/Pest.php.
 */
class RolePermissionSeeder extends Seeder
{
    /**
     * Permission granular.
     *
     * `master.manage` lama dipecah menjadi aset vs konten supaya `staf_aset`
     * dapat mengelola ruangan/barang tanpa ikut menguasai banner & kalender.
     */
    public const PERMISSIONS = [
        // Master data
        'master.aset.manage',    // Ruangan, Barang
        'master.konten.manage',  // Banner, Kalender
        'user.manage',

        // Alur pengajuan
        'pengajuan.create',
        'pengajuan.verify',
        'pengajuan.approve',
        'pengajuan.reject',

        // Lapangan & penegakan
        'serahterima.create',
        'pelanggaran.create',
        'sanksi.manage',

        'laporan.view',
    ];

    /**
     * Peran => permission yang dimilikinya (§5.3.1).
     *
     * Pemisahan intinya: yang MEMVERIFIKASI ketersediaan (staf_aset) berbeda
     * dari yang MENYETUJUI (pimpinan). SOP kampus tidak pernah menyatukan
     * keduanya ke satu tombol.
     */
    public const ROLES = [
        'mahasiswa' => ['pengajuan.create'],
        'dosen' => ['pengajuan.create'],
        'staff' => ['pengajuan.create'],

        'staf_aset' => [
            'pengajuan.verify',
            'pengajuan.reject',
            'serahterima.create',
            'pelanggaran.create',
            'master.aset.manage',
            'laporan.view',
        ],

        'pimpinan' => [
            'pengajuan.approve',
            'pengajuan.reject',
            'laporan.view',
        ],

        // Admin adalah superset.
        'admin' => self::PERMISSIONS,
    ];

    /** Peran yang boleh mengajukan peminjaman. */
    public const PERAN_PEMINJAM = ['mahasiswa', 'dosen', 'staff'];

    public function run(): void
    {
        // Cache permission Spatie harus dilupakan sebelum & sesudah perubahan,
        // kalau tidak pemeriksaan can() masih memakai data lama dalam proses
        // yang sama (terasa sekali di test).
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (self::PERMISSIONS as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        foreach (self::ROLES as $nama => $permissions) {
            $role = Role::findOrCreate($nama, 'web');
            $role->syncPermissions($permissions);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
