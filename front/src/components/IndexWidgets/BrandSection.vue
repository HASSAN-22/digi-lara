<template>

  <div v-if="props.widget !== undefined" class="mt-14 border border-gray-200 rounded-2xl p-4">
    <div class="flex items-center gap-1 justify-center">
      <span class=""><i class="fal fa-star text-yellow-400"></i></span>
      <span class="text-2xl fm:text-lg !font-medium">محبوب‌ترین برندها</span>
    </div>

    <div class="brand">
      <Slider :slides="(displaySize < 769) ? 4 : 8">
        <SwiperSlide v-for="brand in props.widget.brands" :key="brand.id">
          <a href="" class="p-3">
            <img :src="store.state.url + brand.logo" class="rounded"/>
          </a>
          <div class="border-l border-red-500"></div>
        </SwiperSlide>
      </Slider>
    </div>
  </div>
</template>

<script>
export default {name:"BrandSection"}
</script>

<script setup>
import {defineProps, onBeforeUnmount, onMounted, ref} from 'vue'
import { SwiperSlide } from 'swiper/vue';
import Slider from "@/components/Slider.vue";
import store from "@/store";

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