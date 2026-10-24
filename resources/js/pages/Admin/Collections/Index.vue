<script setup>
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { Plus, Trash2, Edit2 } from 'lucide-vue-next';
import AdminLayout from '../../../components/AdminLayout.vue';

const props = defineProps({
    collections: Array,
});

const form = ref({
    name: '',
    description: '',
    image_url: '',
    is_active: true,
});
const isSubmitting = ref(false);

const submit = () => {
    isSubmitting.value = true;
    router.post('/admin/collections', form.value, {
        onSuccess: () => {
            form.value = { name: '', description: '', image_url: '', is_active: true };
            isSubmitting.value = false;
        },
        onFinish: () => {
            isSubmitting.value = false;
        },
    });
};

const deleteCollection = (col) => {
    if (confirm(`Supprimer la collection "${col.name}" ?`)) {
        router.delete(`/admin/collections/${col.id}`);
    }
};
</script>

<template>
    <Head title="Gestion des Collections — SuperAdmin DjaDja" />

    <AdminLayout>
        <div class="space-y-8">
            <div>
                <h1 class="font-serif text-3xl font-bold text-[#141414]">Collections & Univers (CRUD)</h1>
                <p class="text-xs text-[#141414]/70">Créez et organisez vos catégories de robes.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <!-- Left: Create Form -->
                <div class="lg:col-span-5 bg-[#FAF9F6] border border-[#E8E5DF] p-6 shadow-xs space-y-4">
                    <h2 class="font-serif text-xl font-bold text-[#141414] pb-2 border-b border-[#E8E5DF]">
                        Nouvelle Collection
                    </h2>

                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <label class="block text-xs uppercase tracking-wider font-semibold text-[#141414] mb-1">
                                Nom de la Collection *
                            </label>
                            <input v-model="form.name" type="text" required placeholder="Ex: Robes de Cérémonie" class="w-full bg-[#F3F1EC] border border-[#E8E5DF] p-2.5 text-xs focus:outline-none" />
                        </div>

                        <div>
                            <label class="block text-xs uppercase tracking-wider font-semibold text-[#141414] mb-1">
                                Description Éditoriale
                            </label>
                            <textarea v-model="form.description" rows="3" placeholder="Présentation de l'univers..." class="w-full bg-[#F3F1EC] border border-[#E8E5DF] p-2.5 text-xs focus:outline-none"></textarea>
                        </div>

                        <div>
                            <label class="block text-xs uppercase tracking-wider font-semibold text-[#141414] mb-1">
                                Image de Couverture (URL)
                            </label>
                            <input v-model="form.image_url" type="url" placeholder="https://..." class="w-full bg-[#F3F1EC] border border-[#E8E5DF] p-2.5 text-xs focus:outline-none" />
                        </div>

                        <button 
                            type="submit"
                            :disabled="isSubmitting"
                            class="w-full bg-[#141414] text-[#FAF9F6] text-xs uppercase tracking-widest font-semibold py-3 hover:bg-black transition-colors"
                        >
                            {{ isSubmitting ? 'Création...' : '+ Créer la Collection' }}
                        </button>
                    </form>
                </div>

                <!-- Right: List Table -->
                <div class="lg:col-span-7 bg-[#FAF9F6] border border-[#E8E5DF] p-6 shadow-xs">
                    <h2 class="font-serif text-xl font-bold text-[#141414] pb-2 border-b border-[#E8E5DF] mb-4">
                        Collections Existantes ({{ collections.length }})
                    </h2>

                    <div class="divide-y divide-[#E8E5DF]">
                        <div v-for="col in collections" :key="col.id" class="py-4 flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <img v-if="col.image_url" :src="col.image_url" class="w-12 h-14 object-cover border border-[#E8E5DF]" />
                                <div v-else class="w-12 h-14 bg-[#F3F1EC] border flex items-center justify-center text-[10px] text-gray-400">
                                    Sans photo
                                </div>
                                <div>
                                    <h3 class="font-serif text-base font-bold text-[#141414]">{{ col.name }}</h3>
                                    <p class="text-xs text-[#141414]/60">{{ col.products_count }} robes associées</p>
                                </div>
                            </div>

                            <button @click="deleteCollection(col)" class="text-xs font-semibold text-red-600 hover:underline">
                                Supprimer
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
