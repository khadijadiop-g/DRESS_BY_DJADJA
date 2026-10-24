<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { useCurrency } from '../composables/useCurrency.js';

const props = defineProps({
    dress: {
        type: Object,
        required: true,
    },
});

const { formatPrice } = useCurrency();

// Deduplicate colors for swatches
const uniqueColors = computed(() => {
    if (!props.dress.variants || !Array.isArray(props.dress.variants)) return [];
    const seen = new Set();
    const colors = [];
    for (const v of props.dress.variants) {
        if (v.color_name && !seen.has(v.color_name.toLowerCase())) {
            seen.add(v.color_name.toLowerCase());
            colors.push({
                color_name: v.color_name,
                color_hex: v.color_hex || '#141414',
            });
        }
    }
    return colors;
});

const isSoldOut = computed(() => {
    if (!props.dress.variants || props.dress.variants.length === 0) return false;
    return props.dress.variants.every((v) => v.stock_quantity <= 0);
});

// Active color swatch tooltip or selection
const activeColorHover = ref(null);
</script>

<template>
    <div class="group flex flex-col h-full select-none">
        <!-- Visual Container with Crossfade on Desktop, Tap on Mobile -->
        <Link 
            :href="`/robes/${dress.slug}`" 
            class="relative aspect-[3/4] w-full overflow-hidden bg-[#F3F1EC] border border-[#E8E5DF] block shadow-2xs"
        >
            <!-- Badges -->
            <div class="absolute top-2.5 left-2.5 z-10 flex flex-col space-y-1">
                <span 
                    v-if="isSoldOut" 
                    class="bg-[#141414] text-[#FAF9F6] text-[9px] sm:text-[10px] uppercase font-bold tracking-widest px-2 py-0.5 sm:px-2.5 sm:py-1 shadow-xs"
                >
                    Épuisé
                </span>
                <span 
                    v-else-if="dress.is_new_arrival" 
                    class="bg-[#FAF9F6]/95 backdrop-blur-xs text-[#141414] text-[9px] sm:text-[10px] uppercase font-semibold tracking-widest px-2 py-0.5 sm:px-2.5 sm:py-1 border border-[#E8E5DF] shadow-xs"
                >
                    Nouveau
                </span>
            </div>

            <!-- Primary Image -->
            <img 
                :src="dress.main_image || (dress.images && dress.images[0]?.url) || '/images/placeholder-dress.jpg'" 
                :alt="dress.name" 
                loading="lazy"
                class="absolute inset-0 w-full h-full object-cover object-center transition-opacity duration-500 ease-in-out md:group-hover:opacity-0"
            />

            <!-- Hover Image (Almada Label signature effect on desktop) -->
            <img 
                v-if="dress.hover_image || (dress.images && dress.images[1]?.url)"
                :src="dress.hover_image || dress.images[1]?.url" 
                :alt="`${dress.name} portée`" 
                loading="lazy"
                class="hidden md:block absolute inset-0 w-full h-full object-cover object-center opacity-0 transition-opacity duration-500 ease-in-out group-hover:opacity-100 group-hover:scale-105 transition-transform duration-700"
            />

            <!-- Quick View / Discover Overlay on Desktop -->
            <div class="hidden sm:flex absolute inset-x-0 bottom-0 p-3 bg-gradient-to-t from-black/50 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity justify-center items-center">
                <span class="bg-[#FAF9F6] text-[#141414] text-[10px] uppercase tracking-[0.2em] font-semibold px-3 py-1.5 shadow-sm">
                    Découvrir la robe
                </span>
            </div>
        </Link>

        <!-- Product Information (Mobile First) -->
        <div class="mt-3 sm:mt-4 flex flex-col flex-1 justify-between">
            <div>
                <!-- Color Swatches & Count -->
                <div v-if="uniqueColors.length > 0" class="flex items-center space-x-1.5 mb-1.5">
                    <div class="flex items-center space-x-1.5">
                        <span 
                            v-for="color in uniqueColors" 
                            :key="color.color_name"
                            @mouseenter="activeColorHover = color.color_name"
                            @mouseleave="activeColorHover = null"
                            class="w-3 h-3 sm:w-3.5 sm:h-3.5 rounded-full border border-black/25 flex-shrink-0 transition-transform hover:scale-125"
                            :style="{ backgroundColor: color.color_hex }"
                            :title="color.color_name"
                        />
                    </div>
                    <span class="text-[10px] text-[#141414]/60 font-medium tracking-tight">
                        {{ activeColorHover || (uniqueColors.length > 1 ? `${uniqueColors.length} couleurs` : uniqueColors[0]?.color_name) }}
                    </span>
                </div>

                <!-- Dress Title -->
                <Link :href="`/robes/${dress.slug}`" class="block">
                    <h3 class="font-serif text-base sm:text-lg text-[#141414] font-medium leading-snug line-clamp-1 group-hover:underline underline-offset-4 decoration-1">
                        {{ dress.name }}
                    </h3>
                </Link>

                <!-- Subtitle / Fabric hint -->
                <p v-if="dress.subtitle" class="text-[10px] sm:text-xs text-[#141414]/60 tracking-wider uppercase mt-0.5 font-light line-clamp-1">
                    {{ dress.subtitle }}
                </p>
            </div>

            <!-- Price -->
            <div class="mt-2 pt-2 border-t border-[#E8E5DF]/70 flex items-baseline justify-between">
                <span class="text-xs sm:text-sm font-semibold tracking-wide text-[#141414]">
                    {{ formatPrice(dress.base_price_xof) }}
                </span>
                <span v-if="dress.category?.name" class="text-[9px] uppercase tracking-wider text-[#141414]/50 hidden sm:inline">
                    {{ dress.category.name }}
                </span>
            </div>
        </div>
    </div>
</template>