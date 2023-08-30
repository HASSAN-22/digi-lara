<template>
  <div class="mt-10 px-8">
    <div class="flex justify-center">
      <span class="!font-medium text-lg">لیست موضوعات تیکت ها</span>
    </div>
    <div class="mt-10 px-3 mb-3">
      <div class="mb-3 mr-2 flex justify-between fm:flex-col items-center">
        <div class="flex gap-2 items-center">
          <UserCanAction action="create" @create="create()" permission="create_ticketcategories" />
          <Button @click="getData(false, 1, true)" my_class="!bg-white !py-2 !px-2" :btnLoading="refresh"><i class="far fa-sync text-2xl fm:text-lg text-gray-700"></i></Button>
        </div>
        <div class="ml-2 fm:mt-3">
        </div>
      </div>
      <div v-if="allData.length <= 0" class="border border-b w-full my-2"></div>
      <div v-if="allData.length > 0">
        <table class="w-full border-collapse lg:table 2xl:table xl:lg:table md:table">
          <Thead :titles="['عنوان','عملیات']" />
          <tbody class="block lg:table-row-group xl:table-row-group 2xl:table-row-group md:table-row-group">
          <tr v-for="item in allData" :key="item.id" class="border border-gray-200 block lg:table-row xl:table-row 2xl:table-row md:table-row">
            <Td title="عنوان" class="fm:text-sm">{{item.title}}</Td>
            <Td title="عملیات">
              <div class="flex items-center justify-center gap-2">
                <UserCanAction action="show" @show="show(item.id)" permission="view_ticketcategories" :postId="item.id" />
                <UserCanAction action="destroy" @destroy="destroy(item.id)" permission="update_ticketcategories" :postId="item.id" />
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
    <Modal :title="isUpdate ? 'ویرایش موضوع' : 'ثبت موضوع'" :save="isUpdate ? 'ثبت تغییرات' : 'ثبت'" :permission="isUpdate ? 'update_ticketcategories' : 'create_ticketcategories'" :btnLoading="btnLoading" @callback="isUpdate ? update() : insert()" ref="openModal">
      <Input label="عنوان" placeholder="عنوان" v-model="title" id="title" />
    </Modal>
    <Meta :title="$store.state.siteName + ` | لیست موضوعات تیکت ها `"/>
    <Loading :loading="loading" />
    <ValidationError />
  </div>
</template>

<script>
export default{name: "TicketCategory"}
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
let model = ref('ticketcategory')
let search = ref('')
let title = ref('')
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
    title.value = data.title;
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
    title:title.value,
  };
}

function clearData(){
  title.value = '';
  isUpdate.value = false;
  postId.value = null;
  openModal.value.toggleModal();
}

</script>