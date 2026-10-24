<script setup>
import { Link } from '@inertiajs/vue3';
import { X, Trash2, Plus, Minus, ArrowRight, ShieldCheck } from 'lucide-vue-next';
import { useCart } from '../composables/useCart.js';
import { useCurrency } from '../composables/useCurrency.js';

const { 
    items, 
    isDrawerOpen, 
    removeItem, 
    updateQuantity, 
    totalXof, 
    freeShippingProgress, 
    amountLeftForFreeShipping 
} = useCart();

const { formatPrice } = useCurrency();
</script>

<template>
    <div v-if="isDrawerOpen" class="fixed inset-0 z-50 overflow-hidden">
        <!-- Backdrop -->
        <div 
            @click="isDrawerOpen = false"
            class="absolute inset-0 bg-[#141414]/40 backdrop-blur-xs transition-opacity duration-300"
        />

        <div class="fixed inset-y-0 right-0 max-w-full flex pl-3 sm:pl-10">
            <div class="w-screen max-w-md bg-[#FAF9F6] shadow-2xl flex flex-col border-l border-[#E8E5DF]">
                
                <!-- Drawer Header -->
                <div class="p-6 border-b border-[#E8E5DF] flex items-center justify-between">
                    <div>
                        <h2 class="font-serif text-2xl font-bold tracking-wide text-[#141414]">Votre Panier</h2>
                        <p class="text-xs tracking-wider text-[#141414]/60 uppercase mt-0.5">
                            {{ items.length }} {{ items.length > 1 ? 'articles sélectionnés' : 'article sélectionné' }}
                        </p>
                    </div>
                    <button 
                        @click="isDrawerOpen = false"
                        class="p-2 text-[#141414]/60 hover:text-[#141414] transition-colors"
                        aria-label="Fermer"
                    >
                        <X class="w-5 h-5 stroke-[1.5]" />
                    </button>
                </div>

                <!-- Free Delivery Progress Bar -->
                <div class="bg-[#F3F1EC] p-4 border-b border-[#E8E5DF]">
                    <div class="flex items-center justify-between text-xs tracking-wider uppercase font-medium text-[#141414] mb-2">
                        <span v-if="amountLeftForFreeShipping > 0">
                            Plus que <strong class="text-[#141414] font-bold">{{ formatPrice(amountLeftForFreeShipping) }}</strong> pour la livraison offerte à Dakar
                        </span>
                        <span v-else class="text-emerald-800 font-bold flex items-center space-x-1">
                            <ShieldCheck class="w-4 h-4 inline" />
                            <span>Félicitations ! Livraison offerte à Dakar</span>
                        </span>
                    </div>
                    <div class="w-full bg-[#E8E5DF] h-1.5 rounded-full overflow-hidden">
                        <div 
                            class="bg-[#141414] h-full transition-all duration-500 rounded-full"
                            :style="{ width: `${freeShippingProgress}%` }"
                        />
                    </div>
                </div>

                <!-- Cart Items List -->
                <div class="flex-1 overflow-y-auto p-6 divide-y divide-[#E8E5DF]">
                    <div v-if="items.length === 0" class="h-full flex flex-col items-center justify-center text-center py-12">
                        <p class="font-serif text-xl text-[#141414]/60 italic mb-4">Votre panier est vide</p>
                        <p class="text-xs text-[#141414]/60 max-w-xs mb-6 uppercase tracking-wider">
                            Explorez nos créations et trouvez la robe d'exception qui sublimera votre silhouette.
                        </p>
                        <Link 
                            href="/collections" 
                            @click="isDrawerOpen = false"
                            class="inline-block bg-[#141414] text-[#FAF9F6] text-xs uppercase tracking-[0.2em] font-medium px-8 py-3.5 hover:bg-[#141414]/90 transition-all"
                        >
                            Découvrir les robes
                        </Link>
                    </div>

                    <div 
                        v-for="item in items" 
                        :key="item.variant_id"
                        class="py-5 flex space-x-4 first:pt-0 last:pb-0"
                    >
                        <!-- Item Image -->
                        <div class="w-20 h-26 bg-[#F3F1EC] overflow-hidden flex-shrink-0 border border-[#E8E5DF]">
                            <img :src="item.image" :alt="item.product_name" class="w-full h-full object-cover object-center" />
                        </div>

                        <!-- Item Details -->
                        <div class="flex-1 flex flex-col justify-between">
                            <div>
                                <div class="flex justify-between items-start">
                                    <h3 class="font-serif text-base font-semibold text-[#141414] leading-snug">
                                        {{ item.product_name }}
                                    </h3>
                                    <button 
                                        @click="removeItem(item.variant_id)" 
                                        class="text-[#141414]/40 hover:text-red-600 transition-colors ml-2"
                                        title="Retirer"
                                    >
                                        <Trash2 class="w-4 h-4 stroke-[1.5]" />
                                    </button>
                                </div>
                                <div class="mt-1 flex items-center space-x-2 text-xs text-[#141414]/70">
                                    <span class="inline-block w-2.5 h-2.5 rounded-full border border-black/10" :style="{ backgroundColor: item.color_hex }" />
                                    <span>{{ item.color_name }}</span>
                                    <span>•</span>
                                    <span class="font-medium uppercase">Taille {{ item.size }}</span>
                                </div>
                            </div>

                            <div class="flex items-center justify-between mt-3">
                                <!-- Quantity Selector -->
                                <div class="flex items-center border border-[#E8E5DF] bg-[#F3F1EC] rounded-xs text-xs">
                                    <button 
                                        @click="updateQuantity(item.variant_id, item.quantity - 1)" 
                                        class="p-1.5 hover:bg-[#E8E5DF] transition-colors"
                                        aria-label="Diminuer"
                                    >
                                        <Minus class="w-3 h-3" />
                                    </button>
                                    <span class="px-3 font-medium">{{ item.quantity }}</span>
                                    <button 
                                        @click="updateQuantity(item.variant_id, item.quantity + 1)" 
                                        class="p-1.5 hover:bg-[#E8E5DF] transition-colors"
                                        aria-label="Augmenter"
                                    >
                                        <Plus class="w-3 h-3" />
                                    </button>
                                </div>

                                <!-- Line Price -->
                                <span class="font-semibold text-sm text-[#141414]">
                                    {{ formatPrice(item.price_xof * item.quantity) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Drawer Footer -->
                <div v-if="items.length > 0" class="p-6 border-t border-[#E8E5DF] bg-[#FAF9F6]">
                    <div class="flex justify-between items-baseline mb-4">
                        <span class="text-xs uppercase tracking-widest text-[#141414]/70 font-medium">Sous-total</span>
                        <span class="font-serif text-2xl font-bold text-[#141414]">{{ formatPrice(totalXof) }}</span>
                    </div>
                    <p class="text-[11px] text-[#141414]/60 tracking-wide mb-4">
                        Taxes incluses. Frais de livraison calculés lors de la validation.
                    </p>
                    <Link 
                        href="/commander" 
                        @click="isDrawerOpen = false"
                        class="w-full flex items-center justify-center space-x-2 bg-[#141414] text-[#FAF9F6] py-4 text-xs uppercase tracking-[0.2em] font-semibold hover:bg-[#141414]/90 transition-all shadow-md group"
                    >
                        <span>Commander maintenant</span>
                        <ArrowRight class="w-4 h-4 transition-transform group-hover:translate-x-1" />
                    </Link>
                </div>

            </div>
        </div>
    </div>
</template>
