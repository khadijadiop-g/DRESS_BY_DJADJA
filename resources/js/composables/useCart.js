import { ref, computed, watch } from 'vue';

const STORAGE_KEY = 'djadja_cart_v1';
const items = ref([]);
const isDrawerOpen = ref(false);

// Load from localStorage if available
if (typeof window !== 'undefined') {
    const saved = localStorage.getItem(STORAGE_KEY);
    if (saved) {
        try {
            items.value = JSON.parse(saved);
        } catch (e) {
            console.error('Failed to parse cart storage', e);
        }
    }

    watch(
        items,
        (newItems) => {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(newItems));
        },
        { deep: true }
    );
}

export function useCart() {
    const addItem = (item) => {
        const existingIndex = items.value.findIndex((i) => i.variant_id === item.variant_id);
        if (existingIndex > -1) {
            items.value[existingIndex].quantity += item.quantity;
        } else {
            items.value.push({ ...item });
        }
        isDrawerOpen.value = true;
    };

    const removeItem = (variantId) => {
        items.value = items.value.filter((i) => i.variant_id !== variantId);
    };

    const updateQuantity = (variantId, qty) => {
        if (qty <= 0) {
            removeItem(variantId);
            return;
        }
        const item = items.value.find((i) => i.variant_id === variantId);
        if (item) {
            item.quantity = qty;
        }
    };

    const clearCart = () => {
        items.value = [];
    };

    const itemCount = computed(() => {
        return items.value.reduce((total, item) => total + item.quantity, 0);
    });

    const totalXof = computed(() => {
        return items.value.reduce((total, item) => total + item.price_xof * item.quantity, 0);
    });

    const freeShippingThreshold = 50000; // 50 000 FCFA for free delivery in Dakar
    const freeShippingProgress = computed(() => {
        return Math.min(100, Math.round((totalXof.value / freeShippingThreshold) * 100));
    });

    const amountLeftForFreeShipping = computed(() => {
        return Math.max(0, freeShippingThreshold - totalXof.value);
    });

    return {
        items,
        isDrawerOpen,
        addItem,
        removeItem,
        updateQuantity,
        clearCart,
        itemCount,
        totalXof,
        freeShippingThreshold,
        freeShippingProgress,
        amountLeftForFreeShipping,
    };
}
