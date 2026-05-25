<script setup>
import { ref, computed, watch } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import {
  CalendarDays,
  Clock,
  User,
  Mail,
  FileText,
  ChevronLeft,
  ChevronRight,
  CheckCircle2,
  AlertCircle,
  Loader2,
  Building2,
  Package,
} from '@lucide/vue';
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Badge } from '@/components/ui/badge';
import { Separator } from '@/components/ui/separator';
import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectLabel,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select';

const props = defineProps({
  open: Boolean,
  asset: Object,
});

const emit = defineEmits(['update:open']);

const page = usePage();
const user = computed(() => page.props.auth?.user);

// ── Step State ─────────────────────────────────────
const currentStep = ref(1);
const agreedToRules = ref(false);

// ── Form (Inertia useForm) ─────────────────────────
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

// ── Keperluan Options ──────────────────────────────
const keperluanOptions = [
  'Rapat Himpunan',
  'Perkuliahan Pengganti',
  'Sidang Skripsi',
  'Praktikum',
  'Seminar / Workshop',
  'Kegiatan UKM',
  'Lainnya',
];

// ── Computed ───────────────────────────────────────
const isRuangan = computed(() => props.asset?.tipe === 'ruangan');
const assetIcon = computed(() => (isRuangan.value ? Building2 : Package));

const todayString = computed(() => {
  const d = new Date();
  return d.toISOString().split('T')[0];
});

// ── Step 1 Validation ──────────────────────────────
const step1Valid = computed(() => {
  return (
    form.tanggal_mulai &&
    form.tanggal_selesai &&
    form.waktu_mulai &&
    form.waktu_selesai &&
    form.tanggal_selesai >= form.tanggal_mulai
  );
});

// ── Step 2 Validation ──────────────────────────────
const step2Valid = computed(() => {
  return form.keterangan && agreedToRules.value;
});

// ── Reset on open change ───────────────────────────
watch(
  () => props.open,
  (isOpen) => {
    if (isOpen) {
      currentStep.value = 1;
      agreedToRules.value = false;
      form.reset();
      form.clearErrors();

      // Set asset-based fields
      if (props.asset) {
        form.tipe_peminjaman = props.asset.tipe;
        if (props.asset.tipe === 'barang') {
          form.barang_id = props.asset.id;
        } else {
          form.ruangan_id = props.asset.id;
        }
      }
    }
  }
);

// ── Navigation ─────────────────────────────────────
const goToStep2 = () => {
  if (step1Valid.value) {
    currentStep.value = 2;
  }
};

const goBack = () => {
  currentStep.value = 1;
};

// ── Submit ─────────────────────────────────────────
const submit = () => {
  form.post('/bookings', {
    preserveScroll: true,
    onSuccess: () => {
      emit('update:open', false);
    },
  });
};

const closeModal = () => {
  emit('update:open', false);
};
</script>

