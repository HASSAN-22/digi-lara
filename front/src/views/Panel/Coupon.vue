<template>
  <div class="mt-10 px-8">
    <div class="flex justify-center">
      <span class="!font-medium text-lg">لیست تخفیف ها</span>
    </div>
    <div class="mt-10 px-3 mb-3">
      <div class="mb-3 mr-2 flex justify-between fm:flex-col items-center">
        <div class="flex gap-2 items-center">
          <UserCanAction action="create" @create="create()" permission="create_coupons" />
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
              'کد تخفیف',
              'نوغ تخفیف',
              'مقدار/درصد تخفیف',
              'تعداد مجاز استفاده',
              'شروع در',
              'پایان در',
              'عملیات'
              ]" />
          <tbody class="block lg:table-row-group xl:table-row-group 2xl:table-row-group md:table-row-group">
          <tr v-for="item in allData" :key="item.id" class="border border-gray-200 block lg:table-row xl:table-row 2xl:table-row md:table-row">
            <Td title="عنوان" class="fm:text-sm">{{item.title}}</Td>
            <Td title="کد تخفیف" class="fm:text-sm">{{item.code}}</Td>
            <Td title="نوغ تخفیف" class="fm:text-sm">{{item.type}}</Td>
            <Td title="مقدار/درصد تخفیف" class="fm:text-sm">{{item.percent}}</Td>
            <Td title="تعداد مجاز استفاده" class="fm:text-sm">{{item.count}}</Td>
            <Td title="شروع در" class="fm:text-sm">{{item.start_at}}</Td>
            <Td title="پایان در" class="fm:text-sm">{{item.expire_at}}</Td>
            <Td title="عملیات">
              <div class="flex items-center justify-center gap-2">
                <UserCanAction action="show" @show="show(item.id)" permission="view_coupons" :postId="item.id" />
                <UserCanAction action="destroy" @destroy="destroy(item.id)" permission="update_coupons" :postId="item.id" />
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
    <Modal :title="isUpdate ? 'ویرایش تخفیف' : 'ثبت تخفیف'" :save="isUpdate ? 'ثبت تغییرات' : 'ثبت'" :permission="isUpdate ? 'update_coupons' : 'create_coupons'" :btnLoading="btnLoading" @callback="isUpdate ? update() : insert()" ref="openModal">
      <div class="mb-5">
        <Input label="عنوان" placeholder="عنوان" v-model="title" id="title" />
      </div>
      <div class="mb-5">
        <Input label="کد تخفیف" placeholder="کد تخفیف" v-model="code" id="code" />
      </div>
      <div class="mb-5 w-full flex flex-col gap-4">
        <label class="fm:text-sm" for="type">نوع تخفیف<b class="text-red-500 !font-bold">*</b></label>
        <select v-model="type" id="type" class="fm:text-sm border border-gray-200 p-2 outline-none rounded-lg w-full">
          <option value="" selected disabled>--- انتخاب کنید ---</option>
          <option value="percent">درصد</option>
          <option value="fixed">ثابت</option>
        </select>
      </div>
      <div class="mb-5 w-full flex flex-col gap-4">
        <label class="fm:text-sm" for="discount_for">تخفیف برای<b class="text-red-500 !font-bold">*</b></label>
        <select v-model="discount_for" @change="getDiscountFor($event)" id="discount_for" class="fm:text-sm border border-gray-200 p-2 outline-none rounded-lg w-full">
          <option value="" selected disabled>--- انتخاب کنید ---</option>
          <option value="category">دسته بندی ها</option>
          <option value="product">کالا ها</option>
        </select>
      </div>

      <div v-if="discount_for === 'category'" class="mb-5">
        <label class="fm:text-sm">انتخاب دسته ها</label>
        <Multiselect
            v-model="categories"
            :options="allCategories"
            :rtl="true"
            mode="tags"
            :close-on-select="false"
            placeholder="--- انتخاب کنید ---"
            :create-option="false"
            :searchable="true"
            :allow-absent="false"
            :resolve-on-load="false"
            :object="true"
        />
      </div>
      <div v-else-if="discount_for === 'product'" class="mb-5">
        <label class="fm:text-sm">انتخاب کالا ها</label>
        <Multiselect
            v-model="products"
            :options="allProducts"
            :rtl="true"
            mode="tags"
            :close-on-select="false"
            placeholder="--- انتخاب کنید ---"
            :create-option="false"
            :searchable="true"
            :allow-absent="false"
            :resolve-on-load="false"
            :object="true"
        />
      </div>

      <div class="mb-5">
        <Input :label="type === 'percent' ? 'درصد تخفیف' : 'مقدار تخفیف (ریال)'" v-model="percent" id="percent" />
      </div>
      <div class="mb-5">
        <Input label="تعداد تخفیف" placeholder="تعداد تخفیف" v-model="count" id="count" alert="تعداد مجاز استفاده برای هر کاربر" />
      </div>
      <div class="mb-5">
        <label class="fm:text-sm" for="start_at">تاریخ شروع<b class="text-red-500 !font-bold">*</b></label>
        <date-picker v-model="start_at" id="start_at"></date-picker>
      </div>
      <div class="mb-5">
        <label class="fm:text-sm" for="expire_at">تاریخ انقضاء<b class="text-red-500 !font-bold">*</b></label>
        <date-picker v-model="expire_at" id="expire_at"></date-picker>
      </div>
    </Modal>
    <Meta :title="$store.state.siteName + ` | لیست تخفیف ها `"/>
    <Loading :loading="loading" />
    <ValidationError />
  </div>
