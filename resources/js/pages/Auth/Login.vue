<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { Lock, ArrowRight } from 'lucide-vue-next';
import Navbar from '../../components/Navbar.vue';
import Footer from '../../components/Footer.vue';

const login = ref('');
const password = ref('');
const remember = ref(false);
const isSubmitting = ref(false);
const errorMessage = ref('');

const submit = () => {
    isSubmitting.value = true;
    errorMessage.value = '';

    router.post('/login', {
        login: login.value,
        password: password.value,
        remember: remember.value,
    }, {
        onError: (errors) => {
            isSubmitting.value = false;
            errorMessage.value = errors.login || 'Identifiants ou mot de passe incorrects.';
        },
        onFinish: () => {
            isSubmitting.value = false;
        },
    });
};
</script>

<template>
    <Head title="Connexion — Dress By DjaDja" />

    <div class="min-h-screen flex flex-col bg-[#FAF9F6] overflow-x-hidden">
        <Navbar />

        <main class="flex-1 max-w-md mx-auto w-full px-4 sm:px-6 py-16 sm:py-24">
            <div class="bg-[#F3F1EC] p-8 sm:p-10 border border-[#E8E5DF] shadow-sm">
                
                <div class="text-center mb-8">
                    <span class="text-xs uppercase tracking-[0.3em] text-[#141414]/60 font-semibold">Maison DjaDja</span>
                    <h1 class="font-serif text-3xl font-bold text-[#141414] mt-1">Espace Privé</h1>
                    <p class="text-xs text-[#141414]/70 mt-1">Connectez-vous avec votre email ou votre numéro de téléphone.</p>
                </div>

                <div v-if="errorMessage" class="mb-6 p-3.5 bg-red-50 border border-red-200 text-red-700 text-xs">
                    {{ errorMessage }}
                </div>

                <form @submit.prevent="submit" class="space-y-5">
                    <div>
                        <label class="block text-xs uppercase tracking-wider font-semibold text-[#141414] mb-1.5">
                            Email ou Téléphone *
                        </label>
                        <input 
                            v-model="login"
                            type="text" 
                            required
                            placeholder="ex: exemple@email.com ou +221 77 000 00 00"
                            class="w-full bg-[#FAF9F6] border border-[#E8E5DF] p-3 text-xs focus:outline-none focus:border-[#141414]"
                        />
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-1.5">
                            <label class="block text-xs uppercase tracking-wider font-semibold text-[#141414]">
                                Mot de passe *
                            </label>
                        </div>
                        <input 
                            v-model="password"
                            type="password" 
                            required
                            placeholder="••••••••"
                            class="w-full bg-[#FAF9F6] border border-[#E8E5DF] p-3 text-xs focus:outline-none focus:border-[#141414]"
                        />
                    </div>

                    <div class="flex items-center justify-between text-xs">
                        <label class="flex items-center space-x-2 cursor-pointer text-[#141414]/80">
                            <input v-model="remember" type="checkbox" class="text-black" />
                            <span>Se souvenir de moi</span>
                        </label>
                    </div>

                    <button 
                        type="submit"
                        :disabled="isSubmitting"
                        class="w-full flex items-center justify-center space-x-2 bg-[#141414] text-[#FAF9F6] py-3.5 text-xs uppercase tracking-[0.2em] font-semibold hover:bg-black transition-all shadow-md disabled:opacity-50"
                    >
                        <span>{{ isSubmitting ? 'Connexion en cours...' : 'Se Connecter' }}</span>
                        <ArrowRight class="w-3.5 h-3.5" />
                    </button>
                </form>

                <div class="mt-8 pt-6 border-t border-[#E8E5DF] text-center text-xs text-[#141414]/70">
                    <p>Vous n'avez pas encore de compte ?</p>
                    <Link href="/register" class="font-semibold text-[#141414] uppercase tracking-wider hover:underline mt-1 inline-block">
                        Créer un compte client
                    </Link>
                </div>

            </div>
        </main>

        <Footer />
    </div>
</template>
