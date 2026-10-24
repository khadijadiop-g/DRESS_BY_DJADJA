<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowRight } from 'lucide-vue-next';
import Navbar from '../../components/Navbar.vue';
import Footer from '../../components/Footer.vue';

const name = ref('');
const email = ref('');
const phone = ref('');
const password = ref('');
const passwordConfirmation = ref('');
const isSubmitting = ref(false);
const errors = ref({});

const submit = () => {
    isSubmitting.value = true;
    errors.value = {};

    router.post('/register', {
        name: name.value,
        email: email.value,
        phone: phone.value,
        password: password.value,
        password_confirmation: passwordConfirmation.value,
    }, {
        onError: (errs) => {
            isSubmitting.value = false;
            errors.value = errs;
        },
        onFinish: () => {
            isSubmitting.value = false;
        },
    });
};
</script>

<template>
    <Head title="Créer un Compte — Dress By DjaDja" />

    <div class="min-h-screen flex flex-col bg-[#FAF9F6] overflow-x-hidden">
        <Navbar />

        <main class="flex-1 max-w-md mx-auto w-full px-4 sm:px-6 py-16 sm:py-20">
            <div class="bg-[#F3F1EC] p-8 sm:p-10 border border-[#E8E5DF] shadow-sm">
                
                <div class="text-center mb-8">
                    <span class="text-xs uppercase tracking-[0.3em] text-[#141414]/60 font-semibold">Maison DjaDja</span>
                    <h1 class="font-serif text-3xl font-bold text-[#141414] mt-1">Nouveau Compte</h1>
                    <p class="text-xs text-[#141414]/70 mt-1">Rejoignez notre cercle privé pour suivre vos commandes et sauvegarder vos mensurations.</p>
                </div>

                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label class="block text-xs uppercase tracking-wider font-semibold text-[#141414] mb-1">
                            Nom & Prénom *
                        </label>
                        <input 
                            v-model="name"
                            type="text" 
                            required
                            placeholder="Ex: Fatou Sow"
                            class="w-full bg-[#FAF9F6] border border-[#E8E5DF] p-3 text-xs focus:outline-none focus:border-[#141414]"
                        />
                        <span v-if="errors.name" class="text-[10px] text-red-600">{{ errors.name }}</span>
                    </div>

                    <div>
                        <label class="block text-xs uppercase tracking-wider font-semibold text-[#141414] mb-1">
                            Adresse Email *
                        </label>
                        <input 
                            v-model="email"
                            type="email" 
                            required
                            placeholder="exemple@email.com"
                            class="w-full bg-[#FAF9F6] border border-[#E8E5DF] p-3 text-xs focus:outline-none focus:border-[#141414]"
                        />
                        <span v-if="errors.email" class="text-[10px] text-red-600">{{ errors.email }}</span>
                    </div>

                    <div>
                        <label class="block text-xs uppercase tracking-wider font-semibold text-[#141414] mb-1">
                            Numéro WhatsApp / Téléphone *
                        </label>
                        <input 
                            v-model="phone"
                            type="tel" 
                            required
                            placeholder="+221 77 000 00 00"
                            class="w-full bg-[#FAF9F6] border border-[#E8E5DF] p-3 text-xs focus:outline-none focus:border-[#141414]"
                        />
                        <span v-if="errors.phone" class="text-[10px] text-red-600">{{ errors.phone }}</span>
                    </div>

                    <div>
                        <label class="block text-xs uppercase tracking-wider font-semibold text-[#141414] mb-1">
                            Mot de passe *
                        </label>
                        <input 
                            v-model="password"
                            type="password" 
                            required
                            placeholder="Minimum 6 caractères"
                            class="w-full bg-[#FAF9F6] border border-[#E8E5DF] p-3 text-xs focus:outline-none focus:border-[#141414]"
                        />
                        <span v-if="errors.password" class="text-[10px] text-red-600">{{ errors.password }}</span>
                    </div>

                    <div>
                        <label class="block text-xs uppercase tracking-wider font-semibold text-[#141414] mb-1">
                            Confirmer le mot de passe *
                        </label>
                        <input 
                            v-model="passwordConfirmation"
                            type="password" 
                            required
                            placeholder="Répétez votre mot de passe"
                            class="w-full bg-[#FAF9F6] border border-[#E8E5DF] p-3 text-xs focus:outline-none focus:border-[#141414]"
                        />
                    </div>

                    <button 
                        type="submit"
                        :disabled="isSubmitting"
                        class="w-full flex items-center justify-center space-x-2 bg-[#141414] text-[#FAF9F6] py-3.5 text-xs uppercase tracking-[0.2em] font-semibold hover:bg-black transition-all shadow-md disabled:opacity-50 mt-2"
                    >
                        <span>{{ isSubmitting ? 'Création en cours...' : 'Créer mon compte' }}</span>
                        <ArrowRight class="w-3.5 h-3.5" />
                    </button>
                </form>

                <div class="mt-8 pt-6 border-t border-[#E8E5DF] text-center text-xs text-[#141414]/70">
                    <p>Vous avez déjà un compte ?</p>
                    <Link href="/login" class="font-semibold text-[#141414] uppercase tracking-wider hover:underline mt-1 inline-block">
                        Se connecter
                    </Link>
                </div>

            </div>
        </main>

        <Footer />
    </div>
</template>
