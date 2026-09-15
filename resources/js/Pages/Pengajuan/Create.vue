<script setup>
import { ref, computed, watch, onMounted, onBeforeUnmount, defineEmits } from 'vue';
import { Head, usePage, Link } from '@inertiajs/vue3';
import { v4 as uuidv4 } from 'uuid';
import AppShell from '@/Layouts/AppShell.vue';
import { Button, Input, Select, Form, FormGroup, FormLabel, FormDescription } from '@/components/ui';
import { Card, CardHeader, CardTitle, CardContent, CardFooter } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Table, TableRow, TableHead, TableBody, TableCell, TableHeader } from '@/components/ui/table';
import { Alert, AlertTitle, AlertDescription } from '@/components/ui/alert';
import { LoadingButton } from '@/components/ui/loading-button';
import { useToast } from '@/composables/use-toast';
import { useForm } from '@inertiajs/vue3';
import { useRoute } from 'vue-router';

import { RuanganIcon, BarangIcon, CalendarIcon, ClockIcon, CheckCircle2, XCircle, Search, Folder, Upload, Menu, MoreHorizontal } from '@lucide/vue';

const emit = defineEmits(['close']);
const route = useRoute();
const { toast } = useToast();

// ── State ───────────────────────────────────────
const step = ref(1);
const form = useForm({
  jenis: 'tunggal',
  nama_kegiatan: '',
  unit_penyelenggara: '',
  jumlah_peserta: '',
  pj_nama: '',
  pj_phone: '',
  deskripsi: '',
  lampiran_proposal: null,
  mulai_at: '',
  selesai_at: '',
});

const keranjang = ref([]);
const isSubmitting = ref(false);

// ── Langkah 1: Jenis pengajuan ──────────────────
const jenisOptions = [
  { value: 'tunggal', label: 'Aset Tunggal', description: '1 ruangan/barang saja' },
  { value: 'event', label: 'Paket Kegiatan/Event', description: 'Banyak aset dalam satu pengajuan' },
];

const setJenis = (value) => {
  form.jenis = value;
  if (value === 'tunggal') {
    form.nama_kegiatan = '';
    form.unit_penyelenggara = '';
    form.jumlah_peserta = '';
    form.pj_nama = '';
    form.pj_phone = '';
    form.deskripsi = '';
    form.lampiran_proposal = null;
  }
  step.value = 2;
};

// ── Langkah 2: Identitas (hanya untuk event) ────
const setIdentitas = (data) => {
  form.nama_kegiatan = data.nama_kegiatan ?? '';
  form.unit_penyelenggara = data.unit_penyelenggara ?? '';
  form.jumlah_peserta = data.jumlah_peserta ?? '';
  form.pj_nama = data.pj_nama ?? '';
  form.pj_phone = data.pj_phone ?? '';
  form.deskripsi = data.deskripsi ?? '';
  form.lampiran_proposal = data.lampiran_proposal ?? null;
  step.value = 3;
};

// ── Langkah 3: Jadwal ───────────────────────────
const setJadwal = (data) => {
  form.mulai_at = data.mulai_at;
  form.selesai_at = data.selesai_at;
  step.value = 4;
};

// ── Langkah 4: Keranjang ASET ───────────────────
const addToKeranjang = (asetType, asetId, asetNama) => {
  const idx = keranjang.value.findIndex(i => i.assetable_id === asetId && i.assetable_type === asetType);

  if (idx >= 0) {
    keranjang.value[idx].jumlah += 1;
  } else {
    keranjang.value.push({
      id: uuidv4(),
      assetable_type: asetType,
      assetable_id: asetId,
      nama_aset: asetNama,
      jumlah: 1,
    });
  }
};

const removeFromKeranjang = (id) => {
  keranjang.value = keranjang.value.filter(i => i.id !== id);
};

// Cek ketersediaan (dengan debounce)
const checkKetersediaan = (asetType, asetId, mulai, selesai) => {
  // Will be replaced by API call in production
  // For now, just mark as available
};

// ── Langkah 5: Ringkasan ────────────────────────
const canSubmit = computed(() => keranjang.value.length > 0);

const formStep5 = ref({
  tataTertib: false,
});

// ── Navigation ──────────────────────────────────
const nextStep = () => {
  if (step.value < 5) {
    step.value++;
  }
};

const prevStep = () => {
  if (step.value > 1) {
    step.value--;
  }
};

const submitForm = async () => {
  isSubmitting.value = true;

  // Validasi setiap item di keranjang
  let semuaValid = true;
  keranjang.value.forEach((item) => {
    if (item.jumlah > item.tersedia) {
      semuaValid = false;
      toast({
        title: 'Ketersediaan tidak mencukup',
        description: `Baris: hanya ${item.tersedia} dari ${item.jumlah} unit ${item.nama_aset} tersedia`,
        variant: 'destructive',
      });
    }
  });

  if (!semuaValid) {
    isSubmitting.value = false;
    return;
  }

  try {
    await form.post('/pengajuan/store', {
      ...form,
      tata_tertib: formStep5.value.tataTertib,
      items: keranjang.value.map(item => ({
        assetable_type: item.assetable_type,
        assetable_id: item.assetable_id,
        jumlah: item.jumlah,
        mulai_at: item.mulai_at,
        selesai_at: item.selesai_at,
        catatan: item.catatan || '',
      })),
    });
    toast({
      title: 'Berhasil!',
      description: 'Pengajuan # berhasil diajukan. Cek dashboard untuk status selanjutnya.',
    });
    // Reset
    step.value = 1;
    keranjang.value = [];
    form.jenis = 'tunggal';
    form.nama_kegiatan = '';
    form.unit_penyelenggara = '';
    form.jumlah_peserta = '';
    form.pj_nama = '';
    form.pj_phone = '';
    form.deskripsi = '';
    form.lampiran_proposal = null;
    form.mulai_at = '';
    form.selesai_at = '';
    formStep5.value = { tataTertib: false };
    isSubmitting.value = false;
  } catch (e) {
    toast({
      title: 'Gagal',
      description: e.message || 'Terjadi kesalahan saat mengajukan pengajuan',
      variant: 'destructive',
    });
    isSubmitting.value = false;
  }
};