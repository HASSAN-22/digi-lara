<template>
  <div class="relative" @mouseenter="openBasket()" @mouseleave="closeBasket()">
    <routerLink :to="{name:'Basket'}" class="flex items-center gap-1">
      <span class="flex"><i class="fal fa-shopping-cart text-2xl fm:text-lg"></i></span>
    </routerLink>
    <div class="absolute left-[1rem] bg-red-500 rounded-lg px-[7px] py-[3px] text-xs text-white">
      <span>{{$store.state.basketCount}}</span>
    </div>
    <div :class="['fm:hidden absolute bg-white shadow p-3 top-6 left-1 rounded w-[30rem] z-1000',$store.state.showBasket ? 'fd:block' : 'hidden']">
      <div class="flex flex-col">
        <div class="flex items-center justify-between">
          <span class="text-gray-500 text-sm" v-if="!$store.state.basketLoading">{{$store.state.basketCount}} کالا</span>
          <CircleLoading v-else />
          <routerLink :to="{name:'Basket'}" class="text-blue-400 text-sm">مشاهده سبد خرید</routerLink>
        </div>
      </div>
      <div class="mt-5" v-if="!$store.state.basketLoading">
        <div v-if="$store.state.baskets.length > 0" class="overflow-y-scroll scroll h-[30rem] flex flex-col justify-between">
          <div class="mb-5" v-for="basket in $store.state.baskets" :key="basket.id">
            <BasketDetail :basket="basket" :withProduct="true"/>
            <div class="border-b border-gray-200 py-2"></div>
          </div>
          <div class="flex justify-between items-center p-2">
            <div class="flex flex-col gap-2">
              <span class="text-gray-500 text-sm">مبلغ قابل پرداخت</span>
              <span class="!font-bold text-lg">{{$store.getters.getBasketAmount($store.state.baskets)}} ریال</span>
            </div>
            <routerLink :to="{name:'Basket'}" class="text-red-500 rounded bg-white p-2 border border-red-500">ثبت سفارش</routerLink>
          </div>
        </div>
        <div v-else class="flex justify-center">
          <span class="text-sm text-red-500 !font-medium">سبد خرید خالی است</span>
        </div>
      </div>
      <div class="mt-5 flex justify-center" v-else>
        <CircleLoading/>
      </div>
    </div>
  </div>
</template>

<script>
export default {name:'BasketComponent'}
</script>
<script setup>
import store from "@/store";
import BasketDetail from "@/components/BasketDetail.vue"
import CircleLoading from "@/components/CircleLoading";
import {useRoute} from "vue-router";

const route = useRoute();

function closeBasket(){
  store.state.showBasket = false;
}
async function openBasket(){
  if(route.name !== 'Shipping' && route.name !== 'Basket'){
    store.state.showBasket = true;
    if(store.state.basketLoadingAction === false){
      await store.dispatch('getBasket', {loading:true})
    }
  }else {
    store.state.showBasket = false;
  }
}

</script>