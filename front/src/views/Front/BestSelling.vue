<template>
  <div>
    <div class="container mx-auto">
      <div class="">
        <div class="relative">
          <div class="bg-[url('@/assets/images/bestSelling.svg')] h-[9rem] opacity-20"></div>
          <div class="absolute top-[3rem] fd:right-[45%] fm:right-[32%]">
            <h3 class="text-3xl text-red-500 !font-medium fm:text-2xl">پرفروش‌ترین‌ها</h3>
          </div>
        </div>
        <div class="mt-20 mx-10">
          <ProductComponent :products="products" />

          <div class="mt-12" v-if="products.length > 0">
            <Paginate :current="$store.state.current" :next="$store.state.next" :previous="$store.state.previous" :total="$store.state.total" @callGetPage="getData"/>
          </div>
        </div>
      </div>
    </div>
    <Meta :title="$store.state.siteName + ` پرفروش ها `"/>
    <Loading :loading="loading" />
    <ValidationError/>
  </div>
</template>

<script>
export default{name: "BestSelling"}
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
  await axios.get(`${store.state.api}best-sellings?page=${page}`).then(resp=>{
    let data = resp.data.data;
    products.value = data.data;
    store.commit('paginate',data);
  }).catch(err=>{
    Toast.error()
  })
  loading.value = false;
}

</script>