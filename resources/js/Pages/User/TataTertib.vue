<script setup>
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import UserLayout from '@/Layouts/UserLayout.vue';
import {
  ShieldCheck,
  Target,
  BookOpen,
  FileText,
  ClipboardCheck,
  Ban,
  AlertTriangle,
  ChevronDown,
} from '@lucide/vue';

defineOptions({ layout: UserLayout });

const openItems = ref(['tujuan']); // Default open

const toggleItem = (id) => {
  const idx = openItems.value.indexOf(id);
  if (idx >= 0) {
    openItems.value.splice(idx, 1);
  } else {
    openItems.value.push(id);
  }
};

const isOpen = (id) => openItems.value.includes(id);

const sections = [
  {
    id: 'tujuan',
    title: 'Tujuan & Ruang Lingkup',
    icon: Target,
    color: '#007AFF',
    content: [
      'Tata Tertib ini bertujuan untuk mengatur tata cara dan ketentuan peminjaman sarana dan prasarana (ruangan dan barang inventaris) di lingkungan Sekolah Tinggi Teknologi (STITEK) Bontang.',
      'Ruang lingkup mencakup seluruh ruangan gedung utama dan laboratorium, serta seluruh barang inventaris yang terdata dalam sistem SiPinjam.',
      'Berlaku untuk seluruh civitas akademika STITEK Bontang meliputi dosen, mahasiswa, tenaga kependidikan, dan pihak lain yang mendapat izin resmi.',
    ],
  },
  {
    id: 'ketentuan',
    title: 'Ketentuan Umum',
    icon: BookOpen,
    color: '#FFE500',
    content: [
      'Peminjam wajib terdaftar dalam sistem SiPinjam dengan akun aktif yang diverifikasi oleh admin kampus.',
      'Peminjaman hanya dapat dilakukan melalui aplikasi SiPinjam — tidak menerima pengajuan lisan atau manual.',
      'Setiap peminjaman memerlukan persetujuan dari Biro Administrasi sebelum dapat digunakan.',
      'Aset yang dipinjam harus digunakan sesuai dengan keperluan yang tercantum dalam formulir pengajuan.',
      'Peminjam bertanggung jawab penuh atas kondisi aset selama masa peminjaman hingga pengembalian.',
    ],
  },
  {
    id: 'sop-pengajuan',
    title: 'SOP Pengajuan',
    icon: FileText,
    color: '#30D158',
    content: [
      'Login ke SiPinjam menggunakan akun institusi (@stitek.ac.id) atau akun yang terdaftar.',
      'Pilih jenis aset yang ingin dipinjam (Ruangan atau Barang Inventaris) dari katalog.',
      'Isi formulir pengajuan: tanggal mulai, tanggal selesai, waktu, dan keperluan penggunaan.',
      'Pengajuan akan masuk antrian persetujuan admin. Status dapat dipantau secara real-time di dashboard.',
      'Pengajuan yang tidak diproses dalam 48 jam akan otomatis ditolak oleh sistem (SLA Auto-Reject).',
      'Setelah disetujui, peminjam dapat mengunduh Surat Peminjaman resmi dalam format PDF.',
    ],
  },
  {
    id: 'sop-penggunaan',
    title: 'SOP Penggunaan',
    icon: ClipboardCheck,
    color: '#007AFF',
    content: [
      'Penggunaan aset harus sesuai dengan jadwal yang telah disetujui — tidak boleh melebihi batas waktu.',
      'Jam operasional kampus: 07:00 – 22:00 WITA. Penggunaan di luar jam operasional tidak diperkenankan.',
      'Peminjam wajib menjaga kebersihan, ketertiban, dan keutuhan ruangan/barang selama penggunaan.',
      'Setelah selesai menggunakan, pastikan ruangan dalam keadaan rapi dan barang dikembalikan ke kondisi semula.',
      'Laporkan segera jika terjadi kerusakan atau kehilangan aset kepada admin melalui sistem.',
    ],
  },
  {
    id: 'larangan',
    title: 'Larangan',
    icon: Ban,
    color: '#FF3B30',
    content: [
      'Dilarang menggunakan aset untuk kepentingan pribadi atau komersial di luar kegiatan akademik.',
      'Dilarang memindahkan barang inventaris ke lokasi lain tanpa izin tertulis dari admin.',
      'Dilarang merusak, mencoret, atau mengubah kondisi ruangan dan barang kampus.',
      'Dilarang meminjamkan kembali aset yang sudah dipinjam kepada pihak ketiga.',
      'Dilarang membawa makanan/minuman berat ke dalam ruangan laboratorium.',
    ],
  },
  {
    id: 'sanksi',
    title: 'Sanksi',
    icon: AlertTriangle,
    color: '#FF3B30',
    content: [
      'Keterlambatan pengembalian barang/ruangan yang melebihi batas waktu (overtime) akan mengakibatkan pemblokiran akun selama 30 hari.',
      'Pelaporan ruangan dalam kondisi berantakan oleh admin akan menjatuhkan sanksi blokir otomatis 30 hari kepada peminjam terakhir.',
      'Selama masa blokir, pengguna tidak dapat mengakses fitur peminjaman di SiPinjam.',
      'Akun akan otomatis di-unblock setelah masa 30 hari terlewati.',
      'Kerusakan atau kehilangan aset wajib diganti oleh peminjam sesuai ketentuan yang berlaku.',
      'Pelanggaran berulang dapat mengakibatkan pencabutan hak akses secara permanen melalui keputusan Biro Administrasi.',
    ],
  },
];
</script>

