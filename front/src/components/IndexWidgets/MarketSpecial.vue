<template>
  <div v-if="props.widget !== undefined" class="w-full rounded-2xl mt-4 flex items-center justify-between p-5 fm:flex-col fm:gap-6" :style="{background: props.widget.background}">
    <div class="flex flex-row justify-between items-center gap-4">
      <div>
        <img :src="props.widget.basket_image" />
      </div>
      <div>
        <img :src="props.widget.amazing_image" class="fm:w-[80%]"/>
      </div>
      <div>
        <div class="px-3 py-1 rounded-2xl fm:hidden" :style="{background:props.widget.discount_bg}">
          <span class="text-white !font-bold text-sm">{{props.widget.discount_text}}</span>
        </div>
      </div>
    </div>
    <div class="flex items-center gap-5">
      <div class="flex flex-row items-center gap-3">
        <div class="rounded-full bg-white relative w-[6rem] h-[6rem] p-4" v-for="product in (displaySize < 769) ? props.widget.products.slice(0,3) : props.widget.products.slice(0,6)" :key="product.id">
          <routerLink :to="{name:'ProductDetail', params:{slug:product.slug}}">
            <img :src="$store.state.url + product.image" class="w-full h-full rounded"/>
            <div class="absolute right-0">
              <div class="w-[3rem] h-[1.4rem] bg-red-500 text-center rounded-2xl">
                <span class="text-white !font-bold text-xs">{{product.amazing_offer_percent}}%</span>
              </div>
            </div>
          </routerLink>
        </div>
      </div>
      <routerLink :to="{name:'ProductsList',params:{slug:props.widget.category.slug}}" class="p-3 rounded-full text-green-500 flex items-center gap-3" :style="{background: props.widget.product_bg}">
        <span class="!font-medium text-sm fm:hidden" :style="{color: props.widget.product_color}">بیش از {{props.widget.product_length}} کالا</span>
        <span class="flex"><i class="fal fa-arrow-left !font-extrabold"></i></span>
      </routerLink>
    </div>
  </div></template>

<script>
export default {name:"MarketSpecial"}
</script>

<script setup>
import {defineProps, onBeforeUnmount, onMounted, ref} from 'vue'

let props = defineProps(['widget']);
let displaySize = ref(window.innerWidth);

onBeforeUnmount(() => {
  window.removeEventListener('resize', onResize)
})

onMounted(async () => {
  window.addEventListener('resize', onResize)
})

function onResize() {
  displaySize.value = window.innerWidth;
}

</script>