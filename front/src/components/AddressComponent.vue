<template>
  <div>


    <!--    Address list-->
    <Modal title="ادرس ها" save="انتخاب" :btnLoading="btnLoading" @callback="chooseAddress()" ref="addressListModal" width="w-[60%] fm:w-full">
      <div class="mb-8 flex items-center gap-3 cursor-pointer" @click="openAddressModal(true)">
        <span class="flex"><i class="far fa-location-plus"></i></span>
        <h1 class="text-lg !font-medium">افزودن آدرس جدید</h1>
        <span class="flex"><i class="far fa-plus"></i></span>
      </div>

      <div class="border-b text-gray-200 w-full mb-12"></div>
      <div class="flex flex-col gap-3 mb-5" v-for="(address,index) in allAddresses" :key="address.id">
        <div class="flex gap-2 items-center">
          <input type="radio" v-model="selectedAddressId" :checked="props.addressId > 0 ? (props.addressId === address.id) : (address.is_selected === 'yes')" :value="address.id"/>
          <span class="!font-medium">{{address.address}}</span>
        </div>
        <div class="mr-4 flex flex-col gap-3">
          <div class="flex items-center gap-4">
            <span class="text-gray-400"><i class="far fa-envelope"></i></span>
            <span class="text-gray-400">{{address.postal_code}}</span>
          </div>
          <div class="flex items-center gap-4">
            <span class="text-gray-400"><i class="far fa-mobile"></i></span>
            <span class="text-gray-400">{{address.mobile}}</span>
          </div>
          <div class="flex items-center gap-4">
            <span class="text-gray-400"><i class="far fa-user"></i></span>
            <span class="text-gray-400">{{address.receiver_name +' '+ address.receiver_last_name}}</span>
          </div>
          <div>
            <button class="text-sm text-blue-400" @click="showAddress(address.id)">ویرایش</button>
          </div>
        </div>
        <div class="border-b text-gray-200 w-full" v-if="index < allAddresses.length"></div>
      </div>
    </Modal>

    <!--    Add or update address-->
    <Modal :title="isUpdate ? 'ویرایش ادرس' : 'ثبت ادرس'" :save="isUpdate ? 'ثبت تغییرات' : 'ثبت'" :btnLoading="btnLoading" @callback="isUpdate ? updateAddress() : addAddress()" ref="addressModal" width="fm:w-full">
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
    <ValidationError />
  </div>
</template>

<script>
export default {name:'AddressComponent'}
</script>

<script setup>
import {ref,defineExpose,defineProps} from 'vue';
import Modal from "@/components/Modal";
import axios from "@/plugins/axios";
import store from "@/store";
import Toast from "@/plugins/toast";
import ValidationError from "@/components/ValidationError";
import Textarea from "@/components/Textarea";
import Input from "@/components/Input";

const props = defineProps({
  'saveToOrder':{
    tye:String,
    default:'',
  },
  'orderId':{
    type:Number,
    default:0,
  },
  'addressId':{
    type:Number,
    default:0,
  }
})

let isUpdate = ref(false);
let btnLoading = ref(false);
let loading = ref(false);
let postId = ref(0);

let addressListModal = ref(null);
let addressModal = ref(null);


let address = ref('');
let province = ref('');
let city = ref('');
let plaque = ref('');
let unit = ref('');
let postalCode = ref('');
let receiverName = ref('');
let receiverLastName = ref('');
let mobile = ref('');

let cities = ref([])
let provinces = ref([]);
let allAddresses = ref([]);
let currentAddress = ref([]);
let selectedAddressId = ref(null);


defineExpose({
  openAddressModal,
  openAddressListModal,
  getAddresses,
  currentAddress,
  selectedAddressId
})

async function getAddresses(_loading=true){
  loading.value = _loading;
  await axios.get(`${store.state.api}get-addresses`).then(resp=>{
    let data = resp.data.data;
    allAddresses.value = data.addresses;
    provinces.value = data.provinces
    currentAddress.value = allAddresses.value.filter(item=>item.is_selected === 'yes');
    selectedAddressId.value = currentAddress.value.id;
  }).catch(err=>{})
  loading.value = false;
}

async function getCities(event, _cityId=null, _loading=true){
  loading.value = _loading;
  let provinceId = _cityId === null ? event.target.value : _cityId;
  await axios.get(`${store.state.api}get-cities/${provinceId}`).then(resp=>{
    cities.value = resp.data.data;
  }).catch(err=>{
    Toast.error('یک خطای غیر منتظره رخ داده است')
  })
  loading.value = false;
}

async function chooseAddress(){
  btnLoading.value = true;
  let data = {};
  if(props.saveToOrder !== ''){
    data = {
      'save_to_order':props.saveToOrder,
      'order_id':props.orderId,
      'address_id':selectedAddressId.value,
    }
  }
  await axios.post(`${store.state.api}choose-address/${selectedAddressId.value}`,data).then(async resp=>{
    await getAddresses(false);
    openAddressListModal();
    Toast.success();
    store.state.addressComponentKey++;
  }).catch(err=>{
    Toast.error('یک خطای غیر متظره رخ داده است')
  })
  btnLoading.value = false;
}

async function addAddress(){
  clearForm();
  btnLoading.value = true;
  await axios.post(`${store.state.api}address`,setForm()).then(async resp=>{
    await getAddresses();
    addressModal.value.toggleModal();
    Toast.success();
    store.state.addressComponentKey++;
  }).catch(err=>{
    store.commit('handleError',err)
  })
  btnLoading.value = false;
}

async function showAddress(_postId){
  clearForm();
  loading.value = true;
  postId.value = _postId;
  isUpdate.value = true;
  await axios.get(`${store.state.api}address/${postId.value}`).then(async resp=>{
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
    openAddressModal(true)
  }).catch(err=>{
    store.commit('handleError',err)
  })
  loading.value = false;
}

async function updateAddress(){
  btnLoading.value = true;
  await axios.patch(`${store.state.api}address/${postId.value}`,setForm()).then(async resp=>{
    await getAddresses();
    addressModal.value.toggleModal();
    Toast.success();
    store.state.addressComponentKey++;
  }).catch(err=>{
    store.commit('handleError',err)
  })
  btnLoading.value = false;
}

function setForm(){
  let data = {
    province_id:province.value,
    city_id:city.value,
    address:address.value,
    plaque:plaque.value,
    unit:unit.value,
    postal_code:postalCode.value,
    receiver_name:receiverName.value,
    receiver_last_name:receiverLastName.value,
    mobile:mobile.value,
  }
  if(allAddresses.value.length <= 0){
    data['is_selected'] = 'yes';
  }
  return data;
}

function clearForm(){
  province.value = '';
  city.value = '';
  address.value = '';
  plaque.value = '';
  unit.value = '';
  postalCode.value = '';
  receiverName.value = '';
  receiverLastName.value = '';
  mobile.value = '';
  postId.value = 0;
  isUpdate.value = false;
}

function openAddressModal(closeAddressList=true){
  addressModal.value.toggleModal();
  if(closeAddressList){
    addressListModal.value.toggleModal();
  }
}

function openAddressListModal(){
  addressListModal.value.toggleModal();
}
</script>