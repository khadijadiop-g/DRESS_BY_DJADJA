<script setup>
import { ref } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { Package, User, Scissors, MapPin, LogOut, MessageCircle, Clock } from 'lucide-vue-next';
import Navbar from '../../components/Navbar.vue';
import Footer from '../../components/Footer.vue';

const props = defineProps({
    user: Object,
    orders: {
        type: Array,
        default: () => [],
    },
});

const page = usePage();
const whatsappNumber = page.props.whatsappNumber || '+221770000000';

// Active Tab
const activeTab = ref('orders');

// Profile Form
const name = ref(props.user.name || '');
const phone = ref(props.user.phone || '');
const address = ref(props.user.address || '');
const isSavingProfile = ref(false);

// Measurements Form
const measurements = ref({
    poitrine: props.user.measurements?.poitrine || '',
    taille: props.user.measurements?.taille || '',
    hanches: props.user.measurements?.hanches || '',
    longueur: props.user.measurements?.longueur || '',
});
const isSavingMeasurements = ref(false);

const saveProfile = () => {
    isSavingProfile.value = true;
    router.post('/mon-compte/profil', {
        name: name.value,
        phone: phone.value,
        address: address.value,
    }, {
        onFinish: () => {
            isSavingProfile.value = false;
        },
    });
};

const saveMeasurements = () => {
    isSavingMeasurements.value = true;
    router.post('/mon-compte/mensurations', measurements.value, {
        onFinish: () => {
            isSavingMeasurements.value = false;
        },
    });
};

const logout = () => {
    router.post('/logout');
};

const getStatusBadge = (status) => {
    switch (status) {
        case 'pending':
            return { text: 'En attente', class: 'bg-amber-100 text-amber-800 border-amber-300' };
        case 'processing':
            return { text: 'En confection', class: 'bg-blue-100 text-blue-800 border-blue-300' };
        case 'in_transit':
            return { text: 'Remise au coursier / GP', class: 'bg-purple-100 text-purple-800 border-purple-300' };
        case 'delivered':
            return { text: 'Livrée avec succès', class: 'bg-emerald-100 text-emerald-800 border-emerald-300' };
        case 'cancelled':
            return { text: 'Annulée', class: 'bg-gray-100 text-gray-800 border-gray-300' };
        default:
            return { text: status, class: 'bg-gray-100 text-gray-800 border-gray-300' };
    }
};
</script>

