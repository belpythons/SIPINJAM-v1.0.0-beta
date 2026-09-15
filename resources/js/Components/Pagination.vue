<script setup>
/**
 * Navigasi halaman untuk daftar yang dipaginasi.
 *
 * Menerima objek paginator Laravel apa adanya (`links`, `from`, `to`, `total`),
 * sehingga cukup ditulis: <Pagination :meta="peminjamans" />
 */
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { ChevronLeft, ChevronRight } from '@lucide/vue';

const props = defineProps({
    meta: { type: Object, required: true },
});

// Laravel menaruh "Previous"/"Next" di ujung array links.
const numberedLinks = computed(() => (props.meta?.links ?? []).slice(1, -1));
const previousLink = computed(() => (props.meta?.links ?? [])[0] ?? null);
const nextLink = computed(() => {
    const links = props.meta?.links ?? [];
    return links.length ? links[links.length - 1] : null;
});

const showPagination = computed(() => (props.meta?.last_page ?? 1) > 1);
</script>

<template>
    <div
        v-if="meta"
        class="flex flex-col gap-3 border-t border-border px-5 py-4 sm:flex-row sm:items-center sm:justify-between"
    >
        <p class="text-sm text-muted-foreground">
            Menampilkan
            <span class="font-medium text-foreground">{{ meta.from ?? 0 }}</span>
            –
            <span class="font-medium text-foreground">{{ meta.to ?? 0 }}</span>
            dari
            <span class="font-medium text-foreground">{{ meta.total ?? 0 }}</span>
            data
        </p>

        <nav v-if="showPagination" class="flex items-center gap-1" aria-label="Navigasi halaman">
            <component
                :is="previousLink?.url ? Link : 'span'"
                :href="previousLink?.url ?? undefined"
                preserve-scroll
                class="inline-flex h-9 min-w-9 items-center justify-center rounded-md border border-border px-2 text-sm transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                :class="previousLink?.url
                    ? 'text-foreground hover:bg-accent'
                    : 'cursor-not-allowed text-muted-foreground opacity-50'"
                aria-label="Halaman sebelumnya"
            >
                <ChevronLeft class="h-4 w-4" />
            </component>

            <template v-for="(link, i) in numberedLinks" :key="i">
                <span
                    v-if="!link.url"
                    class="inline-flex h-9 min-w-9 items-center justify-center px-2 text-sm text-muted-foreground"
                    v-html="link.label"
                />
                <Link
                    v-else
                    :href="link.url"
                    preserve-scroll
                    class="inline-flex h-9 min-w-9 items-center justify-center rounded-md border px-2 text-sm transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                    :class="link.active
                        ? 'border-primary bg-primary text-primary-foreground'
                        : 'border-border text-foreground hover:bg-accent'"
                    :aria-current="link.active ? 'page' : undefined"
                    v-html="link.label"
                />
            </template>

            <component
                :is="nextLink?.url ? Link : 'span'"
                :href="nextLink?.url ?? undefined"
                preserve-scroll
                class="inline-flex h-9 min-w-9 items-center justify-center rounded-md border border-border px-2 text-sm transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                :class="nextLink?.url
                    ? 'text-foreground hover:bg-accent'
                    : 'cursor-not-allowed text-muted-foreground opacity-50'"
                aria-label="Halaman berikutnya"
            >
                <ChevronRight class="h-4 w-4" />
            </component>
        </nav>
    </div>
</template>
