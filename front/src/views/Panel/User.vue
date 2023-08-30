<template>
  <div class="mt-10 px-8">
    <div class="flex justify-center">
      <span class="!font-medium text-lg">لیست کاربران</span>
    </div>
    <div class="mt-10 px-3 mb-3">
      <div class="mb-3 mr-2 flex justify-between fm:flex-col items-center">
        <div class="flex gap-2 items-center">
          <UserCanAction action="create" @create="create()" permission="create_users" />
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
              'موبایل',
              'تصویر',
              'دسترسی',
              'نقش',
              'وضعیت',
              'تاریخ ثبت',
              'عملیات'
              ]" />
          <tbody class="block lg:table-row-group xl:table-row-group 2xl:table-row-group md:table-row-group">
          <tr v-for="item in allData" :key="item.id" class="border border-gray-200 block lg:table-row xl:table-row 2xl:table-row md:table-row">
            <Td title="نام" class="fm:text-sm">{{item.name}}</Td>
            <Td title="موبایل" class="fm:text-sm">{{item.mobile}}</Td>
            <Td title="تصویر" class="fm:text-sm fd:w-[90px] fm:w-[60px]"><img :src="store.state.url + item.avatar" class="fm:mr-[5rem]"/></Td>
            <Td title="دسترسی" class="fm:text-sm">{{item.access}}</Td>
            <Td title="نقش" class="fm:text-sm">{{item.role}}</Td>
            <Td title="وضعیت" class="fm:text-sm">{{item.status}}</Td>
            <Td title="تاریخ ثبت" class="fm:text-sm">{{item.created_at}}</Td>
            <Td title="عملیات">
              <div class="flex items-center justify-center gap-2">
                <UserCanAction action="show" @show="show(item.id)" permission="view_users" :postId="item.id" />
                <UserCanAction action="destroy" @destroy="destroy(item.id)" permission="delete_users" :postId="item.id" />
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
    <Modal :title="isUpdate ? 'ویرایش کاربر' : 'ثبت کاربر'" :save="isUpdate ? 'ثبت تغییرات' : 'ثبت'" :permission="isUpdate ? 'update_users' : 'create_users'" :btnLoading="btnLoading" @callback="isUpdate ? update() : insert()" ref="openModal">
      <div class="mt-5">
        <Input class="mt-5" :required="true" label="نام" v-model="name" id="name" />
        <Input class="mt-5" type="mobile" :required="true" label="موبایل" v-model="mobile" id="mobile" />
        <div class="mt-5">
          <label class="fm:text-sm">دسترسی<b class="text-red-500 !font-bold">*</b></label>
          <select v-model="access" class="fm:text-sm border border-gray-200 p-2 outline-none rounded-lg w-full">
            <option value="" selected>--- انتخاب کنید ---</option>
            <option value="admin">ادمین</option>
            <option value="user">مشتری</option>
            <option value="seller">فروشنده</option>
          </select>
        </div>
        <div class="mt-5" v-if="access === 'admin'">
          <label class="fm:text-sm">نقش<b class="text-red-500 !font-bold">*</b></label>
          <select v-model="role" class="fm:text-sm border border-gray-200 p-2 outline-none rounded-lg w-full">
            <option value="" selected>--- انتخاب کنید ---</option>
            <option v-for="role in allRoles" :key="role.id" :value="role.id">{{role.name}}</option>
          </select>
        </div>
        <div class="mt-5">
          <label class="fm:text-sm">وضعیت<b class="text-red-500 !font-bold">*</b></label>
          <select v-model="status" class="fm:text-sm border border-gray-200 p-2 outline-none rounded-lg w-full">
            <option value="" selected>--- انتخاب کنید ---</option>
            <option value="yes">فعال</option>
            <option value="no">غیر فعال</option>
          </select>
        </div>
        <Input class="mt-5" type="password" alert="حداکثر 8 کاراکتر" :placeholder="isUpdate ? 'اگر رمز را تغییر نمیدهید خالی بگذارید' : ''" :required="!isUpdate" label="رمز عبور" v-model="password" id="password" />
        <div class="mt-5">
          <Input :required="!isUpdate" label="تصویر" type="file" :alert="`فرمت مجار: JPG|JPEG|PNG|WEBP حداکثر حجم: ${store.state.imageSize}`" @change="getFile($event)" id="image" :key="imageKey" />
          <div v-if="previewImage">
            <img :src="previewImage" width="100" height="100" />
          </div>
        </div>
      </div>
    </Modal>
    <Meta :title="$store.state.siteName + ` | لیست کاربران `"/>
    <Loading :loading="loading" />
    <ValidationError />
  </div>
