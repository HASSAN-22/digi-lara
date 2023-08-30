<template>

  <div class="fd:hidden">
    <Slider :slides="1">
      <SwiperSlide v-for="image in props.images" :key="image.id">
        <div>
          <img :src="$store.state.url + image.image" class=""/>
        </div>
      </SwiperSlide>
    </Slider>
  </div>
  <div class="w-[50%] fm:w-[70%] fm:hidden">
    <img :src="imagePath" />
  </div>
  <div class="w-[50%] overflow-y-scroll h-[30rem] fm:hidden">
    <ul class="flex flex-wrap gap-2">
      <li v-if="props.defaultImage" class="border border-gray-300 rounded-lg w-[20%] h-[20%] p-1 cursor-pointer" @click="replaceImage(props.defaultImage)">
        <img :src="props.defaultImage" />
      </li>
      <li class="border border-gray-300 rounded-lg w-[20%] h-[20%] p-1 cursor-pointer" v-for="image in props.images" :key="image.id" @click="replaceImage($store.state.url + image.image)">
        <img :src="$store.state.url + image.image" />
      </li>
    </ul>
  </div>

</template>

<script>
export default{name:"ProductImage"}
</script>

<script setup>
import {ref, defineProps, onMounted} from "vue";
import Slider from "@/components/Slider.vue";
import { SwiperSlide } from 'swiper/vue';

const props = defineProps({
  'images':{
    type:Object
  },
  defaultImage:{
    type:String,
    default:null,
  }
})
let imagePath = ref('')

onMounted(()=>{
  imagePath.value = props.defaultImage ? props.defaultImage : props.images[0];
})

function replaceImage(_imagePath){
  imagePath.value = _imagePath;
}
</script>