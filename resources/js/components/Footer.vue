<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { MessageCircle } from 'lucide-vue-next';

const page = usePage();
const whatsappNumber = page.props.whatsappNumber || '+221776059575';
</script>

<template>
    <footer class="bg-[#141414] text-[#FAF9F6] border-t border-black">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            
            <!-- Compact 4-Column Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 pb-8 border-b border-white/10">
                
                <!-- Col 1: Brand Essence -->
                <div class="space-y-2.5">
                    <div>
                        <span class="font-serif text-xl tracking-[0.2em] uppercase font-bold text-[#FAF9F6] block">
                            Dress By DjaDja
                        </span>
                        <span class="text-[8px] tracking-[0.4em] uppercase text-[#E5D3B3] font-light">
                            Haute Création • Dakar
                        </span>
                    </div>
                    <p class="text-[11px] text-[#FAF9F6]/60 leading-relaxed font-light">
                        Maison de création de robes et prêt-à-porter d'exception. Confection soignée à Dakar.
                    </p>
                    <a 
                        :href="`https://wa.me/${whatsappNumber.replace(/[^0-9]/g, '')}`" 
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center space-x-1.5 text-[11px] uppercase tracking-wider text-emerald-400 hover:text-emerald-300 font-medium transition-colors"
                    >
                        <MessageCircle class="w-3.5 h-3.5" />
                        <span>WhatsApp Conciergerie</span>
                    </a>
                </div>

                <!-- Col 2: Collections -->
                <div>
                    <h4 class="text-[10px] uppercase tracking-[0.25em] font-bold text-[#E5D3B3] mb-3">Collections</h4>
                    <ul class="space-y-2 text-xs text-[#FAF9F6]/70 font-light">
                        <li><Link href="/collections" class="hover:text-white transition-colors">Toutes les Robes</Link></li>
                        <li><Link href="/collections?collection=robes-de-soiree" class="hover:text-white transition-colors">Robes de Soirée</Link></li>
                        <li><Link href="/collections?collection=capsule-soie-satin" class="hover:text-white transition-colors">Capsule Soie & Satin</Link></li>
                        <li><Link href="/collections?collection=les-intemporelles" class="hover:text-white transition-colors">Les Intemporelles</Link></li>
                    </ul>
                </div>

                <!-- Col 3: Service & Livraisons -->
                <div>
                    <h4 class="text-[10px] uppercase tracking-[0.25em] font-bold text-[#E5D3B3] mb-3">Service & Ateliers</h4>
                    <ul class="space-y-2 text-xs text-[#FAF9F6]/70 font-light">
                        <li><span class="hover:text-white transition-colors cursor-default">Livraison Dakar en 24h-48h</span></li>
                        <li><span class="hover:text-white transition-colors cursor-default">Envois Diaspora (GP & Express)</span></li>
                        <li><Link href="/collections" class="hover:text-white transition-colors">Commandes Sur-Mesure</Link></li>
                        <li><Link href="/login" class="hover:text-white transition-colors">Espace Membre Client</Link></li>
                    </ul>
                </div>

                <!-- Col 4: Newsletter / Cercle Privé -->
                <div>
                    <h4 class="text-[10px] uppercase tracking-[0.25em] font-bold text-[#E5D3B3] mb-3">Cercle Privé</h4>
                    <p class="text-[11px] text-[#FAF9F6]/60 mb-3 font-light">
                        Recevez nos invitations exclusives et lancements de capsules.
                    </p>
                    <form @submit.prevent class="flex">
                        <input 
                            type="email" 
                            placeholder="Votre email" 
                            class="w-full bg-white/5 border border-white/20 px-3 py-2 text-xs text-white placeholder-white/40 focus:outline-none focus:border-[#E5D3B3]"
                        />
                        <button class="bg-[#FAF9F6] text-[#141414] text-[10px] uppercase tracking-widest font-bold px-3 hover:bg-white transition-colors flex-shrink-0">
                            Rejoindre
                        </button>
                    </form>
                </div>

            </div>

            <!-- Compact Bottom Bar -->
            <div class="pt-6 flex flex-col sm:flex-row items-center justify-between text-[10px] text-[#FAF9F6]/45 tracking-wider font-light gap-2">
                <p>© {{ new Date().getFullYear() }} Maison Dress By DjaDja. Tous droits réservés.</p>
                <div class="flex items-center space-x-4">
                    <span>Dakar • Diaspora</span>
                    <span>•</span>
                    <span>Paiements : Wave • Orange Money • CB • Livraison</span>
                </div>
            </div>

        </div>
    </footer>
</template>
