<template>
  <div class="mt-10 px-8">
    <div class="flex justify-center">
      <span class="!font-medium text-lg">لیست کالاهای شگفت انگیز</span>
    </div>
    <div class="mt-10 px-3 mb-3">
      <div :class="['mb-3 mr-2 flex justify-between fm:flex-col items-center']">
        <div class="flex gap-2 items-center" >
          <UserCanAction v-if="isAdmin" action="create" @create="create()" permission="create_amazing_products" />
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
              'نام کالا',
              'درصد شگفت انگیز',
              'تصویر',
              'وضعیت',
              'تاریخ ثبت',
              'عملیات'
              ]" />
          <tbody class="block lg:table-row-group xl:table-row-group 2xl:table-row-group md:table-row-group">
          <tr v-for="item in allData" :key="item.id" class="border border-gray-200 block lg:table-row xl:table-row 2xl:table-row md:table-row">
            <Td title="نام کالا" class="fm:text-sm">
              <div class="flex flex-col">
                <span>{{item.ir_name}}</span>
                <span class="text-xs text-gray-400">شناسه: {{item.sku}}</span>
              </div>
            </Td>
            <Td title="درصد شگفت انگیز" class="fm:text-sm w-[80px] h-[80px]">{{item.amazing_offer_percent}}</Td>
            <Td title="تصویر" class="fm:text-sm w-[80px] h-[80px]"><img :src="$store.state.url + item.image" class="fm:mr-[5rem]" /></Td>
            <Td title="وضعیت" class="fm:text-sm">{{item.ir_status}}</Td>
            <Td title="تاریخ ثبت" class="fm:text-sm">{{item.updated_at}}</Td>
            <Td title="عملیات">
              <div class="flex items-center justify-center gap-2" v-if="isAdmin">
                <UserCanAction action="show" @show="show(item.id,item.status)" permission="update_amazing_products" :postId="item.id" />
                <UserCanAction  action="destroy" @destroy="destroy(item.id)" permission="delete_amazing_products" :postId="item.id" />
              </div>
              <div class="flex items-center justify-center gap-2" v-else>
                <Button my_class="!bg-white border border-red-500 !py-2 !px-2 fm:py-1 fm:px-1" @click="destroy(item.id)"><i class="far fm:text-sm fa-trash text-red-500"></i></Button>
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
    <Modal title="ثبت کالای شگفت انگیز" save="ثبت" :permission="'create_amazing_products'" :btnLoading="btnLoading" @callback="insert()" ref="openModal">
      <div class="mt-2">
        <label class="fm:text-sm">دسته</label>
        <Multiselect
            v-model="category"
            :options="allCategories"
            :rtl="true"
            :close-on-select="true"
            placeholder="--- انتخاب کنید ---"
            :create-option="false"
            :searchable="true"
            :allow-absent="false"
            :resolve-on-load="false"
            :object="true"
            @select="getProducts"
            @deselect="deSelectProduct"
        />
      </div>
      <div>
        <label class="fm:text-sm">کالاها</label>
        <Multiselect
            v-model="products"
            :options="allProducts"
            mode="tags"
            :rtl="true"
            :close-on-select="true"
            placeholder="--- انتخاب کنید ---"
            :create-option="false"
            :searchable="true"
            :allow-absent="false"
            :resolve-on-load="false"
            :object="true"
        />
        <div class="mt-5">
          <div class="mt-4 flex flex-wrap gap-2 items-center">
            <div class="flex items-center gap-1 bg-gray-200 rounded-lg px-2 py-1" v-for="product in products" :key="product.value">
              <routerLink target="_blank" :to="{name:'ProductDetail',params:{slug:product.label.replace(' ','-')}}" class="text-xs">{{product.label}}</routerLink>
            </div>
          </div>
        </div>
        <div class="mt-5">
          <Input label="درصد تخفیف" v-model="percent" id="percent" placeholder="بین 1 تا 100"/>
        </div>
      </div>
    </Modal>

    <Modal title="ویرایش وضعیت کالای شگفت انگیز" save="ثبت تغییرات" :permission="'update_amazing_products'" :btnLoading="btnLoading" @callback="update()" ref="openUpdateModal">
      <div class="mb-5 w-full">
        <label class="fm:text-sm" for="status">وضعیت<b class="text-red-500 !font-bold">*</b></label>
        <select v-model="status" id="status" class="fm:text-sm border border-gray-200 p-2 outline-none rounded-lg w-full">
          <option value="" selected disabled>--- انتخاب کنید ---</option>
          <option value="yes">تایید</option>
          <option value="no">رد</option>
          <option value="pending">در حال بررسی</option>
        </select>
      </div>
    </Modal>
    <Meta :title="$store.state.siteName + ` | لیست کالاهای شگفت انگیز `"/>
    <Loading :loading="loading" />
    <ValidationError />
  </div>
</template>

<script>
export default{name: "AmazingProduct"}
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
import Multiselect from '@vueform/multiselect'


let isAdmin = ref(store.state.user.access === 'admin')
let refresh = ref(false)
let btnLoading = ref(false)
let loading = ref(false)
let model = ref('amazing_product')
let search = ref('')
let category = ref({})
let allCategories = ref([])
let products = ref([])
let allProducts = ref([])
let allData = ref([])
let openModal = ref(null)
let openUpdateModal = ref(null)
let postId = ref(0);
let status = ref('');
let percent = ref('');

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
    allCategories.value = data.categories.map(item=>{
      return {value:item.id, label:item.title}
    });
    store.commit('paginate',data.meta);
  }).catch(err=>{
    store.commit('handleError',err)
  })
  loading.value = refresh.value = false;
}

async function getProducts(data){
  loading.value = true;
  products.value = [];
  await axios.get(`${store.state.api}${model.value}/get-product/${data.value}`).then(resp=>{
    allProducts.value = resp.data.data.map(item=>{
      return {value:item.id, label:item.ir_name}
    })
  })
  loading.value = false;
}

function deSelectProduct(data){
  products.value = products.value.filter(item=>item.value !== data.value);
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


async function show(_postId, _status){
  postId.value = _postId;
  status.value = _status;
  openUpdateModal.value.toggleModal();
}

async function update(){
  btnLoading.value =  true;
  await axios.patch(`${store.state.api}${model.value}/${postId.value}`,{status:status.value}).then(resp=>{
    Toast.success("تغییرات با موفقیت تغییر یافت");
    getData(false,store.state.current)
    openUpdateModal.value.toggleModal();
  }).catch(err=>{
    store.commit('handleError',err)
  })
  btnLoading.value =  false;
}
async function destroy(postId){
  loading.value = false;
  await store.dispatch('deleteRecord',[`${model.value}/${postId}`,'دقت کنید در صورت تایید این ایتم از لیست شگفت انکیز خارج میشود'])
  await getData(false,store.state.current)
  loading.value = false

}


function formData(){
  let _products = products.value.map(item=>item.value);
  return {
    'percent':percent.value,
    'category_id':category.value['value'],
    'product_ids':JSON.stringify(_products),
  };
}

function clearData(){
  category.value = {}
  products.value = []
  percent.value = ''
  allProducts.value = []
  openModal.value.toggleModal();
}

</script>
<style src="@vueform/multiselect/themes/default.css"></style>