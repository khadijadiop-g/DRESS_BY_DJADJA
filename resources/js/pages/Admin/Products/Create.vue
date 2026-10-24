<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowLeft, Plus, Trash2, Upload, Image as ImageIcon, Check, Star, AlertCircle } from 'lucide-vue-next';
import AdminLayout from '../../../components/AdminLayout.vue';

const props = defineProps({
    categories: Array,
});

// Preset luxury fashion colors for 1-click addition
const presetColors = [
    { name: 'Noir Ébène', hex: '#111111' },
    { name: 'Blanc Ivoire', hex: '#F8F6F0' },
    { name: 'Or Champagne', hex: '#D4AF37' },
    { name: 'Vert Émeraude', hex: '#1B4D3E' },
    { name: 'Terracotta', hex: '#C25D43' },
    { name: 'Bordeaux Impérial', hex: '#58111A' },
    { name: 'Bleu Nuit', hex: '#0F1E36' },
    { name: 'Rose Poudré', hex: '#E8D3D1' },
];

// Product form state
const form = ref({
    category_id: props.categories[0]?.id || '',
    name: '',
    subtitle: '',
    description: '',
    fabric_details: '100% Satin de soie lourd, doublure douce au toucher.',
    care_guide: 'Nettoyage à sec délicat. Repassage doux à la vapeur sur l\'envers.',
    base_price_xof: 55000,
    is_featured: false,
    is_active: true,
    is_new_arrival: true,
});

// Photo upload state
const photoFiles = ref([]);
const photoPreviews = ref([]);
const hoverPhotoIndex = ref(1);
const urlImages = ref([]);
const isUrlMode = ref(false);

// Colors and Sizes Matrix
// Each color has its name, hex, and an array of available sizes with stocks
const standardSizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];

const colors = ref([
    {
        name: 'Noir Ébène',
        hex: '#111111',
        sizes: [
            { label: 'XS', active: false, stock: 3 },
            { label: 'S', active: true, stock: 5 },
            { label: 'M', active: true, stock: 5 },
            { label: 'L', active: true, stock: 5 },
            { label: 'XL', active: true, stock: 3 },
            { label: 'XXL', active: false, stock: 2 },
        ],
    },
    {
        name: 'Blanc Ivoire',
        hex: '#F8F6F0',
        sizes: [
            { label: 'XS', active: false, stock: 3 },
            { label: 'S', active: true, stock: 4 },
            { label: 'M', active: true, stock: 4 },
            { label: 'L', active: true, stock: 4 },
            { label: 'XL', active: false, stock: 2 },
            { label: 'XXL', active: false, stock: 2 },
        ],
    },
]);

// Handle file input
const fileInput = ref(null);
const triggerFileInput = () => {
    if (fileInput.value) fileInput.value.click();
};

const onFilesSelected = (e) => {
    const files = Array.from(e.target.files || []);
    if (!files.length) return;

    files.forEach((file) => {
        photoFiles.value.push(file);
        const reader = new FileReader();
        reader.onload = (event) => {
            photoPreviews.value.push({
                file,
                url: event.target.result,
                name: file.name,
            });
        };
        reader.readAsDataURL(file);
    });

    // Reset input value so re-selecting same file triggers change
    if (fileInput.value) fileInput.value.value = '';
};

const removePhoto = (idx) => {
    photoFiles.value.splice(idx, 1);
    photoPreviews.value.splice(idx, 1);
    if (hoverPhotoIndex.value >= photoPreviews.value.length) {
        hoverPhotoIndex.value = Math.max(0, photoPreviews.value.length - 1);
    }
};

const setHoverPhoto = (idx) => {
    hoverPhotoIndex.value = idx;
};

// URL images fallback
const addUrlImage = () => {
    urlImages.value.push({ url: '', is_hover: urlImages.value.length === 1 });
};
const removeUrlImage = (idx) => {
    urlImages.value.splice(idx, 1);
};

// Color actions
const addCustomColor = () => {
    colors.value.push({
        name: 'Nouvelle Couleur',
        hex: '#C5A059',
        sizes: standardSizes.map((sz) => ({
            label: sz,
            active: ['S', 'M', 'L'].includes(sz),
            stock: 5,
        })),
    });
};

