# Dokumentasi Desain, Regulasi, dan Justifikasi Tata Tertib Peminjaman

Dokumen ini berfungsi sebagai spesifikasi fungsional dan operasional yang menghubungkan kebutuhan tata tertib (kebijakan organisasi) dengan implementasi sistem aplikasi (desain UI/UX & *backend logic*), lengkap dengan analisis *rationale* (alasan penetapan aturan).

---

## 1. Matriks Rangkuman Kebijakan

| Modul Kebijakan | Komponen Desain Aplikasi | Inti Ketentuan (Isi) | Justifikasi (*Reason*) |
| :--- | :--- | :--- | :--- |
| **1. Kualifikasi Akun** | Role-Based Access Control (RBAC), Badge Status Akun, Guard Middleware | Akun terverifikasi aktif, bebas denda/sanksi. | Mencegah *fraud*, akun fiktif, dan *moral hazard*. |
| **2. Kuota Pinjam** | Kuota Tracker (*Progress Bar* / Counter Limit pada Dashboard) | Maksimal pinjam $[X]$ unit item bersamaan. | Pemerataan inventaris & mitigasi risiko akumulasi kerugian. |
| **3. Durasi & Booking** | Date-Time Picker Dinamis, Scheduler Auto-Cancel | Durasi dasar $[X]$ hari, reservasi $H-[X]$, auto-cancel jika telat ambil. | Efisiensi perputaran aset (*asset turnover*) dan mencegah penahanan stok fiktif. |
| **4. Serah Terima** | Modul Scanner QR Code / Barcode, Tanda Tangan Digital | Verifikasi fisik 2 arah (Check-in & Check-out) via scan aset. | Pembuktian legalitas transfer tanggung jawab fisik & validitas kondisi awal aset. |
| **5. Perpanjangan** | Tombol Kondisional (*Eligibility Checker* di Detail Peminjaman) | Maksimal $[X]$ kali perpanjangan, syarat: belum ada reservasi antrean berikutnya. | Fleksibilitas pengguna tanpa mengorbankan hak antrean pengguna lain. |
| **6. Keterlambatan & Sanksi** | Batch Cron Job Denda, Alert Banner Keterlambatan, Freeze Akun Otomatis | Penalti harian Rp $[X]$ dan suspensi hak pinjam. | Efek jera (*deterrent effect*) guna menjamin ketepatan jadwal inventaris. |
| **7. Ganti Rugi Kerusakan** | Incident Ticket & Form Bukti Foto Kondisi Barang | Penggantian biaya servis, komponen hilang, atau unit setara. | Perlindungan aset dan menjaga kontinuitas operasional organisasi. |

---

## 2. Bedah Detail: Desain, Isi, dan Reason

### 2.1. Ketentuan Kelayakan Peminjam

#### A. Desain Sistem & UX
* **Frontend**: Tampilan status verifikasi (misal: "Verified User") pada profil. Jika akun berstatus *suspended* atau memiliki tunggakan, tombol "Ajukan Pinjam" dinonaktifkan (*disabled*) dengan *tooltip* informatif: *"Anda memiliki tanggungan denda/sanksi aktif"*.
* **Backend**: Middleware autentikasi memeriksa parameter `is_active == true`, `outstanding_fines == 0`, dan `is_suspended == false` sebelum merespons mutasi data reservasi.

#### B. Isi Aturan
> *"Pengguna wajib berstatus aktif dan terverifikasi. Pengguna dengan penangguhan aktif atau tunggakan sanksi denda dilarang membuat pengajuan peminjaman baru."*

#### C. Reason (Justifikasi & Analisis Risiko)
1. **Mitigasi Gagal Kembali**: Meminjamkan aset kepada pengguna bermasalah melipatgandakan risiko aset hilang atau tidak dikembalikan tepat waktu.
2. **Integritas Data Audit**: Verifikasi identitas (NIM/NIP/KTP) memastikan penanggung jawab dapat dihubungi dan dimintai pertanggungjawaban secara hukum/institusional.

---

### 2.2. Batas Kuota Peminjaman (Item Limit)

#### A. Desain Sistem & UX
* **Frontend**: Kartu ringkasan kuota di dashboard: *"Slot Pinjaman Terpakai: 2 / 3"*. Item yang melebihi kuota otomatis tidak dapat ditambahkan ke keranjang pinjam (*borrowing cart*).
* **Backend**: Validasi level basis data:
  $$\text{Total Active Loans} + \text{New Requests} \le \text{MAX\_LIMIT}$$

#### B. Isi Aturan
> *"Setiap pengguna berhak meminjam maksimal $[X]$ unit item dalam satu periode peminjaman berjalan."*

#### C. Reason (Justifikasi & Analisis Risiko)
1. **Prinsip Keadilan Akses (*Fair Share*)**: Menghindari monopoli pemakaian inventaris oleh segelintir pengguna.
2. **Pembatasan Batas Kerugian (*Loss Cap*)**: Jika peminjam mengalami musibah atau lalai, jumlah aset yang berisiko terdampak dibatasi pada ambang batas yang dapat ditoleransi.

---

### 2.3. Reservasi dan Pembatalan Otomatis (*Auto-Cancel*)

#### A. Desain Sistem & UX
* **Frontend**: Kalender interaktif dengan *slot blackout* untuk waktu yang sudah dipesan. *Countdown timer* pengambilan muncul begitu status pengajuan disetujui (misal: *"Waktu pengambilan tersisa: 01:59:00"*).
* **Backend**: *Background worker* / Cron job berjalan tiap 5–15 menit untuk membatalkan tiket yang kedaluwarsa (`current_timestamp > booking_expired_at && status == 'APPROVED'`) lalu mengubah status inventaris kembali menjadi `AVAILABLE`.

