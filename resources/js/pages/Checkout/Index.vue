<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ShieldCheck, Truck, ArrowLeft, Lock } from 'lucide-vue-next';
import Navbar from '../../components/Navbar.vue';
import Footer from '../../components/Footer.vue';
import { useCart } from '../../composables/useCart.js';
import { useCurrency } from '../../composables/useCurrency.js';

const { items, totalXof, clearCart } = useCart();
const { activeCurrency, formatPrice } = useCurrency();

// Form State
const customerName = ref('');
const customerPhone = ref('');
const customerEmail = ref('');
const deliveryZone = ref('dakar');
const deliveryAddress = ref('');
const paymentMethod = ref('cod');
const notes = ref('');

const isSubmitting = ref(false);
const errorMessage = ref('');

// Shipping cost calculation
const shippingAmountXof = computed(() => {
    switch (deliveryZone.value) {
        case 'dakar':
            return totalXof.value >= 50000 ? 0 : 2500;
        case 'region_sn':
            return 4500;
        case 'international_gp':
            return 15000;
        default:
            return 2500;
    }
});

const grandTotalXof = computed(() => {
    return totalXof.value + shippingAmountXof.value;
});

const submitOrder = () => {
    if (items.value.length === 0) return;

    isSubmitting.value = true;
    errorMessage.value = '';

    const payload = {
        customer_name: customerName.value,
        customer_phone: customerPhone.value,
        customer_email: customerEmail.value || null,
        delivery_zone: deliveryZone.value,
        delivery_address: deliveryAddress.value,
        payment_method: paymentMethod.value,
        currency_used: activeCurrency.value,
        items: items.value.map((i) => ({
            variant_id: i.variant_id,
            quantity: i.quantity,
        })),
        notes: notes.value || null,
    };

    router.post('/commander', payload, {
        onSuccess: () => {
            clearCart();
        },
        onError: (errors) => {
            isSubmitting.value = false;
            errorMessage.value = Object.values(errors)[0] || 'Une erreur est survenue lors de la commande.';
        },
    });
};
</script>

