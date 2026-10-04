<?php

namespace Database\Seeders;

use App\Models\TataTertibVersion;
use Illuminate\Database\Seeder;

class TataTertibSeeder extends Seeder
{
    public function run(): void
    {
        $konten = <<<'MARKDOWN'
# Tata Tertib & Ketentuan Peminjaman Aplikasi

Dokumen ini memuat aturan, alur, serta hak dan kewajiban pengguna dalam proses peminjaman inventaris/aset/buku melalui aplikasi.

---

## 1. Ketentuan Umum Pengguna

1. **Kelayakan Peminjam**:
   - Peminjam wajib memiliki akun aktif yang telah diverifikasi (status keanggotaan/karyawan/mahasiswa aktif).
   - Pengguna dengan sanksi penangguhan (*suspension*) atau tanggungan denda belum lunas tidak diperkenankan mengajukan peminjaman baru.
2. **Kapasitas Peminjaman**:
   - Setiap pengguna dibatasi maksimal meminjam **3 unit/item** secara bersamaan.
3. **Masa Peminjaman**:
   - Durasi standar peminjaman adalah **7 hari (1 minggu)** terhitung sejak status serah terima barang dikonfirmasi di aplikasi.

---

## 2. Alur Pengajuan dan Persetujuan

```
[Pengajuan di Aplikasi] 
       ↓ 
[Verifikasi / Approval Admin] 
       ↓ 
[Pengambilan & Konfirmasi Serah Terima] 
       ↓ 
[Masa Peminjaman] 
       ↓ 
[Pengembalian & Pengecekan Kondisi]
```

1. **Reservasi / Booking**:
   - Pengajuan dilakukan melalui menu peminjaman pada aplikasi minimal **1 hari** sebelum waktu pengambilan.
   - Peminjam wajib mencantumkan keperluan penggunaan serta estimasi waktu pengembalian.
2. **Konfirmasi Admin**:
   - Pengajuan berstatus *Pending* hingga disetujui oleh pengelola/administrator.
   - Apabila surat peminjaman tidak ditandatangani dan dilaporkan dalam kurun waktu **7 hari (1 minggu)** setelah pengajuan, peminjaman otomatis dibatalkan sistem (*auto-reject*).

---

## 3. Tata Tertib Pengambilan & Pengembalian

1. **Serah Terima (Check-out)**:
   - Pengambilan barang/ruangan wajib menunjukkan bukti fisik atau surat izin peminjaman bertandatangan kepada petugas/admin.
   - Peminjam wajib memeriksa fisik dan kelengkapan barang sebelum menandatangani serah terima.
2. **Pengembalian (Check-in)**:
   - Barang/ruangan wajib dikembalikan tepat waktu dalam kondisi bersih, berfungsi baik, dan lengkap bersama aksesoris bawaan.
   - Petugas/admin akan melakukan inspeksi fisik sebelum status peminjaman diubah menjadi *Selesai* (*Completed*).

---

## 4. Perpanjangan Masa Pinjam (Renewal)

- Pengajuan perpanjangan hanya dapat dilakukan maksimal **1 kali** melalui konfirmasi ke pengelola.
- Permohonan perpanjangan wajib diajukan minimal **1 hari** sebelum batas waktu pengembalian berakhir.
- Perpanjangan tidak dapat diproses jika barang telah di-booking oleh pengguna lain.

---

## 5. Sanksi, Keterlambatan, dan Ganti Rugi

| Pelanggaran | Konsekuensi / Sanksi |
| :--- | :--- |
| **Keterlambatan Pengembalian** | Pemblokiran akun selama durasi keterlambatan atau sanksi administratif. |
| **Kerusakan Ringan/Hilang Aksesoris** | Mengganti biaya perbaikan atau mengganti komponen yang rusak/hilang. |
| **Kerusakan Berat / Kehilangan Unit** | Mengganti unit dengan spesifikasi setara atau membayar nilai ganti rugi penuh. |
| **Penyalahgunaan Akun & Ruangan Kotor** | Pemblokiran akun sementara/permanen dan pelaporan ke pihak berwenang kampus. |

---

## 6. Tanggung Jawab & Larangan

- **Dilarang memindahtangankan** barang pinjaman kepada pihak ketiga tanpa izin resmi pengelola.
- Peminjam bertanggung jawab penuh atas keamanan, kebersihan, dan perawatan barang selama masa peminjaman.
- Segala bentuk kerusakan akibat kelalaian operasional menjadi beban peminjam.
MARKDOWN;

        TataTertibVersion::updateOrCreate(
            ['versi' => '1.0'],
            [
                'konten' => $konten,
                'berlaku_sejak' => now()->subMonths(1)->startOfDay(),
            ]
        );
    }
}