const addPresetColor = (preset) => {
    // Check if color already exists
    if (colors.value.some((c) => c.name.toLowerCase() === preset.name.toLowerCase())) {
        return;
    }
    colors.value.push({
        name: preset.name,
        hex: preset.hex,
        sizes: standardSizes.map((sz) => ({
            label: sz,
            active: ['S', 'M', 'L'].includes(sz),
            stock: 5,
        })),
    });
};

const removeColor = (colorIdx) => {
    if (colors.value.length <= 1) {
        alert('Une robe doit avoir au moins une couleur.');
        return;
    }
    colors.value.splice(colorIdx, 1);
};

const toggleAllSizesForColor = (colorIdx, enableAll = true) => {
    colors.value[colorIdx].sizes.forEach((sz) => {
        sz.active = enableAll;
    });
};

// Flattened variants for submission and validation
const generatedVariants = computed(() => {
    const list = [];
    colors.value.forEach((color) => {
        color.sizes.forEach((sz) => {
            if (sz.active) {
                list.push({
                    size: sz.label,
                    color_name: color.name.trim() || 'Unique',
                    color_hex: color.hex || '#141414',
                    stock_quantity: Math.max(0, parseInt(sz.stock, 10) || 0),
                });
            }
        });
    });
    return list;
});

const isSubmitting = ref(false);
const errors = ref({});

const submit = () => {
    isSubmitting.value = true;
    errors.value = {};

    // Validate that at least one variant is selected
    if (generatedVariants.value.length === 0) {
        errors.value.variants = 'Veuillez sélectionner au moins une taille pour une couleur.';
        isSubmitting.value = false;
        return;
    }

    // Validate photos
    if (photoFiles.value.length === 0 && urlImages.value.filter(u => u.url).length === 0) {
        errors.value.photos = 'Veuillez télécharger au moins une photo de la robe.';
        isSubmitting.value = false;
        return;
    }

    const payload = {
        ...form.value,
        photos: photoFiles.value,
        hover_photo_index: hoverPhotoIndex.value,
        images: urlImages.value.filter(u => u.url),
        variants: generatedVariants.value,
    };

    router.post('/admin/products', payload, {
        forceFormData: true,
        onError: (errs) => {
            isSubmitting.value = false;
            errors.value = errs;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },
        onFinish: () => {
            isSubmitting.value = false;
        },
    });
};
</script>

