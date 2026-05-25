<script setup>
import { computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import {
  LayoutDashboard,
  ClipboardList,
  CalendarDays,
  FileText,
  LogOut,
  ChevronRight,
} from '@lucide/vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { ScrollArea } from '@/components/ui/scroll-area';
import {
  Tooltip,
  TooltipContent,
  TooltipProvider,
  TooltipTrigger,
} from '@/components/ui/tooltip';

const page = usePage();
const user = computed(() => page.props.auth?.user);
const currentUrl = computed(() => page.url);

const navItems = [
  { label: 'Dashboard', icon: LayoutDashboard, href: '/dashboard' },
  { label: 'Peminjaman Saya', icon: ClipboardList, href: '/bookings' },
  { label: 'Kalender', icon: CalendarDays, href: '/kalender' },
  { label: 'Laporan', icon: FileText, href: '/laporan' },
];

const isActive = (href) => {
  return currentUrl.value === href || currentUrl.value.startsWith(href + '/');
};

const navigate = (href) => {
  router.visit(href);
};

const logout = () => {
  router.post('/logout');
};

const getInitials = (name) => {
  if (!name) return '?';
  return name
    .split(' ')
    .map((w) => w[0])
    .join('')
    .toUpperCase()
    .slice(0, 2);
};
</script>

<template>
  <aside
    class="fixed inset-y-0 left-0 z-40 flex w-64 flex-col border-r border-slate-800/50 bg-gradient-to-b from-slate-950 via-slate-900 to-slate-950"
  >
    <!-- ── Logo / Branding ──────────────────────────── -->
    <div class="flex items-center gap-3 px-6 py-6">
      <div
        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 shadow-lg shadow-blue-500/20"
      >
        <svg
          class="h-5 w-5 text-white"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
          stroke-width="1.5"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z"
          />
        </svg>
      </div>
      <div class="min-w-0">
        <h1 class="text-base font-bold tracking-tight text-white">SiPinjam</h1>
        <p class="truncate text-[10px] font-medium tracking-widest text-slate-500 uppercase">
          STITEK Bontang
        </p>
      </div>
    </div>

    <Separator class="bg-slate-800/60" />

    <!-- ── Navigation ───────────────────────────────── -->
    <ScrollArea class="flex-1 px-3 py-4">
      <nav class="space-y-1">
        <TooltipProvider :delay-duration="300">
          <Tooltip v-for="item in navItems" :key="item.href">
            <TooltipTrigger as-child>
              <button
                @click="navigate(item.href)"
                :class="[
                  'group flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-all duration-200',
                  isActive(item.href)
                    ? 'bg-blue-600/15 text-blue-400 shadow-sm shadow-blue-500/5'
                    : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200',
                ]"
              >
                <component
                  :is="item.icon"
                  :class="[
                    'h-[18px] w-[18px] shrink-0 transition-colors',
                    isActive(item.href) ? 'text-blue-400' : 'text-slate-500 group-hover:text-slate-300',
                  ]"
                />
                <span class="truncate">{{ item.label }}</span>
                <ChevronRight
                  v-if="isActive(item.href)"
                  class="ml-auto h-3.5 w-3.5 text-blue-400/60"
                />
              </button>
            </TooltipTrigger>
            <TooltipContent side="right" :side-offset="8">
              {{ item.label }}
            </TooltipContent>
          </Tooltip>
        </TooltipProvider>
      </nav>
    </ScrollArea>

    <!-- ── User Footer ──────────────────────────────── -->
    <div class="border-t border-slate-800/60 px-4 py-4">
      <div class="flex items-center gap-3">
        <Avatar class="h-9 w-9 shrink-0 border border-slate-700/50">
          <AvatarImage v-if="user?.avatar" :src="user.avatar" :alt="user?.name" />
          <AvatarFallback class="bg-slate-800 text-xs font-semibold text-slate-300">
            {{ getInitials(user?.name) }}
          </AvatarFallback>
        </Avatar>
        <div class="min-w-0 flex-1">
          <p class="truncate text-sm font-semibold text-slate-200">
            {{ user?.name || 'User' }}
          </p>
          <p class="truncate text-[11px] text-slate-500">
            {{ user?.email || '' }}
          </p>
        </div>
      </div>

      <Button
        variant="ghost"
        size="sm"
        @click="logout"
        class="mt-3 w-full justify-start gap-2 text-slate-400 hover:bg-red-500/10 hover:text-red-400"
      >
        <LogOut class="h-4 w-4" />
        Keluar
      </Button>
    </div>
  </aside>
</template>
