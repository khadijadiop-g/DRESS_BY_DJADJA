<script setup>
import { X } from 'lucide-vue-next';

defineProps({
    isOpen: Boolean,
});

const emit = defineEmits(['close']);
</script>

<template>
    <div v-if="isOpen" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4 text-center">
            <!-- Backdrop -->
            <div @click="emit('close')" class="fixed inset-0 bg-black/60 backdrop-blur-xs transition-opacity" />

            <!-- Modal Content -->
            <div class="relative bg-[#FAF9F6] w-full max-w-lg p-8 text-left shadow-2xl border border-[#E8E5DF] z-10 animate-in zoom-in-95 duration-200">
                <div class="flex justify-between items-center pb-4 border-b border-[#E8E5DF]">
                    <h3 class="font-serif text-2xl font-bold text-[#141414]">Guide des Tailles</h3>
                    <button @click="emit('close')" class="text-[#141414]/60 hover:text-[#141414]">
                        <X class="w-5 h-5" />
                    </button>
                </div>

                <p class="text-xs text-[#141414]/70 mt-3 mb-6 font-light">
                    Nos robes sont confectionnées selon les standards européens avec des aisances étudiées pour flatter votre tombé. Mesures en centimètres (cm).
                </p>

                <!-- Size Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left text-[#141414]">
                        <thead class="bg-[#F3F1EC] uppercase tracking-wider text-[10px] text-[#141414]/80">
                            <tr>
                                <th class="py-2.5 px-3 font-semibold">Taille</th>
                                <th class="py-2.5 px-3 font-semibold">Poitrine</th>
                                <th class="py-2.5 px-3 font-semibold">Taille</th>
                                <th class="py-2.5 px-3 font-semibold">Hanches</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#E8E5DF]">
                            <tr>
                                <td class="py-2.5 px-3 font-medium">XS (34)</td>
                                <td class="py-2.5 px-3 text-[#141414]/70">80 - 84</td>
                                <td class="py-2.5 px-3 text-[#141414]/70">62 - 66</td>
                                <td class="py-2.5 px-3 text-[#141414]/70">86 - 90</td>
                            </tr>
                            <tr>
                                <td class="py-2.5 px-3 font-medium">S (36)</td>
                                <td class="py-2.5 px-3 text-[#141414]/70">85 - 89</td>
                                <td class="py-2.5 px-3 text-[#141414]/70">67 - 71</td>
                                <td class="py-2.5 px-3 text-[#141414]/70">91 - 95</td>
                            </tr>
                            <tr>
                                <td class="py-2.5 px-3 font-medium">M (38 - 40)</td>
                                <td class="py-2.5 px-3 text-[#141414]/70">90 - 95</td>
                                <td class="py-2.5 px-3 text-[#141414]/70">72 - 77</td>
                                <td class="py-2.5 px-3 text-[#141414]/70">96 - 101</td>
                            </tr>
                            <tr>
                                <td class="py-2.5 px-3 font-medium">L (42)</td>
                                <td class="py-2.5 px-3 text-[#141414]/70">96 - 102</td>
                                <td class="py-2.5 px-3 text-[#141414]/70">78 - 84</td>
                                <td class="py-2.5 px-3 text-[#141414]/70">102 - 108</td>
                            </tr>
                            <tr>
                                <td class="py-2.5 px-3 font-medium">XL (44)</td>
                                <td class="py-2.5 px-3 text-[#141414]/70">103 - 110</td>
                                <td class="py-2.5 px-3 text-[#141414]/70">85 - 92</td>
                                <td class="py-2.5 px-3 text-[#141414]/70">109 - 116</td>
                            </tr>
                            <tr>
                                <td class="py-2.5 px-3 font-medium">Sur-Mesure</td>
                                <td colspan="3" class="py-2.5 px-3 text-emerald-800 font-medium">
                                    Confection personnalisée à vos mensurations exactes
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-6 pt-4 border-t border-[#E8E5DF] text-center">
                    <button 
                        @click="emit('close')"
                        class="bg-[#141414] text-[#FAF9F6] text-xs uppercase tracking-widest font-semibold px-6 py-2.5"
                    >
                        Compris
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
