<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { DollarSign, ShoppingCart, ShoppingBag, AlertTriangle, ArrowRight, MessageCircle } from 'lucide-vue-next';
import AdminLayout from '../../components/AdminLayout.vue';

const props = defineProps({
    metrics: Object,
    recentOrders: {
        type: Array,
        default: () => [],
    },
});

const formatPriceXof = (val) => `${Number(val || 0).toLocaleString('fr-FR')} FCFA`;
const formatPriceEur = (val) => `${Number(val || 0).toFixed(2).replace('.', ',')} €`;

const getStatusBadge = (status) => {
    switch (status) {
        case 'pending':
            return { text: 'En attente', class: 'bg-amber-100 text-amber-800' };
        case 'processing':
            return { text: 'En confection', class: 'bg-blue-100 text-blue-800' };
        case 'in_transit':
            return { text: 'Au coursier/GP', class: 'bg-purple-100 text-purple-800' };
        case 'delivered':
            return { text: 'Livrée', class: 'bg-emerald-100 text-emerald-800' };
        default:
            return { text: status, class: 'bg-gray-100 text-gray-800' };
    }
};
</script>

<template>
    <Head title="Tableau de Bord — SuperAdmin DjaDja" />

    <AdminLayout>
        <div class="space-y-8">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h1 class="font-serif text-3xl font-bold text-[#141414]">Tableau de Bord Exécutif</h1>
                    <p class="text-xs text-[#141414]/70">Suivi des ventes, gestion des collections et commandes en cours.</p>
                </div>
                <div class="flex items-center space-x-3">
                    <Link 
                        href="/admin/products/create"
                        class="bg-[#141414] text-[#FAF9F6] text-xs uppercase tracking-widest font-semibold px-4 py-2.5 hover:bg-black transition-colors"
                    >
                        + Ajouter une Robe
                    </Link>
                </div>
            </div>

            <!-- Metrics Cards Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Today Revenue -->
                <div class="bg-[#FAF9F6] p-5 border border-[#E8E5DF] shadow-xs">
                    <span class="text-[11px] uppercase tracking-wider font-semibold text-[#141414]/60">Chiffre d'Affaires du Jour</span>
                    <div class="font-serif text-2xl font-bold text-[#141414] mt-1">
                        {{ formatPriceXof(metrics.todayRevenueXof) }}
                    </div>
                    <p class="text-xs text-[#141414]/60 mt-0.5">≈ {{ formatPriceEur(metrics.todayRevenueEur) }}</p>
                </div>

                <!-- Month Revenue -->
                <div class="bg-[#FAF9F6] p-5 border border-[#E8E5DF] shadow-xs">
                    <span class="text-[11px] uppercase tracking-wider font-semibold text-[#141414]/60">Chiffre d'Affaires du Mois</span>
                    <div class="font-serif text-2xl font-bold text-[#141414] mt-1">
                        {{ formatPriceXof(metrics.monthRevenueXof) }}
                    </div>
                    <p class="text-xs text-[#141414]/60 mt-0.5">≈ {{ formatPriceEur(metrics.monthRevenueEur) }}</p>
                </div>

                <!-- Pending Orders -->
                <div class="bg-[#FAF9F6] p-5 border border-[#E8E5DF] shadow-xs">
                    <span class="text-[11px] uppercase tracking-wider font-semibold text-[#141414]/60">Commandes à Traiter</span>
                    <div class="font-serif text-2xl font-bold text-amber-700 mt-1">
                        {{ metrics.pendingOrders }}
                    </div>
                    <p class="text-xs text-[#141414]/60 mt-0.5">Total historique : {{ metrics.totalOrders }}</p>
                </div>

                <!-- Low Stock & Restock Alert -->
                <div class="bg-[#FAF9F6] p-5 border border-[#E8E5DF] shadow-xs">
                    <span class="text-[11px] uppercase tracking-wider font-semibold text-[#141414]/60">Alertes Réassorts & Stock</span>
                    <div class="font-serif text-2xl font-bold text-red-700 mt-1">
                        {{ metrics.lowStockVariants }} tailles basses
                    </div>
                    <p class="text-xs text-[#141414]/60 mt-0.5">{{ metrics.pendingRestocks }} clientes en attente</p>
                </div>
            </div>

            <!-- Recent Orders Section -->
            <div class="bg-[#FAF9F6] border border-[#E8E5DF] p-6 shadow-xs">
                <div class="flex justify-between items-center pb-4 border-b border-[#E8E5DF]">
                    <h2 class="font-serif text-xl font-bold text-[#141414]">Dernières Commandes Récemment Reçues</h2>
                    <Link href="/admin/orders" class="text-xs uppercase tracking-wider font-semibold text-[#141414] hover:underline flex items-center space-x-1">
                        <span>Voir tout</span>
                        <ArrowRight class="w-3.5 h-3.5" />
                    </Link>
                </div>

                <div v-if="recentOrders.length === 0" class="py-12 text-center text-xs text-[#141414]/60">
                    Aucune commande enregistrée pour l'instant.
                </div>

                <div v-else class="overflow-x-auto mt-4">
                    <table class="w-full text-xs text-left">
                        <thead class="bg-[#F3F1EC] text-[10px] uppercase tracking-wider text-[#141414]/70">
                            <tr>
                                <th class="py-3 px-3">Numéro</th>
                                <th class="py-3 px-3">Cliente</th>
                                <th class="py-3 px-3">Zone</th>
                                <th class="py-3 px-3">Statut</th>
                                <th class="py-3 px-3">Montant</th>
                                <th class="py-3 px-3 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#E8E5DF]">
                            <tr v-for="order in recentOrders" :key="order.id" class="hover:bg-[#F3F1EC]/60 transition-colors">
                                <td class="py-3 px-3 font-semibold">{{ order.order_number }}</td>
                                <td class="py-3 px-3">
                                    <div class="font-medium text-[#141414]">{{ order.customer_name }}</div>
                                    <div class="text-[11px] text-[#141414]/60">{{ order.customer_phone }}</div>
                                </td>
                                <td class="py-3 px-3 uppercase text-[10px] font-semibold">{{ order.delivery_zone }}</td>
                                <td class="py-3 px-3">
                                    <span :class="getStatusBadge(order.order_status).class" class="text-[10px] uppercase font-bold px-2 py-0.5 rounded">
                                        {{ getStatusBadge(order.order_status).text }}
                                    </span>
                                </td>
                                <td class="py-3 px-3 font-semibold">{{ formatPriceXof(order.total_amount_xof) }}</td>
                                <td class="py-3 px-3 text-right">
                                    <Link :href="`/admin/orders/${order.id}`" class="text-xs font-semibold text-[#141414] hover:underline">
                                        Gérer
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