<template>
    <Head title="Ajouter une Robe — SuperAdmin DjaDja" />

    <AdminLayout>
        <div class="max-w-4xl mx-auto space-y-6 pb-20">
            
            <!-- Breadcrumbs -->
            <div class="flex items-center space-x-3">
                <Link href="/admin/products" class="text-xs uppercase tracking-wider text-[#141414]/60 hover:text-[#141414] flex items-center space-x-1.5 transition-colors">
                    <ArrowLeft class="w-4 h-4" />
                    <span>Retour au catalogue des robes</span>
                </Link>
            </div>

            <!-- Error Banner -->
            <div v-if="Object.keys(errors).length > 0" class="bg-red-50 border-l-4 border-red-600 p-4 rounded-xs">
                <div class="flex items-start space-x-3">
                    <AlertCircle class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" />
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-red-800">
                            Veuillez corriger les erreurs suivantes :
                        </h4>
                        <ul class="mt-1 list-disc list-inside text-xs text-red-700 space-y-0.5">
                            <li v-for="(errMsg, field) in errors" :key="field">
                                {{ errMsg }}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Main Form Card -->
            <div class="bg-[#FAF9F6] border border-[#E8E5DF] p-5 sm:p-8 shadow-xs">
                
                <div class="border-b border-[#E8E5DF] pb-4 mb-6">
                    <span class="text-[10px] uppercase tracking-[0.25em] text-[#C5A059] font-bold">Maison Dress By DjaDja</span>
                    <h1 class="font-serif text-2xl sm:text-3xl font-bold text-[#141414] mt-1">
                        Créer une Nouvelle Robe
                    </h1>
                    <p class="text-xs text-[#141414]/60 mt-1">
                        Téléversez les photos réelles, définissez les multiples couleurs et tailles disponibles pour votre clientèle à Dakar et à l'international.
                    </p>
                </div>

                <form @submit.prevent="submit" class="space-y-8">
                    
                    <!-- SECTION 1: Informations Générales -->
                    <div class="space-y-4">
                        <h2 class="text-xs uppercase tracking-[0.2em] font-bold text-[#141414] border-b border-[#E8E5DF] pb-2">
                            1. Informations Générales
                        </h2>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs uppercase tracking-wider font-semibold text-[#141414] mb-1">
                                    Nom de la Robe *
                                </label>
                                <input 
                                    v-model="form.name" 
                                    type="text" 
                                    required 
                                    placeholder="Ex: Robe Nafi Soirée" 
                                    class="w-full bg-[#F3F1EC] border border-[#E8E5DF] p-3 text-xs focus:outline-none focus:border-[#141414]" 
                                />
                            </div>

                            <div>
                                <label class="block text-xs uppercase tracking-wider font-semibold text-[#141414] mb-1">
                                    Sous-titre Éditorial
                                </label>
                                <input 
                                    v-model="form.subtitle" 
                                    type="text" 
                                    placeholder="Ex: Soie Plissée & Dos Nu Plongeant" 
                                    class="w-full bg-[#F3F1EC] border border-[#E8E5DF] p-3 text-xs focus:outline-none focus:border-[#141414]" 
                                />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs uppercase tracking-wider font-semibold text-[#141414] mb-1">
                                    Collection / Catégorie *
                                </label>
                                <select 
                                    v-model="form.category_id" 
                                    required 
                                    class="w-full bg-[#F3F1EC] border border-[#E8E5DF] p-3 text-xs focus:outline-none focus:border-[#141414]"
                                >
                                    <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                                        {{ cat.name }}
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs uppercase tracking-wider font-semibold text-[#141414] mb-1">
                                    Prix de Vente (FCFA / XOF) *
                                </label>
                                <input 
                                    v-model="form.base_price_xof" 
                                    type="number" 
                                    required 
                                    step="500" 
                                    min="1000"
                                    class="w-full bg-[#F3F1EC] border border-[#E8E5DF] p-3 text-xs focus:outline-none focus:border-[#141414]" 
                                />
                                <span class="text-[10px] text-[#141414]/60 mt-1 block">
                                    Converti automatiquement en EUR (€) pour la clientèle diaspora.
                                </span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs uppercase tracking-wider font-semibold text-[#141414] mb-1">
                                Description Haute Couture *
                            </label>
                            <textarea 
                                v-model="form.description" 
                                rows="3" 
                                required 
                                placeholder="Décrivez la silhouette, la coupe, l'allure et les détails artisanaux..." 
                                class="w-full bg-[#F3F1EC] border border-[#E8E5DF] p-3 text-xs focus:outline-none focus:border-[#141414]"
                            ></textarea>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs uppercase tracking-wider font-semibold text-[#141414] mb-1">
                                    Matière & Tissu
                                </label>
                                <input 
                                    v-model="form.fabric_details" 
                                    type="text" 
                                    class="w-full bg-[#F3F1EC] border border-[#E8E5DF] p-3 text-xs focus:outline-none focus:border-[#141414]" 
                                />
                            </div>

                            <div>
                                <label class="block text-xs uppercase tracking-wider font-semibold text-[#141414] mb-1">
                                    Conseils d'Entretien
                                </label>
                                <input 
                                    v-model="form.care_guide" 
                                    type="text" 
                                    class="w-full bg-[#F3F1EC] border border-[#E8E5DF] p-3 text-xs focus:outline-none focus:border-[#141414]" 
                                />
                            </div>
                        </div>

                        <!-- Visibility checkmarks -->
                        <div class="flex flex-wrap gap-4 sm:gap-6 pt-2">
                            <label class="flex items-center space-x-2 text-xs cursor-pointer select-none">
                                <input v-model="form.is_active" type="checkbox" class="w-4 h-4 accent-[#141414]" />
                                <span class="font-medium text-[#141414]">Visible sur la boutique</span>
                            </label>
                            <label class="flex items-center space-x-2 text-xs cursor-pointer select-none">
                                <input v-model="form.is_featured" type="checkbox" class="w-4 h-4 accent-[#141414]" />
                                <span class="font-medium text-[#141414]">Mettre en avant (Pièce Signature)</span>
                            </label>
                            <label class="flex items-center space-x-2 text-xs cursor-pointer select-none">
                                <input v-model="form.is_new_arrival" type="checkbox" class="w-4 h-4 accent-[#141414]" />
                                <span class="font-medium text-[#141414]">Badge "Nouveau"</span>
                            </label>
                        </div>
                    </div>


                    <!-- SECTION 2: Téléversement des Photos Réelles -->
                    <div class="space-y-4 border-t border-[#E8E5DF] pt-6">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <div>
                                <h2 class="text-xs uppercase tracking-[0.2em] font-bold text-[#141414]">
                                    2. Photos de la Robe (Upload Réel) *
                                </h2>
                                <p class="text-[11px] text-[#141414]/60">
                                    Téléchargez vos photos depuis votre téléphone ou ordinateur. La 1ère photo sera la vue principale, la 2ème s'affiche au survol (effet Almada Label).
                                </p>
                            </div>

                            <button 
                                type="button" 
                                @click="isUrlMode = !isUrlMode" 
                                class="text-[11px] uppercase tracking-wider text-[#141414]/60 hover:text-[#141414] underline text-left"
                            >
                                {{ isUrlMode ? 'Masquer mode URL' : 'Ou ajouter par lien URL' }}
                            </button>
                        </div>

                        <!-- Hidden File Input -->
                        <input 
                            ref="fileInput" 
                            type="file" 
                            multiple 
                            accept="image/jpeg,image/png,image/webp,image/jpg" 
                            @change="onFilesSelected" 
                            class="hidden" 
                        />

                        <!-- Drag and Drop Dropzone -->
                        <div 
                            @click="triggerFileInput"
                            class="border-2 border-dashed border-[#141414]/30 hover:border-[#141414] bg-[#F3F1EC] p-6 sm:p-8 text-center cursor-pointer transition-colors group flex flex-col items-center justify-center space-y-3"
                        >
                            <div class="w-12 h-12 rounded-full bg-white flex items-center justify-center shadow-xs group-hover:scale-105 transition-transform">
                                <Upload class="w-6 h-6 text-[#141414]" />
                            </div>
                            <div>
                                <span class="text-xs uppercase tracking-wider font-bold text-[#141414] block">
                                    Cliquez pour choisir des photos de la robe
                                </span>
                                <span class="text-[11px] text-[#141414]/60 mt-0.5 block">
                                    Formats acceptés : JPEG, PNG, WEBP (sélectionnez plusieurs photos à la fois)
                                </span>
                            </div>
                        </div>

                        <!-- Photos Preview Grid -->
                        <div v-if="photoPreviews.length > 0" class="space-y-2">
                            <span class="text-[11px] uppercase tracking-wider font-semibold text-[#141414] block">
                                {{ photoPreviews.length }} photo(s) sélectionnée(s) :
                            </span>

                            <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-5 gap-3">
                                <div 
                                    v-for="(preview, idx) in photoPreviews" 
                                    :key="idx" 
                                    class="relative aspect-[3/4] bg-white border border-[#E8E5DF] overflow-hidden group shadow-xs"
                                >
                                    <img :src="preview.url" :alt="preview.name" class="w-full h-full object-cover" />

                                    <!-- Badges -->
                                    <div class="absolute top-1 left-1 flex flex-col space-y-1">
                                        <span v-if="idx === 0" class="bg-[#141414] text-[#FAF9F6] text-[8px] uppercase tracking-wider font-bold px-1.5 py-0.5">
                                            Principale
                                        </span>
                                        <span v-if="idx === hoverPhotoIndex" class="bg-[#C5A059] text-white text-[8px] uppercase tracking-wider font-bold px-1.5 py-0.5">
                                            Survol
                                        </span>
                                    </div>

                                    <!-- Hover Overlay Controls -->
                                    <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col justify-between p-2">
                                        <div class="flex justify-end">
                                            <button 
                                                type="button" 
                                                @click.stop="removePhoto(idx)" 
                                                class="bg-red-600 text-white p-1 rounded-full hover:bg-red-700" 
                                                title="Supprimer cette photo"
                                            >
                                                <Trash2 class="w-3.5 h-3.5" />
                                            </button>
                                        </div>

                                        <button 
                                            type="button" 
                                            @click.stop="setHoverPhoto(idx)" 
                                            class="w-full bg-white/90 text-[#141414] text-[9px] uppercase font-bold py-1 hover:bg-white"
                                        >
                                            {{ idx === hoverPhotoIndex ? 'Photo au survol ✓' : 'Définir au survol' }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Fallback URL mode -->
                        <div v-if="isUrlMode" class="bg-[#F3F1EC] p-4 border border-[#E8E5DF] space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-xs font-semibold text-[#141414] uppercase">Ou saisie par Liens d'images (URL)</span>
                                <button type="button" @click="addUrlImage" class="text-xs font-bold text-[#141414] flex items-center space-x-1">
                                    <Plus class="w-3.5 h-3.5" />
                                    <span>Ajouter URL</span>
                                </button>
                            </div>

                            <div v-for="(img, idx) in urlImages" :key="idx" class="flex items-center space-x-2">
                                <input v-model="img.url" type="url" placeholder="https://..." class="flex-1 bg-white border border-[#E8E5DF] p-2 text-xs" />
                                <label class="text-[10px] text-[#141414] flex items-center space-x-1 whitespace-nowrap">
                                    <input v-model="img.is_hover" type="checkbox" />
                                    <span>Survol</span>
                                </label>
                                <button type="button" @click="removeUrlImage(idx)" class="text-red-600 p-1">
                                    <Trash2 class="w-4 h-4" />
                                </button>
                            </div>
                        </div>
                    </div>


                    <!-- SECTION 3: Multi-Couleurs & Tailles (Le Cœur du Problème Résolu !) -->
                    <div class="space-y-6 border-t border-[#E8E5DF] pt-6">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <div>
                                <h2 class="text-xs uppercase tracking-[0.2em] font-bold text-[#141414]">
                                    3. Gestion Multi-Couleurs & Tailles *
                                </h2>
                                <p class="text-[11px] text-[#141414]/60">
                                    Ajoutez autant de couleurs que souhaité pour cette robe. Pour chaque couleur, cochez les tailles disponibles et ajustez la quantité en stock.
                                </p>
                            </div>

                            <!-- Real-time summary -->
                            <div class="inline-flex items-center space-x-2 bg-[#F3F1EC] border border-[#E8E5DF] px-3 py-1.5 text-[11px] font-semibold text-[#141414]">
                                <span>{{ colors.length }} couleur(s)</span>
                                <span>•</span>
                                <span>{{ generatedVariants.length }} taille(s) en stock</span>
                            </div>
                        </div>

                        <!-- 1-Click Preset Colors -->
                        <div>
                            <span class="block text-[10px] uppercase tracking-wider font-semibold text-[#141414]/70 mb-2">
                                Ajouter rapidement une couleur haute couture :
                            </span>
                            <div class="flex flex-wrap gap-2">
                                <button 
                                    v-for="p in presetColors" 
                                    :key="p.name" 
                                    type="button" 
                                    @click="addPresetColor(p)"
                                    class="inline-flex items-center space-x-2 bg-[#F3F1EC] hover:bg-white border border-[#E8E5DF] px-2.5 py-1 text-xs transition-colors"
                                >
                                    <span class="w-3 h-3 rounded-full border border-black/20" :style="{ backgroundColor: p.hex }"></span>
                                    <span>+ {{ p.name }}</span>
                                </button>
                            </div>
                        </div>

                        <!-- Color Cards List -->
                        <div class="space-y-4">
                            <div 
                                v-for="(color, colorIdx) in colors" 
                                :key="colorIdx" 
                                class="bg-[#F3F1EC] border border-[#E8E5DF] p-4 sm:p-5 relative transition-all"
                            >
                                <!-- Color Header Bar -->
                                <div class="flex flex-wrap items-center justify-between gap-3 pb-3 border-b border-[#E8E5DF]">
                                    <div class="flex items-center space-x-3">
                                        <!-- Color preview swatch & picker -->
                                        <div class="relative flex items-center">
                                            <input 
                                                v-model="color.hex" 
                                                type="color" 
                                                class="w-9 h-9 rounded-full cursor-pointer border border-[#141414]/20 p-0 overflow-hidden" 
                                                title="Choisir le code couleur"
                                            />
                                        </div>

                                        <!-- Color name input -->
                                        <div>
                                            <label class="block text-[9px] uppercase font-bold text-[#141414]/60">Nom de la couleur</label>
                                            <input 
                                                v-model="color.name" 
                                                type="text" 
                                                required 
                                                placeholder="Ex: Noir Profond, Blanc Ivoire..." 
                                                class="bg-white border border-[#E8E5DF] px-3 py-1.5 text-xs font-semibold text-[#141414] focus:outline-none w-48 sm:w-60" 
                                            />
                                        </div>
                                    </div>

                                    <!-- Quick actions for this color -->
                                    <div class="flex items-center space-x-3">
                                        <button 
                                            type="button" 
                                            @click="toggleAllSizesForColor(colorIdx, true)" 
                                            class="text-[10px] uppercase font-semibold text-[#141414]/70 hover:text-[#141414] underline"
                                        >
                                            Toutes les tailles
                                        </button>
                                        <span class="text-xs text-[#141414]/30">|</span>
                                        <button 
                                            type="button" 
                                            @click="removeColor(colorIdx)" 
                                            class="text-red-600 hover:text-red-800 p-1 flex items-center space-x-1 text-xs" 
                                            title="Supprimer cette couleur"
                                        >
                                            <Trash2 class="w-3.5 h-3.5" />
                                            <span class="text-[10px] uppercase font-bold">Supprimer</span>
                                        </button>
                                    </div>
                                </div>

                                <!-- Sizes & Stock Selector for this Color -->
                                <div class="pt-3">
                                    <span class="block text-[10px] uppercase tracking-wider font-semibold text-[#141414]/70 mb-2">
                                        Tailles & Quantité en stock pour {{ color.name || 'cette couleur' }} :
                                    </span>

                                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-2.5">
                                        <div 
                                            v-for="sz in color.sizes" 
                                            :key="sz.label"
                                            :class="sz.active ? 'bg-white border-[#141414] shadow-xs' : 'bg-white/50 border-[#E8E5DF] opacity-60'"
                                            class="border p-2.5 flex flex-col justify-between transition-all"
                                        >
                                            <label class="flex items-center justify-between cursor-pointer select-none">
                                                <span class="text-xs font-bold tracking-wider text-[#141414]">{{ sz.label }}</span>
                                                <input 
                                                    v-model="sz.active" 
                                                    type="checkbox" 
                                                    class="w-4 h-4 accent-[#141414]" 
                                                />
                                            </label>

                                            <div v-if="sz.active" class="mt-2 pt-2 border-t border-[#E8E5DF]">
                                                <label class="block text-[8px] uppercase tracking-wider text-[#141414]/60">Stock</label>
                                                <input 
                                                    v-model.number="sz.stock" 
                                                    type="number" 
                                                    min="0" 
                                                    class="w-full bg-[#FAF9F6] border border-[#E8E5DF] px-2 py-1 text-xs font-semibold focus:outline-none" 
                                                />
                                            </div>
                                            <div v-else class="mt-2 text-[9px] text-[#141414]/40 text-center py-1">
                                                Non proposé
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Button to Add Another Color -->
                        <button 
                            type="button" 
                            @click="addCustomColor" 
                            class="w-full border-2 border-dashed border-[#141414]/40 hover:border-[#141414] bg-[#FAF9F6] py-3.5 text-xs uppercase tracking-widest font-bold text-[#141414] flex items-center justify-center space-x-2 transition-all hover:bg-white"
                        >
                            <Plus class="w-4 h-4" />
                            <span>Ajouter une autre couleur pour cette robe</span>
                        </button>
                    </div>

                    <!-- Submit Area (Mobile Fixed / Sticky bottom friendly) -->
                    <div class="pt-6 border-t border-[#E8E5DF] flex flex-col-reverse sm:flex-row sm:items-center justify-between gap-4">
                        <Link 
                            href="/admin/products" 
                            class="text-center bg-[#F3F1EC] text-[#141414] text-xs uppercase tracking-widest font-semibold px-6 py-3.5 border border-[#E8E5DF] hover:bg-[#E8E5DF] transition-colors"
                        >
                            Annuler
                        </Link>

                        <button 
                            type="submit" 
                            :disabled="isSubmitting" 
                            class="bg-[#141414] text-[#FAF9F6] text-xs uppercase tracking-[0.2em] font-semibold px-10 py-4 hover:bg-black transition-all disabled:opacity-50 shadow-md active:scale-[0.99] flex items-center justify-center space-x-2"
                        >
                            <span v-if="isSubmitting">Enregistrement et téléversement...</span>
                            <span v-else>Publier la Robe au Catalogue</span>
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </AdminLayout>
</template>