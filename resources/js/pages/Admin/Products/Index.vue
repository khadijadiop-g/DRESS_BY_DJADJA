<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { Plus, Edit, Trash2, Search, Filter } from 'lucide-vue-next';
import AdminLayout from '../../../components/AdminLayout.vue';

const props = defineProps({
    products: Object,
    categories: Array,
    filters: Object,
});

const search = ref(props.filters?.search || '');
const categoryId = ref(props.filters?.category || '');

const handleFilter = () => {
    router.get('/admin/products', {
        search: search.value || undefined,
        category: categoryId.value || undefined,
    }, { preserveState: true, preserveScroll: true });
};

const deleteProduct = (product) => {
    if (confirm(`Êtes-vous sûre de vouloir supprimer la robe "${product.name}" ?`)) {
        router.delete(`/admin/products/${product.id}`);
    }
};

const formatPriceXof = (val) => `${Number(val || 0).toLocaleString('fr-FR')} FCFA`;
</script>

<template>
    <Head title="Gestion des Robes — SuperAdmin DjaDja" />

    <AdminLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h1 class="font-serif text-3xl font-bold text-[#141414]">Catalogue des Robes (CRUD)</h1>
                    <p class="text-xs text-[#141414]/70">Ajoutez, modifiez et gérez les stocks de vos créations.</p>
                </div>
                <Link 
                    href="/admin/products/create"
                    class="inline-flex items-center space-x-2 bg-[#141414] text-[#FAF9F6] text-xs uppercase tracking-widest font-semibold px-4 py-2.5 hover:bg-black transition-colors"
                >
                    <Plus class="w-4 h-4" />
                    <span>Créer une Nouvelle Robe</span>
                </Link>
            </div>

            <!-- Filters Bar -->
            <div class="bg-[#FAF9F6] p-4 border border-[#E8E5DF] flex flex-col sm:flex-row gap-3 items-center justify-between">
                <div class="flex flex-1 items-center space-x-3 w-full sm:w-auto">
                    <div class="relative flex-1 max-w-sm">
                        <Search class="w-3.5 h-3.5 text-[#141414]/40 absolute left-3 top-3" />
                        <input 
                            v-model="search"
                            @keyup.enter="handleFilter"
                            type="text" 
                            placeholder="Rechercher par nom de robe..."
                            class="w-full bg-[#F3F1EC] border border-[#E8E5DF] pl-8 pr-3 py-2 text-xs focus:outline-none"
                        />
                    </div>

                    <select 
                        v-model="categoryId"
                        @change="handleFilter"
                        class="bg-[#F3F1EC] border border-[#E8E5DF] py-2 px-3 text-xs focus:outline-none"
                    >
                        <option value="">Toutes les collections</option>
                        <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                    </select>
                </div>

                <button @click="handleFilter" class="bg-[#141414] text-[#FAF9F6] text-xs uppercase tracking-wider px-4 py-2 font-semibold">
                    Filtrer
                </button>
            </div>

            <!-- Products Table -->
            <div class="bg-[#FAF9F6] border border-[#E8E5DF] overflow-hidden shadow-xs">
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left">
                        <thead class="bg-[#F3F1EC] text-[10px] uppercase tracking-wider text-[#141414]/70">
                            <tr>
                                <th class="py-3 px-4">Visuel</th>
                                <th class="py-3 px-4">Nom de la Robe</th>
                                <th class="py-3 px-4">Collection</th>
                                <th class="py-3 px-4">Prix de Base</th>
                                <th class="py-3 px-4">Déclinaisons (Tailles & Stocks)</th>
                                <th class="py-3 px-4">Statut</th>
                                <th class="py-3 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#E8E5DF]">
                            <tr v-for="product in products.data" :key="product.id" class="hover:bg-[#F3F1EC]/50 transition-colors">
                                <!-- Image -->
                                <td class="py-3 px-4">
                                    <img :src="product.main_image" :alt="product.name" class="w-12 h-16 object-cover border border-[#E8E5DF]" />
                                </td>

                                <!-- Name & Subtitle -->
                                <td class="py-3 px-4">
                                    <div class="font-serif text-base font-bold text-[#141414]">{{ product.name }}</div>
                                    <div class="text-[11px] text-[#141414]/60">{{ product.subtitle }}</div>
                                </td>

                                <!-- Category -->
                                <td class="py-3 px-4 font-medium text-[#141414]/80">
                                    {{ product.category?.name }}
                                </td>

                                <!-- Price -->
                                <td class="py-3 px-4 font-semibold text-[#141414]">
                                    {{ formatPriceXof(product.base_price_xof) }}
                                </td>

                                <!-- Variants (Sizes & Stocks) -->
                                <td class="py-3 px-4">
                                    <div class="flex flex-wrap gap-1 max-w-xs">
                                        <span 
                                            v-for="v in product.variants" 
                                            :key="v.id"
                                            :class="v.stock_quantity > 0 ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : 'bg-red-50 text-red-800 border-red-200'"
                                            class="border px-1.5 py-0.5 text-[9px] font-semibold flex items-center space-x-1"
                                            :title="`${v.color_name} - Taille ${v.size} - Stock: ${v.stock_quantity}`"
                                        >
                                            <span class="w-2 h-2 rounded-full border border-black/20 flex-shrink-0" :style="{ backgroundColor: v.color_hex }"></span>
                                            <span>{{ v.color_name }} {{ v.size }}: {{ v.stock_quantity }}</span>
                                        </span>
                                    </div>
                                </td>

                                <!-- Status -->
                                <td class="py-3 px-4">
                                    <span :class="product.is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-700'" class="text-[10px] uppercase font-bold px-2 py-0.5 rounded">
                                        {{ product.is_active ? 'En ligne' : 'Masquée' }}
                                    </span>
                                </td>

                                <!-- Actions -->
                                <td class="py-3 px-4 text-right space-x-2">
                                    <Link :href="`/admin/products/${product.id}/edit`" class="text-xs font-semibold text-[#141414] hover:underline">
                                        Modifier
                                    </Link>
                                    <button @click="deleteProduct(product)" class="text-xs font-semibold text-red-600 hover:underline">
                                        Supprimer
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
