<template>
  <div class="border border-gray-200 rounded-lg p-8 mb-5" v-if="props.baskets.length > 0">
    <div>
      <div v-if="props.weightType === 'super_heavy'" class="flex items-center gap-1">
        <span>کالاهای فوق سنگین</span>
      </div>
      <div v-else-if="props.weightType === 'heavy'" class="flex items-center gap-1">
        <span>کالاهای سنگین</span>
      </div>
      <div v-else class="flex items-center gap-1">
        <span>کالاهای سبک</span>
      </div>
    </div>
    <div class="border-b border-gray-200 my-5 w-full"></div>
    <div class="flex flex-wrap gap-14">
      <div class="flex flex-col gap-2" v-for="basket in props.baskets" :key="basket.id">
        <div class="flex flex-col relative">
          <div class="w-[80px] h-[80px]">
            <img :src="$store.state.url + basket.product.image.replace(`${$store.state.largeSize}x${$store.state.largeSize}_`,`${$store.state.smallSize}x${$store.state.smallSize}_`)" />

          </div>
          <div class="absolute -left-1 -bottom-2">
            <span class="text-xs bg-gray-200 rounded px-2 text-black">{{basket.count}}</span>
          </div>
        </div>
        <div class="flex items-center gap-2" v-if="basket.product_size !== null">
          <span class="text-gray-500 fm:text-sm"><i class="far fa-ruler-vertical"></i></span>
          <span class="text-gray-500 text-xs">{{basket.product_size.size.size}}</span>
        </div>
        <div class="flex items-center gap-2" v-else-if="basket.product_color !== null">
          <div class="rounded-full border w-[1rem] h-[1rem] flex justify-center items-center" :style="{background:basket.product_color.color.color_code}">
          </div>
          <span class="text-gray-500 text-xs">{{basket.product_color.color.color}}</span>
        </div>
      </div>
    </div>
    <div class="border-b border-gray-200 my-5 w-full"></div>
    <div>
      <div v-if="props.weightType !== 'super_heavy'" class="flex items-center gap-1">
        <span>هزینه ارسال</span>
        <span class="text-blue-400 !font-medium">{{getTransportPrice}} </span>
      </div>
      <div v-else class="flex items-center gap-1">
        <span>هزینه ارسال</span>
        <span class="text-blue-400 !font-medium">باربری (هزینه ارسال به‌صورت پس‌کرایه - لیست قیمت‌ها)</span>
      </div>
    </div>
  </div>
</template>

<script>
export default{name:"ProductWeightSplit"}
</script>

<script setup>
import {computed, defineProps, onMounted} from 'vue';
import store from "@/store";

const props = defineProps({
  'baskets':{
    type:Object,
    required: true,
  },
  'transports':{
    type:Object,
    required: true,
  },
  'weightType':{
    type:String,
    required:true,
  }
})

const getTransportPrice = computed(()=>{
  let transport = props.transports.filter(item=>item.weight_type === props.weightType)[0];
  if(transport){
    return transport.fixed_price > 0 ? store.getters.numberFormat(transport.fixed_price) + ' ریال ' : 'رایگان';
  }else{
    return 'رایگان'
  }

})

</script>