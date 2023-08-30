<template>
  <div class="mt-10 px-8">
    <div class="flex justify-center">
      <span class="!font-medium text-lg">لیست اخبار</span>
    </div>
    <div class="mt-10 px-3 mb-3">
      <div class="mb-3 mr-2 flex justify-between fm:flex-col items-center">
        <div class="flex gap-2 items-center">
          <UserCanAction action="create" @create="create()" permission="create_news" />
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
              'عنوان',
              'دسته',
              'نویسنده',
              'تصویر',
              'وضعیت',
              'عملیات'
          ]" />
          <tbody class="block lg:table-row-group xl:table-row-group 2xl:table-row-group md:table-row-group">
          <tr v-for="item in allData" :key="item.id" class="border border-gray-200 block lg:table-row xl:table-row 2xl:table-row md:table-row">
            <Td title="عنوان" class="fm:text-sm">{{item.title}}</Td>
            <Td title="دسته" class="fm:text-sm">{{item.category}}</Td>
            <Td title="نویسنده" class="fm:text-sm">{{item.user}}</Td>
            <Td title="تصویر" class="fm:text-sm"><img :src="$store.state.url + item.image" class="w-[70px] h-[70px]" /></Td>
            <Td title="وضعیت" class="fm:text-sm">{{item.publish}}</Td>
            <Td title="عملیات">
              <div class="flex items-center justify-center gap-2">
                <UserCanAction action="show" @show="show(item.id)" permission="view_news" :postId="item.id" />
                <UserCanAction action="destroy" @destroy="destroy(item.id)" permission="update_news" :postId="item.id" />
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
    <Modal :title="isUpdate ? 'ویرایش خبر' : 'ثبت خبر'" :save="isUpdate ? 'ثبت تغییرات' : 'ثبت'" :permission="isUpdate ? 'update_news' : 'create_news'" :btnLoading="btnLoading" @callback="isUpdate ? update() : insert()" ref="openModal">
      <div class="mb-5">
        <label class="fm:text-sm" for="publish">دسته<b class="text-red-500 !font-bold">*</b></label>
        <select v-model="categoryId" class="fm:text-sm border border-gray-200 p-2 outline-none rounded-lg w-full">
          <option value="">--- انتخاب کنید ---</option>
          <option v-for="category in categories" :key="category.id" :value="category.id">{{category.title}}</option>
        </select>
      </div>
      <div class="mb-5">
        <Input label="عنوان" placeholder="عنوان" v-model="title" id="title" />
      </div>
      <div class="mb-5">
        <Textarea label="توضیحات کوتاه" :cols="3" :rows="3" :maxlength="300" :alert="shortDescription.length + '/300'" v-model="shortDescription" />
      </div>
      <div class="mb-5">
        <Editor :key="editorKey" contentType="html" v-model="description" placeholder="توضیحات"></Editor>
      </div>
      <div class="mb-5">
        <Input type="file" label="تصویر" @change="getFile($event)" :key="imageKey" id="title" :alert="`حجم ${store.state.imageSize} | فرمت: JPG,PNG,JPEG | سایز: 826x514`"/>
        <div v-if="imagePreview !== null">
          <img :src="imagePreview" class="w-[100px] h-[100px]"/>
        </div>
      </div>
      <div class="mb-5">
        <label class="fm:text-sm" for="publish">وضعیت<b class="text-red-500 !font-bold">*</b></label>
        <select v-model="publish" class="fm:text-sm border border-gray-200 p-2 outline-none rounded-lg w-full">
          <option value="">--- انتخاب کنید ---</option>
          <option value="published">انتشار</option>
          <option value="draft">پیشنویس</option>
        </select>
      </div>
    </Modal>
    <Meta :title="$store.state.siteName + ` | لیست اخبار `"/>
    <Loading :loading="loading" />
    <ValidationError />
  </div>
</template>

<script>
export default{name: "News"}
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
import Textarea from "@/components/Textarea.vue";
import Editor from "@/components/Editor.vue";
import axios from "@/plugins/axios";
import store from "@/store";
import Toast from "@/plugins/toast";


let refresh = ref(false)
let btnLoading = ref(false)
let loading = ref(false)
let isUpdate = ref(false)
let postId = ref(null)
let model = ref('news')
let search = ref('')

let categoryId = ref('')
let title = ref('')
let shortDescription = ref('')
let description = ref('')
let publish = ref('')
let image = ref('')
let imagePreview = ref(null)
let imageKey = ref(0)

let allData = ref([])
let categories = ref([])
let openModal = ref(null)

let editorKey = ref(0)
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
    categories.value = data.categories;
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
  await axios.post(`${store.state.api}${model.value}`,frmData()).then(resp=>{
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
    categoryId.value = data.category_id;
    title.value = data.title;
    shortDescription.value = data.short_description;
    description.value = data.description;
    publish.value = data.publish;
    imagePreview.value = store.state.url + data.image;
  }).catch(err=>{
    store.commit('handleError',err)
  })
  loading.value = false;
}

async function update(){
  isUpdate.value = btnLoading.value =  true;
  await axios.post(`${store.state.api}${model.value}/${postId.value}`,frmData()).then(resp=>{
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


function frmData(){
  let frm = new FormData()
  frm.append('category_id',categoryId.value);
  frm.append('title',title.value);
  frm.append('description',description.value);
  frm.append('short_description',shortDescription.value);
  frm.append('image',image.value);
  frm.append('publish',publish.value);
  if(isUpdate.value){
    frm.append('_method','PATCH')
  }
  return frm;
}

function clearData(){
  editorKey.value++;
  title.value = '';
  description.value = '';
  shortDescription.value = '';
  publish.value = '';
  categoryId.value = '';
  imagePreview.value = null;
  imageKey.value++;
  isUpdate.value = false;
  postId.value = null;
  openModal.value.toggleModal();
}

function getFile(event){
  image.value = event.target.files[0];
  imagePreview.value = URL.createObjectURL(image.value);
  imageKey.value ++;
}


</script>