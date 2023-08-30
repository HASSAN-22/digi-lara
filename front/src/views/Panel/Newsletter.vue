<template>
  <div class="mt-10 px-8">
    <div class="flex justify-center">
      <span class="!font-medium text-lg">لیست خبرنامه ها</span>
    </div>
    <div class="mt-10 px-3 mb-3">
      <div class="mb-3 mr-2 flex justify-between fm:flex-col items-center">
        <div class="flex gap-2 items-center">
          <Button @click="getData(false, 1, true)" my_class="!bg-white !py-2 !px-2" :btnLoading="refresh"><i class="far fa-sync text-2xl fm:text-lg text-gray-700"></i></Button>
        </div>
        <div class="ml-2 fm:mt-3">
        </div>
      </div>
      <div v-if="allData.length <= 0" class="border border-b w-full my-2"></div>
      <div v-if="allData.length > 0">
        <table class="w-full border-collapse lg:table 2xl:table xl:lg:table md:table">
          <Thead :titles="['ایمیل']" />
          <tbody class="block lg:table-row-group xl:table-row-group 2xl:table-row-group md:table-row-group">
          <tr v-for="item in allData" :key="item.id" class="border border-gray-200 block lg:table-row xl:table-row 2xl:table-row md:table-row">
            <Td title="ایمیل" class="fm:text-sm">{{item.email}}</Td>
          </tr>
          </tbody>
        </table>
      </div>
      <div v-else class="flex justify-center">
        <img src="@/assets/images/no-data.svg" class="w-[300px] fm:w-[200px]"/>
      </div>
      <div v-if="allData.length > 0">
        <Paginate :current="$store.state.current" :next="$store.state.next" :previous="$store.state.previous" :total="$store.state.total" @callGetPage="getData"/>
      </div>
    </div>
    <Meta :title="$store.state.siteName + ` | لیست خبرنامه ها `"/>
    <Loading :loading="loading" />
    <ValidationError />
  </div>
</template>

<script>
export default{name: "Newsletter"}
</script>

<script setup>
import {ref, onMounted, defineExpose} from 'vue';
import Meta from "@/components/Meta.vue";
import Td from "@/components/Td.vue";
import Thead from "@/components/Thead.vue";
import Loading from "@/components/Loading.vue";
import Button from "@/components/Button.vue";
import ValidationError from "@/components/ValidationError.vue";
import Paginate from "@/components/Paginate.vue";
import axios from "@/plugins/axios";
import store from "@/store";


let refresh = ref(false)
let loading = ref(false)
let model = ref('newsletter')
let allData = ref([])

onMounted(async()=>{
  await getData();
});
defineExpose({getData});


async function getData(_loading = true,page=1, isRefresh=false){
  refresh.value = isRefresh;
  loading.value = _loading;
  await axios.get(`${store.state.api}${model.value}?page=${page}`).then(resp=>{
    allData.value = resp.data.data.data;
    store.commit('paginate',resp.data.data);
  }).catch(err=>{
    store.commit('handleError',err)
  })
  loading.value = refresh.value = false;
}


</script>