#### B. Isi Aturan
> *"Pengajuan reservasi wajib diajukan minimal $[X]$ jam sebelum pengambilan. Jika barang tidak diambil dalam kurun waktu $[X]$ jam sejak jadwal persetujuan, reservasi otomatis dibatalkan oleh sistem."*

#### C. Reason (Justifikasi & Analisis Risiko)
1. **Mencegah Stok Gantung (*Phantom Booking*)**: Tanpa auto-cancel, pengguna dapat memesan barang tanpa pernah mengambilnya, menghalangi pengguna lain yang benar-benar membutuhkan barang tersebut.
2. **Kesiapan Staf Operasional**: Jadwal pemesanan minimal memberi waktu bagi petugas logistik untuk menyiapkan dan memeriksa kelayakan barang.

---

### 2.4. Validasi Serah Terima Digital (Check-out & Check-in)

#### A. Desain Sistem & UX
* **Frontend**: Fitur pemindai kamera (QR Code/Barcode) di aplikasi petugas dan peminjam. Tampilan *checklist* kelengkapan (misal: kabel daya, adaptor, tas, buku panduan) disertai tombol persetujuan digital.
* **Backend**: Transaksi bersifat atomik; status peminjaman hanya berubah menjadi `ON_LOAN` atau `COMPLETED` setelah *payload* scan cocok antara token pengguna dan barcode aset fisik.

#### B. Isi Aturan
> *"Penyerahan dan pengembalian barang wajib divalidasi melalui pemindaian kode digital oleh kedua belah pihak setelah memeriksa kondisi fisik serta kelengkapan unit secara bersama-sama."*

#### C. Reason (Justifikasi & Analisis Risiko)
1. **Bukti Valid (*Non-repudiation*)**: Menghilangkan sanggahan "barang belum saya terima" atau "bagian ini sudah pecah dari awal".
2. **Integritas Waktu Nyata**: Menghindari selisih waktu antara serah terima fisik di lapangan dan pencatatan di database.

---

### 2.5. Batasan Perpanjangan (*Renewal*)

#### A. Desain Sistem & UX
* **Frontend**: Tombol "Perpanjang" di halaman riwayat transaksi. Tombol dinonaktifkan secara otomatis jika:
  1. Batas maksimal perpanjangan telah tercapai.
  2. Aset telah dipesan oleh pengguna lain di jadwal berikutnya.
* **Backend**: Pengecekan konflik jadwal:
  $$\text{Requested Extension Period} \cap \text{Future Bookings} = \emptyset$$

#### B. Isi Aturan
> *"Perpanjangan durasi pinjam dapat diajukan maksimal $[X]$ kali selambat-lambatnya $[X]$ jam/hari sebelum tenggat, asalkan tidak ada pengguna lain yang telah memesan antrean barang tersebut."*

#### C. Reason (Justifikasi & Analisis Risiko)
1. **Kepastian Pengguna Antrean**: Menghentikan skenario di mana pengguna berikutnya dirugikan karena perpanjangan mendadak dari peminjam lama.
2. **Dorongan Pengelolaan Waktu**: Mendorong pengguna mengelola target pemakaian barang secara disiplin.

---

### 2.6. Sanksi Keterlambatan dan Penalti Harian

#### A. Desain Sistem & UX
* **Frontend**: Notifikasi *push* / email pengingat pada $H-1$, jam $H$, dan saat terlambat (*overdue*). Muncul rincian akumulasi penalti harian di kartu peminjaman.
* **Backend**: Perhitungan denda otomatis berbasis tanggal:
  $$\text{Denda Total} = \max(0, \lceil \text{Tanggal Kembali} - \text{Tenggat Waktu} \rceil) \times \text{Tarif Denda}$$

#### B. Isi Aturan
> *"Keterlambatan pengembalian dikenakan denda administratif sebesar Rp $[X]$/hari atau penangguhan hak akses peminjaman selama $[X]$ hari per hari keterlambatan."*

#### C. Reason (Justifikasi & Analisis Risiko)
1. **Efek Jera (*Deterrence*)**: Tanpa penalti finansial atau fungsional, kepatuhan batas waktu pengembalian akan sangat rendah.
2. **Kompensasi Operasional**: Biaya denda menutup sebagian beban administratif atau kerugian hilangnya utilitas barang bagi organisasi.

---

### 2.7. Ganti Rugi Kerusakan dan Kehilangan

#### A. Desain Sistem & UX
* **Frontend**: Alur *Incident Report* bagi petugas penerima barang dengan fasilitas unggah bukti foto kerusakan, pemilihan kategori (Ringan / Berat / Total Hilang), dan penaksiran estimasi biaya.
* **Backend**: Tiket sanksi di-generate ke profil peminjam, memblokir pembuatan transaksi baru hingga status kasus diubah oleh admin menjadi `RESOLVED`.

#### B. Isi Aturan
> *"Peminjam bertanggung jawab penuh atas keutuhan aset. Kerusakan akibat kelalaian operasional atau kehilangan unit wajib diganti dengan biaya perbaikan resmi atau unit pengganti yang berspesifikasi identik/setara."*

#### C. Reason (Justifikasi & Analisis Risiko)
1. **Keberlanjutan Siklus Hidup Aset (*Asset Lifecycle*)**: Melindungi anggaran modal organisasi dari penyusutan dini akibat kelalaian pengguna.
2. **Kewaspadaan Penggunaan**: Menjamin peminjam memperlakukan aset pinjaman dengan standar kehati-hatian yang sama seperti aset pribadi.