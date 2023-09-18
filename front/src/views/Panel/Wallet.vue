<template>
  <div class="mt-10 px-8">
    <div class="flex justify-center">
      <span class="!font-medium text-lg">کیف پول</span>
    </div>
    <div class="mt-10 px-3 mb-3">
      <div class="mb-3 mr-2 flex justify-between fm:flex-col items-center">
        <div class="flex gap-2 items-center">
          <Button text="اضافه کردن" @click="create()"></Button>
          <Button @click="getData(false, 1, true)" my_class="!bg-white !py-2 !px-2" :btnLoading="refresh"><i class="far fa-sync text-2xl fm:text-lg text-gray-700"></i></Button>
        </div>
        <div class="ml-2 fm:mt-3">
          <Input type="search" v-debounce="searchData" :debounce-events="['keydown']" id="search" placeholder="جستجو: متن خود را وارد کنید" :required="false" />
        </div>
      </div>
      <div v-if="allData.length <= 0" class="border border-b w-full my-2"></div>
      <div v-if="allData.length > 0">
        <table class="w-full border-collapse lg:table 2xl:table xl:lg:table md:table">
          <Thead :titles="[
              'کاربر',
              'مبلغ (ریال)',
              'ایدی تراکنش',
              'وضعیت پرداخت',
              'پرداخت توسط',
              'تاریخ پرداخت',
            ]" :except="!isAdmin ? ['کاربر'] : []" />
          <tbody class="block lg:table-row-group xl:table-row-group 2xl:table-row-group md:table-row-group">
          <tr v-for="item in allData" :key="item.id" class="border border-gray-200 block lg:table-row xl:table-row 2xl:table-row md:table-row">
            <Td v-if="isAdmin" title="کاربر" class="fm:text-sm">{{item.user}}</Td>
            <Td title="مبلغ (ریال)" class="fm:text-sm">{{item.amount}}</Td>
            <Td title="ایدی تراکنش" class="fm:text-sm">{{item.ref_id}}</Td>
            <Td title="وضعیت پرداخت" class="fm:text-sm">{{item.status}}</Td>
            <Td title="پرداخت توسط" class="fm:text-sm">{{item.paid_by}}</Td>
            <Td title="تاریخ پرداخت" class="fm:text-sm">{{item.created_at}}</Td>
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
    <Modal title="شارژ کیف پول" save="پرداخت" :btnLoading="btnLoading" @callback="insert()" ref="openModal">
      <Input label="مبلغ (ریال)" placeholder="مبلغ (ریال)" v-model="amount" id="amount" />
    </Modal>
    <Meta :title="$store.state.siteName + ` | کیف پول `"/>
    <Loading :loading="loading" />
    <ValidationError />
  </div>
</template>

<script>
export default{name: "Wallet"}
</script>

<script setup>
import {ref, onMounted, defineExpose} from 'vue';
import Meta from "@/components/Meta.vue";
import Td from "@/components/Td.vue";
import Thead from "@/components/Thead.vue";
import Input from "@/components/Input.vue";
import Modal from "@/components/Modal.vue";
import Loading from "@/components/Loading.vue";
import Button from "@/components/Button.vue";
import ValidationError from "@/components/ValidationError.vue";
import Paginate from "@/components/Paginate.vue";
import axios from "@/plugins/axios";
import store from "@/store";
import Toast from "@/plugins/toast";


let isAdmin = ref(store.state.user.access === 'admin')
let refresh = ref(false)
let btnLoading = ref(false)
let loading = ref(false)
let model = ref('wallet')
let search = ref('')
let amount = ref('')
let allData = ref([])
let openModal = ref(null)

onMounted(async()=>{
  await getData();
});
defineExpose({getData});


async function getData(_loading = true,page=1, isRefresh=false){
  if(isRefresh) search.value = '';
  refresh.value = true;
  loading.value = _loading;
  await axios.get(`${store.state.api}${model.value}?page=${page}&search=${search.value}`).then(resp=>{
    let data = resp.data;
    allData.value = data.data;
    store.commit('paginate',data.meta);
  }).catch(err=>{
    store.commit('handleError',err)
  })
  loading.value = refresh.value = false;
}

function searchData(text){
  search.value = text
  getData(false)
}

function create(){
  clearData();
}

async function insert(){
  btnLoading.value = true;
  await axios.post(`${store.state.api}${model.value}`,formData()).then(resp=>{
    clearData();
    Toast.success();
    getData(false, store.state.current)
  }).catch(err=>{
    store.commit('handleError',err)
  })
  btnLoading.value = false;
}

function formData(){
  return {
    amount:amount.value,
  };
}

function clearData(){
  amount.value = '';
  openModal.value.toggleModal();
}

</script>