<template>
    <Head title="Commander — Dress By DjaDja" />

    <div class="min-h-screen flex flex-col bg-[#FAF9F6]">
        <Navbar />

        <main class="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-16">
            
            <div class="mb-8">
                <Link href="/collections" class="inline-flex items-center space-x-2 text-xs uppercase tracking-widest text-[#141414]/60 hover:text-[#141414]">
                    <ArrowLeft class="w-3.5 h-3.5" />
                    <span>Continuer mes achats</span>
                </Link>
                <h1 class="font-serif text-3xl sm:text-4xl font-bold text-[#141414] mt-2">
                    Finaliser votre Commande
                </h1>
            </div>

            <!-- Empty cart state -->
            <div v-if="items.length === 0" class="text-center py-20 bg-[#F3F1EC] border border-[#E8E5DF] p-8 max-w-xl mx-auto">
                <p class="font-serif text-2xl text-[#141414]/70 italic mb-4">Votre panier est actuellement vide.</p>
                <Link href="/collections" class="inline-block bg-[#141414] text-[#FAF9F6] text-xs uppercase tracking-widest font-semibold px-8 py-3.5">
                    Découvrir nos créations
                </Link>
            </div>

            <!-- Checkout Form Grid -->
            <form v-else @submit.prevent="submitOrder" class="grid grid-cols-1 lg:grid-cols-12 gap-12">
                
                <!-- Left: Shipping & Payment Form -->
                <div class="lg:col-span-7 space-y-10">
                    
                    <div v-if="errorMessage" class="p-4 bg-red-50 border border-red-200 text-red-700 text-xs">
                        {{ errorMessage }}
                    </div>

                    <!-- Step 1: Coordonnées -->
                    <div class="space-y-4">
                        <div class="flex items-center space-x-3 pb-2 border-b border-[#E8E5DF]">
                            <span class="w-6 h-6 rounded-full bg-[#141414] text-[#FAF9F6] text-xs flex items-center justify-center font-bold">1</span>
                            <h2 class="font-serif text-xl font-bold text-[#141414]">Vos Coordonnées</h2>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs uppercase tracking-wider font-semibold text-[#141414] mb-1.5">
                                    Nom & Prénom *
                                </label>
                                <input 
                                    v-model="customerName"
                                    type="text" 
                                    required
                                    placeholder="Ex: Aïda Diop"
                                    class="w-full bg-[#FAF9F6] border border-[#E8E5DF] p-3 text-xs focus:outline-none focus:border-[#141414]"
                                />
                            </div>

                            <div>
                                <label class="block text-xs uppercase tracking-wider font-semibold text-[#141414] mb-1.5">
                                    Numéro WhatsApp / Téléphone *
                                </label>
                                <input 
                                    v-model="customerPhone"
                                    type="tel" 
                                    required
                                    placeholder="+221 77 000 00 00"
                                    class="w-full bg-[#FAF9F6] border border-[#E8E5DF] p-3 text-xs focus:outline-none focus:border-[#141414]"
                                />
                                <span class="text-[10px] text-[#141414]/50">Indispensable pour la livraison et le suivi.</span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs uppercase tracking-wider font-semibold text-[#141414] mb-1.5">
                                Adresse Email (Optionnel)
                            </label>
                            <input 
                                v-model="customerEmail"
                                type="email" 
                                placeholder="exemple@email.com"
                                class="w-full bg-[#FAF9F6] border border-[#E8E5DF] p-3 text-xs focus:outline-none focus:border-[#141414]"
                            />
                        </div>
                    </div>

                    <!-- Step 2: Livraison -->
                    <div class="space-y-4">
                        <div class="flex items-center space-x-3 pb-2 border-b border-[#E8E5DF]">
                            <span class="w-6 h-6 rounded-full bg-[#141414] text-[#FAF9F6] text-xs flex items-center justify-center font-bold">2</span>
                            <h2 class="font-serif text-xl font-bold text-[#141414]">Zone & Adresse de Livraison</h2>
                        </div>

                        <div>
                            <label class="block text-xs uppercase tracking-wider font-semibold text-[#141414] mb-2">
                                Destination
                            </label>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                                <label 
                                    :class="deliveryZone === 'dakar' ? 'border-[#141414] bg-[#F3F1EC]' : 'border-[#E8E5DF] bg-[#FAF9F6]'"
                                    class="p-4 border cursor-pointer transition-all flex flex-col justify-between"
                                >
                                    <input type="radio" value="dakar" v-model="deliveryZone" class="sr-only" />
                                    <div>
                                        <p class="font-bold text-[#141414] uppercase tracking-wider">Dakar & Banlieue</p>
                                        <p class="text-[11px] text-[#141414]/60 mt-1">Livreur coursier en 24h-48h</p>
                                    </div>
                                    <span class="mt-3 font-semibold text-emerald-800">
                                        {{ totalXof >= 50000 ? 'Livraison Offerte' : formatPrice(2500) }}
                                    </span>
                                </label>

                                <label 
                                    :class="deliveryZone === 'region_sn' ? 'border-[#141414] bg-[#F3F1EC]' : 'border-[#E8E5DF] bg-[#FAF9F6]'"
                                    class="p-4 border cursor-pointer transition-all flex flex-col justify-between"
                                >
                                    <input type="radio" value="region_sn" v-model="deliveryZone" class="sr-only" />
                                    <div>
                                        <p class="font-bold text-[#141414] uppercase tracking-wider">Régions Sénégal</p>
                                        <p class="text-[11px] text-[#141414]/60 mt-1">Thiès, Mbour, Saint-Louis...</p>
                                    </div>
                                    <span class="mt-3 font-semibold text-[#141414]">
                                        {{ formatPrice(4500) }}
                                    </span>
                                </label>

                                <label 
                                    :class="deliveryZone === 'international_gp' ? 'border-[#141414] bg-[#F3F1EC]' : 'border-[#E8E5DF] bg-[#FAF9F6]'"
                                    class="p-4 border cursor-pointer transition-all flex flex-col justify-between"
                                >
                                    <input type="radio" value="international_gp" v-model="deliveryZone" class="sr-only" />
                                    <div>
                                        <p class="font-bold text-[#141414] uppercase tracking-wider">Diaspora (GP)</p>
                                        <p class="text-[11px] text-[#141414]/60 mt-1">France, Europe, USA</p>
                                    </div>
                                    <span class="mt-3 font-semibold text-[#141414]">
                                        {{ formatPrice(15000) }}
                                    </span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs uppercase tracking-wider font-semibold text-[#141414] mb-1.5">
                                Adresse détaillée / Quartier & Repères *
                            </label>
                            <textarea 
                                v-model="deliveryAddress"
                                rows="3"
                                required
                                placeholder="Ex: Almadies, en face du restaurant X, maison avec portail noir..."
                                class="w-full bg-[#FAF9F6] border border-[#E8E5DF] p-3 text-xs focus:outline-none focus:border-[#141414]"
                            />
                        </div>

                        <div>
                            <label class="block text-xs uppercase tracking-wider font-semibold text-[#141414] mb-1.5">
                                Instructions spéciales (Optionnel)
                            </label>
                            <input 
                                v-model="notes"
                                type="text"
                                placeholder="Ex: Horaires de passage préférés, précisions sur la confection..."
                                class="w-full bg-[#FAF9F6] border border-[#E8E5DF] p-3 text-xs focus:outline-none focus:border-[#141414]"
                            />
                        </div>
                    </div>

                    <!-- Step 3: Mode de Règlement -->
                    <div class="space-y-4">
                        <div class="flex items-center space-x-3 pb-2 border-b border-[#E8E5DF]">
                            <span class="w-6 h-6 rounded-full bg-[#141414] text-[#FAF9F6] text-xs flex items-center justify-center font-bold">3</span>
                            <h2 class="font-serif text-xl font-bold text-[#141414]">Mode de Règlement</h2>
                        </div>

                        <div class="space-y-2.5">
                            <!-- Option COD (Cash on Delivery) -->
                            <label 
                                v-if="deliveryZone === 'dakar'"
                                :class="paymentMethod === 'cod' ? 'border-[#141414] bg-[#F3F1EC]' : 'border-[#E8E5DF]'"
                                class="p-4 border flex items-center justify-between cursor-pointer transition-colors"
                            >
                                <div class="flex items-center space-x-3">
                                    <input type="radio" value="cod" v-model="paymentMethod" class="text-black" />
                                    <div>
                                        <p class="text-xs uppercase font-semibold text-[#141414]">Paiement à la Livraison</p>
                                        <p class="text-[11px] text-[#141414]/60">Réglez en espèces ou par Wave directement au coursier à Dakar.</p>
                                    </div>
                                </div>
                                <ShieldCheck class="w-5 h-5 text-[#141414]/50" />
                            </label>

                            <!-- Option Wave -->
                            <label 
                                :class="paymentMethod === 'wave' ? 'border-[#141414] bg-[#F3F1EC]' : 'border-[#E8E5DF]'"
                                class="p-4 border flex items-center justify-between cursor-pointer transition-colors"
                            >
                                <div class="flex items-center space-x-3">
                                    <input type="radio" value="wave" v-model="paymentMethod" class="text-black" />
                                    <div>
                                        <p class="text-xs uppercase font-semibold text-[#141414]">Wave Sénégal</p>
                                        <p class="text-[11px] text-[#141414]/60">Paiement instantané sans frais via l'application Wave.</p>
                                    </div>
                                </div>
                                <span class="bg-[#1EBBD7] text-white text-[10px] font-bold px-2 py-0.5 rounded">Wave</span>
                            </label>

                            <!-- Option Orange Money -->
                            <label 
                                :class="paymentMethod === 'orange_money' ? 'border-[#141414] bg-[#F3F1EC]' : 'border-[#E8E5DF]'"
                                class="p-4 border flex items-center justify-between cursor-pointer transition-colors"
                            >
                                <div class="flex items-center space-x-3">
                                    <input type="radio" value="orange_money" v-model="paymentMethod" class="text-black" />
                                    <div>
                                        <p class="text-xs uppercase font-semibold text-[#141414]">Orange Money</p>
                                        <p class="text-[11px] text-[#141414]/60">Validation sécurisée par code secret OM.</p>
                                    </div>
                                </div>
                                <span class="bg-[#FF6600] text-white text-[10px] font-bold px-2 py-0.5 rounded">OM</span>
                            </label>

                            <!-- Option Carte bancaire / Stripe -->
                            <label 
                                :class="paymentMethod === 'stripe' ? 'border-[#141414] bg-[#F3F1EC]' : 'border-[#E8E5DF]'"
                                class="p-4 border flex items-center justify-between cursor-pointer transition-colors"
                            >
                                <div class="flex items-center space-x-3">
                                    <input type="radio" value="stripe" v-model="paymentMethod" class="text-black" />
                                    <div>
                                        <p class="text-xs uppercase font-semibold text-[#141414]">Carte Bancaire / Visa / Mastercard</p>
                                        <p class="text-[11px] text-[#141414]/60">Paiement sécurisé en ligne (idéal pour la diaspora).</p>
                                    </div>
                                </div>
                                <Lock class="w-4 h-4 text-[#141414]/50" />
                            </label>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button 
                        type="submit"
                        :disabled="isSubmitting"
                        class="w-full bg-[#141414] text-[#FAF9F6] py-4 text-xs uppercase tracking-[0.2em] font-semibold hover:bg-black transition-all shadow-lg disabled:opacity-50"
                    >
                        {{ isSubmitting ? 'Enregistrement de votre commande...' : `Confirmer ma commande (${formatPrice(grandTotalXof)})` }}
                    </button>

                </div>

                <!-- Right: Order Summary Sidebar -->
                <div class="lg:col-span-5">
                    <div class="bg-[#F3F1EC] p-6 sm:p-8 border border-[#E8E5DF] sticky top-28 space-y-6">
                        <h3 class="font-serif text-2xl font-bold text-[#141414] pb-4 border-b border-[#E8E5DF]">
                            Récapitulatif
                        </h3>

                        <!-- Items list -->
                        <div class="divide-y divide-[#E8E5DF] max-h-80 overflow-y-auto pr-1">
                            <div 
                                v-for="item in items" 
                                :key="item.variant_id"
                                class="py-3 flex space-x-3 first:pt-0 last:pb-0"
                            >
                                <img :src="item.image" :alt="item.product_name" class="w-14 h-18 object-cover border border-[#E8E5DF]" />
                                <div class="flex-1 text-xs">
                                    <h4 class="font-semibold text-[#141414]">{{ item.product_name }}</h4>
                                    <p class="text-[#141414]/60 text-[11px] mt-0.5">
                                        {{ item.color_name }} • {{ item.size }}
                                    </p>
                                    <div class="flex justify-between items-center mt-2">
                                        <span class="text-[#141414]/70">Qté : {{ item.quantity }}</span>
                                        <span class="font-semibold text-[#141414]">{{ formatPrice(item.price_xof * item.quantity) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Calculations -->
                        <div class="border-t border-[#E8E5DF] pt-4 space-y-2 text-xs">
                            <div class="flex justify-between text-[#141414]/70">
                                <span>Sous-total articles</span>
                                <span class="font-medium text-[#141414]">{{ formatPrice(totalXof) }}</span>
                            </div>
                            <div class="flex justify-between text-[#141414]/70">
                                <span>Frais de livraison</span>
                                <span class="font-medium text-[#141414]">
                                    {{ shippingAmountXof === 0 ? 'Offerte' : formatPrice(shippingAmountXof) }}
                                </span>
                            </div>
                            <div class="border-t border-[#E8E5DF] pt-3 flex justify-between items-baseline">
                                <span class="text-sm font-bold uppercase tracking-wider text-[#141414]">Total à régler</span>
                                <span class="font-serif text-2xl font-bold text-[#141414]">
                                    {{ formatPrice(grandTotalXof) }}
                                </span>
                            </div>
                        </div>

                        <div class="p-4 bg-[#FAF9F6] border border-[#E8E5DF] text-[11px] text-[#141414]/70 space-y-1">
                            <div class="flex items-center space-x-1.5 font-semibold text-[#141414]">
                                <Truck class="w-4 h-4" />
                                <span>Expédition soignée DjaDja</span>
                            </div>
                            <p>Toutes nos robes sont livrées sous housse protectrice avec étiquette de la maison.</p>
                        </div>
                    </div>
                </div>

            </form>

        </main>

        <Footer />
    </div>
</template>