</template>

<script>
export default{name: "User"}
</script>

<script setup>
import {ref, onMounted, defineExpose} from 'vue';
import Meta from "@/components/Meta.vue";
import Td from "@/components/Td.vue";
import Thead from "@/components/Thead.vue";
import Input from "@/components/Input.vue";
import Modal from "@/components/Modal.vue";
import Loading from "@/components/Loading.vue";
import ValidationError from "@/components/ValidationError.vue";
import Button from "@/components/Button.vue";
import Paginate from "@/components/Paginate.vue";
import UserCanAction from "@/components/UserCanAction.vue";
import axios from "@/plugins/axios";
import store from "@/store";
import Toast from "@/plugins/toast";


let refresh = ref(false)
let btnLoading = ref(false)
let loading = ref(false)
let isUpdate = ref(false)
let postId = ref(null)
let search = ref('')
let role = ref('')
let name = ref('')
let mobile = ref('')
let access = ref('')
let status = ref('')
let avatar = ref('')
let password = ref('')
let imageKey = ref(0)
let previewImage = ref(null)
let allData = ref([])
let allRoles = ref([])
let openModal = ref(null)

onMounted(async()=>{
  await getData();
});
defineExpose({getData});


async function getData(_loading = true,page=1, isRefresh=false){
  if(isRefresh) search.value = '';
  refresh.value = true;
  loading.value = _loading;
  await axios.get(`${store.state.api}user?page=${page}&search=${search.value}`).then(resp=>{
    allData.value = resp.data.data;
    store.commit('paginate',resp.data.meta);
  }).catch(err=>{
    store.commit('handleError',err)
  })
  loading.value = refresh.value = false;
}

function searchData(text){
  search.value = text
  getData(false)
}

async function getRoles(){
  await axios.get(`${store.state.api}get-roles`).then(resp=>{
    allRoles.value = resp.data.data;
  }).catch(err=>{
    store.commit('handleError',err)
  })
}

async function create(){
  await getRoles();
  clearData();
}

async function insert(){
  btnLoading.value = true;
  await axios.post(`${store.state.api}user`,formData()).then(resp=>{
    clearData();
    Toast.success();
    getData(false, store.state.current)
  }).catch(err=>{
    store.commit('handleError',err)
  })
  btnLoading.value = false;
}

async function show(_postId){
  await getRoles();
  clearData();
  postId.value = _postId;
  isUpdate.value = loading.value = true;
  await axios.get(`${store.state.api}user/${postId.value}`).then(async resp=>{
    let data = resp.data.data;
    name.value = data.name;
    mobile.value = data.mobile;
    access.value = data.access;
    status.value = data.status;
    role.value = data.roles.length > 0 ? data.roles[0].id : '';
    previewImage.value = data.avatar !== null ? store.state.url + data.avatar : null;
  }).catch(err=>{
    store.commit('handleError',err)
  })
  loading.value = false;
}

async function update(){
  isUpdate.value = btnLoading.value =  true;
  await axios.post(`${store.state.api}user/${postId.value}`,formData(true)).then(resp=>{
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
  await store.dispatch('deleteRecord',[`user/${postId}`])
  await getData(false,store.state.current)
  loading.value = false

}


function formData(isUpdate=false){
  let frm = new FormData();
  frm.append('name',name.value);
  frm.append('access',access.value);
  frm.append('mobile',mobile.value);
  frm.append('avatar',avatar.value);
  frm.append('status',status.value);
  frm.append('password',password.value);
  frm.append('role',role.value);
  if(isUpdate){
    frm.append('_method','PATCH');
  }
  return frm;
}

function clearData(){
  name.value = '';
  role.value = '';
  access.value = '';
  mobile.value = '';
  avatar.value = '';
  status.value = '';
  password.value = '';
  imageKey.value++;
  previewImage.value = null;
  openModal.value.toggleModal();
}

function getFile(event){
  avatar.value = event.target.files[0];
  previewImage.value = URL.createObjectURL(avatar.value);
  imageKey.value++;
}

</script>