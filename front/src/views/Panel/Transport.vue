<template>
  <div class="mt-10 px-8">
    <div class="flex justify-center">
      <span class="!font-medium text-lg">لیست حمل و نقل ها</span>
    </div>
    <div class="mt-10 px-3 mb-3">
      <div class="mb-3 mr-2 flex justify-between fm:flex-col items-center">
        <div class="flex gap-2 items-center">
          <UserCanAction action="create" @create="create()" permission="create_transports" />
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
              'نام',
              'قیمت (ریال)',
              'ایا رایگان است',
              'ایا هزینه ارسال پس ‌کرایه است',
              'مالیات',
              'نوع وزن',
              'عملیات'
          ]" />
          <tbody class="block lg:table-row-group xl:table-row-group 2xl:table-row-group md:table-row-group">
          <tr v-for="item in allData" :key="item.id" class="border border-gray-200 block lg:table-row xl:table-row 2xl:table-row md:table-row">
            <Td title="نام" class="fm:text-sm">{{item.name}}</Td>
            <Td title="قیمت (ریال)" class="fm:text-sm">{{item.fixed_price}}</Td>
            <Td title=" ایا رایگان است" class="fm:text-sm">{{item.is_free}}</Td>
            <Td title="ایا هزینه ارسال پس ‌کرایه است" class="fm:text-sm">{{item.is_freight}}</Td>
            <Td title="مالیات" class="fm:text-sm">{{item.tax}}%</Td>
            <Td title="نوع وزن" class="fm:text-sm">{{item.weight_type}}</Td>
            <Td title="عملیات">
              <div class="flex items-center justify-center gap-2">
                <UserCanAction action="show" @show="show(item.id)" permission="view_transports" :postId="item.id" />
                <UserCanAction action="destroy" @destroy="destroy(item.id)" permission="update_transports" :postId="item.id" />
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
    <Modal width="w-[55%]" :title="isUpdate ? 'ویرایش حمل و نقل' : 'ثبت حمل و نقل'" :save="isUpdate ? 'ثبت تغییرات' : 'ثبت'" :permission="isUpdate ? 'update_transports' : 'create_transports'" :btnLoading="btnLoading" @callback="isUpdate ? update() : insert()" ref="openModal">
      <div class="mb-5">
        <Input label="نام" placeholder="نام" v-model="transportName" id="transportName" />
      </div>
      <div class="mb-5">
        <Input label="درصد مالیات" placeholder="درصد مالیات" v-model="tax" id="tax" />
      </div>
      <div class="mb-5">
        <label class="fm:text-sm">برای کالای <b class="text-red-500 !font-bold">*</b></label>
        <select v-model="weightType" class="fm:text-sm border border-gray-200 p-2 outline-none rounded-lg w-full">
          <option value="" selected>--- انتخاب کنید ---</option>
          <option value="style">سبک</option>
          <option value="heavy">سنگین</option>
          <option value="super_heavy">فوق سنگین</option>
        </select>
      </div>
      <div v-if="weightType === 'style' && weightType !== ''">
        <div class="mb-5">
          <label class="fm:text-sm">ایا رایگان هست <b class="text-red-500 !font-bold">*</b></label>
          <select v-model="isFree" class="fm:text-sm border border-gray-200 p-2 outline-none rounded-lg w-full">
            <option value="" selected>--- انتخاب کنید ---</option>
            <option value="yes">بله</option>
            <option value="no">خیر</option>
          </select>
        </div>
        <div class="mb-9" v-if="isFree === 'no'">
          <Input label="قیمت پایه (ریال)" placeholder="قیمت پایه (ریال)" v-model="fixedPrice" id="fixedPrice" />
        </div>
      </div>
      <div v-if="weightType !== 'style' && weightType !== ''">
        <div class="mb-5">
          <label class="fm:text-sm">ایا هزینه ارسال به‌صورت پس ‌کرایه است<b class="text-red-500 !font-bold">*</b></label>
          <select v-model="isFreight" class="fm:text-sm border border-gray-200 p-2 outline-none rounded-lg w-full">
            <option value="" selected>--- انتخاب کنید ---</option>
            <option value="yes">بله</option>
            <option value="no">خیر</option>
          </select>
        </div>
        <div class="mb-9" v-if="isFreight === 'no'">
          <Input label="قیمت (ریال)" placeholder="قیمت (ریال)" v-model="fixedPrice" id="fixedPrice" />
        </div>
      </div>
      <div class="flex items-center fm:flex-col fd:justify-between gap-4">
          <Input label="زمان تقریبی تحویل (روز)" placeholder="مثال 1" v-model="fromDay" id="fromDay" />
          <Input label="زمان تقریبی تحویل (روز)" placeholder="مثال 3" v-model="toDay" id="toDay" />
      </div>
    </Modal>
    <Meta :title="$store.state.siteName + ` |حمل و نقل ها `"/>
    <Loading :loading="loading" />
    <ValidationError />
  </div>
</template>

<script>
export default{name: "Transport"}
</script>

<script setup>
import {ref, onMounted, defineExpose} from 'vue';
import Meta from "@/components/Meta.vue";
import Td from "@/components/Td.vue";
import Thead from "@/components/Thead.vue";
import UserCanAction from "@/components/UserCanAction.vue";
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
let isUpdate = ref(false)
let postId = ref(null)
let model = ref('transport')
let search = ref('')

let transportName = ref('')
let fixedPrice = ref('')
let isFree = ref('')
let isFreight = ref('')
let tax = ref('')
let weightType = ref('')
let fromDay = ref('')
let toDay = ref('')

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

async function show(_postId){
  clearData();
  postId.value = _postId;
  isUpdate.value = loading.value = true;
  await axios.get(`${store.state.api}${model.value}/${postId.value}`).then(async resp=>{
    let data = resp.data.data;
    transportName.value = data.name;
    fixedPrice.value = data.fixed_price;
    isFree.value = data.is_free;
    isFreight.value = data.is_freight;
    tax.value = data.tax;
    fromDay.value = data.from_day;
    toDay.value = data.to_day;
    weightType.value = data.weight_type;
  }).catch(err=>{
    store.commit('handleError',err)
  })
  loading.value = false;
}

async function update(){
  isUpdate.value = btnLoading.value =  true;
  await axios.patch(`${store.state.api}${model.value}/${postId.value}`,formData()).then(resp=>{
    clearData();
    Toast.success("تغییرات با موفقیت ثبت شد");
    getData(false,store.state.current)
  }).catch(err=>{
    store.commit('handleError',err)
  })
  btnLoading.value =  false;
}

async function destroy(postId){
  loading.value = false;
  await store.dispatch('deleteRecord',[`${model.value}/${postId}`])
  await getData(false,store.state.current)
  loading.value = false

}

function formData(){
  return {
    transport_name:transportName.value,
    fixed_price:fixedPrice.value,
    is_free:isFree.value,
    is_freight:isFreight.value,
    tax:tax.value,
    from_day:fromDay.value,
    to_day:toDay.value,
    weight_type:weightType.value,
  };
}

function clearData(){
  transportName.value = ''
  fixedPrice.value = ''
  isFreight.value = ''
  isFree.value = ''
  tax.value = ''
  fromDay.value = ''
  toDay.value = ''
  weightType.value = ''
  isUpdate.value = false;
  postId.value = null;
  openModal.value.toggleModal();
}

</script>