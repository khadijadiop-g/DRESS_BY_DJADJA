<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { Search, MessageCircle, ExternalLink } from 'lucide-vue-next';
import AdminLayout from '../../../components/AdminLayout.vue';

const props = defineProps({
    orders: Object,
    filters: Object,
});

const status = ref(props.filters?.status || '');
const search = ref(props.filters?.search || '');

const handleFilter = () => {
    router.get('/admin/orders', {
        status: status.value || undefined,
        search: search.value || undefined,
    }, { preserveState: true, preserveScroll: true });
};

const formatPriceXof = (val) => `${Number(val || 0).toLocaleString('fr-FR')} FCFA`;

const getStatusBadge = (s) => {
    switch (s) {
        case 'pending': return { text: 'En attente', class: 'bg-amber-100 text-amber-800' };
        case 'processing': return { text: 'En confection', class: 'bg-blue-100 text-blue-800' };
        case 'in_transit': return { text: 'Au coursier/GP', class: 'bg-purple-100 text-purple-800' };
        case 'delivered': return { text: 'Livrée', class: 'bg-emerald-100 text-emerald-800' };
        default: return { text: s, class: 'bg-gray-100 text-gray-800' };
    }
};
</script>

<template>
    <Head title="Gestion des Commandes — SuperAdmin DjaDja" />

    <AdminLayout>
        <div class="space-y-6">
            <div>
                <h1 class="font-serif text-3xl font-bold text-[#141414]">Gestion des Commandes</h1>
                <p class="text-xs text-[#141414]/70">Suivi des confections, livraisons Dakar et envois GP diaspora.</p>
            </div>

            <!-- Filters -->
            <div class="bg-[#FAF9F6] p-4 border border-[#E8E5DF] flex flex-col sm:flex-row gap-3 items-center justify-between">
                <div class="flex flex-1 items-center space-x-3 w-full sm:w-auto">
                    <input 
                        v-model="search"
                        @keyup.enter="handleFilter"
                        type="text" 
                        placeholder="Rechercher par n° commande, nom ou téléphone..."
                        class="w-full sm:max-w-md bg-[#F3F1EC] border border-[#E8E5DF] p-2 text-xs focus:outline-none"
                    />

                    <select v-model="status" @change="handleFilter" class="bg-[#F3F1EC] border border-[#E8E5DF] p-2 text-xs focus:outline-none">
                        <option value="">Tous les statuts</option>
                        <option value="pending">En attente</option>
                        <option value="processing">En confection</option>
                        <option value="in_transit">Au coursier / GP</option>
                        <option value="delivered">Livrée</option>
                    </select>
                </div>

                <button @click="handleFilter" class="bg-[#141414] text-[#FAF9F6] text-xs uppercase tracking-wider px-4 py-2 font-semibold">
                    Filtrer
                </button>
            </div>

            <!-- Orders Table -->
            <div class="bg-[#FAF9F6] border border-[#E8E5DF] overflow-hidden shadow-xs">
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left">
                        <thead class="bg-[#F3F1EC] text-[10px] uppercase tracking-wider text-[#141414]/70">
                            <tr>
                                <th class="py-3 px-4">Commande</th>
                                <th class="py-3 px-4">Date</th>
                                <th class="py-3 px-4">Cliente</th>
                                <th class="py-3 px-4">Zone</th>
                                <th class="py-3 px-4">Paiement</th>
                                <th class="py-3 px-4">Statut Confection/Livraison</th>
                                <th class="py-3 px-4">Montant Total</th>
                                <th class="py-3 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#E8E5DF]">
                            <tr v-for="order in orders.data" :key="order.id" class="hover:bg-[#F3F1EC]/50 transition-colors">
                                <td class="py-3 px-4 font-bold text-[#141414]">{{ order.order_number }}</td>
                                <td class="py-3 px-4 text-[#141414]/60">{{ new Date(order.created_at).toLocaleDateString('fr-FR') }}</td>
                                <td class="py-3 px-4">
                                    <div class="font-medium text-[#141414]">{{ order.customer_name }}</div>
                                    <div class="text-[11px] text-[#141414]/60">{{ order.customer_phone }}</div>
                                </td>
                                <td class="py-3 px-4 uppercase text-[10px] font-semibold">{{ order.delivery_zone }}</td>
                                <td class="py-3 px-4 uppercase text-[10px] font-semibold">{{ order.payment_method }}</td>
                                <td class="py-3 px-4">
                                    <span :class="getStatusBadge(order.order_status).class" class="text-[10px] uppercase font-bold px-2 py-0.5 rounded">
                                        {{ getStatusBadge(order.order_status).text }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 font-semibold text-[#141414]">{{ formatPriceXof(order.total_amount_xof) }}</td>
                                <td class="py-3 px-4 text-right">
                                    <Link :href="`/admin/orders/${order.id}`" class="text-xs font-semibold text-[#141414] hover:underline">
                                        Détails & Suivi
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
