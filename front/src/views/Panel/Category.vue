<template>
  <div class="mt-10 px-8">
    <div class="flex justify-center">
      <span class="!font-medium text-lg">لیست دسته بندی ها</span>
    </div>

    <div class="flex fm:flex-col gap-14">
      <div class="w-[60%] fm:w-full">

        <div class="mb-6" v-if="isUpdate">
          <UserCanAction action="create" @create="clearData()" permission="create_categories" />
        </div>
        <div class="mt-5">
          <Input v-model="title" label="عنوان" />
        </div>

        <div class="mt-5">
          <label class="fm:text-sm">نوع دسته<b class="text-red-500 !font-bold">*</b></label>
          <select v-model="categoryType" @change="getCategoryType($event)" class="fm:text-sm border border-gray-200 p-2 outline-none rounded-lg w-full">
            <option value="" selected>--- انتخاب کنید ---</option>
            <option value="category">دسته بندی</option>
            <option value="news">اخبار</option>
            <option value="faq">سوالات متداول</option>
          </select>
        </div>


        <div class="mt-5">
          <TreeSelect :key="treeSelectKey" @selectItem="selectItem" title="دسته" :multi="false" :categories="categories" :clickChild="false" :headCategory="true"/>
        </div>
        <div class="mt-5">
          <Input type="file" @change="getFile($event)" label="تصویر دسته" :alert="`فرمت مجار: jpg,jpeg,png,webp | حداکثر حجم: ${$store.state.imageSize}`" :key="imageKey" :required="false" />
          <div v-if="previewImage">
            <img :src="previewImage" width="100" height="100" />
          </div>
        </div>
        <div class="mt-5">
          <Input v-model="icon" label="ایکون" :required="false" placeholder="مثال: far fa-laptop" alert="ایکون را از سایت fontawesome.com انتخاب کنید " />
        </div>
        <div class="mt-5">
          <label class="fm:text-sm">وضعیت دسته<b class="text-red-500 !font-bold">*</b></label>
          <select v-model="status" class="fm:text-sm border border-gray-200 p-2 outline-none rounded-lg w-full">
            <option value="" selected>--- انتخاب کنید ---</option>
            <option value="yes">فعال</option>
            <option value="no">غیر فعال</option>
          </select>
        </div>
        <div v-if="categoryType === 'category' && categoryType !== '' && parentId != '0'">
          <div class="mt-5">
            <label class="fm:text-sm">وزن کالاهای این دسته؟ <b class="text-red-500 !font-bold">*</b></label>
            <select v-model="weightType" class="fm:text-sm border border-gray-200 p-2 outline-none rounded-lg w-full">
              <option value="" selected>--- انتخاب کنید ---</option>
              <option value="style">سبک</option>
              <option value="heavy">سنگین</option>
              <option value="super_heavy">فوق سنگین</option>
            </select>
          </div>

          <div class="mt-5">
            <Input text="number" :required="false" v-model="commission" label="کمیسیون" placeholder="بین 0 الی 100" />
          </div>

          <div class="mt-5">
            <label class="fm:text-sm">ویژگی</label>
            <Multiselect
                v-model="properties"
                :options="allProperties"
                :rtl="true"
                mode="tags"
                :close-on-select="false"
                placeholder="--- انتخاب کنید ---"
                :searchable="true"
                :allow-absent="true"
                :resolve-on-load="false"
                :object="true"
            />
          </div>
        </div>

        <div class="mt-5">
          <Button v-if="$store.getters.userCan(isUpdate ? 'update_categories' : 'create_categories')" :text="isUpdate ? 'ذخیره تغییرات' : 'ذخیره'" my_class="!w-full" :btnLoading="btnLoading" @callback="isUpdate ? update() : create()" />
          <Button v-else :text="isUpdate ? 'ذخیره تغییرات' : 'ذخیره'" my_class="!w-full !bg-black" :btnLoading="btnLoading"/>
        </div>
      </div>
      <div class="w-[50%] fm:w-full">
        <div class="flex justify-end flex-col">
          <ul class="tree" v-for="item in allCategory" :key="item.id">
            <li class="tree-item " v-if="item.parent_id == 0">
              <a  href="javascript:void(0)" :class="['trigger flex items-center gap-1']">
                <span class="mt-[.4rem] fm:mt-[.2rem] fm:text-sm"><i class="fas fa-folder fm:text-sm text-yellow-500"></i></span>
                <span :class="['fm:text-sm text-blue-400 !font-medium',item.status === 'yes' ? 'text-green-500' : 'text-red-500']">{{ item.title}}</span>
                <UserCanAction action="show" @show="show(item.id)" permission="view_categories" :postId="item.id" my_class="!border-none !px-0 !py-0" />
                <UserCanAction action="destroy" @destroy="destroy(item.id)"  permission="delete_categories" :postId="item.id" my_class="!border-none !px-0 !py-0" />
              </a>
              <Tree @show="show" @destroy="destroy" :categories="item.children"></Tree>
            </li>
          </ul>
        </div>

      </div>
    </div>
    <Meta :title="$store.state.siteName + ` | لیست دسته بندی ها `"/>
    <Loading :loading="loading" />
    <ValidationError />
  </div>
</template>

<script>
export default{name: "Category"}
</script>

<script setup>
import {ref, onMounted} from 'vue';
import Meta from "@/components/Meta.vue";
import Tree from "@/components/Tree.vue";
import Input from "@/components/Input.vue";
import Loading from "@/components/Loading.vue";
import Button from "@/components/Button.vue";
import UserCanAction from "@/components/UserCanAction.vue";
import TreeSelect from "@/components/TreeSelect.vue";
import ValidationError from "@/components/ValidationError.vue";
import axios from "@/plugins/axios";
import store from "@/store";
import Toast from "@/plugins/toast";
import Multiselect from '@vueform/multiselect'

