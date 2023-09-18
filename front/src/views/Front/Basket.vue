<template>
  <div>
    <div class="container mx-auto">
      <div class="mx-4 mt-10">

        <div class="flex fm:flex-col relative">
          <div class="fd:w-[75%] p-2">
            <div class="flex justify-between mb-5">
              <h1 class="text-2xl !font-medium fm:text-lg">سبد خرید</h1>
              <h1 class="text-gray-500 !font-medium fm:text-sm">{{basketLength}} کالا</h1>
            </div>
            <div v-if="basketLength > 0 && loadeIsDone">
              <div  class="border border-gray-200 rounded-lg p-2 flex justify-between mb-2" v-for="basket in $store.state.baskets" :key="basket.id">
                <BasketDetail :key="basket.id" :basket="basket" :disableGuarantee="false" :disableBrand="false" :showBasket="false" :withCount="true" :withProduct="true" imageSize="w-[120px] h-[120px]" :calcAmount="true" />
                <div>
                <span v-if="!$store.state.removeBasketLoading || $store.state.basketId !== basket.id" @click="removeBasket(basket.id)" class="cursor-pointer">
                  <i class="cursor-pointer far fa-trash text-red-500 text-sm"></i>
                </span>
                  <CircleLoading v-else />
                </div>
              </div>
            </div>
            <div v-else class="flex justify-center">
              <img src="@/assets/images/no-data.svg" class="w-[300px] fm:w-[200px]"/>
            </div>
          </div>
          <div class="fd:w-[25%] relative">
            <div class="fixed sticky top-[9rem] flex-col gap-5">
              <div class="flex flex-col gap-6 border border-gray-200 rounded-lg p-4">
                <div class="flex items-start justify-between">
                  <span class="text-gray-500 fm:text-sm">قیمت کالاها ({{basketLength}})</span>
                  <span class="text-gray-500 fm:text-sm">{{productAmount}} ریال</span>
                </div>
                <div class="flex items-start justify-between">
                  <span class="!font-medium fm:text-sm">جمع سبد خرید</span>
                  <span class="!font-medium fm:text-sm">{{$store.getters.numberFormat(fullAmount)}} ریال</span>
                </div>
                <div class="flex items-start justify-between" v-if="calcPercent > 0">
                  <span class="!font-medium text-red-500 fm:text-sm">سود شما از خرید</span>

                  <span class="!font-medium text-red-500 fm:text-sm"> ({{calcPercent}}٪) {{$store.getters.numberFormat(price - fullPrice)}} ریال</span>
                </div>
                <div v-if="basketLength > 0" class="w-full">
                  <routerLink :to="{name:'Shipping'}" id="router-link" class="bg-red-500 p-3 w-full text-white text-sm text-center rounded-lg">مرحله بعد</routerLink>
                </div>
                <div v-else class="w-full">
                  <button class="bg-gray-200 text-gray-500 p-3 w-full text-sm text-center rounded-lg">سبد خرید خالی است</button>
                </div>
              </div>
              <div class="mt-3">
                <p class="text-sm text-gray-500">
                  هزینه این سفارش هنوز پرداخت نشده‌ و در صورت اتمام موجودی، کالاها از سبد حذف می‌شوند
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <Meta :title="$store.state.siteName + ` سبد خرید | صفحه اصلی `"/>
    <Loading :loading="loading" />
  </div>
</template>

<script>
export default{name: "Basket"}
</script>

<script setup>
import {computed, onMounted, ref} from "vue";
import BasketDetail from "@/components/BasketDetail.vue";
import Meta from "@/components/Meta.vue";
import store from "@/store";
import Loading from "@/components/Loading";
import CircleLoading from "@/components/CircleLoading";

let loading = ref(false);
let loadeIsDone = ref(false);
let price = ref(0)
let fullPrice = ref(0)
let basketLength = ref(0)
onMounted(async () => {
  loading.value = true;
  await store.dispatch('getBasket', {loading:true, withCoupon:false, withCount:true,withProduct:true})
  basketLength.value = store.state.baskets.length;
  loading.value = false;
  loadeIsDone.value = true;
})

const productAmount = computed(()=>{
  price.value = store.state.basketOriginalAmount;
  return store.getters.numberFormat(store.state.basketOriginalAmount)
})

const fullAmount = computed(()=>{
  let amount = store.state.basketAmount
  fullPrice.value = amount;
  return amount;
})
const calcPercent = computed(()=>{
  let result = (price.value-fullPrice.value)/price.value * 100;
  return result.toFixed(2);
})

async function removeBasket(basketId){
  if(store.state.basketLoadingAction === false){
    await store.dispatch('removeBasket',{basketId:basketId,showBasket:false, withProduct:true})
    store.state.showBasket=false;
  }
}
</script>

<style scoped>
#router-link {
  display: inline-block;
}
</style>