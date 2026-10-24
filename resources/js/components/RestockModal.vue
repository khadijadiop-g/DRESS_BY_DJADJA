<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { X, Bell, CheckCircle } from 'lucide-vue-next';

const props = defineProps({
    isOpen: Boolean,
    variantId: Number,
    dressName: String,
    size: String,
});

const emit = defineEmits(['close']);

const contactInfo = ref('');
const contactType = ref('whatsapp');
const isSubmitting = ref(false);
const isSuccess = ref(false);

const submit = () => {
    if (!props.variantId || !contactInfo.value) return;

    isSubmitting.value = true;
    router.post(
        '/restock/subscribe',
        {
            variant_id: props.variantId,
            contact_info: contactInfo.value,
            contact_type: contactType.value,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                isSubmitting.value = false;
                isSuccess.value = true;
                setTimeout(() => {
                    isSuccess.value = false;
                    contactInfo.value = '';
                    emit('close');
                }, 2000);
            },
            onError: () => {
                isSubmitting.value = false;
            },
        }
    );
};
</script>

<template>
    <div v-if="isOpen" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4 text-center">
            <!-- Backdrop -->
            <div @click="emit('close')" class="fixed inset-0 bg-black/60 backdrop-blur-xs transition-opacity" />

            <!-- Modal Content -->
            <div class="relative bg-[#FAF9F6] w-full max-w-md p-8 text-left shadow-2xl border border-[#E8E5DF] z-10 animate-in zoom-in-95 duration-200">
                <div class="flex justify-between items-center pb-4 border-b border-[#E8E5DF]">
                    <div class="flex items-center space-x-2">
                        <Bell class="w-5 h-5 text-[#141414]" />
                        <h3 class="font-serif text-2xl font-bold text-[#141414]">Alerte Réassort</h3>
                    </div>
                    <button @click="emit('close')" class="text-[#141414]/60 hover:text-[#141414]">
                        <X class="w-5 h-5" />
                    </button>
                </div>

                <div v-if="isSuccess" class="py-8 text-center space-y-3">
                    <CheckCircle class="w-12 h-12 text-emerald-700 mx-auto" />
                    <p class="font-serif text-xl font-bold text-[#141414]">Votre demande est enregistrée !</p>
                    <p class="text-xs text-[#141414]/70">Nous vous préviendrons en priorité dès le retour en stock.</p>
                </div>

                <div v-else class="mt-4">
                    <p class="text-xs text-[#141414]/70 mb-4 font-light leading-relaxed">
                        La robe <strong class="font-semibold text-[#141414]">{{ dressName }}</strong> en taille <strong class="font-semibold text-[#141414]">{{ size }}</strong> est actuellement indisponible. Laissez-nous vos coordonnées pour être avertie dès la confection du réassort.
                    </p>

                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <label class="block text-xs uppercase tracking-wider font-semibold text-[#141414] mb-2">
                                Préférence de notification
                            </label>
                            <div class="grid grid-cols-2 gap-2 text-xs">
                                <button 
                                    type="button"
                                    @click="contactType = 'whatsapp'"
                                    :class="contactType === 'whatsapp' ? 'bg-[#141414] text-[#FAF9F6]' : 'bg-[#F3F1EC] text-[#141414]/70'"
                                    class="py-2.5 px-3 border border-[#E8E5DF] font-medium tracking-wider uppercase transition-colors"
                                >
                                    WhatsApp
                                </button>
                                <button 
                                    type="button"
                                    @click="contactType = 'email'"
                                    :class="contactType === 'email' ? 'bg-[#141414] text-[#FAF9F6]' : 'bg-[#F3F1EC] text-[#141414]/70'"
                                    class="py-2.5 px-3 border border-[#E8E5DF] font-medium tracking-wider uppercase transition-colors"
                                >
                                    Email
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs uppercase tracking-wider font-semibold text-[#141414] mb-1.5">
                                {{ contactType === 'whatsapp' ? 'Numéro WhatsApp (avec indicatif)' : 'Adresse Email' }}
                            </label>
                            <input 
                                v-model="contactInfo"
                                :type="contactType === 'whatsapp' ? 'tel' : 'email'" 
                                :placeholder="contactType === 'whatsapp' ? '+221 77 000 00 00' : 'exemple@email.com'"
                                required
                                class="w-full bg-[#FAF9F6] border border-[#E8E5DF] p-3 text-xs focus:outline-none focus:border-[#141414]"
                            />
                        </div>

                        <button 
                            type="submit"
                            :disabled="isSubmitting"
                            class="w-full bg-[#141414] text-[#FAF9F6] py-3.5 text-xs uppercase tracking-[0.2em] font-semibold hover:bg-black transition-colors disabled:opacity-50"
                        >
                            {{ isSubmitting ? 'Enregistrement...' : 'M\'avertir du réassort' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