<template>
    <Head title="Mon Espace Client — Dress By DjaDja" />

    <div class="min-h-screen flex flex-col bg-[#FAF9F6] overflow-x-hidden">
        <Navbar />

        <main class="flex-1 max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-8 sm:py-16 w-full">
            
            <!-- Dashboard Header -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center pb-8 border-b border-[#E8E5DF] gap-4">
                <div>
                    <span class="text-xs uppercase tracking-[0.3em] text-[#141414]/60 font-semibold">Cercle Privé DjaDja</span>
                    <h1 class="font-serif text-3xl sm:text-4xl font-bold text-[#141414] mt-1">
                        Bonjour, {{ user.name }}
                    </h1>
                </div>

                <div class="flex items-center space-x-3">
                    <Link 
                        v-if="user.role === 'super_admin'" 
                        href="/admin" 
                        class="bg-[#C5A059] text-white text-xs uppercase tracking-widest font-semibold px-4 py-2.5 hover:bg-[#B38F48] transition-colors"
                    >
                        Accès Back-Office Admin
                    </Link>
                    <button 
                        @click="logout"
                        class="inline-flex items-center space-x-1.5 text-xs uppercase tracking-widest text-red-700 hover:text-red-900 font-semibold border border-red-200 bg-red-50 px-4 py-2.5 transition-colors"
                    >
                        <LogOut class="w-3.5 h-3.5" />
                        <span>Déconnexion</span>
                    </button>
                </div>
            </div>

            <!-- Dashboard Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mt-10">
                
                <!-- Left: Navigation Sidebar -->
                <div class="lg:col-span-3 space-y-2">
                    <button 
                        @click="activeTab = 'orders'"
                        :class="activeTab === 'orders' ? 'bg-[#141414] text-[#FAF9F6]' : 'bg-[#F3F1EC] text-[#141414] hover:bg-[#E8E5DF]'"
                        class="w-full flex items-center space-x-3 p-3.5 text-xs uppercase tracking-wider font-semibold transition-colors text-left"
                    >
                        <Package class="w-4 h-4" />
                        <span>Mes Commandes ({{ orders.length }})</span>
                    </button>

                    <button 
                        @click="activeTab = 'measurements'"
                        :class="activeTab === 'measurements' ? 'bg-[#141414] text-[#FAF9F6]' : 'bg-[#F3F1EC] text-[#141414] hover:bg-[#E8E5DF]'"
                        class="w-full flex items-center space-x-3 p-3.5 text-xs uppercase tracking-wider font-semibold transition-colors text-left"
                    >
                        <Scissors class="w-4 h-4" />
                        <span>Mes Mensurations Sur-Mesure</span>
                    </button>

                    <button 
                        @click="activeTab = 'profile'"
                        :class="activeTab === 'profile' ? 'bg-[#141414] text-[#FAF9F6]' : 'bg-[#F3F1EC] text-[#141414] hover:bg-[#E8E5DF]'"
                        class="w-full flex items-center space-x-3 p-3.5 text-xs uppercase tracking-wider font-semibold transition-colors text-left"
                    >
                        <User class="w-4 h-4" />
                        <span>Mon Profil & Adresses</span>
                    </button>
                </div>

                <!-- Right: Tab Contents -->
                <div class="lg:col-span-9">
                    
                    <!-- Tab 1: Orders -->
                    <div v-if="activeTab === 'orders'" class="space-y-6">
                        <h2 class="font-serif text-2xl font-bold text-[#141414] pb-2 border-b border-[#E8E5DF]">
                            Historique des Commandes
                        </h2>

                        <div v-if="orders.length === 0" class="text-center py-16 bg-[#F3F1EC] border border-[#E8E5DF] p-6">
                            <Package class="w-10 h-10 text-[#141414]/40 mx-auto mb-3" />
                            <p class="font-serif text-xl text-[#141414]/70 italic mb-2">Vous n'avez pas encore passé de commande.</p>
                            <Link href="/collections" class="inline-block bg-[#141414] text-[#FAF9F6] text-xs uppercase tracking-widest font-semibold px-6 py-3 mt-4">
                                Découvrir nos robes
                            </Link>
                        </div>

                        <div v-else class="space-y-4">
                            <div 
                                v-for="order in orders" 
                                :key="order.id"
                                class="bg-[#F3F1EC] border border-[#E8E5DF] p-6 space-y-4"
                            >
                                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center pb-4 border-b border-[#E8E5DF] gap-2">
                                    <div>
                                        <span class="font-serif text-lg font-bold text-[#141414]">Commande n° {{ order.order_number }}</span>
                                        <p class="text-xs text-[#141414]/60">Passée le {{ new Date(order.created_at).toLocaleDateString('fr-FR') }}</p>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <span 
                                            :class="getStatusBadge(order.order_status).class"
                                            class="text-[10px] uppercase font-bold tracking-wider px-3 py-1 rounded-full border"
                                        >
                                            {{ getStatusBadge(order.order_status).text }}
                                        </span>
                                        <a 
                                            :href="`https://wa.me/${whatsappNumber.replace(/[^0-9]/g, '')}?text=Bonjour%20Dress%20By%20DjaDja,%20je%20suhaite%20un%20suivi%20pour%20ma%20commande%20n°%20${encodeURIComponent(order.order_number)}.`" 
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="inline-flex items-center space-x-1 text-xs text-emerald-800 font-semibold hover:underline"
                                        >
                                            <MessageCircle class="w-4 h-4" />
                                            <span>Suivi WhatsApp</span>
                                        </a>
                                    </div>
                                </div>

                                <!-- Items -->
                                <div class="divide-y divide-[#E8E5DF] text-xs">
                                    <div v-for="item in order.items" :key="item.id" class="py-2.5 flex justify-between">
                                        <div>
                                            <span class="font-semibold text-[#141414]">{{ item.product_name }}</span>
                                            <span class="text-[#141414]/70 ml-2">({{ item.color_name }} • {{ item.size }}) x {{ item.quantity }}</span>
                                        </div>
                                        <span class="font-semibold text-[#141414]">{{ item.subtotal_xof.toLocaleString('fr-FR') }} FCFA</span>
                                    </div>
                                </div>

                                <div class="flex justify-between items-baseline pt-2 border-t border-[#E8E5DF] text-xs font-bold text-[#141414]">
                                    <span>Montant Total</span>
                                    <span class="font-serif text-xl">{{ order.total_amount_xof.toLocaleString('fr-FR') }} FCFA ({{ order.total_amount_eur }} €)</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 2: Measurements -->
                    <div v-if="activeTab === 'measurements'" class="bg-[#F3F1EC] border border-[#E8E5DF] p-6 sm:p-8 space-y-6">
                        <div>
                            <h2 class="font-serif text-2xl font-bold text-[#141414]">Mes Mensurations Sur-Mesure</h2>
                            <p class="text-xs text-[#141414]/70 mt-1">
                                Enregistrez vos mensurations une seule fois : elles seront automatiquement utilisées pour la confection de vos robes sur-mesure.
                            </p>
                        </div>

                        <form @submit.prevent="saveMeasurements" class="space-y-4 max-w-lg">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs uppercase tracking-wider font-semibold text-[#141414] mb-1">
                                        Tour de Poitrine (cm)
                                    </label>
                                    <input 
                                        v-model="measurements.poitrine" 
                                        type="number" 
                                        placeholder="Ex: 88" 
                                        class="w-full bg-[#FAF9F6] border border-[#E8E5DF] p-3 text-xs focus:outline-none" 
                                    />
                                </div>
                                <div>
                                    <label class="block text-xs uppercase tracking-wider font-semibold text-[#141414] mb-1">
                                        Tour de Taille (cm)
                                    </label>
                                    <input 
                                        v-model="measurements.taille" 
                                        type="number" 
                                        placeholder="Ex: 70" 
                                        class="w-full bg-[#FAF9F6] border border-[#E8E5DF] p-3 text-xs focus:outline-none" 
                                    />
                                </div>
                                <div>
                                    <label class="block text-xs uppercase tracking-wider font-semibold text-[#141414] mb-1">
                                        Tour de Hanches (cm)
                                    </label>
                                    <input 
                                        v-model="measurements.hanches" 
                                        type="number" 
                                        placeholder="Ex: 96" 
                                        class="w-full bg-[#FAF9F6] border border-[#E8E5DF] p-3 text-xs focus:outline-none" 
                                    />
                                </div>
                                <div>
                                    <label class="block text-xs uppercase tracking-wider font-semibold text-[#141414] mb-1">
                                        Stature / Longueur (cm)
                                    </label>
                                    <input 
                                        v-model="measurements.longueur" 
                                        type="number" 
                                        placeholder="Ex: 168" 
                                        class="w-full bg-[#FAF9F6] border border-[#E8E5DF] p-3 text-xs focus:outline-none" 
                                    />
                                </div>
                            </div>

                            <button 
                                type="submit"
                                :disabled="isSavingMeasurements"
                                class="bg-[#141414] text-[#FAF9F6] text-xs uppercase tracking-widest font-semibold px-6 py-3 hover:bg-black transition-colors"
                            >
                                {{ isSavingMeasurements ? 'Enregistrement...' : 'Enregistrer mes mensurations' }}
                            </button>
                        </form>
                    </div>

                    <!-- Tab 3: Profile & Address -->
                    <div v-if="activeTab === 'profile'" class="bg-[#F3F1EC] border border-[#E8E5DF] p-6 sm:p-8 space-y-6">
                        <div>
                            <h2 class="font-serif text-2xl font-bold text-[#141414]">Mon Profil & Coordonnées</h2>
                            <p class="text-xs text-[#141414]/70 mt-1">Mettez à jour vos coordonnées pour faciliter vos prochaines livraisons.</p>
                        </div>

                        <form @submit.prevent="saveProfile" class="space-y-4 max-w-lg">
                            <div>
                                <label class="block text-xs uppercase tracking-wider font-semibold text-[#141414] mb-1">
                                    Nom & Prénom
                                </label>
                                <input v-model="name" type="text" required class="w-full bg-[#FAF9F6] border border-[#E8E5DF] p-3 text-xs focus:outline-none" />
                            </div>

                            <div>
                                <label class="block text-xs uppercase tracking-wider font-semibold text-[#141414] mb-1">
                                    Numéro WhatsApp / Téléphone
                                </label>
                                <input v-model="phone" type="tel" required class="w-full bg-[#FAF9F6] border border-[#E8E5DF] p-3 text-xs focus:outline-none" />
                            </div>

                            <div>
                                <label class="block text-xs uppercase tracking-wider font-semibold text-[#141414] mb-1">
                                    Adresse habituelle de livraison (Quartier, détails)
                                </label>
                                <textarea v-model="address" rows="3" class="w-full bg-[#FAF9F6] border border-[#E8E5DF] p-3 text-xs focus:outline-none"></textarea>
                            </div>

                            <button 
                                type="submit"
                                :disabled="isSavingProfile"
                                class="bg-[#141414] text-[#FAF9F6] text-xs uppercase tracking-widest font-semibold px-6 py-3 hover:bg-black transition-colors"
                            >
                                {{ isSavingProfile ? 'Mise à jour...' : 'Sauvegarder les modifications' }}
                            </button>
                        </form>
                    </div>

                </div>

            </div>

        </main>

        <Footer />
    </div>
</template>
