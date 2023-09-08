<template>
  <div class="mt-10 px-8">
    <div class="flex justify-center">
      <span class="!font-medium text-lg">لیست برند ها</span>
    </div>
    <div class="mt-10 px-3 mb-3">
      <div class="mb-3 mr-2 flex justify-between fm:flex-col items-center">
        <div class="flex gap-2 items-center">
          <div v-if="isAdmin">
            <UserCanAction action="create" @create="create()" permission="create_brands" />
          </div>
          <div v-else>
            <Button text="اضافه کردن" @click="create()"></Button>
          </div>
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
              'کاربر',
              'نام پارسی',
              'نام انگلیسی',
              'نوع برند',
              'وضعیت',
              'تاریخ ثبت',
              'مشاهده',
              'عملیات'
              ]" :except="!isAdmin ? ['کاربر'] : []" />
          <tbody class="block lg:table-row-group xl:table-row-group 2xl:table-row-group md:table-row-group">
          <tr v-for="item in allData" :key="item.id" class="border border-gray-200 block lg:table-row xl:table-row 2xl:table-row md:table-row">
            <Td v-if="isAdmin" title="کاربر" class="fm:text-sm">{{item.user}}</Td>
            <Td title="نام پارسی" class="fm:text-sm">{{item.ir_name}}</Td>
            <Td title="نام انگلیسی" class="fm:text-sm">{{item.en_name}}</Td>
            <Td title="نوع برند" class="fm:text-sm">{{item.brand_type}}</Td>
            <Td title="وضعیت" class="fm:text-sm">{{item.status}}</Td>
            <Td title="تاریخ ثبت" class="fm:text-sm">{{item.created_at}}</Td>
            <Td title="مشاهده" class="fm:text-sm">
              <Button @click="showDetail(item.id)" my_class="!bg-white border border-blue-500 !py-2 !px-2 fm:py-1 fm:px-1"><i class="far fa-eye text-green-500 fm:text-sm"></i></Button>
            </Td>
            <Td title="عملیات">
                <div v-if="isAdmin" class="flex items-center gap-2 w-full">
                  <UserCanAction action="show" @show="show(item.id)" permission="view_brands" :postId="item.id" />
                  <UserCanAction action="destroy" @destroy="destroy(item.id)" permission="delete_brands" :postId="item.id" />
                </div>
                <div v-else>
                  <Button :my_class="'!bg-white border border-blue-500 !py-2 !px-2 fm:py-1 fm:px-1'" @click="show(item.id)"><i class="far fm:text-sm fa-edit text-blue-500"></i></Button>
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
    <Modal :title="isUpdate ? 'ویرایش برند' : 'ثبت برند'" :save="isUpdate ? 'ثبت تغییرات' : 'ثبت'" :permission="isAdmin ? (isUpdate ? 'update_brands' : 'create_brands') : ''" :btnLoading="btnLoading" @callback="isUpdate ? update() : insert()" ref="openModal">
      <div class="mt-5 p-2 rounded bg-red-100">
        <ul class="flex flex-col gap-6">
          <li>
            <p class="break-words text-red-500 text-sm">
              - برند های ایرانی باید گواهی ثبت علامت تجاری داشته باشند و تصویر ان را باید همراه با درخواست در فرم ارسال کتید
            </p>
          </li>
          <li>
            <p class="break-words text-red-500 text-sm">
              - برند های ایرانی که دارای گواهی ثبت علامت تجاری نیستند ثبت نمیشوند
            </p>
          </li>
          <li>
            <p class="break-words text-red-500 text-sm">
              - برای ثبت برند اظهارنامه قابل قبول نیست و حتما باید گواهی ثبت برند را ارسال کنید
            </p>
          </li>
        </ul>
      </div>
      <div class="mt-5" v-if="isAdmin">
        <label class="fm:text-sm">برای کاربر<b class="text-red-500 !font-bold">*</b></label>
        <select v-model="user_id" class="fm:text-sm border border-gray-200 p-2 outline-none rounded-lg w-full">
          <option value="" selected>--- انتخاب کنید ---</option>
          <option v-for="user in allUsers" :key="user.id" :value="user.id">{{user.name}}</option>
        </select>
      </div>
      <div class="mt-5">
       <Input label="نام پارسی" v-model="ir_name" id="ir_name" />
     </div>
      <div class="mt-5">
        <Input label="نام انگلیسی" v-model="en_name" id="en_name" />
      </div>
      <div class="mt-5">
        <Textarea v-model="description" label="شرح" placeholder="توضیحات باید بین 200 تا 1000 کاراکتر درباره تاریخچه و محصولات برند باشد" id="description" :rows="3" :maxlength="1000" :alert="description.length+'/1000'"/>
      </div>
      <div class="mt-5">
        <label class="fm:text-sm">نوع برند<b class="text-red-500 !font-bold">*</b></label>
        <select v-model="brand_type" class="fm:text-sm border border-gray-200 p-2 outline-none rounded-lg w-full">
          <option value="" selected>--- انتخاب کنید ---</option>
          <option value="ir">ایرانی</option>
          <option value="en">خارجی</option>
        </select>
      </div>

      <div class="mt-5">
        <div v-if="brand_type === 'ir'">
          <span class="text-sm text-red-500">یکی از دومورد (برگه ثبت برند ,لینک قوه قضاییه) یا هردو مورد را وارد کنید</span>
        </div>
       <div class="flex gap-5">
         <div class="flex flex-col fd:w-[50%]" v-if="brand_type === 'ir'">
           <Input :key="registrationFormKey" type="file" label="برگه ثبت برند" :alert="`فرمت مجار: jpg,jpeg,png,webp | حداکثر حجم: ${$store.state.imageSize}`" @change="getFile($event)" id="registration_form" />
           <div v-if="previewRegistrationForm">
             <img :src="previewRegistrationForm" width="100" height="100" />
           </div>
         </div>
         <Input :label="brand_type === 'ir' ? 'لینک قوه قضاییه' : 'لینک سایت معتبر خارجی'" alert="لینک به صورت کامل وارد شود" v-model="link" id="link"  :class="[brand_type === 'ir' ? 'fd:w-[50%]' : 'w-full']"/>
       </div>
      </div>
      <div class="mt-5">
        <Input :key="logoKey" type="file" label="لوگو" id="logo" @change="getFile($event,'logo')" :alert="`فرمت مجار: jpg,jpeg,png,webp | حداکثر حجم: ${$store.state.imageSize}`"/>
        <div v-if="previewLogo">
          <img :src="previewLogo" width="100" height="100" />
        </div>
      </div>

      <div class="mt-5" v-if="isAdmin">
        <label class="fm:text-sm">وضعیت<b class="text-red-500 !font-bold">*</b></label>
        <select v-model="status" class="fm:text-sm border border-gray-200 p-2 outline-none rounded-lg w-full">
          <option value="" selected>--- انتخاب کنید ---</option>
          <option value="yes">تایید</option>
          <option value="no">رد</option>
          <option value="pending">در انتظار بررسی</option>
        </select>
      </div>
      <div class="mt-5" v-if="status === 'no'">
        <Textarea v-model="reason_rejection" label="دلیل رد" id="reason_rejection" :rows="3" :maxlength="1000" :alert="reason_rejection.length+'/1000'"/>
      </div>
    </Modal>

    <Modal title="جزئیات برند" ref="detailModal">
      <ul>
        <li class="flex gap-2 mb-2">
          <div class="w-[20%] rounded bg-gray-200 p-3"><span>کاربر</span></div>
          <div class="w-[80%] rounded bg-gray-200 p-3"><p>{{detailBrand.user}}</p></div>
        </li>
        <li class="flex gap-2 mb-2">
          <div class="w-[20%] rounded bg-gray-200 p-3"><span>نام پارسی</span></div>
          <div class="w-[80%] rounded bg-gray-200 p-3"><p>{{detailBrand.ir_name}}</p></div>
        </li>
        <li class="flex gap-2 mb-2">
          <div class="w-[20%] rounded bg-gray-200 p-3"><span>نام انگلیسی</span></div>
          <div class="w-[80%] rounded bg-gray-200 p-3"><p>{{detailBrand.en_name}}</p></div>
        </li>
        <li class="flex gap-2 mb-2">
          <div class="w-[20%] rounded bg-gray-200 p-3"><span>نوع برند</span></div>
          <div class="w-[80%] rounded bg-gray-200 p-3"><p>{{detailBrand.brand_type}}</p></div>
        </li>
        <li class="flex gap-2 mb-2">
          <div class="w-[20%] rounded bg-gray-200 p-3"><span>وضعیت</span></div>
          <div class="w-[80%] rounded bg-gray-200 p-3"><p>{{detailBrand.status}}</p></div>
        </li>
        <li class="flex gap-2 mb-2">
          <div class="w-[20%] rounded bg-gray-200 p-3"><span>برگه ثبت برند</span></div>
          <div class="w-[80%] rounded bg-gray-200 p-3" >
            <span v-if="detailBrand.registration_form">
              <a :href="$store.state.url + detailBrand.registration_form" target="_blank">
                <img :src="$store.state.url + detailBrand.registration_form" class="w-[100px] h-[100px]" />
              </a>
            </span>
            <span v-else>---</span>
          </div>
        </li>
        <li class="flex gap-2 mb-2">
          <div class="w-[20%] rounded bg-gray-200 p-3"><span>لوگو</span></div>
          <div class="w-[80%] rounded bg-gray-200 p-3">
            <a :href="$store.state.url + detailBrand.logo" target="_blank">
              <img :src="$store.state.url + detailBrand.logo" class="w-[100px] h-[100px]" />
            </a>
          </div>
        </li>
        <li class="flex gap-2 mb-2">
          <div class="w-[20%] rounded bg-gray-200 p-3"><span>لینک</span></div>
          <div class="w-[80%] rounded bg-gray-200 p-3"><a :href="detailBrand.link" target="_blank">مشاهده</a></div>
        </li>
        <li class="flex gap-2 mb-2">
          <div class="w-[20%] rounded bg-gray-200 p-3"><span>دلیل رد</span></div>
          <div class="w-[80%] rounded bg-gray-200 p-3"><p>{{detailBrand.reason_rejection}}</p></div>
        </li>
        <li class="flex gap-2 mb-2">
          <div class="w-[20%] rounded bg-gray-200 p-3"><span>شرح</span></div>
          <div class="w-[80%] rounded bg-gray-200 p-3"><p>{{detailBrand.description}}</p></div>
        </li>
        <li class="flex gap-2 mb-2">
          <div class="w-[20%] rounded bg-gray-200 p-3"><span>تاریخ ثبت</span></div>
          <div class="w-[80%] rounded bg-gray-200 p-3"><p>{{detailBrand.created_at}}</p></div>
        </li>
      </ul>
    </Modal>
    <Meta :title="$store.state.siteName + ` | لیست برند ها `"/>
    <Loading :loading="loading" />
    <ValidationError />
  </div>
