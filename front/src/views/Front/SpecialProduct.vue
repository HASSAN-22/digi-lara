<template>
  <div>
    <div class="container mx-auto">
      <div class="">
        <div class="relative">
          <div class="bg-[url('@/assets/images/incredible-offer.svg'),linear-gradient(45deg,#b500d0,#ff3a49)] h-[13rem] bg-no-repeat"></div>
          <div class="absolute fd:-top-3 fm:top-[1rem] fd:right-[45%] fm:right-[32%] bg-[url('@/assets/images/incredible-offer.webp')] w-[175px] h-[210px] fm:h-[80%] fm:w-[34%] fm:bg-cover"></div>
          <div class="absolute top-[6rem] fd:right-[45%] fm:right-[32%]">
            <h3 class="text-3xl text-white !font-medium fm:text-2xl">کالاهای ویژه</h3>
          </div>
        </div>
        <div class="mt-20">
          <ProductComponent :products="products" />

          <div class="mt-12" v-if="products.length > 0">
            <Paginate :current="$store.state.current" :next="$store.state.next" :previous="$store.state.previous" :total="$store.state.total" @callGetPage="getData"/>
          </div>
        </div>
      </div>
    </div>
    <Meta :title="$store.state.siteName + ` کالاهای شگفت انگیز `"/>
    <Loading :loading="loading" />
    <ValidationError/>
  </div>
</template>

<script>
export default{name: "SpecialProduct"}
</script>

<script setup>
import {defineExpose, onMounted, ref} from "vue";
import Meta from "@/components/Meta.vue";
import Loading from "@/components/Loading";
import Paginate from "@/components/Paginate";
import ValidationError from "@/components/ValidationError";
import axios from "@/plugins/axios";
import store from "@/store";
import Toast from "@/plugins/toast";
import ProductComponent from "@/components/ProductComponent";

let loading = ref(false);
let products = ref([]);

onMounted(async ()=>{
  await getData()
})
defineExpose({getData});

async function getData(_loading = true,page=1){
  loading.value = _loading;
  await axios.get(`${store.state.api}special-products?page=${page}`).then(resp=>{
    let data = resp.data.data;
    products.value = data.data;
    store.commit('paginate',data);
  }).catch(err=>{
    Toast.error()
  })
  loading.value = false;
}

</script>