</template>

<script>
export default{name: "Coupon"}
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


let refresh = ref(false)
let btnLoading = ref(false)
let loading = ref(false)
let isUpdate = ref(false)
let postId = ref(null)
let model = ref('coupon')
let search = ref('')
let title = ref('')
let code = ref('')
let type = ref('')
let percent = ref('')
let count = ref('')
let start_at = ref('')
let expire_at = ref('')
let discount_for = ref('')
let products = ref([])
let categories = ref([])
let allData = ref([])
let allCategories = ref([])
let allProducts = ref([])
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
    code.value = data.code;
    type.value = data.type;
    percent.value = data.percent;
    count.value = data.count;
    start_at.value = data.start_at;
    expire_at.value = data.expire_at;
    discount_for.value = data.coupon_products.length > 0 ? 'product' : 'category';
    await getDiscountFor('',type.value)
    products.value = data.coupon_products.map(item=>{
      return {value:item.product.id, label:item.product.ir_name}
    })
    categories.value = data.coupon_categories.map(item=>{
      return {value:item.category.id, label:item.category.title}
    })
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

async function getDiscountFor(event,type=null){
  let _type = type === null ? event.target.value : type;
  loading.value = true;
  if(_type === 'category'){
    await axios.get(`${store.state.api}coupon-get-categories`).then(resp=>{
      allCategories.value = resp.data.data.map(item=>{
        return {value:item.id, label:item.title}
      });
    }).catch(err=>{
    })
  }else{
    await axios.get(`${store.state.api}coupon-get-products`).then(resp=>{
      allProducts.value = resp.data.data.map(item=>{
        return {value:item.id, label:item.ir_name}
      });
    }).catch(err=>{})
  }
  loading.value = refresh.value = false;

}

function formData(){
  return {
    title:title.value,
    code:code.value,
    type:type.value,
    percent:percent.value,
    count:count.value,
    start_at:start_at.value,
    expire_at:expire_at.value,
    products:JSON.stringify(products.value.map(item=>item.value)),
    categories:JSON.stringify(categories.value.map(item=>item.value)),
  };
}

function clearData(){
  title.value = ''
  code.value = ''
  type.value = ''
  percent.value = ''
  count.value = ''
  start_at.value = ''
  expire_at.value = ''
  discount_for.value = ''
  products.value = []
  categories.value = []
  allCategories.value = []
  allProducts.value = []
  isUpdate.value = false;
  postId.value = null;
  openModal.value.toggleModal();
}

</script>
<style src="@vueform/multiselect/themes/default.css"></style>