</template>

<script>
export default{name: "CommentSubject"}
</script>

<script setup>
import {ref, onMounted, defineExpose} from 'vue';
import Meta from "@/components/Meta.vue";
import Td from "@/components/Td.vue";
import Thead from "@/components/Thead.vue";
import Input from "@/components/Input.vue";
import Modal from "@/components/Modal.vue";
import Loading from "@/components/Loading.vue";
import Button from "@/components/Button.vue";
import ValidationError from "@/components/ValidationError.vue";
import Paginate from "@/components/Paginate.vue";
import UserCanAction from "@/components/UserCanAction.vue";
import Textarea from "@/components/Textarea.vue";
import axios from "@/plugins/axios";
import store from "@/store";
import Toast from "@/plugins/toast";


let isAdmin = ref(store.state.user.access === 'admin')
let refresh = ref(false)
let btnLoading = ref(false)
let loading = ref(false)
let isUpdate = ref(false)
let postId = ref(null)
let model = ref('brand');
let search = ref('')
let user_id = ref('')
let ir_name = ref('')
let en_name = ref('')
let link = ref('')
let registration_form = ref('')
let logo = ref('')
let brand_type = ref('ir')
let status = ref('')
let description = ref('')
let reason_rejection = ref('')
let allData = ref([])
let allUsers = ref([])
let detailBrand = ref([])
let openModal = ref(null)
let detailModal = ref(null)
let previewRegistrationForm = ref(null)
let previewLogo = ref(null)
let registrationFormKey = ref(0);
let logoKey = ref(0);
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
    allUsers.value = data.users;
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