<template>
  <Dialog :open="open" @update:open="closeModal">
    <DialogContent class="sm:max-w-lg max-h-[90vh] overflow-y-auto p-0">
      <!-- ── Header ─────────────────────────────────── -->
      <DialogHeader class="px-6 pt-6 pb-0">
        <div class="flex items-center gap-3 mb-1">
          <div
            :class="[
              'flex h-10 w-10 shrink-0 items-center justify-center rounded-xl',
              isRuangan
                ? 'bg-indigo-50 text-indigo-600'
                : 'bg-blue-50 text-blue-600',
            ]"
          >
            <component :is="assetIcon" class="h-5 w-5" />
          </div>
          <div class="min-w-0">
            <DialogTitle class="text-base font-bold text-slate-900">
              Peminjaman {{ isRuangan ? 'Ruangan' : 'Barang' }}
            </DialogTitle>
            <DialogDescription class="text-xs text-slate-500">
              {{ asset?.nama }}
            </DialogDescription>
          </div>
        </div>

        <!-- Step Indicator -->
        <div class="flex items-center gap-2 pt-4 pb-2">
          <div class="flex items-center gap-2 flex-1">
            <div
              :class="[
                'flex h-7 w-7 items-center justify-center rounded-full text-xs font-bold transition-colors',
                currentStep >= 1
                  ? 'bg-blue-600 text-white'
                  : 'bg-slate-100 text-slate-400',
              ]"
            >
              1
            </div>
            <span
              :class="[
                'text-xs font-medium transition-colors',
                currentStep >= 1 ? 'text-slate-700' : 'text-slate-400',
              ]"
            >
              Jadwal
            </span>
          </div>
          <div
            :class="[
              'h-px flex-1 transition-colors',
              currentStep >= 2 ? 'bg-blue-600' : 'bg-slate-200',
            ]"
          />
          <div class="flex items-center gap-2 flex-1 justify-end">
            <div
              :class="[
                'flex h-7 w-7 items-center justify-center rounded-full text-xs font-bold transition-colors',
                currentStep >= 2
                  ? 'bg-blue-600 text-white'
                  : 'bg-slate-100 text-slate-400',
              ]"
            >
              2
            </div>
            <span
              :class="[
                'text-xs font-medium transition-colors',
                currentStep >= 2 ? 'text-slate-700' : 'text-slate-400',
              ]"
            >
              Detail
            </span>
          </div>
        </div>
      </DialogHeader>

      <Separator />

      <!-- ── Global Error ───────────────────────────── -->
      <div
        v-if="form.errors.booking"
        class="mx-6 mt-4 flex items-center gap-2 rounded-lg border border-red-200 bg-red-50 px-3 py-2.5 text-sm text-red-700"
      >
        <AlertCircle class="h-4 w-4 shrink-0" />
        {{ form.errors.booking }}
      </div>

      <!-- ── STEP 1: Jadwal ─────────────────────────── -->
      <div v-if="currentStep === 1" class="px-6 py-5 space-y-5">
        <div class="grid grid-cols-2 gap-4">
          <div class="space-y-2">
            <Label for="tanggal_mulai" class="text-xs font-semibold text-slate-600 flex items-center gap-1.5">
              <CalendarDays class="h-3.5 w-3.5 text-slate-400" />
              Tanggal Mulai
            </Label>
            <Input
              id="tanggal_mulai"
              v-model="form.tanggal_mulai"
              type="date"
              :min="todayString"
              class="text-sm"
            />
            <p v-if="form.errors.tanggal_mulai" class="text-xs text-red-500">
              {{ form.errors.tanggal_mulai }}
            </p>
          </div>
          <div class="space-y-2">
            <Label for="tanggal_selesai" class="text-xs font-semibold text-slate-600 flex items-center gap-1.5">
              <CalendarDays class="h-3.5 w-3.5 text-slate-400" />
              Tanggal Selesai
            </Label>
            <Input
              id="tanggal_selesai"
              v-model="form.tanggal_selesai"
              type="date"
              :min="form.tanggal_mulai || todayString"
              class="text-sm"
            />
            <p v-if="form.errors.tanggal_selesai" class="text-xs text-red-500">
              {{ form.errors.tanggal_selesai }}
            </p>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div class="space-y-2">
            <Label for="waktu_mulai" class="text-xs font-semibold text-slate-600 flex items-center gap-1.5">
              <Clock class="h-3.5 w-3.5 text-slate-400" />
              Waktu Mulai
            </Label>
            <Input
              id="waktu_mulai"
              v-model="form.waktu_mulai"
              type="time"
              class="text-sm"
            />
            <p v-if="form.errors.waktu_mulai" class="text-xs text-red-500">
              {{ form.errors.waktu_mulai }}
            </p>
          </div>
          <div class="space-y-2">
            <Label for="waktu_selesai" class="text-xs font-semibold text-slate-600 flex items-center gap-1.5">
              <Clock class="h-3.5 w-3.5 text-slate-400" />
              Waktu Selesai
            </Label>
            <Input
              id="waktu_selesai"
              v-model="form.waktu_selesai"
              type="time"
              class="text-sm"
            />
            <p v-if="form.errors.waktu_selesai" class="text-xs text-red-500">
              {{ form.errors.waktu_selesai }}
            </p>
          </div>
        </div>

        <!-- Info tip -->
        <div class="rounded-lg bg-blue-50/60 border border-blue-100 px-3 py-2.5 text-xs text-blue-700 flex items-start gap-2">
          <AlertCircle class="h-3.5 w-3.5 mt-0.5 shrink-0 text-blue-500" />
          <span>Pastikan jadwal tidak bentrok dengan peminjaman lain. Sistem akan memeriksa ketersediaan secara otomatis.</span>
        </div>
      </div>

      <!-- ── STEP 2: Detail & Tata Tertib ───────────── -->
      <div v-if="currentStep === 2" class="px-6 py-5 space-y-5">
        <!-- Autofill Identity (read-only) -->
        <div class="rounded-xl bg-slate-50 border border-slate-100 p-4 space-y-3">
          <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
            Identitas Peminjam
          </p>
          <div class="grid grid-cols-2 gap-3">
            <div class="space-y-1">
              <Label class="text-xs text-slate-500 flex items-center gap-1">
                <User class="h-3 w-3" /> Nama
              </Label>
              <p class="text-sm font-semibold text-slate-800">{{ user?.name }}</p>
            </div>
            <div class="space-y-1">
              <Label class="text-xs text-slate-500 flex items-center gap-1">
                <Mail class="h-3 w-3" /> Email
              </Label>
              <p class="text-sm font-semibold text-slate-800">{{ user?.email }}</p>
            </div>
          </div>
        </div>

        <!-- Asset Info -->
        <div class="flex items-center gap-3 rounded-lg border border-slate-200 bg-white px-3 py-2.5">
          <component
            :is="assetIcon"
            :class="[
              'h-5 w-5',
              isRuangan ? 'text-indigo-500' : 'text-blue-500',
            ]"
          />
          <div class="min-w-0 flex-1">
            <p class="text-sm font-semibold text-slate-800 truncate">{{ asset?.nama }}</p>
            <p class="text-[11px] text-slate-400">
              {{ isRuangan ? 'Ruangan' : 'Barang' }} — {{ asset?.kode }}
            </p>
          </div>
          <Badge :class="isRuangan ? 'bg-indigo-50 text-indigo-700' : 'bg-blue-50 text-blue-700'" class="text-[10px]">
            {{ isRuangan ? 'Ruangan' : 'Barang' }}
          </Badge>
        </div>

        <!-- Keperluan -->
        <div class="space-y-2">
          <Label for="keterangan" class="text-xs font-semibold text-slate-600 flex items-center gap-1.5">
            <FileText class="h-3.5 w-3.5 text-slate-400" />
            Keperluan
          </Label>
          <Select v-model="form.keterangan">
            <SelectTrigger class="text-sm">
              <SelectValue placeholder="Pilih keperluan..." />
            </SelectTrigger>
            <SelectContent>
              <SelectGroup>
                <SelectLabel>Keperluan Peminjaman</SelectLabel>
                <SelectItem
                  v-for="opt in keperluanOptions"
                  :key="opt"
                  :value="opt"
                >
                  {{ opt }}
                </SelectItem>
              </SelectGroup>
            </SelectContent>
          </Select>
          <p v-if="form.errors.keterangan" class="text-xs text-red-500">
            {{ form.errors.keterangan }}
          </p>
        </div>

        <!-- Catatan -->
        <div class="space-y-2">
          <Label for="catatan" class="text-xs font-semibold text-slate-600">
            Catatan Tambahan
            <span class="text-slate-400 font-normal">(opsional)</span>
          </Label>
          <textarea
            id="catatan"
            v-model="form.catatan"
            placeholder="Tambahkan detail atau catatan khusus..."
            rows="3"
            class="w-full rounded-lg border border-input bg-background px-3 py-2 text-sm text-foreground placeholder:text-muted-foreground shadow-sm transition-colors focus:border-ring focus:outline-none focus:ring-2 focus:ring-ring/20 resize-none"
          />
        </div>

        <!-- Tata Tertib Agreement -->
        <div class="rounded-xl bg-amber-50/60 border border-amber-100 p-4 space-y-3">
          <p class="text-xs font-semibold text-amber-800">Tata Tertib Peminjaman</p>
          <ul class="space-y-1 text-[11px] text-amber-700 leading-relaxed">
            <li>• Peminjam bertanggung jawab atas kondisi aset selama peminjaman</li>
            <li>• Pengembalian harus tepat waktu sesuai jadwal yang diajukan</li>
            <li>• Kerusakan/kehilangan menjadi tanggung jawab peminjam</li>
            <li>• Admin berhak menolak/membatalkan peminjaman yang tidak sesuai aturan</li>
          </ul>
          <label class="flex items-start gap-2 cursor-pointer pt-1">
            <input
              v-model="agreedToRules"
              type="checkbox"
              class="mt-0.5 h-4 w-4 rounded border-amber-300 text-amber-600 focus:ring-amber-500/20"
            />
            <span class="text-xs font-medium text-amber-800">
              Saya menyetujui tata tertib peminjaman di atas
            </span>
          </label>
        </div>
      </div>

      <!-- ── Footer Actions ─────────────────────────── -->
      <DialogFooter class="border-t border-slate-100 px-6 py-4">
        <div class="flex w-full items-center justify-between gap-3">
          <!-- Left: Back or Cancel -->
          <Button
            v-if="currentStep === 2"
            variant="ghost"
            size="sm"
            @click="goBack"
            class="gap-1 text-slate-600"
          >
            <ChevronLeft class="h-4 w-4" />
            Kembali
          </Button>
          <Button
            v-else
            variant="ghost"
            size="sm"
            @click="closeModal"
            class="text-slate-500"
          >
            Batal
          </Button>

          <!-- Right: Next or Submit -->
          <Button
            v-if="currentStep === 1"
            @click="goToStep2"
            :disabled="!step1Valid"
            class="gap-1 bg-blue-600 hover:bg-blue-700 text-white"
          >
            Lanjutkan
            <ChevronRight class="h-4 w-4" />
          </Button>
          <Button
            v-else
            @click="submit"
            :disabled="!step2Valid || form.processing"
            class="gap-2 bg-blue-600 hover:bg-blue-700 text-white min-w-[140px]"
          >
            <Loader2 v-if="form.processing" class="h-4 w-4 animate-spin" />
            <CheckCircle2 v-else class="h-4 w-4" />
            {{ form.processing ? 'Mengirim...' : 'Ajukan Peminjaman' }}
          </Button>
        </div>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>
