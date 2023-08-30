<template>
  <div class="mt-10 px-8">
    <div class="flex justify-center">
      <span class="!font-medium text-lg">لیست ادرس ها</span>
    </div>
    <div class="mt-10 px-3 mb-3">
      <div class="mb-3 mr-2 flex justify-between fm:flex-col items-center">
        <div class="flex gap-2 items-center">
          <Button text="اضافه کردن" @click="create()"></Button>
          <Button @click="getData(false, 1, true)" my_class="!bg-white !py-2 !px-2" :btnLoading="refresh"><i class="far fa-sync text-2xl fm:text-lg text-gray-700"></i></Button>
        </div>
        <div class="ml-2 fm:mt-3">
        </div>
      </div>
      <div v-if="allData.length <= 0" class="border border-b w-full my-2"></div>
      <div v-if="allData.length > 0">
        <table class="w-full border-collapse lg:table 2xl:table xl:lg:table md:table">
          <Thead :titles="[
              'نام',
              'موبایل',
              'ادرس',
              'استان',
              'شهرستان',
              'کد پستی',
              'عملیات'
            ]" />
          <tbody class="block lg:table-row-group xl:table-row-group 2xl:table-row-group md:table-row-group">
          <tr v-for="item in allData" :key="item.id" class="border border-gray-200 block lg:table-row xl:table-row 2xl:table-row md:table-row">
            <Td title="نام" class="fm:text-sm">{{item.name}}</Td>
            <Td title="موبایل" class="fm:text-sm">{{item.mobile}}</Td>
            <Td title="ادرس" class="fm:text-sm">{{item.address}}</Td>
            <Td title="استان" class="fm:text-sm">{{item.province}}</Td>
            <Td title="شهرستان" class="fm:text-sm">{{item.city}}</Td>
            <Td title="کد پستی" class="fm:text-sm">{{item.postal_code}}</Td>
            <Td title="عملیات">
              <div class="flex items-center justify-center gap-2">
                <Button :my_class="'!bg-white border border-blue-500 !py-2 !px-2 fm:py-1 fm:px-1'" @click="show(item.id)"><i class="far fm:text-sm fa-edit text-blue-500"></i></Button>
                <Button :my_class="'!bg-white border border-red-500 !py-2 !px-2 fm:py-1 fm:px-1'" @click="destroy(item.id)"><i class="far fm:text-sm fa-trash text-red-500"></i></Button>
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
    <Modal :title="isUpdate ? 'ویرایش ادرس' : 'ثبت ادرس'" :save="isUpdate ? 'ثبت تغییرات' : 'ثبت'" :btnLoading="btnLoading" @callback="isUpdate ? update() : insert()" ref="openModal">
      <div class="mb-5">
        <Textarea label="نشانی پستی" v-model="address" id="address" :maxlength="2000" :alert="address.length + '/2000'"/>
      </div>
      <div class="mb-5 flex items-center gap-2 justify-between">
        <div class="mb-5 w-full">
          <label class="fm:text-sm" for="province">استان<b class="text-red-500 !font-bold">*</b></label>
          <select v-model="province" id="province" @change="getCities($event)" class="fm:text-sm border border-gray-200 p-2 outline-none rounded-lg w-full">
            <option value="" selected disabled>--- انتخاب کنید ---</option>
            <option v-for="_province in provinces" :key="_province.id" :value="_province.id">{{_province.name}}</option>
          </select>
        </div>
        <div class="mb-5 w-full">
          <label class="fm:text-sm" for="city">شهرستان<b class="text-red-500 !font-bold">*</b></label>
          <select v-model="city" id="city" class="fm:text-sm border border-gray-200 p-2 outline-none rounded-lg w-full">
            <option value="" selected disabled>--- انتخاب کنید ---</option>
            <option v-for="_city in cities" :key="_city.id" :value="_city.id">{{_city.name}}</option>
          </select>
        </div>
      </div>
      <div class="mb-5 flex items-center gap-2 justify-between">
        <Input type="number" label="پلاک" v-model="plaque" :id="plaque"/>
        <Input type="number" label="واحد" v-model="unit" :id="unit" :required="false" />
        <Input type="number" label="کد پستی" v-model="postalCode" id="postalCode" />
      </div>
      <div class="mb-5 flex items-center gap-2 justify-between">
        <Input label="نام گیرنده" v-model="receiverName" id="receiverName" />
        <Input label="نام‌خانوادگی گیرنده" v-model="receiverLastName" id="receiverLastName" />
      </div>
      <div class="mb-5">
        <Input type="number" label="شماره موبایل" alert="مثل: 09121112233" v-model="mobile" id="mobile" />
      </div>
    </Modal>
    <Meta :title="$store.state.siteName + ` | لیست ادرس ها `"/>
    <Loading :loading="loading" />
    <ValidationError />
  </div>
</template>

<script>
export default{name: "Color"}
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
import Textarea from "@/components/Textarea.vue";
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
let model = ref('address')
let search = ref('')

let cities = ref([]);
let provinces = ref([]);
let address = ref('');
let province = ref('');
let city = ref('');
let plaque = ref('');
let unit = ref('');
let postalCode = ref('');
let receiverName = ref('');
let receiverLastName = ref('');
let mobile = ref('');

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
    provinces.value = data.provinces;
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
    province.value = data.province_id;
    city.value = data.city_id;
    address.value = data.address;
    plaque.value = data.plaque;
    unit.value = data.unit;
    postalCode.value = data.postal_code;
    receiverName.value = data.receiver_name;
    receiverLastName.value = data.receiver_last_name;
    mobile.value = data.mobile;
    await getCities('',province.value,false)
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

async function getCities(event, _cityId=null, _loading=true){
  loading.value = _loading;
  let provinceId = _cityId === null ? event.target.value : _cityId;
  await axios.get(`${store.state.api}get-cities/${provinceId}`).then(resp=>{
    cities.value = resp.data.data;
  })
  loading.value = false;
}

function formData(){
  return {
    province_id:province.value,
    city_id:city.value,
    address:address.value,
    plaque:plaque.value,
    unit:unit.value,
    postal_code:postalCode.value,
    receiver_name:receiverName.value,
    receiver_last_name:receiverLastName.value,
    mobile:mobile.value,
    is_selected:'no',
  };
}

function clearData(){
  cities.value = [];
  address.value = '';
  province.value = '';
  city.value = '';
  plaque.value = '';
  unit.value = '';
  postalCode.value = '';
  receiverName.value = '';
  receiverLastName.value = '';
  mobile.value = '';
  isUpdate.value = false;
  postId.value = null;
  openModal.value.toggleModal();
}

</script>