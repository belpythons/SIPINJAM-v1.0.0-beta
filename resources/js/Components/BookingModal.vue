<script setup>
import { ref, computed, watch } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import {
  CalendarDays, Clock, User, Mail, FileText, ChevronLeft, ChevronRight,
  CheckCircle2, AlertCircle, Loader2, Building2, Package,
} from '@lucide/vue';
import {
  Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle,
} from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Badge } from '@/components/ui/badge';
import { Separator } from '@/components/ui/separator';
import {
  Select, SelectContent, SelectGroup, SelectItem, SelectLabel, SelectTrigger, SelectValue,
} from '@/components/ui/select';

const props = defineProps({
  open: Boolean,
  asset: Object,
  prefillDates: { type: Object, default: null },
  ruangans: { type: Array, default: () => [] },
  barangs: { type: Array, default: () => [] },
});

const emit = defineEmits(['update:open']);
const page = usePage();
const user = computed(() => page.props.auth?.user);

const currentStep = ref(1);
const agreedToRules = ref(false);

const form = useForm({
  tipe_peminjaman: '',
  barang_id: null,
  ruangan_id: null,
  tanggal_mulai: '',
  tanggal_selesai: '',
  waktu_mulai: '',
  waktu_selesai: '',
  keterangan: '',
  catatan: '',
});

const keperluanOptions = [
  'Rapat Himpunan', 'Perkuliahan Pengganti', 'Sidang Skripsi',
  'Praktikum', 'Seminar / Workshop', 'Kegiatan UKM', 'Lainnya',
];

// Pre-selected asset means type+item are locked
const hasPreselectedAsset = computed(() => !!props.asset);
const isRuangan = computed(() => form.tipe_peminjaman === 'ruangan');

const selectedAssetName = computed(() => {
  if (isRuangan.value && form.ruangan_id) {
    return props.ruangans.find(r => String(r.id) === String(form.ruangan_id))?.nama || '';
  }
  if (!isRuangan.value && form.barang_id) {
    return props.barangs.find(b => String(b.id) === String(form.barang_id))?.nama || '';
  }
  return '';
});

const todayString = computed(() => new Date().toISOString().split('T')[0]);

// ── Step 1 Validation ──────────────────────────────
const isTimeValid = computed(() => {
  if (!form.waktu_mulai || !form.waktu_selesai) return false;
  const [sh, sm] = form.waktu_mulai.split(':').map(Number);
  const [eh, em] = form.waktu_selesai.split(':').map(Number);
  const s = sh * 60 + sm, e = eh * 60 + em;
  return s >= 420 && s <= 1320 && e >= 420 && e <= 1320 && e > s;
});

const timeWarning = computed(() => {
  if (!form.waktu_mulai || !form.waktu_selesai) return '';
  const [sh, sm] = form.waktu_mulai.split(':').map(Number);
  const [eh, em] = form.waktu_selesai.split(':').map(Number);
  const s = sh * 60 + sm, e = eh * 60 + em;
  if (s < 420 || s > 1320 || e < 420 || e > 1320)
    return 'Waktu harus dalam jam operasional kampus (07:00 - 22:00 WITA).';
  if (e <= s) return 'Waktu selesai harus lebih lambat dari waktu mulai.';
  return '';
});

const step1Valid = computed(() =>
  form.tanggal_mulai && form.tanggal_selesai &&
  form.waktu_mulai && form.waktu_selesai &&
  form.tanggal_selesai >= form.tanggal_mulai && isTimeValid.value
);

// ── Step 2 Validation ──────────────────────────────
const step2Valid = computed(() => {
  if (!form.tipe_peminjaman) return false;
  if (form.tipe_peminjaman === 'ruangan' && !form.ruangan_id) return false;
  if (form.tipe_peminjaman === 'barang' && !form.barang_id) return false;
  return true;
});

// ── Step 3 Validation ──────────────────────────────
const step3Valid = computed(() => form.keterangan && agreedToRules.value);

