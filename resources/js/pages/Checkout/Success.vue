<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import { CheckCircle2, MessageCircle, ArrowRight, Package } from 'lucide-vue-next';
import Navbar from '../../components/Navbar.vue';
import Footer from '../../components/Footer.vue';

const props = defineProps({
    order: {
        type: Object,
        required: true,
    },
});

const page = usePage();
const whatsappNumber = page.props.whatsappNumber || '+221770000000';

const formattedPriceXof = (amount) => {
    return `${Number(amount).toLocaleString('fr-FR')} FCFA`;
};
</script>

<template>
    <Head title="Commande Confirmée — Dress By DjaDja" />

    <div class="min-h-screen flex flex-col bg-[#FAF9F6]">
        <Navbar />

        <main class="flex-1 max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="bg-[#F3F1EC] p-8 sm:p-12 border border-[#E8E5DF] text-center shadow-sm">
                
                <CheckCircle2 class="w-16 h-16 text-emerald-700 mx-auto mb-4 stroke-[1.5]" />
                
                <span class="text-xs uppercase tracking-[0.3em] text-[#141414]/60 font-semibold">Maison DjaDja</span>
                <h1 class="font-serif text-3xl sm:text-5xl font-bold text-[#141414] mt-1 mb-2">
                    Merci pour votre Commande
                </h1>

                <p class="text-xs uppercase tracking-widest text-[#141414]/70 mb-6">
                    Numéro de commande : <strong class="text-[#141414] font-bold">{{ order.order_number }}</strong>
                </p>

                <p class="text-xs sm:text-sm text-[#141414]/80 max-w-md mx-auto leading-relaxed font-light mb-8">
                    Chère {{ order.customer_name }}, votre commande a bien été enregistrée. Notre atelier prépare soigneusement votre robe. Notre livreur vous contactera au <strong>{{ order.customer_phone }}</strong> avant la livraison.
                </p>

                <!-- WhatsApp Direct Assistance -->
                <div class="bg-[#FAF9F6] p-5 border border-[#E8E5DF] max-w-md mx-auto text-left mb-8 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-[#141414]">Besoin d'un suivi direct ?</p>
                        <p class="text-[11px] text-[#141414]/60">Échangez en direct avec la maison DjaDja.</p>
                    </div>
                    <a 
                        :href="`https://wa.me/${whatsappNumber.replace(/[^0-9]/g, '')}?text=Bonjour%20Dress%20By%20DjaDja,%20je%20suis%20${encodeURIComponent(order.customer_name)},%20ma%20commande%20est%20la%20n°%20${encodeURIComponent(order.order_number)}.`"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center space-x-1.5 bg-emerald-800 text-white text-xs uppercase tracking-wider font-medium px-4 py-2 hover:bg-emerald-900 transition-colors"
                    >
                        <MessageCircle class="w-4 h-4" />
                        <span>WhatsApp</span>
                    </a>
                </div>

                <!-- Order Recap -->
                <div class="text-left border-t border-[#E8E5DF] pt-6 mb-8">
                    <h3 class="font-serif text-lg font-bold text-[#141414] mb-3 flex items-center space-x-2">
                        <Package class="w-4 h-4" />
                        <span>Détails de vos créations</span>
                    </h3>
                    <div class="divide-y divide-[#E8E5DF] text-xs">
                        <div v-for="item in order.items" :key="item.id" class="py-2.5 flex justify-between">
                            <div>
                                <span class="font-semibold text-[#141414]">{{ item.product_name }}</span>
                                <span class="text-[#141414]/60 ml-2">({{ item.color_name }}, {{ item.size }}) x {{ item.quantity }}</span>
                            </div>
                            <span class="font-semibold text-[#141414]">{{ formattedPriceXof(item.subtotal_xof) }}</span>
                        </div>
                    </div>
                    <div class="border-t border-[#E8E5DF] pt-3 flex justify-between text-sm font-bold text-[#141414] mt-2">
                        <span>Montant Total</span>
                        <span>{{ formattedPriceXof(order.total_amount_xof) }}</span>
                    </div>
                </div>

                <Link 
                    href="/collections" 
                    class="inline-flex items-center space-x-2 bg-[#141414] text-[#FAF9F6] text-xs uppercase tracking-widest font-semibold px-8 py-3.5 hover:bg-black transition-colors"
                >
                    <span>Retour à la Boutique</span>
                    <ArrowRight class="w-4 h-4" />
                </Link>

            </div>
        </main>

        <Footer />
    </div>
</template>
