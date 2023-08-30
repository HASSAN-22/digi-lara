<template>
  <div class="mt-10 px-8">
    <div class="flex justify-center flex-col gap-5">
      <span class="!font-medium text-lg">برداشت از کیف پول</span>
      <span v-if="!isAdmin" class="text-sm text-red-500">کاربر گرامی چنانچه درخواست را ثبت کنید مبلغ انتخابی فریز خواهد شد و اگر پرداخت توسط مدیریت لغو شود مبلغ انتخابی به کیف پول شما برگردانده خواهد شد</span>
    </div>
    <div class="mt-10 px-3 mb-3">
      <div class="mb-3 mr-2 flex justify-between fm:flex-col items-center">
        <div class="flex gap-2 items-center">
          <Button v-if="!isAdmin" text="اضافه کردن" @click="create()"></Button>
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
              'شبا',
              'شماره کارت',
              'وضعیت',
              'تاریخ',
              'عملیات',
            ]" :except="!isAdmin ? ['کاربر','عملیات'] : []" />
          <tbody class="block lg:table-row-group xl:table-row-group 2xl:table-row-group md:table-row-group">
          <tr v-for="item in allData" :key="item.id" class="border border-gray-200 block lg:table-row xl:table-row 2xl:table-row md:table-row">
            <Td v-if="isAdmin" title="کاربر" class="fm:text-sm">{{item.user}}</Td>
            <Td title="مبلغ (ریال)" class="fm:text-sm">{{item.amount}} ریال </Td>
            <Td title="شبا" class="fm:text-sm">{{item.shaba}}</Td>
            <Td title="شماره کارت" class="fm:text-sm">{{item.cart_number}}</Td>
            <Td title=" پرداخت" class="fm:text-sm">{{item.ir_status}}</Td>
            <Td title=" پرداخت" class="fm:text-sm">{{item.created_at}}</Td>
            <Td v-if="isAdmin" title="عملیات">
              <div class="flex items-center justify-center gap-2" v-if="item.status === 'pending'">
                <UserCanAction action="show" @show="show(item.id,item.status)" permission="view_withdrawwallets" :postId="item.id" />
              </div>
              <div class="flex items-center justify-center gap-2" v-else>
                <Button :my_class="'!cursor-not-allowed !bg-white border border-black !py-2 !px-2 fm:py-1 fm:px-1 '"><i class="far fm:text-sm fa-edit text-black"></i></Button>
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
    <Modal v-if="isAdmin" title="ویرایش وضعیت" save="ثبت" permission="update_withdrawwallets" :btnLoading="btnLoading" @callback="update()" ref="openUpdateStatusModal">
      <div class="mb-5 w-full">
        <label class="fm:text-sm" for="status">وضعیت پرداخت<b class="text-red-500 !font-bold">*</b></label>
        <select v-model="status" id="status" class="fm:text-sm border border-gray-200 p-2 outline-none rounded-lg w-full">
          <option value="" selected disabled>--- انتخاب کنید ---</option>
          <option value="success">پرداخت شد</option>
          <option value="cancel">لغو شد</option>
          <option value="pending">در حال بررسی</option>
        </select>
      </div>
    </Modal>

    <Modal title="ثبت درخواست" save="ثبت" :btnLoading="btnLoading" @callback="insert()" ref="openModal">
      <div class="mb-10 bg-red-100 p-2 rounded-lg">
        <span class="fm:text-sm text-red-500">توجه نمایید اطلاعات وارد شده به نام شخص شما باشد </span>
      </div>
      <div class="mb-5">
        <Input label="مبلغ (ریال)" placeholder="مبلغ (ریال)" v-model="amount" id="amount" />
      </div>
      <div class="mb-5">
        <Input label="شبا" placeholder="شبا" v-model="shaba" id="shaba" />
      </div>
      <div class="mb-5">
        <Input label="شماره کارت" placeholder="شماره کارت" v-model="cartNumber" id="cartNumber" />
      </div>
    </Modal>
    <Meta :title="$store.state.siteName + ` | برداشت از کیف پول `"/>
    <Loading :loading="loading" />
    <ValidationError />
  </div>
</template>

<script>
export default{name: "Withdraw"}
</script>

<script setup>
import {ref, onMounted, defineExpose} from 'vue';
import Meta from "@/components/Meta.vue";
import Td from "@/components/Td.vue";
import Thead from "@/components/Thead.vue";
import Input from "@/components/Input.vue";
import Modal from "@/components/Modal.vue";
import UserCanAction from "@/components/UserCanAction.vue";
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
let model = ref('withdrawwallet')
let postId = ref(0)
let search = ref('')
let amount = ref('')
let shaba = ref('')
let cartNumber = ref('')
let status = ref('')
let allData = ref([])
let openUpdateStatusModal = ref(null)
let openModal = ref(null)
let isAdmin = ref(store.state.user.access === 'admin')

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

async function show(_postId,_status){
  postId.value = _postId;
  status.value = _status;
  openUpdateStatusModal.value.toggleModal();
}

async function update(){
  btnLoading.value =  true;
  await axios.patch(`${store.state.api}${model.value}/${postId.value}`,{status:status.value}).then(async resp=>{
    status.value = '';
    await getData(false, store.state.current, false);
    Toast.success("تغییرات با موفقیت ثبت شد");
    openUpdateStatusModal.value.toggleModal();
  }).catch(err=>{
    store.commit('handleError',err)
  })
  btnLoading.value =  false;
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
    shaba:shaba.value,
    cart_number:cartNumber.value,
  };
}

function clearData(){
  amount.value = '';
  shaba.value = '';
  cartNumber.value = '';
  openModal.value.toggleModal();
}

</script>