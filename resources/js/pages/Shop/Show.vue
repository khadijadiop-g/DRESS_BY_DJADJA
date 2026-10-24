<script setup>
import { ref, computed } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import { ShoppingBag, MessageCircle, ChevronDown, Check, Ruler, Scissors, Share2 } from 'lucide-vue-next';
import Navbar from '../../components/Navbar.vue';
import CartDrawer from '../../components/CartDrawer.vue';
import ProductCard from '../../components/ProductCard.vue';
import Footer from '../../components/Footer.vue';
import SizeGuideModal from '../../components/SizeGuideModal.vue';
import RestockModal from '../../components/RestockModal.vue';
import { useCart } from '../../composables/useCart.js';
import { useCurrency } from '../../composables/useCurrency.js';

const props = defineProps({
    dress: {
        type: Object,
        required: true,
    },
    relatedDresses: {
        type: Array,
        default: () => [],
    },
});

const page = usePage();
const whatsappNumber = page.props.whatsappNumber || '+221770000000';

const { addItem } = useCart();
const { formatPrice } = useCurrency();

// Modals
const isSizeGuideOpen = ref(false);
const isRestockOpen = ref(false);

// Active Image
const activeImageUrl = ref(props.dress.images?.[0]?.url || props.dress.main_image || '/images/placeholder-dress.jpg');

// Colors Deduplicated with clean trimming
const availableColors = computed(() => {
    if (!props.dress.variants || !Array.isArray(props.dress.variants)) return [];
    const seen = new Set();
    const colors = [];
    for (const v of props.dress.variants) {
        const name = (v.color_name || '').trim();
        if (name && !seen.has(name.toLowerCase())) {
            seen.add(name.toLowerCase());
            colors.push({
                color_name: name,
                color_hex: v.color_hex || '#141414',
            });
        }
    }
    return colors;
});

const selectedColor = ref(availableColors.value[0]?.color_name || '');

// Sizes for the selected color
const availableSizes = computed(() => {
    if (!props.dress.variants) return [];
    return props.dress.variants.filter((v) => (v.color_name || '').trim().toLowerCase() === selectedColor.value.trim().toLowerCase());
});

// Selected Variant
const selectedVariantId = ref(
    availableSizes.value.find((v) => v.stock_quantity > 0)?.id || availableSizes.value[0]?.id || null
);

const currentVariant = computed(() => {
    return props.dress.variants?.find((v) => v.id === selectedVariantId.value);
});

const isCurrentVariantInStock = computed(() => {
    return (currentVariant.value?.stock_quantity ?? 0) > 0;
});

// Sur-Mesure State
const isCustomSizeSelected = ref(false);
const customMeasurements = ref({
    poitrine: '',
    taille: '',
    hanches: '',
    longueur: '',
});

// Switch color
const selectColor = (colorName) => {
    selectedColor.value = colorName;
    const matchingSizes = props.dress.variants?.filter((v) => (v.color_name || '').trim().toLowerCase() === colorName.trim().toLowerCase()) || [];
    const firstInStock = matchingSizes.find((v) => v.stock_quantity > 0);
    selectedVariantId.value = firstInStock?.id || matchingSizes[0]?.id || null;
    isCustomSizeSelected.value = false;
};

// Add to Cart
const isAddingToCart = ref(false);
const addedNotification = ref(false);

const handleAddToCart = () => {
    if (!currentVariant.value && !isCustomSizeSelected.value) return;

    isAddingToCart.value = true;

    const sizeLabel = isCustomSizeSelected.value
        ? `Sur-Mesure (P:${customMeasurements.value.poitrine || '-'}cm, T:${customMeasurements.value.taille || '-'}cm, H:${customMeasurements.value.hanches || '-'}cm)`
        : currentVariant.value?.size || 'Unique';

    addItem({
        variant_id: currentVariant.value?.id || 0,
        product_id: props.dress.id,
        product_name: props.dress.name,
        slug: props.dress.slug,
        size: sizeLabel,
        color_name: selectedColor.value,
        color_hex: currentVariant.value?.color_hex || '#141414',
        image: activeImageUrl.value,
        price_xof: currentVariant.value?.price_xof_override || props.dress.base_price_xof,
        quantity: 1,
    });

    isAddingToCart.value = false;
    addedNotification.value = true;
    setTimeout(() => {
        addedNotification.value = false;
    }, 2500);
};

// Accordions State
const openAccordion = ref('fabric');
const toggleAccordion = (name) => {
    openAccordion.value = openAccordion.value === name ? null : name;
};
</script>