// ── Reset on open ──────────────────────────────────
watch(() => props.open, (isOpen) => {
  if (isOpen) {
    currentStep.value = 1;
    agreedToRules.value = false;
    form.reset();
    form.clearErrors();
    if (props.asset) {
      form.tipe_peminjaman = props.asset.tipe;
      if (props.asset.tipe === 'barang') form.barang_id = String(props.asset.id);
      else form.ruangan_id = String(props.asset.id);
    }
    if (props.prefillDates) {
      form.tanggal_mulai = props.prefillDates.start;
      const endDate = new Date(props.prefillDates.end);
      endDate.setDate(endDate.getDate() - 1);
      form.tanggal_selesai = endDate.toISOString().split('T')[0];
    }
  }
});

const goToStep = (n) => { currentStep.value = n; };

const submit = () => {
  form.post('/bookings', {
    preserveScroll: true,
    onSuccess: () => emit('update:open', false),
  });
};

const closeModal = () => emit('update:open', false);

const stepLabels = ['Jadwal', 'Aset', 'Detail'];
</script>

<template>
  <Dialog :open="open" @update:open="closeModal">
    <DialogContent class="sm:max-w-lg max-h-[90vh] overflow-y-auto p-0" style="z-index: 60;">
      <form @submit.prevent="submit" class="flex flex-col">
        <!-- ── Header ─────────────────────────────────── -->
        <DialogHeader class="px-6 pt-6 pb-0">
          <div class="flex items-center gap-3 mb-1">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
              <Building2 v-if="isRuangan" class="h-5 w-5" />
              <Package v-else class="h-5 w-5" />
            </div>
            <div class="min-w-0">
              <DialogTitle class="text-base font-bold text-slate-900">
                Formulir Peminjaman
              </DialogTitle>
              <DialogDescription class="text-xs text-slate-500">
                {{ hasPreselectedAsset ? (props.asset?.nama || '') : 'Pilih aset yang ingin dipinjam' }}
              </DialogDescription>
            </div>
          </div>

          <!-- Step Indicator (3 steps) -->
          <div class="flex items-center gap-1 pt-4 pb-2">
            <template v-for="(label, idx) in stepLabels" :key="idx">
              <div class="flex items-center gap-1.5">
                <div :class="[
                  'flex h-7 w-7 items-center justify-center rounded-full text-xs font-bold transition-colors',
                  currentStep > idx + 1 ? 'bg-emerald-500 text-white' :
                  currentStep === idx + 1 ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-400',
                ]">
                  <CheckCircle2 v-if="currentStep > idx + 1" class="h-4 w-4" />
                  <span v-else>{{ idx + 1 }}</span>
                </div>
                <span :class="['text-xs font-medium', currentStep >= idx + 1 ? 'text-slate-700' : 'text-slate-400']">
                  {{ label }}
                </span>
              </div>
              <div v-if="idx < 2" :class="['h-px flex-1 mx-1', currentStep > idx + 1 ? 'bg-emerald-400' : 'bg-slate-200']" />
            </template>
          </div>
        </DialogHeader>

        <Separator />

        <!-- Global Error -->
        <div v-if="form.errors.booking" class="mx-6 mt-4 flex items-center gap-2 rounded-lg border border-red-200 bg-red-50 px-3 py-2.5 text-sm text-red-700">
          <AlertCircle class="h-4 w-4 shrink-0" />
          {{ form.errors.booking }}
        </div>

        <!-- ── STEP 1: Jadwal ─────────────────────────── -->
        <div v-if="currentStep === 1" class="px-6 py-5 space-y-5">
          <div class="grid grid-cols-2 gap-4">
            <div class="space-y-2">
              <Label for="tanggal_mulai" class="text-xs font-semibold text-slate-600 flex items-center gap-1.5">
                <CalendarDays class="h-3.5 w-3.5 text-slate-400" /> Tanggal Mulai
              </Label>
              <Input id="tanggal_mulai" v-model="form.tanggal_mulai" type="date" :min="todayString" class="text-sm" />
              <p v-if="form.errors.tanggal_mulai" class="text-xs text-red-500">{{ form.errors.tanggal_mulai }}</p>
            </div>
            <div class="space-y-2">
              <Label for="tanggal_selesai" class="text-xs font-semibold text-slate-600 flex items-center gap-1.5">
                <CalendarDays class="h-3.5 w-3.5 text-slate-400" /> Tanggal Selesai
              </Label>
              <Input id="tanggal_selesai" v-model="form.tanggal_selesai" type="date" :min="form.tanggal_mulai || todayString" class="text-sm" />
              <p v-if="form.errors.tanggal_selesai" class="text-xs text-red-500">{{ form.errors.tanggal_selesai }}</p>
            </div>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div class="space-y-2">
              <Label for="waktu_mulai" class="text-xs font-semibold text-slate-600 flex items-center gap-1.5 justify-between">
                <span class="flex items-center gap-1.5"><Clock class="h-3.5 w-3.5 text-slate-400" /> Waktu Mulai</span>
                <span class="text-[9px] text-slate-400 font-normal">24 Jam / WITA</span>
              </Label>
              <Input id="waktu_mulai" v-model="form.waktu_mulai" type="time" class="text-sm" />
              <p v-if="form.errors.waktu_mulai" class="text-xs text-red-500">{{ form.errors.waktu_mulai }}</p>
            </div>
            <div class="space-y-2">
              <Label for="waktu_selesai" class="text-xs font-semibold text-slate-600 flex items-center gap-1.5 justify-between">
                <span class="flex items-center gap-1.5"><Clock class="h-3.5 w-3.5 text-slate-400" /> Waktu Selesai</span>
                <span class="text-[9px] text-slate-400 font-normal">24 Jam / WITA</span>
              </Label>
              <Input id="waktu_selesai" v-model="form.waktu_selesai" type="time" class="text-sm" />
              <p v-if="form.errors.waktu_selesai" class="text-xs text-red-500">{{ form.errors.waktu_selesai }}</p>
            </div>
          </div>
          <div v-if="timeWarning" class="rounded-lg bg-amber-50 border border-amber-200 px-3 py-2.5 text-xs text-amber-800 flex items-start gap-2">
            <AlertCircle class="h-3.5 w-3.5 mt-0.5 shrink-0 text-amber-600" />
            <span>{{ timeWarning }}</span>
          </div>
          <div class="rounded-lg bg-blue-50/60 border border-blue-100 px-3 py-2.5 text-xs text-blue-700 flex items-start gap-2">
            <AlertCircle class="h-3.5 w-3.5 mt-0.5 shrink-0 text-blue-500" />
            <span>Jam Operasional Kampus: 07:00 - 22:00 WITA.</span>
          </div>
        </div>

        <!-- ── STEP 2: Pilih Tipe & Aset ──────────────── -->
        <div v-if="currentStep === 2" class="px-6 py-5 space-y-5">
          <!-- Tipe Peminjaman -->
          <div class="space-y-2">
            <Label class="text-xs font-semibold text-slate-600">Tipe Peminjaman</Label>
            <div class="grid grid-cols-2 gap-3">
              <button type="button" @click="() => { if (!hasPreselectedAsset) { form.tipe_peminjaman = 'ruangan'; form.barang_id = null; } }"
                :disabled="hasPreselectedAsset"
                :class="['flex flex-col items-center gap-2 rounded-xl border-2 p-4 transition-all duration-200',
                  form.tipe_peminjaman === 'ruangan' ? 'border-indigo-500 bg-indigo-50/50' : 'border-slate-200 hover:border-slate-300',
                  hasPreselectedAsset ? 'opacity-70 cursor-not-allowed' : 'cursor-pointer']">
                <Building2 :class="['h-6 w-6', form.tipe_peminjaman === 'ruangan' ? 'text-indigo-600' : 'text-slate-400']" />
                <span :class="['text-xs font-semibold', form.tipe_peminjaman === 'ruangan' ? 'text-indigo-700' : 'text-slate-500']">Ruangan</span>
              </button>
              <button type="button" @click="() => { if (!hasPreselectedAsset) { form.tipe_peminjaman = 'barang'; form.ruangan_id = null; } }"
                :disabled="hasPreselectedAsset"
                :class="['flex flex-col items-center gap-2 rounded-xl border-2 p-4 transition-all duration-200',
                  form.tipe_peminjaman === 'barang' ? 'border-sky-500 bg-sky-50/50' : 'border-slate-200 hover:border-slate-300',
                  hasPreselectedAsset ? 'opacity-70 cursor-not-allowed' : 'cursor-pointer']">
                <Package :class="['h-6 w-6', form.tipe_peminjaman === 'barang' ? 'text-sky-600' : 'text-slate-400']" />
                <span :class="['text-xs font-semibold', form.tipe_peminjaman === 'barang' ? 'text-sky-700' : 'text-slate-500']">Barang</span>
              </button>
            </div>
          </div>

          <!-- Select Specific Asset -->
          <div v-if="form.tipe_peminjaman" class="space-y-2">
            <Label class="text-xs font-semibold text-slate-600 flex items-center gap-1.5">
              <component :is="isRuangan ? Building2 : Package" class="h-3.5 w-3.5 text-slate-400" />
              Pilih {{ isRuangan ? 'Ruangan' : 'Barang' }}
            </Label>
            <!-- Ruangan Select -->
            <Select v-if="isRuangan" v-model="form.ruangan_id" :disabled="hasPreselectedAsset">
              <SelectTrigger class="text-sm">
                <SelectValue placeholder="Pilih ruangan..." />
              </SelectTrigger>
              <SelectContent>
                <SelectGroup>
                  <SelectLabel>Daftar Ruangan</SelectLabel>
                  <SelectItem v-for="r in ruangans" :key="r.id" :value="String(r.id)">
                    {{ r.nama }} — {{ r.lokasi || 'Kampus' }}
                  </SelectItem>
                </SelectGroup>
              </SelectContent>
            </Select>
            <!-- Barang Select -->
            <Select v-else v-model="form.barang_id" :disabled="hasPreselectedAsset">
              <SelectTrigger class="text-sm">
                <SelectValue placeholder="Pilih barang..." />
              </SelectTrigger>
              <SelectContent>
                <SelectGroup>
                  <SelectLabel>Daftar Barang</SelectLabel>
                  <SelectItem v-for="b in barangs" :key="b.id" :value="String(b.id)">
                    {{ b.nama }} (Stok: {{ b.stok_tersedia }})
                  </SelectItem>
                </SelectGroup>
              </SelectContent>
            </Select>
            <p v-if="form.errors.ruangan_id" class="text-xs text-red-500">{{ form.errors.ruangan_id }}</p>
            <p v-if="form.errors.barang_id" class="text-xs text-red-500">{{ form.errors.barang_id }}</p>
          </div>

          <!-- Selected Asset Preview -->
          <div v-if="selectedAssetName" class="flex items-center gap-3 rounded-lg border border-slate-200 bg-slate-50/50 px-3 py-2.5">
            <component :is="isRuangan ? Building2 : Package" :class="['h-5 w-5', isRuangan ? 'text-indigo-500' : 'text-sky-500']" />
            <div class="min-w-0 flex-1">
              <p class="text-sm font-semibold text-slate-800 truncate">{{ selectedAssetName }}</p>
              <p class="text-[11px] text-slate-400">{{ isRuangan ? 'Ruangan' : 'Barang' }}</p>
            </div>
            <Badge :class="isRuangan ? 'bg-indigo-50 text-indigo-700' : 'bg-sky-50 text-sky-700'" class="text-[10px]">
              {{ isRuangan ? 'Ruangan' : 'Barang' }}
            </Badge>
          </div>
        </div>

        <!-- ── STEP 3: Detail & Tata Tertib ───────────── -->
        <div v-if="currentStep === 3" class="px-6 py-5 space-y-5">
          <!-- Identity (read-only) -->
          <div class="rounded-xl bg-slate-50 border border-slate-100 p-4 space-y-3">
            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Identitas Peminjam</p>
            <div class="grid grid-cols-2 gap-3">
              <div class="space-y-1">
                <Label class="text-xs text-slate-500 flex items-center gap-1"><User class="h-3 w-3" /> Nama</Label>
                <p class="text-sm font-semibold text-slate-800">{{ user?.name }}</p>
              </div>
              <div class="space-y-1">
                <Label class="text-xs text-slate-500 flex items-center gap-1"><Mail class="h-3 w-3" /> Email</Label>
                <p class="text-sm font-semibold text-slate-800">{{ user?.email }}</p>
              </div>
            </div>
          </div>

          <!-- Keperluan -->
          <div class="space-y-2">
            <Label for="keterangan" class="text-xs font-semibold text-slate-600 flex items-center gap-1.5">
              <FileText class="h-3.5 w-3.5 text-slate-400" /> Keperluan
            </Label>
            <Select v-model="form.keterangan">
              <SelectTrigger class="text-sm">
                <SelectValue placeholder="Pilih keperluan..." />
              </SelectTrigger>
              <SelectContent>
                <SelectGroup>
                  <SelectLabel>Keperluan Peminjaman</SelectLabel>
                  <SelectItem v-for="opt in keperluanOptions" :key="opt" :value="opt">{{ opt }}</SelectItem>
                </SelectGroup>
              </SelectContent>
            </Select>
            <p v-if="form.errors.keterangan" class="text-xs text-red-500">{{ form.errors.keterangan }}</p>
          </div>

          <!-- Catatan -->
          <div class="space-y-2">
            <Label for="catatan" class="text-xs font-semibold text-slate-600">
              Catatan Tambahan <span class="text-slate-400 font-normal">(opsional)</span>
            </Label>
            <textarea id="catatan" v-model="form.catatan" placeholder="Tambahkan detail atau catatan khusus..." rows="3"
              class="w-full rounded-lg border border-input bg-background px-3 py-2 text-sm text-foreground placeholder:text-muted-foreground shadow-sm transition-colors focus:border-ring focus:outline-none focus:ring-2 focus:ring-ring/20 resize-none" />
          </div>

          <!-- Tata Tertib -->
          <div class="rounded-xl bg-amber-50/60 border border-amber-100 p-4 space-y-3">
            <p class="text-xs font-semibold text-amber-800">Tata Tertib Peminjaman</p>
            <ul class="space-y-1 text-[11px] text-amber-700 leading-relaxed">
              <li>• Peminjam bertanggung jawab atas kondisi aset selama peminjaman</li>
              <li>• Pengembalian harus tepat waktu sesuai jadwal yang diajukan</li>
              <li>• Kerusakan/kehilangan menjadi tanggung jawab peminjam</li>
              <li>• Admin berhak menolak/membatalkan peminjaman yang tidak sesuai aturan</li>
            </ul>
            <label class="flex items-start gap-2 cursor-pointer pt-1">
              <input v-model="agreedToRules" type="checkbox" class="mt-0.5 h-4 w-4 rounded border-amber-300 text-amber-600 focus:ring-amber-500/20" />
              <span class="text-xs font-medium text-amber-800">Saya menyetujui tata tertib peminjaman di atas</span>
            </label>
          </div>
        </div>

        <!-- ── Footer Actions ─────────────────────────── -->
        <DialogFooter class="border-t border-slate-100 px-6 py-4">
          <div class="flex w-full items-center justify-between gap-3">
            <Button v-if="currentStep > 1" variant="ghost" size="sm" @click="goToStep(currentStep - 1)" class="gap-1 text-slate-600">
              <ChevronLeft class="h-4 w-4" /> Kembali
            </Button>
            <Button v-else variant="ghost" size="sm" @click="closeModal" class="text-slate-500">Batal</Button>

            <Button v-if="currentStep === 1" @click="goToStep(2)" :disabled="!step1Valid" class="gap-1 bg-blue-600 hover:bg-blue-700 text-white">
              Lanjutkan <ChevronRight class="h-4 w-4" />
            </Button>
            <Button v-else-if="currentStep === 2" @click="goToStep(3)" :disabled="!step2Valid" class="gap-1 bg-blue-600 hover:bg-blue-700 text-white">
              Lanjutkan <ChevronRight class="h-4 w-4" />
            </Button>
            <Button v-else type="submit" :disabled="!step3Valid || form.processing" class="gap-2 bg-blue-600 hover:bg-blue-700 text-white min-w-[140px]">
              <Loader2 v-if="form.processing" class="h-4 w-4 animate-spin" />
              <CheckCircle2 v-else class="h-4 w-4" />
              {{ form.processing ? 'Mengirim...' : 'Ajukan Peminjaman' }}
            </Button>
          </div>
        </DialogFooter>
      </form>
    </DialogContent>
  </Dialog>
</template>
