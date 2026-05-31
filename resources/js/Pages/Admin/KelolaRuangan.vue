<script setup>
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { DoorOpen, Plus, Pencil, Trash2, X, AlertTriangle, MessageSquare, Image as ImageIcon } from '@lucide/vue';

defineOptions({ layout: AdminLayout });

const props = defineProps({ ruangans: Array });

const showForm = ref(false);
const editingId = ref(null);
const imagePreview = ref(null);
const existingImage = ref(null);

const form = useForm({
    nama: '', kode: '', kapasitas: '', lokasi: '', deskripsi: '', status: 'tersedia', image_path: null,
});

const openCreate = () => { 
    editingId.value = null; 
    form.reset(); 
    imagePreview.value = null;
    existingImage.value = null;
    showForm.value = true; 
};
const openEdit = (r) => {
    editingId.value = r.id;
    form.nama = r.nama; form.kode = r.kode; form.kapasitas = r.kapasitas;
    form.lokasi = r.lokasi || ''; form.deskripsi = r.deskripsi || ''; form.status = r.status;
    form.image_path = null;
    imagePreview.value = null;
    existingImage.value = r.image_path || null;
    showForm.value = true;
};
const close = () => { 
    showForm.value = false; 
    form.reset(); 
    editingId.value = null; 
    imagePreview.value = null;
    existingImage.value = null;
};

const handleFileChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.image_path = file;
        imagePreview.value = URL.createObjectURL(file);
    } else {
        form.image_path = null;
        imagePreview.value = null;
    }
};

const submit = () => {
    if (editingId.value) {
        form.transform((data) => ({
            ...data,
            _method: 'put',
        })).post(`/admin/kelola-ruangan/${editingId.value}`, { onSuccess: close, preserveScroll: true });
    } else {
        form.post('/admin/kelola-ruangan', { onSuccess: close, preserveScroll: true });
    }
};
const destroy = (id) => { if (confirm('Hapus ruangan ini?')) router.delete(`/admin/kelola-ruangan/${id}`, { preserveScroll: true }); };

// ── Feedback Modal for Lapor Berantakan ────────────
const showLaporModal = ref(false);
const laporTarget = ref({ id: null, nama: '' });
const laporForm = useForm({ feedback: '' });

const openLaporModal = (id, nama) => {
    laporTarget.value = { id, nama };
    laporForm.reset();
    showLaporModal.value = true;
};

const closeLaporModal = () => {
    showLaporModal.value = false;
    laporTarget.value = { id: null, nama: '' };
    laporForm.reset();
};

const submitLapor = () => {
    laporForm.post(`/admin/kelola-ruangan/${laporTarget.value.id}/lapor-berantakan`, {
        preserveScroll: true,
        onSuccess: closeLaporModal,
    });
};
</script>