<template>
  <Head title="Tata Tertib Peminjaman" />

  <div class="max-w-7xl mx-auto px-8 py-10 lg:px-12 flex flex-col gap-8">
    <!-- Header -->
    <div class="mb-8">
      <div class="flex items-center gap-3 mb-2">
        <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center">
          <ShieldCheck class="h-5 w-5 text-primary" />
        </div>
        <div>
          <h1 class="text-2xl font-bold tracking-tight text-foreground">Tata Tertib Peminjaman</h1>
          <p class="text-xs text-muted-foreground">
            Panduan dan ketentuan resmi peminjaman aset kampus STITEK Bontang
          </p>
        </div>
      </div>
    </div>

    <!-- Accordion Sections (Clean Enterprise) -->
    <div class="space-y-4">
      <div
        v-for="section in sections"
        :key="section.id"
        class="bg-card border border-border rounded-xl shadow-card overflow-hidden transition-all duration-200"
      >
        <!-- Accordion Header -->
        <button
          @click="toggleItem(section.id)"
          class="w-full flex items-center gap-4 p-5 text-left transition-all duration-200 hover:bg-muted/50 group"
          :id="'accordion-' + section.id"
        >
          <div
            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg"
            :style="{ backgroundColor: section.color + '18' }"
          >
            <component :is="section.icon" class="h-[18px] w-[18px]" :style="{ color: section.color }" />
          </div>

          <span class="flex-1 text-sm font-semibold text-foreground">
            {{ section.title }}
          </span>

          <ChevronDown
            :class="[
              'h-4 w-4 text-muted-foreground transition-transform duration-200 shrink-0',
              isOpen(section.id) ? 'rotate-180' : ''
            ]"
          />
        </button>

        <!-- Accordion Content -->
        <Transition
          enter-active-class="transition-all duration-300 ease-out"
          enter-from-class="max-h-0 opacity-0"
          enter-to-class="max-h-[1000px] opacity-100"
          leave-active-class="transition-all duration-200 ease-in"
          leave-from-class="max-h-[1000px] opacity-100"
          leave-to-class="max-h-0 opacity-0"
        >
          <div v-show="isOpen(section.id)" class="overflow-hidden">
            <div class="border-t border-border px-5 py-5">
              <ul class="space-y-3">
                <li
                  v-for="(item, idx) in section.content"
                  :key="idx"
                  class="flex items-start gap-3"
                >
                  <span
                    class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-md text-[10px] font-bold text-white"
                    :style="{ backgroundColor: section.color }"
                  >
                    {{ idx + 1 }}
                  </span>
                  <p class="text-sm text-muted-foreground leading-relaxed">{{ item }}</p>
                </li>
              </ul>
            </div>
          </div>
        </Transition>
      </div>
    </div>

    <!-- Tagline -->
    <div class="text-center pt-4">
      <p class="text-xs tracking-widest uppercase text-muted-foreground font-medium">
        The Knowledgeable and Virtue Campus — STITEK Bontang
      </p>
    </div>
  </div>
</template>

