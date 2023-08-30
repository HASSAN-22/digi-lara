<template>
  <div class="flex gap-[1rem]">
    <div class="flex flex-col">
      <div class="flex flex-col justify-between gap-3">
        <div class="flex flex-col items-center">
          <div :class="[imageSize]">
            <img :src="$store.state.url + basket.product.image.replace(`${$store.state.largeSize}x${$store.state.largeSize}_`,`${$store.state.smallSize}x${$store.state.smallSize}_`)"/>
          </div>
          <div class="absolute top-0" v-if="basket.product.amazing_offer_status === 'yes'">
            <img src="@/assets/images/IncredibleOffer.svg" />
          </div>
          <div class="" v-else-if="basket.product.amazing_price !== null">
            <img src="@/assets/images/SpecialSell.svg" />
          </div>
        </div>
        <div class="w-[85%] border border-gray-200 p-2 flex items-center gap-1 justify-evenly  rounded-lg">
          <span class="cursor-pointer" v-if="!$store.state.addBasketLoading || $store.state.basketId !== basket.id" @click="addBasket(basket.product.id, basket.productcolor_id,basket.productsize_id,basket.id)">
              <i class="far fa-plus text-gray-500 text-sm"></i>
            </span>
          <CircleLoading v-else />
          <span>{{basket.count}}</span>
          <div>
            <div v-if="basket.count > 1">
              <span class="cursor-pointer" v-if="!$store.state.minusBasketLoading || $store.state.basketId !== basket.id" @click="minusBasket(basket.product.id, basket.productcolor_id,basket.productsize_id,basket.id)">
                <i class="far fa-minus text-gray-500 text-sm"></i>
              </span>
              <CircleLoading v-else />
            </div>
            <div v-else>
              <span v-if="!$store.state.removeBasketLoading || $store.state.basketId !== basket.id" @click="removeBasket(basket.id)"><i class="cursor-pointer far fa-trash text-red-500 text-sm"></i></span>
              <CircleLoading v-else />
            </div>
          </div>
        </div>


      </div>
    </div>
    <div class="flex flex-col gap-3">
      <div class="flex flex-col gap-3">
        <h1 class="text-md !font-medium fm:text-sm">{{basket.product.ir_name}}</h1>
        <div class="flex flex-col items-start justify-start gap-2">
          <div class="flex items-center gap-2" v-if="basket.product_size !== null">
            <span class="text-gray-500 text-lg fm:text-sm"><i class="far fa-ruler-vertical"></i></span>
            <span class="text-gray-500 text-sm fm:text-xs">{{basket.product_size.size.size}}</span>
            <small class="text-gray-500 text-xs" v-if="basket.product_size.price > 0">{{$store.getters.numberFormat(basket.product_size.price)}} ریال</small>
          </div>
          <div class="flex items-center gap-2" v-else-if="basket.product_color !== null">
            <div class="rounded-full border h-[2rem] w-[2rem] fm:w-[1rem] fm:h-[1rem] flex justify-center items-center" :style="{background:basket.product_color.color.color_code}">
            </div>
            <span class="text-gray-500 text-sm fm:text-xs">{{basket.product_color.color.color}}</span>
            <small class="text-gray-500 text-xs" v-if="basket.product_color.price > 0">{{$store.getters.numberFormat(basket.product_color.price)}} ریال</small>
          </div>

          <div class="flex items-center gap-2" v-if="!disableGuarantee">
            <span class="text-gray-500 text-lg fm:text-sm"><i class="far fa-shield-check"></i></span>
            <span class="text-gray-500 text-sm fm:text-xs">گارانتی {{basket.product.guarantee.guarantee}}</span>
          </div>
          <div class="flex items-center gap-2" v-if="!disableBrand">
            <span class="text-gray-500 text-lg fm:text-sm"><i class="far fa-shop"></i></span>
            <span class="text-gray-500 text-sm fm:text-xs">{{basket.product.brand.name}}</span>
          </div>
          <div class="flex items-center gap-2">

            <span class="text-gray-500 text-lg fm:text-sm"><i class="far fa-box-archive"></i></span>
            <span class="text-gray-500 text-sm fm:text-xs" v-if="basket.product.count > 0">موجود در انبار </span>
            <span class="text-red-500 text-sm fm:text-xs" v-else>---- ناموجود ----</span>
          </div>
        </div>
        <div class="flex items-center gap-2">
          <div class="flex flex-col gap-2">
            <span class="text-red-500 text-xs !font-medium" v-if="getPrice.discount !== '0'">{{getPrice.discount}} ریال تخفیف</span>
            <span class="!font-medium fm:text-sm">{{getPrice.amount}} ریال</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {name:"BasketDetail"}
</script>
<script setup>
import store from "@/store";
import CircleLoading from "@/components/CircleLoading";
import {computed, defineProps, ref} from 'vue';

let discountAmount = ref(0)
let amount = ref(0)


const props = defineProps({
  'basket':{
    type:Object,
  },
  'disableGuarantee':{
    type:Boolean,
    default:true
  },
  'disableBrand':{
    type:Boolean,
    default:true
  },
  'calcAmount':{
    type:Boolean,
    default:false
  },
  'imageSize':{
    type:String,
    default:'w-[100px] h-[100px]'
  },
  'withCount':{
    type:Boolean,
    default:false,
  },
  'withProduct':{
    type:Boolean,
    default:false,
  },
  'showBasket':{
    type:Boolean,
    default:true,
  }
})
const getPrice = computed(()=>{
  let product = props.basket.product;
  let price = product.price;
  let count = props.basket.count;
  if(product.amazing_offer_status === 'yes'){
    discountAmount.value = discount.value(price*count, product.amazing_offer_percent) - price
    amount.value = discount.value(price*count, product.amazing_offer_percent);

  }else if(product.amazing_price !== null && product.amazing_price > 0){
    discountAmount.value = (product.price - product.amazing_price) * count
    amount.value = product.amazing_price*count
  }else{
    amount.value = product.price*count
  }
  return {discount:store.getters.numberFormat(discountAmount.value), amount:store.getters.numberFormat(amount.value)}
})

const discount = computed(()=>(price, discount)=>{
  return store.getters.getDiscount(price, discount,false)
})

async function addBasket(productId,color,size,basketId){
  if(store.state.basketLoadingAction === false){
    await store.dispatch('addBasket',{productId:productId, color:color,size:size,basketId:basketId,withProduct:props.withProduct})
  }
}
async function minusBasket(productId,color,size,basketId){
  if(store.state.basketLoadingAction === false){
    await store.dispatch('minusBasket',{productId:productId, color:color,size:size,basketId:basketId,withProduct:props.withProduct})
  }
}

async function removeBasket(basketId){
  if(store.state.basketLoadingAction === false){
    await store.dispatch('removeBasket',{basketId:basketId,showBasket:props.showBasket,withCount:props.withCount,withProduct:props.withProduct})
  }
}
</script>