<template>
  <div class="mt-10 px-8">
    <div class="flex justify-center">
      <span class="!font-medium text-lg">لیست اسلایدر ها</span>
    </div>
    <div class="mt-10 px-3 mb-3">
      <div class="mb-3 mr-2 flex justify-between fm:flex-col items-center">
        <div class="flex gap-2 items-center">
          <UserCanAction action="create" @create="create()" permission="create_sliders" />
          <Button @click="getData(false, 1)" my_class="!bg-white !py-2 !px-2" :btnLoading="refresh"><i class="far fa-sync text-2xl fm:text-lg text-gray-700"></i></Button>
        </div>
        <div class="ml-2 fm:mt-3">
        </div>
      </div>
      <div v-if="allData.length <= 0" class="border border-b w-full my-2"></div>
      <div v-if="allData.length > 0">
        <table class="w-full border-collapse lg:table 2xl:table xl:lg:table md:table">
          <Thead :titles="[
              'تصویر',
              'لینک',
              'عملیات'
              ]" />
          <tbody class="block lg:table-row-group xl:table-row-group 2xl:table-row-group md:table-row-group">
          <tr v-for="item in allData" :key="item.id" class="border border-gray-200 block lg:table-row xl:table-row 2xl:table-row md:table-row">
            <Td title="تصویر" class="fm:text-sm fd:w-[100px] fm:w-[100px]"><img :src="store.state.url + item.image" class="fm:mr-[5rem]"/></Td>
            <Td title="لینک">{{item.link}}</Td>
            <Td title="عملیات">
              <div class="flex items-center justify-center gap-2">
                <UserCanAction action="show" @show="show(item.id)" permission="view_sliders" :postId="item.id" />
                <UserCanAction action="destroy" @destroy="destroy(item.id)" permission="delete_sliders" :postId="item.id" />
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
    <Modal :title="isUpdate ? 'ویرایش اسلایدر' : 'ثبت اسلایدر'" :save="isUpdate ? 'ثبت تغییرات' : 'ثبت'" :permission="isUpdate ? 'update_sliders' : 'create_sliders'" :btnLoading="btnLoading" @callback="isUpdate ? update() : insert()" ref="openModal">
        <div class="mt-5">
          <Input :required="!isUpdate" label="تصویر" type="file" :alert="`فرمت مجار: JPG|JPEG|PNG|webp حداکثر حجم:${$store.state.imageSize} | انداره: 1275x408px` " @change="getFile($event)" id="image" :key="imageKey" />
          <div v-if="previewImage">
            <img :src="previewImage" width="100" height="100" />
          </div>
        </div>
      <div class="mt-5">
        <Input label="لینک" v-model="link" id="link"/>
      </div>
    </Modal>
    <Meta :title="$store.state.siteName + ` | لیست اسلایدر ها `"/>
    <Loading :loading="loading" />
    <ValidationError />
  </div>
</template>

<script>
export default{name: "Slider"}
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
let model = ref('slider')
let image = ref('')
let link = ref('')
let imageKey = ref(0)
let previewImage = ref(null)
let allData = ref([])
let openModal = ref(null)

onMounted(async()=>{
  await getData();
});
defineExpose({getData});


async function getData(_loading = true,page=1){
  refresh.value = true;
  loading.value = _loading;
  await axios.get(`${store.state.api}${model.value}?page=${page}`).then(resp=>{
    allData.value = resp.data.data.data;
    store.commit('paginate',resp.data.data);
  }).catch(err=>{
    store.commit('handleError',err)
  })
  loading.value = refresh.value = false;
}

async function create(){
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
    previewImage.value = store.state.url + data.image;
    link.value = data.link;
  }).catch(err=>{
    store.commit('handleError',err)
  })
  loading.value = false;
}

async function update(){
  isUpdate.value = btnLoading.value =  true;
  await axios.post(`${store.state.api}${model.value}/${postId.value}`,formData(true)).then(resp=>{
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


function formData(isUpdate=false){
  let frm = new FormData();
  frm.append('image',image.value);
  frm.append('link',link.value);
  if(isUpdate){
    frm.append('_method','PATCH');
  }
  return frm;
}

function clearData(){
  image.value = '';
  link.value = '';
  imageKey.value++;
  previewImage.value = null;
  openModal.value.toggleModal();
}

function getFile(event){
  image.value = event.target.files[0];
  previewImage.value = URL.createObjectURL(image.value);
  imageKey.value++;
}

</script>