let btnLoading = ref(false)
let loading = ref(false)
let parentId = ref('')
let commission = ref('')
let title = ref('')
let image = ref('')
let previewImage = ref(null)
let icon = ref('')
let status = ref('')
let weightType = ref('')
let categoryType = ref('')

let allProperties = ref([])
let properties = ref([])
let categories = ref([])
let allCategory = ref([])
let isUpdate = ref(false)
let categoryId = ref(0)
let imageKey = ref(0)
let treeSelectKey = ref(0)

onMounted(async()=>{
  await getData();
});

function selectItem(data){
  parentId.value = store.state.selectIds[0];
}

async function getData(_loading = true){
  loading.value = _loading;
  await axios.get(`${store.state.api}category`).then(resp=>{
    let data = resp.data.data;
    allCategory.value = data.categories;

    allProperties.value = data.properties.map((item)=>{
      return {value:item.id, label:item.property}
    });
  }).catch(err=>{
    store.commit('handleError',err)
  })
  loading.value = false;
}

async function getCategoryType(event, categoryType=null, _loading=true){
  store.state.selectIds = [];
  loading.value = _loading;
  let type = event ? event.target.value : categoryType;
  await axios.get(`${store.state.api}get-categories?type=${type}`).then(resp=>{
    categories.value = resp.data.data;
    treeSelectKey.value++
  }).catch(err=>{
    store.commit('handleError',err)
  })
  loading.value = false;
}

async function create(){
  btnLoading.value = true;
  await axios.post(`${store.state.api}category`,formData()).then(async resp=>{
    await clearData();
    Toast.success();
    await getData(false)
  }).catch(err=>{
    store.commit('handleError',err)
  })
  btnLoading.value = false;
}

async function show(postId){
  clearData()
  categoryId.value = postId;
  isUpdate.value = loading.value = true;
  await axios.get(`${store.state.api}category/${categoryId.value}`).then(async resp=>{
    let data = resp.data.data;
    parentId.value = data.parent_id
    commission.value = data.commission
    title.value = data.title
    previewImage.value = data.image ? store.state.url+data.image : null;
    status.value = data.status
    weightType.value = data.weight_type !== null ? data.weight_type : ''
    icon.value = data.icon ?? ''
    categoryType.value = data.type;
    await getCategoryType(null, data.type, false)
    properties.value = data.category_properties.map((item)=>{
      return {value:item.property_id, label:item.property.property}
    });
    store.state.selectIds = [parseInt(parentId.value)];
    treeSelectKey.value ++;
  }).catch(err=>{
    store.commit('handleError',err)
  })
  loading.value = false;
}

async function update(){
  isUpdate.value = btnLoading.value =  true;
  await axios.post(`${store.state.api}category/${categoryId.value}`,formData(true)).then(resp=>{
    clearData();
    Toast.success("تغییرات با موفقیت ثبت شد");
    getData(false)
  }).catch(err=>{
    store.commit('handleError',err)
  })
  btnLoading.value =  false;
}

async function destroy(postId){
  loading.value = false;
  await store.dispatch('deleteRecord',[`category/${postId}`,'چنانچه دسته فغلی دارای زیر دسته باشد تمامی زیر دسته ها حذف میشوند'])
  await getData(false)
  loading.value = false

}

function formData(isUpdate){
  let frm = new FormData();
  frm.append('title',title.value);
  frm.append('icon',icon.value);
  frm.append('commission',commission.value);
  frm.append('parent_id',parentId.value);
  frm.append('category_type',categoryType.value);
  frm.append('image',image.value);
  frm.append('status',status.value);
  frm.append('weight_type',weightType.value);
  if(isUpdate){
    frm.append('_method','PATCH');
  }
  let fixProperties = properties.value.map(item=>item.value);
  frm.append('properties',JSON.stringify(fixProperties))
  return frm
}

async function clearData(){
  parentId.value = icon.value = title.value = image.value = status.value = commission.value = categoryType.value = weightType.value = '';
  isUpdate.value = false;
  isUpdate.value = false;
  previewImage.value = null;
  imageKey.value = categoryId.value = treeSelectKey.value = 0;
  properties.value = []
  store.state.selectIds = [];

}



function getFile(event){
  image.value = event.target.files[0];
  previewImage.value = URL.createObjectURL(image.value);
  imageKey.value++;
}

</script>

<style lang="scss">

.tree{
  direction: ltr !important;
}

.tree-parent{
  margin-top: 0.5rem;
  display: none;
}
.tree-parent.open{
  display: block;
}
.tree-item{
  position: relative;
  margin-left: 1.7rem;
  line-height: 2rem;
}
.tree-item:last-child{
  border: none;
}
.tree-item:before{
  content:'';
  display: block;
  border-top: 1px solid #5a5a5a !important;
  /*border-left: 1px solid #5a5a5a !important;*/
  height: 100%;
  width: 1rem;
  position: absolute;
  bottom: -1.1rem;
  left: -0.9rem;
  z-index: 1;
}
.tree-item:last-child:before{
  border-left: none;
}
.tree-item:first-child:after{
  content:'';
  display: block;
  border-left: 1px solid #5a5a5a;
  height: 100%;
  width: 1rem;
  position: absolute;
  bottom: 0.9rem;
  left: -0.9rem;
  z-index: 1;
}



</style>
<style src="@vueform/multiselect/themes/default.css"></style>