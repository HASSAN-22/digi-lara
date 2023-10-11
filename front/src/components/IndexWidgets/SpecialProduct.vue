<template>
  <div v-if="props.widget !== undefined" :class="[`mt-8 fd:rounded-2xl fm:rounded`]" :style="{background:props.widget.box_bg}">
    <div class="flex flex-row items-center fd:py-4 fm:py-2">
      <div class="flex flex-col gap-4 fd:px-12 fm:px-3 items-center fd:w-[20%] fm:w-[30%]">
        <div>
          <img :src="props.widget.amazing_image" />
        </div>
        <div>
          <img :src="props.widget.box_image" />
        </div>
        <routerLink :to="{name:'SpecialProduct'}" class="flex flex-row gap-2 items-center text-white">
          <span class="!font-bold fm:text-sm">مشاهده همه</span><span class="flex"><i class="fa fa-chevron-left text-sm fm:text-xs"></i></span>
        </routerLink>
      </div>
      <div class="py-4 fd:w-[80%] fm:w-[70%]">
        <Slider :slides="(displaySize < 769) ? 2 : 5">
          <SwiperSlide v-for="product in props.widget.products" :key="product.id">
            <routerLink :to="{name:'ProductDetail', params:{slug:product.slug}}" class="bg-white flex flex-col gap-3 rounded">
              <div>
                <img :src="$store.state.url + product.image" :alt="product.ir_name" class="rounded"/>
              </div>
              <div class="flex flex-row justify-between p-3">
                <div class="w-[3rem] h-[1.4rem] bg-red-500 text-center rounded-2xl">
                  <span class="text-white !font-bold text-sm fm:text-xs">{{product.amazing_offer_percent}}%</span>
                </div>
                <div class="flex flex-col gap-3 items-center">
                  <span class="text-sm fm:text-xs">{{$store.getters.getDiscount(product.price, product.amazing_offer_percent)}} ریال</span>
                  <span class="text-gray-400 text-sm fm:text-xs"><del>{{$store.getters.numberFormat(product.price)}} ریال</del></span>
                </div>
              </div>
            </routerLink>
          </SwiperSlide>
        </Slider>
      </div>
    </div>
  </div>
</template>

<script>
export default {name:"SpecialProduct"}
</script>

<script setup>
import {defineProps, onBeforeUnmount, onMounted, ref} from 'vue'
import { SwiperSlide } from 'swiper/vue';
import Slider from "@/components/Slider.vue";

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