<template>
    <Head :title="`${dress.name} — Dress By DjaDja`" />

    <div class="min-h-screen flex flex-col bg-[#FAF9F6] overflow-x-hidden">
        <Navbar />
        <CartDrawer />
        <SizeGuideModal :is-open="isSizeGuideOpen" @close="isSizeGuideOpen = false" />
        <RestockModal 
            :is-open="isRestockOpen" 
            :variant-id="currentVariant?.id"
            :dress-name="dress.name"
            :size="currentVariant?.size"
            @close="isRestockOpen = false" 
        />

        <main class="flex-1 max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-6 sm:py-14">
            
            <!-- Breadcrumbs Navigation -->
            <nav class="flex items-center space-x-2 text-[11px] uppercase tracking-wider text-[#141414]/60 mb-6">
                <a href="/" class="hover:text-[#141414]">Accueil</a>
                <span>/</span>
                <a href="/collections" class="hover:text-[#141414]">Robes</a>
                <span v-if="dress.category">/</span>
                <span v-if="dress.category" class="hover:text-[#141414]">{{ dress.category.name }}</span>
                <span>/</span>
                <span class="text-[#141414] font-semibold truncate">{{ dress.name }}</span>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-14">
                
                <!-- Left: Multi-photo Gallery (Thumbnails + Main) -->
                <div class="lg:col-span-7 flex flex-col-reverse sm:flex-row gap-3 sm:gap-4">
                    
                    <!-- Thumbnail Selector (Horizontal on mobile, vertical on tablet/desktop) -->
                    <div 
                        v-if="dress.images && dress.images.length > 1"
                        class="flex sm:flex-col gap-2.5 overflow-x-auto sm:overflow-y-auto max-h-[580px] pb-2 sm:pb-0 scrollbar-none"
                    >
                        <button 
                            v-for="img in dress.images" 
                            :key="img.id"
                            @click="activeImageUrl = img.url"
                            :class="activeImageUrl === img.url ? 'border-[#141414] ring-1 ring-[#141414]' : 'border-[#E8E5DF] opacity-75 hover:opacity-100'"
                            class="w-16 h-20 sm:w-20 sm:h-26 flex-shrink-0 bg-[#F3F1EC] border overflow-hidden transition-all shadow-2xs"
                        >
                            <img :src="img.url" :alt="dress.name" class="w-full h-full object-cover" />
                        </button>
                    </div>

                    <!-- Main Showcase Image -->
                    <div class="flex-1 aspect-[3/4] bg-[#F3F1EC] border border-[#E8E5DF] overflow-hidden relative shadow-xs">
                        <img 
                            :src="activeImageUrl" 
                            :alt="dress.name" 
                            class="w-full h-full object-cover object-center transition-all duration-300"
                        />
                        
                        <!-- Badges -->
                        <div class="absolute top-3 left-3 flex flex-col space-y-1">
                            <span v-if="dress.is_new_arrival" class="bg-[#FAF9F6]/95 backdrop-blur-xs text-[#141414] text-[10px] uppercase font-bold tracking-widest px-2.5 py-1 border border-[#E8E5DF]">
                                Pièce Signature
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Right: Product Information & Purchase Area -->
                <div class="lg:col-span-5 flex flex-col justify-start space-y-6">
                    
                    <!-- Titles & Price -->
                    <div class="border-b border-[#E8E5DF] pb-5">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] uppercase tracking-[0.25em] text-[#C5A059] font-bold">Maison DjaDja</span>
                            <span v-if="availableColors.length > 1" class="text-[11px] font-semibold text-[#141414]/60 uppercase tracking-wider">
                                {{ availableColors.length }} Couleurs
                            </span>
                        </div>

                        <h1 class="font-serif text-3xl sm:text-4xl font-bold text-[#141414] mt-1 leading-tight">
                            {{ dress.name }}
                        </h1>

                        <p v-if="dress.subtitle" class="text-xs text-[#141414]/70 uppercase tracking-widest mt-1 font-light">
                            {{ dress.subtitle }}
                        </p>

                        <div class="mt-4 flex items-baseline space-x-3">
                            <span class="font-serif text-2xl sm:text-3xl font-bold text-[#141414]">
                                {{ formatPrice(currentVariant?.price_xof_override || dress.base_price_xof) }}
                            </span>
                        </div>
                    </div>

                    <!-- MULTI-COLOR SELECTOR (Prominently displays ALL colors) -->
                    <div class="space-y-2">
                        <div class="flex justify-between items-center text-xs">
                            <span class="uppercase tracking-widest font-bold text-[#141414]">
                                Déclinaisons de Couleur :
                            </span>
                            <span class="text-[#141414] font-semibold bg-[#F3F1EC] px-2.5 py-0.5 rounded-xs">
                                {{ selectedColor }}
                            </span>
                        </div>

                        <div class="flex flex-wrap items-center gap-3 pt-1">
                            <button 
                                v-for="c in availableColors" 
                                :key="c.color_name"
                                @click="selectColor(c.color_name)"
                                :class="selectedColor.toLowerCase() === c.color_name.toLowerCase() ? 'ring-2 ring-[#141414] ring-offset-3 scale-110' : 'border border-black/25 hover:scale-105 opacity-80 hover:opacity-100'"
                                class="w-8 h-8 sm:w-9 sm:h-9 rounded-full transition-all relative flex items-center justify-center shadow-xs"
                                :style="{ backgroundColor: c.color_hex }"
                                :title="c.color_name"
                            >
                                <Check 
                                    v-if="selectedColor.toLowerCase() === c.color_name.toLowerCase()" 
                                    class="w-4 h-4" 
                                    :class="['#111111', '#141414', '#1B4D3E', '#58111A', '#0F1E36'].includes(c.color_hex) ? 'text-white' : 'text-black'" 
                                />
                            </button>
                        </div>
                    </div>

                    <!-- SIZES & SUR-MESURE SELECTION -->
                    <div class="space-y-3 pt-1">
                        <div class="flex justify-between items-center text-xs">
                            <span class="uppercase tracking-widest font-bold text-[#141414]">
                                Sélectionner la Taille :
                            </span>
                            <button 
                                @click="isSizeGuideOpen = true"
                                class="inline-flex items-center space-x-1 text-[11px] uppercase tracking-wider text-[#141414]/70 hover:text-[#141414] underline underline-offset-4"
                            >
                                <Ruler class="w-3.5 h-3.5" />
                                <span>Guide des tailles</span>
                            </button>
                        </div>

                        <!-- Standard Sizes Buttons (Min 44px for easy mobile thumb tap) -->
                        <div class="grid grid-cols-4 sm:grid-cols-5 gap-2">
                            <button 
                                v-for="variant in availableSizes" 
                                :key="variant.id"
                                @click="selectedVariantId = variant.id; isCustomSizeSelected = false"
                                :disabled="variant.stock_quantity <= 0"
                                :class="[
                                    !isCustomSizeSelected && selectedVariantId === variant.id 
                                        ? 'bg-[#141414] text-[#FAF9F6] border-[#141414] shadow-xs' 
                                        : 'bg-[#FAF9F6] text-[#141414] border-[#E8E5DF] hover:border-[#141414]',
                                    variant.stock_quantity <= 0 ? 'opacity-40 line-through cursor-not-allowed bg-gray-100' : ''
                                ]"
                                class="min-h-[46px] py-2.5 text-xs uppercase tracking-wider font-semibold border transition-all text-center flex flex-col items-center justify-center select-none"
                            >
                                <span>{{ variant.size }}</span>
                                <span v-if="variant.stock_quantity <= 0" class="text-[8px] text-red-500 font-normal no-underline">
                                    Épuisé
                                </span>
                            </button>

                            <!-- Option Sur-Mesure Button -->
                            <button 
                                @click="isCustomSizeSelected = true"
                                :class="isCustomSizeSelected ? 'bg-[#141414] text-[#FAF9F6] border-[#141414] shadow-xs' : 'bg-[#FAF9F6] text-[#141414] border-[#E8E5DF] hover:border-[#141414]'"
                                class="col-span-2 min-h-[46px] py-2.5 text-xs uppercase tracking-wider font-semibold border transition-all text-center flex items-center justify-center space-x-1.5"
                            >
                                <Scissors class="w-3.5 h-3.5 text-[#C5A059]" />
                                <span>Sur-Mesure</span>
                            </button>
                        </div>

                        <!-- Formulaire Sur-Mesure -->
                        <div v-if="isCustomSizeSelected" class="mt-3 p-4 bg-[#F3F1EC] border border-[#E8E5DF] space-y-3 animate-in fade-in duration-200">
                            <div class="flex items-center space-x-2 text-xs font-bold text-[#141414] uppercase tracking-wider">
                                <Scissors class="w-4 h-4 text-[#C5A059]" />
                                <span>Atelier Sur-Mesure DjaDja (en centimètres)</span>
                            </div>
                            <p class="text-[11px] text-[#141414]/70">
                                Indiquez vos mensurations. Nos modélistes à Dakar ajusteront le patron sur-mesure pour un tombé impérial.
                            </p>
                            <div class="grid grid-cols-2 gap-2.5 text-xs">
                                <div>
                                    <label class="block text-[10px] uppercase font-bold text-[#141414]/70 mb-1">Tour de Poitrine (cm)</label>
                                    <input v-model="customMeasurements.poitrine" type="number" placeholder="ex: 90" class="w-full bg-[#FAF9F6] border border-[#E8E5DF] p-2.5 text-xs focus:outline-none" />
                                </div>
                                <div>
                                    <label class="block text-[10px] uppercase font-bold text-[#141414]/70 mb-1">Tour de Taille (cm)</label>
                                    <input v-model="customMeasurements.taille" type="number" placeholder="ex: 72" class="w-full bg-[#FAF9F6] border border-[#E8E5DF] p-2.5 text-xs focus:outline-none" />
                                </div>
                                <div>
                                    <label class="block text-[10px] uppercase font-bold text-[#141414]/70 mb-1">Tour de Hanches (cm)</label>
                                    <input v-model="customMeasurements.hanches" type="number" placeholder="ex: 98" class="w-full bg-[#FAF9F6] border border-[#E8E5DF] p-2.5 text-xs focus:outline-none" />
                                </div>
                                <div>
                                    <label class="block text-[10px] uppercase font-bold text-[#141414]/70 mb-1">Longueur souhaitée (cm)</label>
                                    <input v-model="customMeasurements.longueur" type="number" placeholder="ex: 145" class="w-full bg-[#FAF9F6] border border-[#E8E5DF] p-2.5 text-xs focus:outline-none" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ACTION BUTTONS -->
                    <div class="space-y-3 pt-2">
                        <!-- Add to Cart or Restock Notification -->
                        <button 
                            v-if="isCustomSizeSelected || isCurrentVariantInStock"
                            @click="handleAddToCart"
                            class="w-full min-h-[50px] flex items-center justify-center space-x-3 bg-[#141414] text-[#FAF9F6] py-4 text-xs uppercase tracking-[0.2em] font-semibold hover:bg-black transition-all shadow-md active:scale-[0.99]"
                        >
                            <ShoppingBag class="w-4 h-4" />
                            <span>{{ isCustomSizeSelected ? 'Commander en Sur-Mesure' : 'Ajouter au Panier' }}</span>
                        </button>

                        <button 
                            v-else
                            @click="isRestockOpen = true"
                            class="w-full min-h-[50px] bg-[#FAF9F6] text-[#141414] border-2 border-[#141414] py-4 text-xs uppercase tracking-[0.2em] font-semibold hover:bg-[#141414] hover:text-[#FAF9F6] transition-all"
                        >
                            M'avertir du Retour en Stock
                        </button>

                        <!-- Notification Toast -->
                        <div v-if="addedNotification" class="bg-black text-[#FAF9F6] text-xs text-center py-2 uppercase tracking-widest animate-in fade-in">
                            ✓ Robe ajoutée à votre panier d'achat
                        </div>

                        <!-- Direct WhatsApp Order Button -->
                        <a 
                            :href="`https://wa.me/${whatsappNumber.replace(/[^0-9]/g, '')}?text=Bonjour%20Dress%20By%20DjaDja,%20je%20souhaite%20commander%20la%20robe%20${encodeURIComponent(dress.name)}%20en%20couleur%20${encodeURIComponent(selectedColor)}${isCustomSizeSelected ? '%20en%20Sur-Mesure' : '%20en%20taille%20' + encodeURIComponent(currentVariant?.size || '')}.`"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="w-full min-h-[48px] flex items-center justify-center space-x-2 bg-emerald-800 text-white py-3.5 text-xs uppercase tracking-[0.18em] font-medium hover:bg-emerald-900 transition-colors shadow-xs"
                        >
                            <MessageCircle class="w-4 h-4" />
                            <span>Commander sur WhatsApp (1-Clic)</span>
                        </a>
                    </div>

                    <!-- Description Text -->
                    <p class="text-xs sm:text-sm text-[#141414]/80 leading-relaxed font-light pt-2">
                        {{ dress.description }}
                    </p>

                    <!-- Accordions -->
                    <div class="border-t border-[#E8E5DF] divide-y divide-[#E8E5DF] text-xs">
                        
                        <!-- Accordion 1: Matière & Tissu -->
                        <div class="py-3.5">
                            <button 
                                @click="toggleAccordion('fabric')"
                                class="w-full flex justify-between items-center text-left uppercase tracking-wider font-semibold text-[#141414] py-1"
                            >
                                <span>Matière & Confection</span>
                                <ChevronDown class="w-4 h-4 transition-transform duration-200" :class="openAccordion === 'fabric' ? 'rotate-180' : ''" />
                            </button>
                            <div v-show="openAccordion === 'fabric'" class="mt-2 text-[#141414]/75 font-light leading-relaxed">
                                {{ dress.fabric_details || 'Soie de qualité supérieure sélectionnée auprès des meilleurs tisseurs. Confection soignée à la main dans notre atelier de Dakar.' }}
                            </div>
                        </div>

                        <!-- Accordion 2: Entretien -->
                        <div class="py-3.5">
                            <button 
                                @click="toggleAccordion('care')"
                                class="w-full flex justify-between items-center text-left uppercase tracking-wider font-semibold text-[#141414] py-1"
                            >
                                <span>Conseils d'Entretien</span>
                                <ChevronDown class="w-4 h-4 transition-transform duration-200" :class="openAccordion === 'care' ? 'rotate-180' : ''" />
                            </button>
                            <div v-show="openAccordion === 'care'" class="mt-2 text-[#141414]/75 font-light leading-relaxed">
                                {{ dress.care_guide || 'Nettoyage à sec délicat recommandé. Repassage doux à la vapeur sur l\'envers pour préserver l\'éclat des fibres.' }}
                            </div>
                        </div>

                        <!-- Accordion 3: Livraison & Retours -->
                        <div class="py-3.5">
                            <button 
                                @click="toggleAccordion('shipping')"
                                class="w-full flex justify-between items-center text-left uppercase tracking-wider font-semibold text-[#141414] py-1"
                            >
                                <span>Livraison Dakar & Diaspora</span>
                                <ChevronDown class="w-4 h-4 transition-transform duration-200" :class="openAccordion === 'shipping' ? 'rotate-180' : ''" />
                            </button>
                            <div v-show="openAccordion === 'shipping'" class="mt-2 space-y-2 text-[#141414]/75 font-light leading-relaxed">
                                <p><strong>Dakar & Alentours :</strong> Livraison par coursier en 24h à 48h. Paiement Wave, Orange Money ou à la livraison.</p>
                                <p><strong>Régions du Sénégal :</strong> Envois sécurisés par agence relais sous 48h à 72h.</p>
                                <p><strong>Diaspora (France, Belgique, USA, Canada) :</strong> Expéditions hebdomadaires par coursiers GP (Grand Porteur) ou DHL Express.</p>
                            </div>
                        </div>

                    </div>

                </div>
            </div>

            <!-- Related Dresses Section -->
            <section v-if="relatedDresses.length > 0" class="mt-20 pt-14 border-t border-[#E8E5DF]">
                <div class="text-center mb-10">
                    <span class="text-xs uppercase tracking-[0.3em] text-[#141414]/60 font-semibold">Complétez votre vestiaire</span>
                    <h2 class="font-serif text-2xl sm:text-3xl font-bold text-[#141414] mt-1">Vous Aimerez Aussi</h2>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-6">
                    <ProductCard 
                        v-for="rel in relatedDresses" 
                        :key="rel.id" 
                        :dress="rel" 
                    />
                </div>
            </section>
        </main>

        <!-- STICKY MOBILE BOTTOM BAR (Luxury Mobile UX) -->
        <div class="lg:hidden fixed bottom-0 inset-x-0 z-30 bg-[#FAF9F6]/95 backdrop-blur-md border-t border-[#E8E5DF] p-3 shadow-xl flex items-center justify-between gap-3">
            <div class="min-w-0 flex-1">
                <span class="block text-xs font-serif font-bold text-[#141414] truncate">
                    {{ dress.name }}
                </span>
                <span class="text-xs font-semibold text-[#141414]">
                    {{ formatPrice(currentVariant?.price_xof_override || dress.base_price_xof) }}
                </span>
            </div>

            <button 
                @click="handleAddToCart"
                :disabled="!isCurrentVariantInStock && !isCustomSizeSelected"
                class="flex-shrink-0 bg-[#141414] text-[#FAF9F6] text-[11px] uppercase tracking-wider font-bold px-5 py-3 hover:bg-black transition-colors disabled:opacity-50"
            >
                {{ isCustomSizeSelected ? 'Commander' : 'Ajouter' }}
            </button>
        </div>

        <Footer />
    </div>
</template>
