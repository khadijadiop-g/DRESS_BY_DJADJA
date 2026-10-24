<script setup>
import { ref, computed } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import { ShoppingBag, Menu, X, MessageCircle, User, ShieldCheck, LogOut, ChevronDown } from 'lucide-vue-next';
import { useCart } from '../composables/useCart.js';
import { useCurrency } from '../composables/useCurrency.js';

const { isDrawerOpen, itemCount } = useCart();
const { activeCurrency, setCurrency } = useCurrency();
const page = usePage();

const isMobileMenuOpen = ref(false);
const isCollectionsOpen = ref(false);

const whatsappNumber = page.props.whatsappNumber || '+221776059575';
const categories = computed(() => page.props.categoriesMenu || []);
const user = computed(() => page.props.auth?.user || null);

const logout = () => {
    router.post('/logout');
};
</script>

<template>
    <!-- Main Luxury Navigation Header -->
    <header class="sticky top-0 z-40 bg-[#FAF9F6]/95 backdrop-blur-md border-b border-[#E8E5DF] transition-all">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="relative flex items-center justify-between h-16 sm:h-20">
                
                <!-- Left: Navigation Links (Desktop) & Hamburger (Mobile) -->
                <div class="flex items-center space-x-6">
                    <!-- Mobile Menu Button -->
                    <button 
                        @click="isMobileMenuOpen = !isMobileMenuOpen"
                        class="lg:hidden p-2 text-[#141414] -ml-2 focus:outline-none"
                        aria-label="Menu"
                    >
                        <Menu v-if="!isMobileMenuOpen" class="w-6 h-6 stroke-[1.5]" />
                        <X v-else class="w-6 h-6 stroke-[1.5]" />
                    </button>

                    <!-- Desktop Nav (Clean, single-line, airy) -->
                    <nav class="hidden lg:flex items-center space-x-7 text-[11px] font-medium uppercase tracking-[0.2em] text-[#141414]/75">
                        
                        <!-- Collections Dropdown Flyout -->
                        <div 
                            class="relative"
                            @mouseenter="isCollectionsOpen = true"
                            @mouseleave="isCollectionsOpen = false"
                        >
                            <Link 
                                href="/collections" 
                                class="flex items-center space-x-1 py-4 hover:text-[#141414] transition-colors"
                            >
                                <span>Collections</span>
                                <ChevronDown 
                                    class="w-3 h-3 transition-transform duration-200" 
                                    :class="isCollectionsOpen ? 'rotate-180' : ''" 
                                />
                            </Link>

                            <!-- Ethereal Dropdown Menu -->
                            <div 
                                v-show="isCollectionsOpen" 
                                class="absolute top-full left-0 w-56 bg-[#FAF9F6] border border-[#E8E5DF] shadow-xl py-3 z-50 animate-in fade-in slide-in-from-top-1 duration-150"
                            >
                                <Link 
                                    href="/collections" 
                                    class="block px-4 py-2 text-[10px] uppercase tracking-[0.18em] font-bold text-[#141414] hover:bg-[#F3F1EC] transition-colors"
                                >
                                    Toutes les Robes
                                </Link>
                                <div class="my-1.5 border-t border-[#E8E5DF]/60"></div>
                                <Link 
                                    v-for="cat in categories" 
                                    :key="cat.id" 
                                    :href="`/collections?collection=${cat.slug}`"
                                    class="block px-4 py-2 text-[10px] uppercase tracking-[0.18em] font-medium text-[#141414]/70 hover:text-[#141414] hover:bg-[#F3F1EC] transition-colors"
                                >
                                    {{ cat.name }}
                                </Link>
                            </div>
                        </div>

                        <!-- Direct clean links -->
                        <Link href="/collections" class="hover:text-[#141414] transition-colors">
                            Sur-Mesure
                        </Link>
                        <a href="/#atelier" class="hover:text-[#141414] transition-colors">
                            L'Atelier
                        </a>
                    </nav>
                </div>

                <!-- Center: Absolutely Centered Luxury Logo (Scaled for mobile so it never collides) -->
                <div class="absolute left-1/2 -translate-x-1/2 text-center pointer-events-auto">
                    <Link href="/" class="group flex flex-col items-center">
                        <span class="font-serif text-lg sm:text-2xl md:text-3xl tracking-[0.14em] sm:tracking-[0.22em] uppercase font-bold text-[#141414] hover:opacity-85 transition-opacity whitespace-nowrap">
                            Dress By DjaDja
                        </span>
                        <span class="text-[7px] sm:text-[8px] tracking-[0.35em] sm:tracking-[0.45em] uppercase text-[#141414]/60 -mt-0.5 sm:-mt-1 font-sans font-light whitespace-nowrap">
                            Haute Création • Dakar
                        </span>
                    </Link>
                </div>

                <!-- Right: Utility Actions (Desktop: Currency + WA + Auth + Cart; Mobile: ONLY Cart) -->
                <div class="flex items-center space-x-3 sm:space-x-5">
                    
                    <!-- Desktop Currency Switcher (Hidden on Mobile) -->
                    <div class="hidden md:flex items-center text-[11px] font-semibold tracking-wider text-[#141414]/50">
                        <button 
                            @click="setCurrency('XOF')"
                            :class="activeCurrency === 'XOF' ? 'text-[#141414] font-bold' : 'hover:text-[#141414]'"
                            class="transition-colors cursor-pointer"
                        >
                            XOF
                        </button>
                        <span class="mx-1.5 opacity-30 text-[10px]">/</span>
                        <button 
                            @click="setCurrency('EUR')"
                            :class="activeCurrency === 'EUR' ? 'text-[#141414] font-bold' : 'hover:text-[#141414]'"
                            class="transition-colors cursor-pointer"
                        >
                            EUR
                        </button>
                    </div>

                    <!-- Desktop WhatsApp Icon Button (Hidden on Mobile) -->
                    <a 
                        :href="`https://wa.me/${whatsappNumber.replace(/[^0-9]/g, '')}`" 
                        target="_blank"
                        rel="noopener noreferrer"
                        class="hidden md:block p-1.5 text-[#141414]/70 hover:text-emerald-800 transition-colors"
                        title="Échanger sur WhatsApp"
                    >
                        <MessageCircle class="w-4.5 h-4.5 stroke-[1.5]" />
                    </a>

                    <!-- Desktop Account / Auth (Hidden on Mobile) -->
                    <div class="hidden md:flex items-center">
                        <template v-if="user">
                            <div class="flex items-center space-x-2">
                                <Link 
                                    v-if="user.role === 'super_admin'" 
                                    href="/admin"
                                    class="inline-flex items-center space-x-1 text-[10px] uppercase tracking-wider font-bold text-[#C5A059] hover:underline"
                                >
                                    <ShieldCheck class="w-3.5 h-3.5" />
                                    <span>Admin</span>
                                </Link>

                                <Link 
                                    v-else 
                                    href="/mon-compte"
                                    class="p-1.5 text-[#141414]/70 hover:text-[#141414] transition-colors"
                                    title="Mon Espace Client"
                                >
                                    <User class="w-4.5 h-4.5 stroke-[1.5] text-[#C5A059]" />
                                </Link>

                                <button 
                                    @click="logout" 
                                    class="text-[#141414]/40 hover:text-red-700 p-1 transition-colors"
                                    title="Se déconnecter"
                                >
                                    <LogOut class="w-3.5 h-3.5 stroke-[1.5]" />
                                </button>
                            </div>
                        </template>

                        <template v-else>
                            <Link 
                                href="/login" 
                                class="text-[11px] uppercase tracking-[0.18em] font-medium text-[#141414]/80 hover:text-[#141414] transition-colors"
                            >
                                Connexion
                            </Link>
                        </template>
                    </div>

                    <!-- Shopping Bag Trigger Button (Clean on both mobile & desktop) -->
                    <button 
                        @click="isDrawerOpen = true" 
                        class="relative p-2 text-[#141414] hover:opacity-70 transition-opacity"
                        aria-label="Panier"
                    >
                        <ShoppingBag class="w-5 h-5 stroke-[1.5]" />
                        <span 
                            v-if="itemCount > 0"
                            class="absolute top-0.5 right-0.5 bg-[#141414] text-[#FAF9F6] text-[8px] w-3.5 h-3.5 rounded-full flex items-center justify-center font-bold tracking-tighter"
                        >
                            {{ itemCount }}
                        </span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Slide Menu (Opened by Hamburger) -->
        <div v-if="isMobileMenuOpen" class="lg:hidden bg-[#FAF9F6] border-b border-[#E8E5DF] px-6 py-6 space-y-5 animate-in slide-in-from-top duration-200 shadow-xl">
            
            <!-- Mobile Auth Card -->
            <div class="p-4 bg-[#F3F1EC] border border-[#E8E5DF]">
                <template v-if="user">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <User class="w-4 h-4 text-[#C5A059]" />
                            <span class="text-xs font-bold text-[#141414] truncate">
                                {{ user.name }}
                            </span>
                        </div>
                        <span v-if="user.role === 'super_admin'" class="bg-[#141414] text-[#FAF9F6] text-[8px] uppercase tracking-wider font-bold px-2 py-0.5">
                            SuperAdmin
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <Link 
                            v-if="user.role === 'super_admin'"
                            href="/admin" 
                            @click="isMobileMenuOpen = false"
                            class="text-center bg-[#141414] text-white py-2 font-bold uppercase tracking-wider text-[11px]"
                        >
                            Dashboard
                        </Link>
                        <Link 
                            href="/mon-compte" 
                            @click="isMobileMenuOpen = false"
                            class="text-center bg-white border border-[#E8E5DF] text-[#141414] py-2 font-semibold uppercase tracking-wider text-[11px]"
                        >
                            Mon Compte
                        </Link>
                        <button 
                            @click="logout(); isMobileMenuOpen = false" 
                            class="col-span-2 text-center text-red-700 py-1.5 font-semibold uppercase tracking-wider text-[10px] mt-1"
                        >
                            Déconnexion
                        </button>
                    </div>
                </template>

                <template v-else>
                    <div class="grid grid-cols-2 gap-2">
                        <Link 
                            href="/login" 
                            @click="isMobileMenuOpen = false"
                            class="text-center bg-white border border-[#141414] text-[#141414] text-xs uppercase tracking-wider font-bold py-2.5"
                        >
                            Connexion
                        </Link>
                        <Link 
                            href="/register" 
                            @click="isMobileMenuOpen = false"
                            class="text-center bg-[#141414] text-[#FAF9F6] text-xs uppercase tracking-wider font-bold py-2.5"
                        >
                            Inscription
                        </Link>
                    </div>
                </template>
            </div>

            <!-- Mobile Currency Switcher -->
            <div class="flex items-center justify-between py-2 border-b border-[#E8E5DF] text-xs">
                <span class="uppercase tracking-wider text-[#141414]/60 text-[10px] font-medium">Devise des prix</span>
                <div class="flex items-center bg-[#F3F1EC] p-0.5 rounded-full border border-[#E8E5DF] text-[11px] font-semibold">
                    <button 
                        @click="setCurrency('XOF')"
                        :class="activeCurrency === 'XOF' ? 'bg-[#141414] text-[#FAF9F6] shadow-xs' : 'text-[#141414]/60'"
                        class="px-3 py-1 rounded-full transition-all"
                    >
                        FCFA
                    </button>
                    <button 
                        @click="setCurrency('EUR')"
                        :class="activeCurrency === 'EUR' ? 'bg-[#141414] text-[#FAF9F6] shadow-xs' : 'text-[#141414]/60'"
                        class="px-3 py-1 rounded-full transition-all"
                    >
                        EUR (€)
                    </button>
                </div>
            </div>

            <!-- Navigation Links -->
            <div class="space-y-3 pt-1">
                <Link 
                    href="/collections" 
                    @click="isMobileMenuOpen = false"
                    class="block text-xs uppercase tracking-[0.2em] font-bold text-[#141414] py-1"
                >
                    Toutes les Robes
                </Link>

                <div class="pl-2 space-y-2 border-l-2 border-[#E8E5DF]">
                    <Link 
                        v-for="cat in categories" 
                        :key="cat.id" 
                        :href="`/collections?collection=${cat.slug}`"
                        @click="isMobileMenuOpen = false"
                        class="block text-[11px] uppercase tracking-[0.18em] font-medium text-[#141414]/75 py-0.5 hover:text-[#141414]"
                    >
                        {{ cat.name }}
                    </Link>
                </div>

                <Link 
                    href="/collections" 
                    @click="isMobileMenuOpen = false"
                    class="block text-xs uppercase tracking-[0.2em] font-medium text-[#141414] py-1"
                >
                    Sur-Mesure
                </Link>

                <a 
                    href="/#atelier" 
                    @click="isMobileMenuOpen = false"
                    class="block text-xs uppercase tracking-[0.2em] font-medium text-[#141414] py-1"
                >
                    L'Atelier
                </a>
            </div>

            <!-- WhatsApp Direct Contact -->
            <div class="pt-4 border-t border-[#E8E5DF] flex items-center justify-between">
                <span class="text-[11px] tracking-wider uppercase text-[#141414]/60">Conciergerie DjaDja</span>
                <a 
                    :href="`https://wa.me/${whatsappNumber.replace(/[^0-9]/g, '')}`" 
                    target="_blank"
                    rel="noopener noreferrer"
                    class="text-xs font-semibold text-emerald-800 flex items-center space-x-1"
                >
                    <MessageCircle class="w-4 h-4" />
                    <span>WhatsApp Direct</span>
                </a>
            </div>
        </div>
    </header>
</template>
