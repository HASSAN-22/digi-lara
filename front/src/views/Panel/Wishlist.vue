<template>
  <div class="mt-10 px-8">
    <div class="flex justify-center">
      <span class="!font-medium text-lg">لیست من</span>
    </div>
    <div class="mt-10 px-3 mb-3">
      <div class="mb-3 mr-2 flex justify-between fm:flex-col items-center">
        <div class="flex gap-2 items-center">
          <Button @click="getData(false, 1, true)" my_class="!bg-white !py-2 !px-2" :btnLoading="refresh"><i class="far fa-sync text-2xl fm:text-lg text-gray-700"></i></Button>
        </div>
        <div class="ml-2 fm:mt-3">
          <Input type="search" v-debounce="searchData" :debounce-events="['keydown']" id="search" placeholder="جستجو: متن خود را وارد کنید" :required="false" />
        </div>
      </div>
      <div v-if="allData.length <= 0" class="border border-b w-full my-2"></div>
      <div v-if="allData.length > 0">
        <div class="flex flex-wrap justify-evenly gap-4">
          <div class="border border-gray-100 p-3 flex flex-col justify-end w-[28rem] rounded-lg" v-for="item in allData" :key="item.id">
            <div class="flex gap-5 mb-8">
              <div class="w-[6rem]"><img :src="$store.state.url + item.product.image" /></div>
              <span class="!font-medium fm:text-sm">{{item.product.ir_name}}</span>
            </div>
            <div class="flex justify-between items-center">
              <routerLink :to="{name:'ProductDetail', params:{slug:item.product.slug}}" class="text-blue-400 fm:text-sm">مشاهده</routerLink>
              <Button :btnLoading="btnLoading" @click="destroy(item.id)" text="حذف" my_class="!py-1 !bg-white !text-red-500 !border !border-red-500 !hover:bg-white !text-sm"/>
            </div>
          </div>
        </div>
      </div>
      <div v-else class="flex justify-center">
        <img src="@/assets/images/no-data.svg" class="w-[300px] fm:w-[200px]"/>
      </div>
      <div v-if="allData.length > 0">
        <Paginate :current="$store.state.current" :next="$store.state.next" :previous="$store.state.previous" :total="$store.state.total" @callGetPage="getData"/>
      </div>
    </div>

    <Meta :title="$store.state.siteName + ` | لیست من `"/>
    <Loading :loading="loading" />
    <ValidationError />
  </div>
</template>

<script>
export default{name: "WishList"}
</script>

<script setup>
import {ref, onMounted, defineExpose} from 'vue';
import Meta from "@/components/Meta.vue";
import Input from "@/components/Input.vue";
import Loading from "@/components/Loading.vue";
import Button from "@/components/Button.vue";
import ValidationError from "@/components/ValidationError.vue";
import Paginate from "@/components/Paginate.vue";
import axios from "@/plugins/axios";
import store from "@/store";

let refresh = ref(false)
let btnLoading = ref(false)
let loading = ref(false)
let model = ref('wishlist')
let search = ref('')
let allData = ref([])

onMounted(async()=>{
  await getData();
});
defineExpose({getData});


async function getData(_loading = true,page=1, isRefresh=false){
  if(isRefresh) search.value = '';
  refresh.value = true;
  loading.value = _loading;
  await axios.get(`${store.state.api}${model.value}?page=${page}&search=${search.value}`).then(resp=>{
    allData.value = resp.data.data.data;
    store.commit('paginate',resp.data.data);
  }).catch(err=>{
    store.commit('handleError',err)
  })
  loading.value = refresh.value = false;
}

function searchData(text){
  search.value = text
  getData(false)
}


async function destroy(postId){
  loading.value = false;
  await store.dispatch('deleteRecord',[`${model.value}/${postId}`])
  await getData(false,store.state.current)
  loading.value = false

}


</script>