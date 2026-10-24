import { ref } from 'vue';
import { usePage, router } from '@inertiajs/vue3';

const activeCurrency = ref('XOF');

export function useCurrency() {
    const page = usePage();
    const rate = Number(page.props.currencyRate) || 655.957;

    // Sync from server session
    if (page.props.currency && (page.props.currency === 'XOF' || page.props.currency === 'EUR')) {
        activeCurrency.value = page.props.currency;
    }

    const setCurrency = (currency) => {
        activeCurrency.value = currency;
        router.post(
            '/currency/switch',
            { currency },
            {
                preserveScroll: true,
                preserveState: true,
            }
        );
    };

    const formatPrice = (amountXof) => {
        if (activeCurrency.value === 'EUR') {
            const eur = (amountXof / rate).toFixed(2).replace('.', ',');
            return `${eur} €`;
        }
        return `${Number(amountXof).toLocaleString('fr-FR')} FCFA`;
    };

    return {
        activeCurrency,
        rate,
        setCurrency,
        formatPrice,
    };
}
