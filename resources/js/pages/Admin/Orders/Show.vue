<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowLeft, MessageCircle, Package, User, MapPin, CheckCircle } from 'lucide-vue-next';
import AdminLayout from '../../../components/AdminLayout.vue';

const props = defineProps({
    order: Object,
});

const form = ref({
    order_status: props.order.order_status,
    payment_status: props.order.payment_status,
    notes: props.order.notes || '',
});
const isUpdating = ref(false);

const updateStatus = () => {
    isUpdating.value = true;
    router.post(`/admin/orders/${props.order.id}/status`, form.value, {
        onFinish: () => {
            isUpdating.value = false;
        },
    });
};

const formatPriceXof = (val) => `${Number(val || 0).toLocaleString('fr-FR')} FCFA`;

// WhatsApp Link for 1-click messaging
const generateWhatsAppLink = () => {
    const phone = props.order.customer_phone.replace(/[^0-9]/g, '');
    const message = `Bonjour ${props.order.customer_name}, c'est l'atelier Dress By DjaDja concernant votre commande n° ${props.order.order_number}.`;
    return `https://wa.me/${phone}?text=${encodeURIComponent(message)}`;
};
</script>

<template>
    <Head :title="`Commande ${order.order_number} — SuperAdmin`" />

    <AdminLayout>
        <div class="space-y-6 max-w-5xl mx-auto">
            <div class="flex items-center justify-between">
                <Link href="/admin/orders" class="text-xs uppercase tracking-wider text-[#141414]/60 hover:text-[#141414] flex items-center space-x-1">
                    <ArrowLeft class="w-3.5 h-3.5" />
                    <span>Retour aux commandes</span>
                </Link>

                <!-- WhatsApp 1-Click Action -->
                <a 
                    :href="generateWhatsAppLink()" 
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex items-center space-x-2 bg-emerald-800 text-white text-xs uppercase tracking-wider font-semibold px-4 py-2 hover:bg-emerald-900 transition-colors shadow-xs"
                >
                    <MessageCircle class="w-4 h-4" />
                    <span>Échanger sur WhatsApp avec la cliente</span>
                </a>
            </div>

            <!-- Main Order Card -->
            <div class="bg-[#FAF9F6] border border-[#E8E5DF] p-8 shadow-xs space-y-8">
                
                <!-- Order Header -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center pb-6 border-b border-[#E8E5DF] gap-4">
                    <div>
                        <span class="text-xs uppercase tracking-[0.2em] text-[#141414]/60 font-semibold">Détail de Commande</span>
                        <h1 class="font-serif text-3xl font-bold text-[#141414] mt-1">{{ order.order_number }}</h1>
                        <p class="text-xs text-[#141414]/60 mt-0.5">Enregistrée le {{ new Date(order.created_at).toLocaleString('fr-FR') }}</p>
                    </div>

                    <!-- Status Update Form -->
                    <form @submit.prevent="updateStatus" class="flex items-center space-x-3 bg-[#F3F1EC] p-3 border border-[#E8E5DF]">
                        <div>
                            <label class="block text-[9px] uppercase font-semibold text-[#141414]/60 mb-1">Statut Confection</label>
                            <select v-model="form.order_status" class="bg-white border border-[#E8E5DF] p-1.5 text-xs font-semibold">
                                <option value="pending">En attente</option>
                                <option value="processing">En confection (Atelier)</option>
                                <option value="in_transit">Au coursier / GP</option>
                                <option value="delivered">Livrée avec succès</option>
                                <option value="cancelled">Annulée</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[9px] uppercase font-semibold text-[#141414]/60 mb-1">Paiement</label>
                            <select v-model="form.payment_status" class="bg-white border border-[#E8E5DF] p-1.5 text-xs font-semibold">
                                <option value="pending">En attente</option>
                                <option value="paid">Payé</option>
                                <option value="failed">Échoué</option>
                            </select>
                        </div>

                        <button 
                            type="submit" 
                            :disabled="isUpdating"
                            class="bg-[#141414] text-white text-xs uppercase tracking-wider font-semibold px-4 py-2 mt-4 hover:bg-black transition-colors"
                        >
                            {{ isUpdating ? '...' : 'Valider' }}
                        </button>
                    </form>
                </div>

                <!-- Customer & Delivery Coordinates -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
                    <div class="bg-[#F3F1EC] p-5 border border-[#E8E5DF] space-y-2">
                        <div class="flex items-center space-x-2 text-xs font-semibold uppercase tracking-wider text-[#141414]">
                            <User class="w-4 h-4 text-[#C5A059]" />
                            <span>Coordonnées Cliente</span>
                        </div>
                        <p class="font-bold text-sm text-[#141414]">{{ order.customer_name }}</p>
                        <p class="text-xs text-[#141414]">Tél / WhatsApp : <strong>{{ order.customer_phone }}</strong></p>
                        <p v-if="order.customer_email" class="text-xs text-[#141414]">Email : {{ order.customer_email }}</p>
                    </div>

                    <div class="bg-[#F3F1EC] p-5 border border-[#E8E5DF] space-y-2">
                        <div class="flex items-center space-x-2 text-xs font-semibold uppercase tracking-wider text-[#141414]">
                            <MapPin class="w-4 h-4 text-[#C5A059]" />
                            <span>Adresse & Instructions de Livraison</span>
                        </div>
                        <p class="text-xs font-bold uppercase text-[#141414]">Zone : {{ order.delivery_zone }}</p>
                        <p class="text-xs text-[#141414] leading-relaxed">{{ order.delivery_address }}</p>
                        <p v-if="order.notes" class="text-xs text-[#141414]/70 italic mt-2">Note : "{{ order.notes }}"</p>
                    </div>
                </div>

                <!-- Items Table -->
                <div>
                    <h3 class="font-serif text-xl font-bold text-[#141414] pb-3 border-b border-[#E8E5DF] mb-4">
                        Pièces Commandées
                    </h3>

                    <div class="divide-y divide-[#E8E5DF] text-xs">
                        <div v-for="item in order.items" :key="item.id" class="py-4 flex justify-between items-center">
                            <div>
                                <h4 class="font-serif text-base font-bold text-[#141414]">{{ item.product_name }}</h4>
                                <div class="text-[#141414]/70 mt-1">
                                    <span>Couleur : <strong>{{ item.color_name }}</strong></span>
                                    <span class="mx-2">•</span>
                                    <span>Taille / Mensurations : <strong class="text-[#141414]">{{ item.size }}</strong></span>
                                </div>
                            </div>

                            <div class="text-right">
                                <span class="font-bold text-sm text-[#141414]">{{ formatPriceXof(item.subtotal_xof) }}</span>
                                <div class="text-[11px] text-[#141414]/60">Qté : {{ item.quantity }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-[#E8E5DF] flex justify-between items-baseline text-sm font-bold text-[#141414]">
                        <span>Montant Total Réglé / Dû</span>
                        <span class="font-serif text-2xl">{{ formatPriceXof(order.total_amount_xof) }} ({{ order.total_amount_eur }} €)</span>
                    </div>
                </div>

            </div>
        </div>
    </AdminLayout>
</template>
