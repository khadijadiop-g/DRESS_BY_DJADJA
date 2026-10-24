<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import Navbar from '../../components/Navbar.vue';
import CartDrawer from '../../components/CartDrawer.vue';
import ProductCard from '../../components/ProductCard.vue';
import Footer from '../../components/Footer.vue';

const props = defineProps({
    dresses: {
        type: Array,
        default: () => [],
    },
    collections: {
        type: Array,
        default: () => [],
    },
    activeCollection: Object,
    currentSort: {
        type: String,
        default: 'newest',
    },
});

const handleSortChange = (event) => {
    const url = new URL(window.location.href);
    url.searchParams.set('sort', event.target.value);
    router.visit(url.toString(), { preserveState: true, preserveScroll: true });
};
</script>

<template>
    <Head :title="activeCollection ? `${activeCollection.name} — Dress By DjaDja` : 'Toutes les Robes — Dress By DjaDja'" />

    <div class="min-h-screen flex flex-col bg-[#FAF9F6] overflow-x-hidden">
        <Navbar />
        <CartDrawer />

        <main class="flex-1 max-w-7xl mx-auto w-full px-3 sm:px-6 lg:px-8 py-8 sm:py-12">
            
            <!-- Page Header -->
            <div class="text-center max-w-2xl mx-auto mb-12">
                <span class="text-xs uppercase tracking-[0.3em] text-[#141414]/60 font-semibold">Maison DjaDja</span>
                <h1 class="font-serif text-3xl sm:text-5xl font-bold text-[#141414] mt-2 mb-3">
                    {{ activeCollection ? activeCollection.name : 'Toutes les Créations' }}
                </h1>
                <p class="text-xs sm:text-sm text-[#141414]/70 font-light leading-relaxed">
                    {{ activeCollection?.description || 'Découvrez notre vestiaire exclusif de robes pensées pour illuminer chaque instant avec grâce et distinction.' }}
                </p>
            </div>

            <!-- Filters & Sorting Bar -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 py-6 border-y border-[#E8E5DF] mb-12">
                <!-- Collection Filter Pills -->
                <div class="flex items-center flex-wrap gap-2 text-xs">
                    <Link 
                        href="/collections" 
                        :class="!activeCollection ? 'bg-[#141414] text-[#FAF9F6]' : 'bg-[#F3F1EC] text-[#141414]/70 hover:text-[#141414]'"
                        class="px-4 py-2 uppercase tracking-widest font-medium transition-colors border border-[#E8E5DF]"
                    >
                        Toutes
                    </Link>

                    <Link 
                        v-for="cat in collections" 
                        :key="cat.id" 
                        :href="`/collections?collection=${cat.slug}`"
                        :class="activeCollection?.id === cat.id ? 'bg-[#141414] text-[#FAF9F6]' : 'bg-[#F3F1EC] text-[#141414]/70 hover:text-[#141414]'"
                        class="px-4 py-2 uppercase tracking-widest font-medium transition-colors border border-[#E8E5DF]"
                    >
                        {{ cat.name }}
                    </Link>
                </div>

                <!-- Sort Dropdown -->
                <div class="flex items-center space-x-2 text-xs w-full sm:w-auto justify-end">
                    <label class="text-[#141414]/60 uppercase tracking-wider text-[11px]">Trier par :</label>
                    <select 
                        :value="currentSort" 
                        @change="handleSortChange"
                        class="bg-[#FAF9F6] border border-[#E8E5DF] py-2 px-3 text-xs uppercase tracking-wider text-[#141414] focus:outline-none focus:border-[#141414]"
                    >
                        <option value="newest">Plus récents</option>
                        <option value="price_asc">Prix croissant</option>
                        <option value="price_desc">Prix décroissant</option>
                    </select>
                </div>
            </div>

            <!-- Products Grid -->
            <div v-if="dresses.length > 0" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-x-4 gap-y-10 sm:gap-x-6 sm:gap-y-12">
                <ProductCard 
                    v-for="dress in dresses" 
                    :key="dress.id" 
                    :dress="dress" 
                />
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-20">
                <p class="font-serif text-2xl text-[#141414]/60 italic mb-4">Aucune création dans cette catégorie</p>
                <Link 
                    href="/collections" 
                    class="inline-block bg-[#141414] text-[#FAF9F6] text-xs uppercase tracking-widest font-semibold px-8 py-3.5"
                >
                    Voir toutes les robes
                </Link>
            </div>

        </main>

        <Footer />
    </div>
</template>
