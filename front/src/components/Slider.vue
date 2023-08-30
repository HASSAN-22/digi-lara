<template>
    <div>
        <swiper
                :modules="modules"
                :space-between="props.space"
                :slides-per-view="props.slides"
                @swiper="onSwiper"
                :pagination="props.paginate"
                @slideChange="onSlideChange"
                effect="coverflow"
                :autoplay="false"
                :navigation="navigation"
                :loop="false"

        >
            <slot></slot>
        </swiper>
    </div>
</template>
<script>
export default {
  name: 'Slider',
}
</script>
<script setup>
    import {defineProps} from 'vue'
    import { Navigation, Pagination, Autoplay, Virtual } from 'swiper';
    import { Swiper} from 'swiper/vue';
    import 'swiper/css';
    import 'swiper/scss';
    import 'swiper/scss/navigation';
    import 'swiper/css/effect-coverflow';
    import 'swiper/css/autoplay'
    import 'swiper/css/pagination';
    const props = defineProps({
      "navigation":{
        type:Boolean,
        default:false
      },
      "paginate":{
        type:Boolean,
        default:false
      },
      "space":{
        type:Number,
        default:5
      },
      "slides":{
        type:Number,
        default:1
      }
    })
    const onSwiper = (swiper) => {};
    const onSlideChange = () => {};
    const modules = [Navigation, Pagination, Autoplay, Virtual];
</script>