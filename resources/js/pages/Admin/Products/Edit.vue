<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowLeft, Upload, Trash2 } from 'lucide-vue-next';
import AdminLayout from '../../../components/AdminLayout.vue';

const props = defineProps({
    product: Object,
    categories: Array,
});

const form = ref({
    category_id: props.product.category_id,
    name: props.product.name,
    subtitle: props.product.subtitle || '',
    description: props.product.description,
    fabric_details: props.product.fabric_details || '',
    care_guide: props.product.care_guide || '',
    base_price_xof: props.product.base_price_xof,
    is_featured: Boolean(props.product.is_featured),
    is_active: Boolean(props.product.is_active),
    is_new_arrival: Boolean(props.product.is_new_arrival),
});

const newPhotos = ref([]);
const newPhotoPreviews = ref([]);
const fileInput = ref(null);

const triggerFileInput = () => {
    if (fileInput.value) fileInput.value.click();
};

const onFilesSelected = (e) => {
    const files = Array.from(e.target.files || []);
    files.forEach((file) => {
        newPhotos.value.push(file);
        const reader = new FileReader();
        reader.onload = (event) => {
            newPhotoPreviews.value.push({
                file,
                url: event.target.result,
                name: file.name,
            });
        };
        reader.readAsDataURL(file);
    });
    if (fileInput.value) fileInput.value.value = '';
};

const removeNewPhoto = (idx) => {
    newPhotos.value.splice(idx, 1);
    newPhotoPreviews.value.splice(idx, 1);
};

const isSubmitting = ref(false);

const submit = () => {
    isSubmitting.value = true;

    if (newPhotos.value.length > 0) {
        // Send multipart POST with _method=PUT
        router.post(`/admin/products/${props.product.id}`, {
            ...form.value,
            photos: newPhotos.value,
            _method: 'PUT',
        }, {
            forceFormData: true,
            onFinish: () => {
                isSubmitting.value = false;
            },
        });
    } else {
        router.put(`/admin/products/${props.product.id}`, form.value, {
            onFinish: () => {
                isSubmitting.value = false;
            },
        });
    }
};
</script>

