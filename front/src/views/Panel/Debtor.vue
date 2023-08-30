<template>
  <div class="mt-10 px-8">
    <div class="flex justify-center">
      <span v-if="isAdmin" class="!font-medium text-lg">لیست بستانکاری ها</span>
      <span v-else class="!font-medium text-lg">لیست بدهکاری ها</span>
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
        <table class="w-full border-collapse lg:table 2xl:table xl:lg:table md:table">
          <Thead :titles="[
              'فروشنده',
              'نام فروشگاه',
              'سفارش',
              'مبلغ بدهی (ریال)',
              'توضیحات بدهی',
              'وضعیت پرداخت',
              'تاریخ',
              'عملیات'
          ]" :except="isAdmin ? ['عملیات'] : []" />
          <tbody class="block lg:table-row-group xl:table-row-group 2xl:table-row-group md:table-row-group">
          <tr v-for="item in allData" :key="item.id" class="border border-gray-200 block lg:table-row xl:table-row 2xl:table-row md:table-row">
            <Td title="فروشنده" class="fm:text-sm">{{item.user}}</Td>
            <Td title="نام فروشگاه" class="fm:text-sm">{{item.shop_name}}</Td>
            <Td title="سفارش" class="fm:text-sm">{{item.order_id}}</Td>
            <Td title="مبلغ بدهی (ریال)" class="fm:text-sm">{{item.amount}}</Td>
            <Td title="توضیحات بدهی" class="fm:text-sm">
              <Button :my_class="'!bg-white border border-green-500 !py-2 !px-2 fm:py-1 fm:px-1'" @click="openDescription(item.description)"><i class="far fm:text-sm fa-eye text-green-500"></i></Button>
            </Td>
            <Td title="وضعیت پرداخت" class="fm:text-sm">{{item.ir_status}}</Td>
            <Td title="تاریخ" class="fm:text-sm">{{item.created_at}}</Td>
            <Td title="عملیات" v-if="!isAdmin">
              <div class="flex items-center justify-center gap-2" v-if="item.status === 'pending'">
                <Button :my_class="'!bg-white border border-blue-500 !py-2 !px-2 fm:py-1 fm:px-1'" @click="showPayModal(item.id)"><i class="far fm:text-sm fa-edit text-blue-500"></i></Button>
              </div>
              <div v-else>
                ---
              </div>
            </Td>
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
    <Modal title="پرداخت بدهی" save="پرداخت" :btnLoading="btnLoading" @callback="pay()" ref="openModal">
      <div class="mb-5">
        <label class="fm:text-sm" for="payType">روش پرداخت<b class="text-red-500 !font-bold">*</b></label>
        <select v-model="payType" id="payType" class="fm:text-sm border border-gray-200 p-2 outline-none rounded-lg w-full">
          <option value="" selected disabled>--- انتخاب کنید ---</option>
          <option value="wallet">کیف پول</option>
          <option value="bank">درگاه بانکی</option>
        </select>
      </div>
    </Modal>
    <Modal title="مشاهده توضیحات بدهی" ref="openDescriptionModal">
      <div class="mb-5">
        <p v-html="description" class="break-all"></p>
      </div>
    </Modal>
    <Meta v-if="isAdmin" :title="$store.state.siteName + ` | لیست بستانکاری ها `"/>
    <Meta v-else :title="$store.state.siteName + ` | لیست بدهکاری ها `"/>
    <Loading :loading="loading" />
    <ValidationError />
  </div>
</template>

<script>
export default{name: "Debtor"}
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


let refresh = ref(false)
let btnLoading = ref(false)
let loading = ref(false)
let postId = ref(null)
let model = ref('debtor')
let search = ref('')
let payType = ref('')
let description = ref('')
let allData = ref([])
let openModal = ref(null)
let openDescriptionModal = ref(null)
let isAdmin = ref(store.state.user.access === 'admin');
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

function showPayModal(_postId){
  payType.value = '';
  postId.value = _postId;
  openModal.value.toggleModal();
}

async function pay(){
  btnLoading.value = true;
  await axios.post(`${store.state.api}${model.value}/${postId.value}`,{pay_type:payType.value}).then(resp=>{
    Toast.success();
    getData(false, store.state.current)
    openModal.value.toggleModal();
  }).catch(err=>{
    store.commit('handleError',err)
  })
  btnLoading.value = false;
}

function openDescription(_description){
  description.value = _description;
  openDescriptionModal.value.toggleModal()
}

</script>