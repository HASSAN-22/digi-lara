<template>
  <div class="fd:w-[38rem] relative">
      <div class="absolute top-3 right-4" v-if="$store.state.searchText.length <= 0">
        <div class="flex items-center gap-5 fm:gap-2">
          <span><i class="fa fa-search text-xl fm:text-sm text-gray-400"></i></span>
        </div>
      </div>
      <input v-model="store.state.searchText" v-debounce="search" :debounce-events="['keydown']" placeholder="جستجو ..." type="search" class="indent-[2rem] outline-none rounded-lg border-none bg-gray-100 w-full p-3" />
      <div v-if="searchData.length > 0 && $store.state.searchText.length > 0 && $store.state.searchModal" class="absolute shadow-lg bg-white w-full z-70">
        <div class="h-[25rem] overflow-y-scroll scroll">
          <div class="flex gap-3 p-4" v-for="product in searchData" :key="product.id">
            <routerLink :to="{name:'ProductDetail', params:{slug:product.slug}}" class="w-[20%]">
              <img :src="$store.state.url + product.image" class="w-full h-full rounded"/>
            </routerLink>
            <routerLink :to="{name:'ProductDetail', params:{slug:product.slug}}" class="">
              <ul class="flex flex-col p-0 m-0">
                <li class="flex flex-row gap-2 text-gray-600">
                  <span class="fm:text-sm">کالا:</span>
                  <span class="fm:text-sm">{{product.ir_name}}</span>
                </li>
                <li class="flex flex-row gap-2 text-gray-600">
                  <span class="fm:text-sm">قیمت:</span>
                  <span class="fm:text-sm">{{$store.getters.numberFormat(product.price)}} ریال</span>
                </li>
              </ul>
            </routerLink>
          </div>
        </div>
      </div>
      <div v-else-if="searchData.length <= 0 && $store.state.searchText.length > 0 && $store.state.searchModal" class="absolute text-center shadow-lg bg-white w-full z-70">
        <span class="fm:text-sm text-red-500">موردی یافت نشد</span>
      </div>
      <Loading :loading="loading" />
    </div>
</template>

<script>
export default {name: "Search"}
</script>

<script setup>
import {ref} from "vue"
import store from "@/store";
import axios from "@/plugins/axios";
import Loading from "@/components/Loading";

let searchData = ref([]);
let loading = ref(false);

async function search(text){
  loading.value = true;
  store.state.searchModal = true;
  store.state.searchText = text;
  await axios.get(`${store.state.api}search-product?search=${text}`).then(resp=>{
    searchData.value = resp.data.data;
  }).catch(err=>{})
  loading.value = false;
}
</script>