<template>
    <Head title="Kelola Ruangan" />
    <div class="px-6 py-8 lg:px-10">
        <div class="mb-6 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <DoorOpen class="h-5 w-5 text-orange-500" />
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Kelola Ruangan</h1>
            </div>
            <button @click="openCreate" class="inline-flex items-center gap-1.5 rounded-lg bg-orange-600 px-4 py-2 text-sm font-semibold text-white hover:bg-orange-700 transition-colors shadow-sm">
                <Plus class="h-4 w-4" /> Tambah
            </button>
        </div>

        <!-- Inline Form -->
        <div v-if="showForm" class="mb-6 rounded-2xl border border-orange-200 bg-orange-50/30 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-bold text-slate-800">{{ editingId ? 'Edit Ruangan' : 'Tambah Ruangan Baru' }}</h2>
                <button @click="close" class="text-slate-400 hover:text-slate-600 transition-colors"><X class="h-4 w-4" /></button>
            </div>
            <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><label class="text-xs font-semibold text-slate-600">Nama</label><input v-model="form.nama" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500" /></div>
                <div><label class="text-xs font-semibold text-slate-600">Kode</label><input v-model="form.kode" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500" /></div>
                <div><label class="text-xs font-semibold text-slate-600">Kapasitas</label><input v-model="form.kapasitas" type="number" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500" /></div>
                <div><label class="text-xs font-semibold text-slate-600">Lokasi</label><input v-model="form.lokasi" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500" /></div>
                <div class="md:col-span-2"><label class="text-xs font-semibold text-slate-600">Deskripsi</label><textarea v-model="form.deskripsi" rows="2" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 resize-none" /></div>
                <div><label class="text-xs font-semibold text-slate-600">Status</label>
                    <select v-model="form.status" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                        <option value="tersedia">Tersedia</option><option value="tidak_tersedia">Tidak Tersedia</option>
                    </select>
                </div>
                <div class="flex gap-4 items-end">
                    <div class="flex-1">
                        <label class="text-xs font-semibold text-slate-600">Unggah Foto Baru</label>
                        <input type="file" @change="handleFileChange" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-1.5 text-sm focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 bg-white" accept="image/png, image/jpeg, image/jpg" />
                        <p v-if="form.errors.image_path" class="text-xs text-red-500 mt-1">{{ form.errors.image_path }}</p>
                    </div>
                    <div class="h-10 w-16 shrink-0 overflow-hidden rounded-lg border border-slate-200 bg-slate-50 flex items-center justify-center">
                        <img v-if="imagePreview" :src="imagePreview" class="h-full w-full object-cover" />
                        <img v-else-if="existingImage" :src="existingImage" class="h-full w-full object-cover" />
                        <div v-else class="text-slate-300">
                            <ImageIcon class="h-5 w-5" />
                        </div>
                    </div>
                </div>
                <div class="flex items-end">
                    <button type="submit" :disabled="form.processing" class="rounded-lg bg-orange-600 px-6 py-2 text-sm font-semibold text-white hover:bg-orange-700 disabled:opacity-50 transition-colors shadow-sm">{{ form.processing ? 'Menyimpan...' : 'Simpan' }}</button>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm">
            <table class="w-full text-sm">
                <thead class="border-b border-slate-100 bg-slate-50/80">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Kode</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Foto</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Nama</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Lokasi</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Kapasitas</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Status</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-slate-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr v-for="r in ruangans" :key="r.id" class="hover:bg-slate-50/60 transition-colors">
                        <td class="px-5 py-3.5 font-mono text-xs text-slate-500">{{ r.kode }}</td>
                        <td class="px-5 py-3.5">
                            <div class="h-10 w-16 overflow-hidden rounded-lg border border-slate-100 bg-slate-50">
                                <img v-if="r.image_path" :src="r.image_path" :alt="r.nama" class="h-full w-full object-cover" />
                                <div v-else class="flex h-full w-full items-center justify-center text-slate-300">
                                    <ImageIcon class="h-4 w-4" />
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3.5 font-semibold text-slate-800">{{ r.nama }}</td>
                        <td class="px-5 py-3.5 text-slate-600">{{ r.lokasi || '-' }}</td>
                        <td class="px-5 py-3.5 text-slate-600">{{ r.kapasitas }}</td>
                        <td class="px-5 py-3.5">
                            <span v-if="r.is_terpakai" class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-[10px] font-semibold bg-rose-50 text-rose-700 border-rose-200">
                                <span class="mr-1 h-1.5 w-1.5 rounded-full bg-rose-500 inline-block animate-pulse" />
                                Sedang Terpakai
                            </span>
                            <span v-else :class="['inline-flex items-center rounded-full border px-2.5 py-0.5 text-[10px] font-semibold', r.status === 'tersedia' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-red-50 text-red-700 border-red-200']">
                                <span v-if="r.status === 'tersedia'" class="mr-1 h-1.5 w-1.5 rounded-full bg-emerald-500 inline-block" />
                                {{ r.status === 'tersedia' ? 'Tersedia' : 'Tidak Tersedia' }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button @click="openEdit(r)" class="rounded-lg p-1.5 text-slate-400 hover:bg-orange-50 hover:text-orange-600 transition-colors" title="Edit"><Pencil class="h-4 w-4" /></button>
                                <button @click="destroy(r.id)" class="rounded-lg p-1.5 text-slate-400 hover:bg-red-50 hover:text-red-600 transition-colors" title="Hapus"><Trash2 class="h-4 w-4" /></button>
                                <button
                                  @click="openLaporModal(r.id, r.nama)"
                                  class="inline-flex items-center gap-1 rounded-lg border border-red-200 bg-red-50 px-2 py-1 text-[10px] font-bold text-red-700 uppercase hover:bg-red-500 hover:text-white transition-all duration-200"
                                  title="Lapor Ruangan Berantakan"
                                >
                                  <AlertTriangle class="h-3 w-3" />
                                  Lapor
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="!ruangans.length"><td colspan="7" class="px-5 py-12 text-center text-slate-400">Belum ada data ruangan.</td></tr>
                </tbody>
            </table>
        </div>

        <!-- ── Lapor Feedback Modal ─────────────────── -->
        <Teleport to="body">
          <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
          >
            <div v-if="showLaporModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
              <div class="w-full max-w-md rounded-2xl border border-border bg-white shadow-lg" @click.stop>
                <!-- Header -->
                <div class="flex items-center gap-3 border-b border-slate-100 px-6 py-4">
                  <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-red-50 text-red-600">
                    <AlertTriangle class="h-5 w-5" />
                  </div>
                  <div>
                    <h3 class="text-base font-bold text-slate-900">Lapor Ruangan Berantakan</h3>
                    <p class="text-xs text-slate-500">{{ laporTarget.nama }}</p>
                  </div>
                </div>

                <!-- Body -->
                <form @submit.prevent="submitLapor" class="px-6 py-5 space-y-4">
                  <div class="rounded-lg bg-amber-50 border border-amber-200 px-3 py-2.5 text-xs text-amber-800 flex items-start gap-2">
                    <AlertTriangle class="h-3.5 w-3.5 mt-0.5 shrink-0 text-amber-600" />
                    <span>Peminjam terakhir hari ini akan diblokir selama <strong>30 hari</strong>. Pastikan Anda sudah memverifikasi kondisi ruangan.</span>
                  </div>

                  <div class="space-y-2">
                    <label class="text-xs font-semibold text-slate-600 flex items-center gap-1.5">
                      <MessageSquare class="h-3.5 w-3.5 text-slate-400" />
                      Catatan Pelanggaran (Feedback)
                    </label>
                    <textarea
                      v-model="laporForm.feedback"
                      rows="3"
                      placeholder="Jelaskan kondisi ruangan yang ditemukan (misal: kursi berantakan, AC tidak dimatikan, sampah berserakan)..."
                      class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm placeholder:text-slate-400 focus:ring-2 focus:ring-red-500/20 focus:border-red-500 resize-none"
                      required
                    />
                    <p v-if="laporForm.errors.feedback" class="text-xs text-red-500">{{ laporForm.errors.feedback }}</p>
                  </div>

                  <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" @click="closeLaporModal" class="rounded-lg px-4 py-2 text-sm font-medium text-slate-500 hover:text-slate-700 transition-colors">
                      Batal
                    </button>
                    <button type="submit" :disabled="laporForm.processing || !laporForm.feedback"
                      class="inline-flex items-center gap-1.5 rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700 disabled:opacity-50 transition-colors">
                      <AlertTriangle class="h-4 w-4" />
                      {{ laporForm.processing ? 'Memproses...' : 'Laporkan & Blokir' }}
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </Transition>
        </Teleport>
    </div>
</template>
