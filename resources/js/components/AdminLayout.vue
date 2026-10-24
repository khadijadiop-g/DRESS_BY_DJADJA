<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { LayoutDashboard, ShoppingBag, Layers, ShoppingCart, LogOut, ExternalLink, Plus } from 'lucide-vue-next';

const page = usePage();
const appName = page.props.appName || 'Dress By DjaDja';

const currentUrl = page.url;
</script>

<template>
    <div class="min-h-screen bg-[#F3F1EC] text-[#141414] flex flex-col overflow-x-hidden">
        <!-- Admin Top Navigation Bar -->
        <header class="bg-[#141414] text-[#FAF9F6] border-b border-black">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between h-16">
                <div class="flex items-center space-x-6">
                    <Link href="/admin" class="font-serif text-lg sm:text-xl font-bold tracking-widest uppercase">
                        DjaDja Admin
                    </Link>
                    <nav class="hidden md:flex items-center space-x-6 text-xs uppercase tracking-wider text-[#FAF9F6]/80">
                        <Link href="/admin" class="hover:text-white transition-colors flex items-center space-x-1.5" :class="page.url === '/admin' ? 'text-white font-bold' : ''">
                            <LayoutDashboard class="w-3.5 h-3.5" />
                            <span>Dashboard</span>
                        </Link>
                        <Link href="/admin/products" class="hover:text-white transition-colors flex items-center space-x-1.5" :class="page.url.startsWith('/admin/products') ? 'text-white font-bold' : ''">
                            <ShoppingBag class="w-3.5 h-3.5" />
                            <span>Robes </span>
                        </Link>
                        <Link href="/admin/collections" class="hover:text-white transition-colors flex items-center space-x-1.5" :class="page.url.startsWith('/admin/collections') ? 'text-white font-bold' : ''">
                            <Layers class="w-3.5 h-3.5" />
                            <span>Collections</span>
                        </Link>
                        <Link href="/admin/orders" class="hover:text-white transition-colors flex items-center space-x-1.5" :class="page.url.startsWith('/admin/orders') ? 'text-white font-bold' : ''">
                            <ShoppingCart class="w-3.5 h-3.5" />
                            <span>Commandes</span>
                        </Link>
                    </nav>
                </div>

                <div class="flex items-center space-x-3 sm:space-x-4">
                    <Link 
                        href="/" 
                        class="text-[11px] sm:text-xs uppercase tracking-wider text-[#FAF9F6]/80 hover:text-white flex items-center space-x-1"
                    >
                        <span class="hidden sm:inline">Boutique</span>
                        <ExternalLink class="w-3.5 h-3.5" />
                    </Link>

                    <Link 
                        href="/logout" 
                        method="post" 
                        as="button"
                        class="text-[11px] sm:text-xs uppercase tracking-wider text-red-400 hover:text-red-300 flex items-center space-x-1"
                    >
                        <LogOut class="w-3.5 h-3.5" />
                        <span class="hidden sm:inline">Sortir</span>
                    </Link>
                </div>
            </div>

            <!-- Mobile Admin Navigation Bar (Scrollable horizontally) -->
            <div class="md:hidden flex items-center space-x-2 px-3 py-2 bg-black/40 overflow-x-auto text-[11px] uppercase tracking-wider font-semibold border-t border-white/10 scrollbar-none">
                <Link 
                    href="/admin" 
                    class="px-3 py-1.5 rounded-xs flex-shrink-0 flex items-center space-x-1"
                    :class="page.url === '/admin' ? 'bg-[#C5A059] text-[#141414]' : 'text-white/80 hover:text-white'"
                >
                    <LayoutDashboard class="w-3 h-3" />
                    <span>Dashboard</span>
                </Link>
                <Link 
                    href="/admin/products" 
                    class="px-3 py-1.5 rounded-xs flex-shrink-0 flex items-center space-x-1"
                    :class="page.url.startsWith('/admin/products') ? 'bg-[#C5A059] text-[#141414]' : 'text-white/80 hover:text-white'"
                >
                    <ShoppingBag class="w-3 h-3" />
                    <span>Robes</span>
                </Link>
                <Link 
                    href="/admin/collections" 
                    class="px-3 py-1.5 rounded-xs flex-shrink-0 flex items-center space-x-1"
                    :class="page.url.startsWith('/admin/collections') ? 'bg-[#C5A059] text-[#141414]' : 'text-white/80 hover:text-white'"
                >
                    <Layers class="w-3 h-3" />
                    <span>Collections</span>
                </Link>
                <Link 
                    href="/admin/orders" 
                    class="px-3 py-1.5 rounded-xs flex-shrink-0 flex items-center space-x-1"
                    :class="page.url.startsWith('/admin/orders') ? 'bg-[#C5A059] text-[#141414]' : 'text-white/80 hover:text-white'"
                >
                    <ShoppingCart class="w-3 h-3" />
                    <span>Commandes</span>
                </Link>
                <Link 
                    href="/admin/products/create" 
                    class="px-3 py-1.5 bg-[#FAF9F6] text-[#141414] rounded-xs flex-shrink-0 font-bold flex items-center space-x-1"
                >
                    <Plus class="w-3 h-3" />
                    <span>+ Robe</span>
                </Link>
            </div>
        </header>

        <!-- Flash Messages -->
        <div v-if="page.props.flash?.success" class="bg-emerald-800 text-white text-xs py-2.5 px-4 text-center tracking-wider font-medium">
            {{ page.props.flash.success }}
        </div>
        <div v-if="page.props.flash?.error" class="bg-red-800 text-white text-xs py-2.5 px-4 text-center tracking-wider font-medium">
            {{ page.props.flash.error }}
        </div>

        <!-- Main Admin Content Area -->
        <main class="flex-1 max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-6 sm:py-8 w-full overflow-x-hidden">
            <slot />
        </main>
    </div>
</template>