<template>
    <Head :title="`Modifier ${product.name} — SuperAdmin`" />

    <AdminLayout>
        <div class="max-w-4xl mx-auto space-y-6 pb-20">
            <Link href="/admin/products" class="text-xs uppercase tracking-wider text-[#141414]/60 hover:text-[#141414] flex items-center space-x-1.5 transition-colors">
                <ArrowLeft class="w-4 h-4" />
                <span>Retour au catalogue</span>
            </Link>

            <div class="bg-[#FAF9F6] border border-[#E8E5DF] p-5 sm:p-8 shadow-xs">
                <div class="border-b border-[#E8E5DF] pb-4 mb-6">
                    <span class="text-[10px] uppercase tracking-[0.25em] text-[#C5A059] font-bold">Édition Robe</span>
                    <h1 class="font-serif text-2xl sm:text-3xl font-bold text-[#141414] mt-1">
                        Modifier {{ product.name }}
                    </h1>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs uppercase tracking-wider font-semibold text-[#141414] mb-1">
                                Nom de la Robe *
                            </label>
                            <input v-model="form.name" type="text" required class="w-full bg-[#F3F1EC] border border-[#E8E5DF] p-3 text-xs focus:outline-none" />
                        </div>

                        <div>
                            <label class="block text-xs uppercase tracking-wider font-semibold text-[#141414] mb-1">
                                Sous-titre
                            </label>
                            <input v-model="form.subtitle" type="text" class="w-full bg-[#F3F1EC] border border-[#E8E5DF] p-3 text-xs focus:outline-none" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs uppercase tracking-wider font-semibold text-[#141414] mb-1">
                                Collection *
                            </label>
                            <select v-model="form.category_id" required class="w-full bg-[#F3F1EC] border border-[#E8E5DF] p-3 text-xs focus:outline-none">
                                <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs uppercase tracking-wider font-semibold text-[#141414] mb-1">
                                Prix de Base (FCFA) *
                            </label>
                            <input v-model="form.base_price_xof" type="number" required class="w-full bg-[#F3F1EC] border border-[#E8E5DF] p-3 text-xs focus:outline-none" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs uppercase tracking-wider font-semibold text-[#141414] mb-1">
                            Description
                        </label>
                        <textarea v-model="form.description" rows="4" required class="w-full bg-[#F3F1EC] border border-[#E8E5DF] p-3 text-xs focus:outline-none"></textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs uppercase tracking-wider font-semibold text-[#141414] mb-1">
                                Matière & Tissu
                            </label>
                            <input v-model="form.fabric_details" type="text" class="w-full bg-[#F3F1EC] border border-[#E8E5DF] p-3 text-xs focus:outline-none" />
                        </div>

                        <div>
                            <label class="block text-xs uppercase tracking-wider font-semibold text-[#141414] mb-1">
                                Entretien
                            </label>
                            <input v-model="form.care_guide" type="text" class="w-full bg-[#F3F1EC] border border-[#E8E5DF] p-3 text-xs focus:outline-none" />
                        </div>
                    </div>

                    <!-- Photos Existantes & Upload -->
                    <div class="border-t border-[#E8E5DF] pt-6 space-y-4">
                        <label class="block text-xs uppercase tracking-wider font-bold text-[#141414]">
                            Photos de la robe
                        </label>

                        <!-- Existing Images -->
                        <div v-if="product.images && product.images.length > 0" class="flex flex-wrap gap-3">
                            <div v-for="img in product.images" :key="img.id" class="w-16 h-20 bg-white border border-[#E8E5DF] overflow-hidden">
                                <img :src="img.url" class="w-full h-full object-cover" />
                            </div>
                        </div>

                        <!-- Upload new photos -->
                        <input 
                            ref="fileInput" 
                            type="file" 
                            multiple 
                            accept="image/*" 
                            @change="onFilesSelected" 
                            class="hidden" 
                        />

                        <div 
                            @click="triggerFileInput"
                            class="border border-dashed border-[#141414]/30 hover:border-[#141414] bg-[#F3F1EC] p-4 text-center cursor-pointer flex items-center justify-center space-x-2 text-xs font-semibold text-[#141414]"
                        >
                            <Upload class="w-4 h-4" />
                            <span>Ajouter de nouvelles photos</span>
                        </div>

                        <!-- New photos previews -->
                        <div v-if="newPhotoPreviews.length > 0" class="flex flex-wrap gap-3">
                            <div v-for="(p, idx) in newPhotoPreviews" :key="idx" class="relative w-16 h-20 bg-white border border-[#E8E5DF] overflow-hidden">
                                <img :src="p.url" class="w-full h-full object-cover" />
                                <button type="button" @click="removeNewPhoto(idx)" class="absolute top-0 right-0 bg-red-600 text-white p-0.5">
                                    <Trash2 class="w-3 h-3" />
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-6 pt-2">
                        <label class="flex items-center space-x-2 text-xs cursor-pointer">
                            <input v-model="form.is_active" type="checkbox" class="w-4 h-4 accent-black" />
                            <span>En ligne</span>
                        </label>
                        <label class="flex items-center space-x-2 text-xs cursor-pointer">
                            <input v-model="form.is_featured" type="checkbox" class="w-4 h-4 accent-black" />
                            <span>Pièce Signature</span>
                        </label>
                        <label class="flex items-center space-x-2 text-xs cursor-pointer">
                            <input v-model="form.is_new_arrival" type="checkbox" class="w-4 h-4 accent-black" />
                            <span>Nouveau</span>
                        </label>
                    </div>

                    <div class="pt-6 border-t border-[#E8E5DF] flex justify-end space-x-3">
                        <Link href="/admin/products" class="bg-[#F3F1EC] text-[#141414] text-xs uppercase tracking-widest font-semibold px-6 py-3 border border-[#E8E5DF]">
                            Annuler
                        </Link>
                        <button type="submit" :disabled="isSubmitting" class="bg-[#141414] text-[#FAF9F6] text-xs uppercase tracking-widest font-semibold px-8 py-3 hover:bg-black transition-colors disabled:opacity-50">
                            {{ isSubmitting ? 'Mise à jour...' : 'Sauvegarder les modifications' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>