async function showDetail(_postId){
  detailModal.value.toggleModal();
   loading.value = true;
  await axios.get(`${store.state.api}get-brand/${_postId}`).then(async resp=>{
    detailBrand.value = resp.data.data;
  }).catch(err=>{
    store.commit('handleError',err)
  })
  loading.value = false;
}

async function show(_postId){
  clearData();
  postId.value = _postId;
  isUpdate.value = loading.value = true;
  await axios.get(`${store.state.api}${model.value}/${postId.value}`).then(async resp=>{
    let data = resp.data.data;
    user_id.value = data.user.id
    ir_name.value = data.name
    en_name.value = data.en_name
    link.value = data.link
    previewRegistrationForm.value = data.registration_form !== null ? store.state.url + data.registration_form : null;
    previewLogo.value = store.state.url + data.logo
    brand_type.value = data.brand_type
    status.value = data.status
    description.value = data.description
    reason_rejection.value = data.reason_rejection ?? '';
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
  frm.append('user_id',user_id.value);
  frm.append('ir_name',ir_name.value);
  frm.append('en_name',en_name.value);
  frm.append('link',link.value);
  frm.append('registration_form',registration_form.value);
  frm.append('logo',logo.value);
  frm.append('brand_type',brand_type.value);
  frm.append('status',status.value);
  frm.append('description',description.value);
  frm.append('reason_rejection',reason_rejection.value);
  if(isUpdate){
    frm.append('_method','PATCH');
  }
  return frm;
}

function clearData(){
  user_id.value = ''
  ir_name.value = ''
  en_name.value = ''
  link.value = ''
  registration_form.value = ''
  logo.value = ''
  brand_type.value = 'ir'
  status.value = ''
  description.value = ''
  reason_rejection.value = ''
  previewRegistrationForm.value = previewLogo.value = null
  registrationFormKey.value = logoKey.value = 0
  isUpdate.value = false;
  openModal.value.toggleModal();
}

function getFile(event, type){
  let image = event.target.files[0]
  if(type === 'logo'){
    logo.value = image;
    previewLogo.value = URL.createObjectURL(image);
    logoKey.value ++;
  }else{
    registration_form.value = image;
    previewRegistrationForm.value = URL.createObjectURL(image);
    registrationFormKey.value ++;
  }
}


</script>