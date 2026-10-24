<script setup>
const props =defineProps({
    p:{
        type:Object,
        validator:p=>Boolean(
            p.id && p.name && Number.isInteger(p.price)
            && p.price >0 && Number.isInteger(p.stock)
            && p.stock >=0
        ) 
    },
    compact:{type:Boolean , default:false},
})
const emit =defineEmits({
    add:p=>Boolean(p?.id)
})
function formatPrice(price){
    return Number(price).toLocaleString('fr-FR')+ " FCFA"
}

</script>
<template>
        <article class="product-card" data-component="ProductCard" data-product-id="2">
       <div class="product-media">
        <a aria-label="Voir Oignons de Potou" href="produits/02-oignons.html">
         <img alt="p.name de Potou" height="240" loading="lazy" :src="p.image" width="360"/>
        </a>
        <span class="promo-tag" v-if="p.sale_price !=null && p.sale_price < p.price">
         Promo
        </span>
       </div>
       <div class="product-body">
        <p class="overline">
         {{ p.origine }}
        </p>
        <h3>
         <a href="produits/02-oignons.html">
          {{ p.name }}
         </a>
        </h3>
        <p class="muted small">
         {{ p.unit }} · {{ p.stock }} disponible(s)
        </p>
        <span class="stock low" :class="{empty:p.stock==0}">
         <span aria-hidden="true">
         </span>
         {{ p.stock == 0 ? 'Rupture' : p.stock < 5 ?'stock faible':'Disponible' }}
        </span>
        <div class="product-bottom">
         <div>
          <del v-if=" p.sale_price !=null && p.sale_price < p.price">
           {{formatPrice(p.price)}}
          </del>
          <strong>
           {{ p.sale_price !=null && p.sale_price < p.price ? formatPrice(p.sale_price) : formatPrice(p.price ) }}
          </strong>
         </div>
         <button aria-label="Ajouter Oignons de Potou au panier" class="add-button" data-action="ajouter-panier" type="button" :disabled="p.stock==0" @click="emit('add',p)">
          <svg aria-hidden="true" class="icon" fill="none" height="20" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" viewbox="0 0 24 24" width="20">
           <path d="M12 5v14M5 12h14">
           </path>
          </svg>
         </button>
        </div>
       </div>
      </article>
</template>



<style>

</style>