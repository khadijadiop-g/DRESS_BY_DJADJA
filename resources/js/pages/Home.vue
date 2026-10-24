<script setup>
import { computed } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ArrowRight, Sparkles, User, UserPlus, ShieldCheck, Scissors } from 'lucide-vue-next';
import Navbar from '../components/Navbar.vue';
import CartDrawer from '../components/CartDrawer.vue';
import ProductCard from '../components/ProductCard.vue';
import Footer from '../components/Footer.vue';

defineProps({
    featuredDresses: {
        type: Array,
        default: () => [],
    },
    newArrivals: {
        type: Array,
        default: () => [],
    },
    collections: {
        type: Array,
        default: () => [],
    },
});

const page = usePage();
const user = computed(() => page.props.auth?.user || null);
</script>

<template>

    <Head title="Robes de Créateur & Prêt-à-Porter Féminin — Dress By DjaDja Dakar" />

    <div class="min-h-screen flex flex-col bg-[#FAF9F6] overflow-x-hidden">
        <Navbar />
        <CartDrawer />

        <main class="flex-1 w-full overflow-x-hidden">
            
            <!-- Hero Section (Immersive Full Screen Visual Banner) -->
            <section class="relative h-[80vh] sm:h-[88vh] w-full overflow-hidden bg-[#141414]">
                <img 
                    src="/images/hero3.png" 
                    alt="Dress By DjaDja Robe de Créateur" 
                    class="absolute inset-0 w-full h-full object-cover object-[54%_center] md:object-right opacity-80 filter brightness-95 transform scale-100 transition-transform duration-1000"
                />
                <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/30 to-black/30" />

                <!-- Hero Content Overlay (Mobile-optimized typography) -->
                <div class="relative h-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col justify-end pb-12 sm:pb-20">
                    <div class="max-w-2xl text-left text-[#FAF9F6] space-y-3 sm:space-y-4 animate-in fade-in slide-in-from-bottom-6 duration-700">
                        <span class="inline-flex items-center space-x-2 text-[10px] sm:text-xs uppercase tracking-[0.25em] sm:tracking-[0.3em] font-medium text-[#E5D3B3]">
                            <Sparkles class="w-3.5 h-3.5" />
                            <span>Collection Cérémonie & Soirée 2026</span>
                        </span>

                        <h1 class="font-serif text-3xl sm:text-5xl lg:text-7xl font-bold tracking-tight leading-[1.08] text-[#FAF9F6]">
                            L'Élégance Pure, Façonnée pour Vous.
                        </h1>

                        <p class="text-xs sm:text-base text-[#FAF9F6]/85 max-w-lg font-light leading-relaxed">
                            Robes sculpturales, soies impériales et coupes intemporelles confectionnées avec passion par la maison DjaDja à Dakar.
                        </p>

                        <!-- Main CTAs -->
                        <div class="pt-3 sm:pt-4 flex flex-col sm:flex-row gap-3">
                            <Link 
                                href="/collections"
                                class="inline-flex items-center justify-center space-x-3 bg-[#FAF9F6] text-[#141414] text-xs uppercase tracking-[0.2em] font-bold px-8 py-4 hover:bg-white transition-all shadow-lg text-center"
                            >
                                <span>Explorer la collection</span>
                                <ArrowRight class="w-4 h-4" />
                            </Link>

                            <template v-if="!user">
                                <Link 
                                    href="/register"
                                    class="inline-flex items-center justify-center space-x-2 bg-transparent border border-[#FAF9F6]/50 text-[#FAF9F6] text-xs uppercase tracking-[0.2em] font-medium px-6 py-4 hover:bg-white/10 hover:border-white transition-all text-center"
                                >
                                    <UserPlus class="w-4 h-4" />
                                    <span>Rejoindre le Cercle Privé</span>
                                </Link>
                            </template>
                            <template v-else>
                                <Link 
                                    :href="user.role === 'super_admin' ? '/admin' : '/mon-compte'"
                                    class="inline-flex items-center justify-center space-x-2 bg-[#C5A059] text-[#141414] text-xs uppercase tracking-[0.2em] font-bold px-6 py-4 hover:bg-[#d6b574] transition-all text-center"
                                >
                                    <span>Mon Espace Client</span>
                                    <ArrowRight class="w-4 h-4" />
                                </Link>
                            </template>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Featured Dresses Section (Pièces Signatures) -->
            <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 sm:py-20">
                <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-8 sm:mb-12">
                    <div>
                        <span class="text-[10px] sm:text-xs uppercase tracking-[0.3em] text-[#141414]/60 font-semibold">Sélection Privée</span>
                        <h2 class="font-serif text-2xl sm:text-4xl font-bold text-[#141414] mt-1">
                            Les Pièces Signatures
                        </h2>
                    </div>
                    <Link 
                        href="/collections" 
                        class="mt-3 sm:mt-0 inline-flex items-center space-x-1.5 text-xs uppercase tracking-[0.2em] font-semibold text-[#141414] hover:opacity-70 transition-opacity"
                    >
                        <span>Voir toutes les créations</span>
                        <ArrowRight class="w-4 h-4" />
                    </Link>
                </div>

                <!-- Products Grid (2 columns on mobile, 4 on desktop) -->
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-x-3 gap-y-8 sm:gap-x-6 sm:gap-y-12">
                    <ProductCard 
                        v-for="dress in featuredDresses" 
                        :key="dress.id" 
                        :dress="dress" 
                    />
                </div>
            </section>

            <!-- DEDICATED MEMBER SECTION: Cercle Privé DjaDja (Connexion & Inscription) -->


            <!-- Categories Univers Banner Section -->
            <section class="bg-[#F3F1EC] py-16 sm:py-20 border-b border-[#E8E5DF]">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="text-center max-w-xl mx-auto mb-10 sm:mb-14">
                        <span class="text-[10px] sm:text-xs uppercase tracking-[0.3em] text-[#141414]/60 font-semibold">Univers</span>
                        <h2 class="font-serif text-2xl sm:text-4xl font-bold text-[#141414] mt-1">
                            Nos Univers de Création
                        </h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8">
                        <Link 
                            v-for="cat in collections" 
                            :key="cat.id" 
                            :href="`/collections?collection=${cat.slug}`"
                            class="group relative aspect-[4/5] overflow-hidden bg-[#FAF9F6] border border-[#E8E5DF] block shadow-xs"
                        >
                            <img 
                                :src="cat.image_url" 
                                :alt="cat.name" 
                                class="w-full h-full object-cover object-center transition-transform duration-700 group-hover:scale-105"
                            />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/25 to-transparent" />
                            
                            <div class="absolute inset-x-0 bottom-0 p-6 sm:p-8 text-white">
                                <h3 class="font-serif text-xl sm:text-2xl font-bold mb-1">{{ cat.name }}</h3>
                                <p class="text-xs text-white/80 line-clamp-2 font-light mb-4">{{ cat.description }}</p>
                                <span class="inline-flex items-center space-x-2 text-xs uppercase tracking-widest font-semibold text-[#E5D3B3] group-hover:underline underline-offset-4">
                                    <span>Découvrir</span>
                                    <ArrowRight class="w-3.5 h-3.5" />
                                </span>
                            </div>
                        </Link>
                    </div>
                </div>
            </section>

            <!-- Editorial Brand Story Section (Mobile Responsive) -->
            <section id="atelier" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-16 items-center">
                    <div class="aspect-[4/5] bg-[#F3F1EC] border border-[#E8E5DF] overflow-hidden shadow-xs">
                        <img 
                            src="/images/hero.jpeg" 
                            alt="Atelier Dress By DjaDja Dakar" 
                            class="w-full h-full object-cover object-center"
                        />
                    </div>

                    <div class="space-y-5 sm:space-y-6">
                        <span class="text-[10px] sm:text-xs uppercase tracking-[0.3em] text-[#141414]/60 font-semibold">L'Atelier DjaDja</span>
                        <h2 class="font-serif text-2xl sm:text-4xl lg:text-5xl font-bold text-[#141414] leading-tight">
                            L'Art du Beau Tombé & des Matières Nobles.
                        </h2>
                        <p class="text-xs sm:text-sm text-[#141414]/75 leading-relaxed font-light">
                            Chaque robe <strong>Dress By DjaDja</strong> est pensée comme une œuvre vestimentaire unique. Née à Dakar et célébrée par la diaspora à Paris, Londres et New York, notre maison refuse les tendances éphémères pour créer des silhouettes qui traversent le temps avec grâce.
                        </p>
                        <p class="text-xs sm:text-sm text-[#141414]/75 leading-relaxed font-light">
                            Du choix méticuleux des soies lourdes aux finitions des coutures réalisées à la main, nous nous engageons à offrir à chaque femme l'assurance d'une allure majestueuse et confortable.
                        </p>

                        <div class="pt-4 grid grid-cols-3 gap-4 border-t border-[#E8E5DF] text-center sm:text-left">
                            <div>
                                <span class="font-serif text-2xl sm:text-3xl font-bold text-[#141414]">100%</span>
                                <p class="text-[9px] sm:text-[10px] uppercase tracking-wider text-[#141414]/60 mt-0.5">Finitions Main</p>
                            </div>
                            <div>
                                <span class="font-serif text-2xl sm:text-3xl font-bold text-[#141414]">Dakar</span>
                                <p class="text-[9px] sm:text-[10px] uppercase tracking-wider text-[#141414]/60 mt-0.5">Atelier Local</p>
                            </div>
                            <div>
                                <span class="font-serif text-2xl sm:text-3xl font-bold text-[#141414]">GP Express</span>
                                <p class="text-[9px] sm:text-[10px] uppercase tracking-wider text-[#141414]/60 mt-0.5">Diaspora Direct</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        </main>

        <Footer />
        
    </div>
</template>