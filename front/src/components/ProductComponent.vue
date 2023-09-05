<template>
  <div>
    <div class="flex fm:flex-col fm:justify-evenly flex-wrap gap-1 justify-center" v-if="props.products.length > 0">
      <div v-for="product in props.products" :key="product" :class="['border border-gray-100 bg-white fm:w-full pt-8 px-4 pb-3 hover:shadow transition ease-in-out duration-300',props.boxClass]">
        <div  class="flex flex-col gap-3">
          <routerLink :to="{name:'ProductDetail', params:{slug:product.slug}}" class="flex flex-col gap-3  items-center">
            <div class="relative h-[287px] w-[287px]">
              <img :src="$store.state.url + product.image.replace(`${$store.state.largeSize}x${$store.state.largeSize}_`,`${$store.state.mediumSize}x${$store.state.mediumSize}_`)" class="h-[287px] w-[287px]" :alt="product.ir_name" />
              <div class="absolute top-0" v-if="product.amazing_offer_status === 'yes'">
                <img src="@/assets/images/IncredibleOffer.svg" />
              </div>
              <div class="absolute top-0" v-else-if="product.amazing_price !== null">
                <img src="@/assets/images/SpecialSell.svg" />
              </div>
            </div>
          </routerLink>
          <routerLink :to="{name:'ProductDetail', params:{slug:product.slug}}" class="flex flex-col gap-3">
            <div>
              <strong>
                <p class="text-sm">
                  {{product.ir_name}}
                </p>
              </strong>
            </div>
            <div class="flex items-center justify-between">
              <div>
                <span class="text-sm text-red-400" v-if="product.count <= 3">تنها {{product.count}} عدد در انبار باقی مانده</span>
              </div>
              <div class="flex items-center gap-2">
                <span class="text-sm !font-medium">{{product.rating.toFixed(1)}}</span>
                <span><i class="far fa-star text-yellow-300"></i></span>
              </div>
            </div>
            <div class="flex items-center justify-between">
              <Countdown v-if="product.amazing_offer_status === 'yes'" :expireAt="product.amazing_offer_expire" />

              <div class="flex flex-col gap-1" v-if="(product.amazing_price !== null && product.amazing_price > 0) && product.amazing_offer_status !== 'yes'">
                <span class="!font-medium fm:text-sm">{{$store.getters.numberFormat(product.amazing_price)}} ریال</span>
                <span class="!font-medium text-sm text-gray-500"><del>{{$store.getters.numberFormat(product.price)}} ریال</del></span>
              </div>
              <div class="flex flex-col gap-1" v-else>
                <span class="!font-medium fm:text-sm" v-if="product.amazing_offer_status !== 'yes'">{{$store.getters.numberFormat(product.price)}} ریال</span>
                <span class="!font-medium fm:text-sm" v-else>{{$store.getters.getDiscount(product.price, product.amazing_offer_percent)}} ریال</span>
              </div>
            </div>
          </routerLink>
        </div>
        <Button v-if="compare" :btnLoading="btnLoading" @click="addCompare(product.id)" class="mt-5" text="اضافه به مقایسه" my_class="!w-full !bg-white !border !border-red-500 !text-red-500"/>
      </div>
    </div>
    <div v-else class="flex justify-center">
      <img src="@/assets/images/no-data.svg" class="w-[300px] fm:w-[200px]"/>
    </div>
  </div>

</template>

<script>
export default{name:"ProductComponent"}
</script>

<script setup>
import {defineProps, ref, defineEmits, defineExpose} from "vue"
import Countdown from "@/components/Countdown.vue";
import Button from "@/components/Button";
const props = defineProps({
  "products":{
    type:Array,
    default:[]
  },
  'boxClass':{
    type:String,
    default:'w-[32%]'
  },
  'compare':{
    type:Boolean,
    default:false
  }
})

const emits = defineEmits(['addCompare'])

let btnLoading = ref(false);

defineExpose({
  btnLoading
})

async function addCompare(productId){
  emits('addCompare', productId)
}


</script>