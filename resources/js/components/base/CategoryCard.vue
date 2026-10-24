<script setup>
    const props =defineProps({
        c:{
            type:Object,
        }
    })

</script>
<template>
        <a class="category" href="categories/cereales.html" :style="{'--category-color': c.color}">
       <img alt="" height="48" :src="c.image" width="56"/>
       <span>
        {{ c.name }}
       </span>
      </a>
</template>



<style>

</style>