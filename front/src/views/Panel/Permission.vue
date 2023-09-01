<template>
  <div class="mt-10 px-8">
    <div class="flex justify-center">
      <span class="!font-medium text-lg">لیست دسترسی ها</span>
    </div>
    <div class="mt-10 px-3 mb-3">
      <div class="mb-3 mr-2 flex justify-between fm:flex-col items-center">
        <div class="flex gap-2 items-center">
          <Button text="اضافه کردن" color="green" @click="create()"></Button>
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
              'نقش',
              'دسترسی ها',
              'عملیات'
              ]" />
          <tbody class="block lg:table-row-group xl:table-row-group 2xl:table-row-group md:table-row-group">
          <tr v-for="item in allData" :key="item.id" class="border border-gray-200 block lg:table-row xl:table-row 2xl:table-row md:table-row">
            <Td title="نقش" class="fm:text-sm">{{item.name}}</Td>
            <Td title="دسترسی ها">
              <Button @click="showPermissions(item.permissions)" my_class="!bg-white border border-green-500 !py-2 !px-2 fm:py-1 fm:px-1"><i class="far fm:text-sm fa-eye text-green-500"></i></Button>
            </Td>
            <Td title="عملیات">
              <div class="flex items-center justify-center gap-2">
                <Button @click="show(item.id)" my_class="!bg-white border border-blue-500 !py-2 !px-2 fm:py-1 fm:px-1"><i class="far fm:text-sm fa-edit text-blue-500"></i></Button>
                <Button @click="destroy(item.id)" my_class="!bg-white border border-red-500 !py-2 !px-2 fm:py-1 fm:px-1"><i class="far fm:text-sm fa-trash text-red-500"></i></Button>
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
    <Modal :title="isUpdate ? 'ویرایش دسترسی' : 'ثبت دسترسی'" :save="isUpdate ? 'ثبت تغییرات' : 'ثبت'" :btnLoading="btnLoading" @callback="isUpdate ? update() : insert()" ref="openModal" width="fd:w-[70%]">
      <div class="mt-5">
        <label class="fm:text-sm">نقش<b class="text-red-500 !font-bold">*</b></label>
        <select v-model="role" class="fm:text-sm border border-gray-200 p-2 outline-none rounded-lg w-full">
          <option value="" selected>--- انتخاب کنید ---</option>
          <option v-for="role in allRoles" :key="role.id" :value="role.id">{{role.name}}</option>
        </select>
      </div>
      <div class="mt-5">
        <label class="fm:text-sm">دسترسی ها<b class="text-red-500 !font-bold">*</b></label>
        <div class="mt-5 overflow-y-scroll scroll h-[21rem]">
          <PermissionComponent
              title="کاربران"
              :permissions="[
              {id:1,label:'لیست کاربران',perm:['view_any_users']},
              {id:2,label:'مشاهده کاربر',perm:['view_users']},
              {id:3,label:'ثبت کاربر',perm:['create_users']},
              {id:4,label:'ویرایش کاربر',perm:['update_users']},
              {id:5,label:'حذف کاربر',perm:['delete_users']},
              ]">
          </PermissionComponent>
          <PermissionComponent
              title="دسته ها"
              :permissions="[
              {id:1,label:'لیست دسته ها',perm:['view_any_categories']},
              {id:2,label:'مشاهده دسته',perm:['view_categories']},
              {id:3,label:'ثبت دسته',perm:['create_categories']},
              {id:4,label:'ویرایش دسته',perm:['update_categories']},
              {id:5,label:'حذف دسته',perm:['delete_categories']},
              ]">
          </PermissionComponent>
          <PermissionComponent
              title="ویژگی ها"
              :permissions="[
              {id:1,label:'لیست ویژگی ها',perm:['view_any_properties']},
              {id:2,label:'مشاهده ویژگی',perm:['view_properties']},
              {id:3,label:'ثبت ویژگی',perm:['create_properties']},
              {id:4,label:'ویرایش ویژگی',perm:['update_properties']},
              {id:5,label:'حذف ویژگی',perm:['delete_properties']},
              ]">
          </PermissionComponent>
          <PermissionComponent
              title="برند ها"
              :permissions="[
              {id:1,label:'لیست برند ها',perm:['view_any_brands']},
              {id:2,label:'مشاهده برند',perm:['view_brands']},
              {id:3,label:'ثبت برند',perm:['create_brands']},
              {id:4,label:'ویرایش برند',perm:['update_brands']},
              {id:5,label:'حذف برند',perm:['delete_brands']},
              ]">
          </PermissionComponent>
          <PermissionComponent
              title="گارانتی ها"
              :permissions="[
              {id:1,label:'لیست گارانتی ها',perm:['view_any_guarantees']},
              {id:2,label:'مشاهده گارانتی',perm:['view_guarantees']},
              {id:3,label:'ثبت گارانتی',perm:['create_guarantees']},
              {id:4,label:'ویرایش گارانتی',perm:['update_guarantees']},
              {id:5,label:'حذف گارانتی',perm:['delete_guarantees']},
              ]">
          </PermissionComponent>
          <PermissionComponent
              title="کالا ها"
              :permissions="[
              {id:1,label:'لیست کالا ها',perm:['view_any_products']},
              {id:2,label:'مشاهده کالا',perm:['view_products']},
              {id:3,label:'ثبت کالا',perm:['create_products']},
              {id:4,label:'ویرایش کالا',perm:['update_products']},
              {id:5,label:'حذف کالا',perm:['delete_products']},
              ]">
          </PermissionComponent>
          <PermissionComponent
              title="کالاهای شگفت انگیز"
              :permissions="[
              {id:1,label:'لیست کالاهای شگفت انگیز',perm:['view_any_amazing_products']},
              {id:2,label:'ثبت کالای شگفت انگیز',perm:['create_amazing_products']},
              {id:3,label:'حذف کالای شگفت انگیز',perm:['delete_amazing_products']},
              ]">
          </PermissionComponent>

          <PermissionComponent
              title="ویجت ها"
              :permissions="[
              {id:1,label:'لیست ویجت ها',perm:['view_any_widgets','view_widgets']},
              {id:2,label:'ثبت ویجت',perm:'create_widgets'},
              ]">
          </PermissionComponent>
          <PermissionComponent
              title="اسلایدر ها"
              :permissions="[
              {id:1,label:'لیست اسلایدر ها',perm:['view_any_sliders']},
              {id:2,label:'مشاهده اسلایدر',perm:['view_sliders']},
              {id:3,label:'ثبت اسلایدر',perm:['create_sliders']},
              {id:4,label:'ویرایش اسلایدر',perm:['update_sliders']},
              {id:5,label:'حذف اسلایدر',perm:['delete_sliders']},
              ]">
          </PermissionComponent>
          <PermissionComponent
              title="رنگ ها"
              :permissions="[
              {id:1,label:'لیست رنگ ها',perm:['view_any_colors']},
              {id:2,label:'مشاهده رنگ',perm:['view_colors']},
              {id:3,label:'ثبت رنگ',perm:['create_colors']},
              {id:4,label:'ویرایش رنگ',perm:['update_colors']},
              {id:5,label:'حذف رنگ',perm:['delete_colors']},
              ]">
          </PermissionComponent>
          <PermissionComponent
              title="اندازه ها"
              :permissions="[
              {id:1,label:'لیست اندازه ها',perm:['view_any_sizes']},
              {id:2,label:'مشاهده اندازه',perm:['view_sizes']},
              {id:3,label:'ثبت اندازه',perm:['create_sizes']},
              {id:4,label:'ویرایش اندازه',perm:['update_sizes']},
              {id:5,label:'حذف اندازه',perm:['delete_sizes']},
              ]">
          </PermissionComponent>
          <PermissionComponent
              title="تخفیف ها"
              :permissions="[
              {id:1,label:'لیست تخفیف ها',perm:['view_any_coupons']},
              {id:2,label:'مشاهده تخفیف',perm:['view_coupons']},
              {id:3,label:'ثبت تخفیف',perm:['create_coupons']},
              {id:4,label:'ویرایش تخفیف',perm:['update_coupons']},
              {id:5,label:'حذف تخفیف',perm:['delete_coupons']},
              ]">
          </PermissionComponent>
          <PermissionComponent
              title="شغل ها"
              :permissions="[
              {id:1,label:'لیست شغل ها',perm:['view_any_works']},
              {id:2,label:'مشاهده شغل',perm:['view_works']},
              {id:3,label:'ثبت شغل',perm:['create_works']},
              {id:4,label:'ویرایش شغل',perm:['update_works']},
              {id:5,label:'حذف شغل',perm:['delete_works']},
              ]">
          </PermissionComponent>
          <PermissionComponent
              title="سفارش ها"
              :permissions="[
              {id:1,label:'لیست سفارش ها',perm:['view_any_orders']},
              {id:2,label:'مشاهده سفارش',perm:['view_orders']},
              {id:3,label:'ویرایش سفارش',perm:['update_orders']},
              ]">
          </PermissionComponent>
          <PermissionComponent
              title="حمل و نقل ها"
              :permissions="[
              {id:1,label:'لیست حمل و نقل ها',perm:['view_any_transports']},
              {id:2,label:'مشاهده حمل و نقل',perm:['view_transports']},
              {id:3,label:'ثبت حمل و نقل',perm:['create_transports']},
              {id:4,label:'ویرایش حمل و نقل',perm:['update_transports']},
              {id:5,label:'حذف حمل و نقل',perm:['delete_transports']},
              ]">
          </PermissionComponent>
          <PermissionComponent
              title="فروشنده شوید"
              :permissions="[
              {id:1,label:'لیست فروشنده شوید',perm:['view_any_becomesellers']},
              {id:2,label:'مشاهده فروشنده شوید',perm:['view_becomesellers']},
              {id:3,label:'ثبت فروشنده شوید',perm:['create_becomesellers']},
              {id:4,label:'ویرایش فروشنده شوید',perm:['update_becomesellers']},
              ]">
          </PermissionComponent>
          <PermissionComponent
              title="سوالات متداول"
              :permissions="[
              {id:1,label:'لیست سوالات متداول',perm:['view_any_faqs']},
              {id:2,label:'مشاهده سوالات متداول',perm:['view_faqs']},
              {id:3,label:'ثبت سوالات متداول',perm:['create_faqs']},
              {id:4,label:'ویرایش سوالات متداول',perm:['update_faqs']},
              {id:5,label:'حذف سوالات متداول',perm:['delete_faqs']},
              ]">
          </PermissionComponent>
          <PermissionComponent
              title="اخبار"
              :permissions="[
              {id:1,label:'لیست اخبار',perm:['view_any_news']},
              {id:2,label:'مشاهده خبر',perm:['view_news']},
              {id:3,label:'ثبت خبر',perm:['create_news']},
              {id:4,label:'ویرایش خبر',perm:['update_news']},
              {id:5,label:'حذف خبر',perm:['delete_news']},
              ]">
          </PermissionComponent>
          <PermissionComponent
              title="برگه ها"
              :permissions="[
              {id:1,label:'لیست برگه ها',perm:['view_any_pages']},
              {id:2,label:'مشاهده برگه',perm:['view_pages']},
              {id:3,label:'ثبت برگه',perm:['create_pages']},
              {id:4,label:'ویرایش برگه',perm:['update_pages']},
              {id:5,label:'حذف برگه',perm:['delete_pages']},
              ]">
          </PermissionComponent>
          <PermissionComponent
              title="موضوعات تیکت ها"
              :permissions="[
              {id:1,label:'لیست موضوعات تیکت ها',perm:['view_any_ticketcategories']},
              {id:2,label:'مشاهده موضوعات تیکت',perm:['view_ticketcategories']},
              {id:3,label:'ثبت موضوعات تیکت',perm:['create_ticketcategories']},
              {id:4,label:'ویرایش موضوعات تیکت',perm:['update_ticketcategories']},
              {id:5,label:'حذف  موضوعات تیکت',perm:['delete_ticketcategories']},
              ]">
          </PermissionComponent>
          <PermissionComponent
              title="تیکت ها"
              :permissions="[
              {id:1,label:'لیست تیکت ها',perm:['view_any_tickets']},
              {id:2,label:'مشاهده تیکت',perm:['view_tickets']},
              {id:3,label:'ثبت پاسخ',perm:['create_tickets']},
              {id:4,label:'ویرایش تیکت',perm:['update_tickets']},
              ]">
          </PermissionComponent>
          <PermissionComponent
              title="تماس با ما"
              :permissions="[
              {id:1,label:'لیست تماس با ما',perm:['view_any_contacts']},
              {id:2,label:'مشاهده تماس با ما',perm:['view_contacts']},
              {id:3,label:'ثبت پاسخ تماس با ما',perm:['update_contacts']},
              ]">
          </PermissionComponent>
          <PermissionComponent
              title="برداشت از کیف پول"
              :permissions="[
              {id:1,label:'لیست برداشت از کیف پول',perm:['view_any_withdrawwallets']},
              {id:2,label:'ویرایش وضعیت برداشت',perm:['update_withdrawwallets']},
              ]">
          </PermissionComponent>
          <PermissionComponent
              title="خبرنامه ها"
              :permissions="[
              {id:1,label:'لیست خبرنامه ها',perm:['view_any_newsletters']},
              ]">
          </PermissionComponent>
          <PermissionComponent
              title="دیدکاه های کالا"
              :permissions="[
              {id:1,label:'لیست دیدکاه های کالا',perm:['view_any_productcomments']},
              {id:2,label:'ویرایش وضعیت دیدکاها',perm:['update_productcomments']},
              ]">
          </PermissionComponent>
          <PermissionComponent
              title="پرسش های کالا"
              :permissions="[
              {id:1,label:'لیست پرسش های کالا',perm:['view_any_productquestions']},
              {id:2,label:'ویرایش وضعیت پرسش',perm:['update_productquestions']},
              ]">
          </PermissionComponent>
          <PermissionComponent
              title="پاسخ های پرسش کالا"
              :permissions="[
              {id:1,label:'لیست پاسخ های پرسش کالا',perm:['view_any_productquestionanswers']},
              {id:2,label:'ویرایش وضعیت پاسخ',perm:['update_productquestionanswers']},
              ]">
          </PermissionComponent>
          <PermissionComponent
              title="ایمیل ها"
              :permissions="[
              {id:1,label:'لیست ایمیل ها',perm:['view_any_emails']},
              {id:2,label:'ارسال ایمیل',perm:['create_emails']},
              ]">
          </PermissionComponent>
          <PermissionComponent
              title="sms ها"
              :permissions="[
              {id:1,label:'لیست sms ها',perm:['view_any_sms']},
              {id:2,label:'ارسال sms',perm:['create_sms']},
              ]">
          </PermissionComponent>
          <PermissionComponent
              title="مرجوع ها"
              :permissions="[
              {id:1,label:'ویرایش وضعیت مرجوعی',perm:['update_returneds']},
              ]">
          </PermissionComponent>
          <PermissionComponent
              title=" بستانکاری ها"
              :permissions="[
              {id:1,label:'لیست بستانکاری ها',perm:['view_any_debtors']},
            ]">
          </PermissionComponent>
        </div>
      </div>
    </Modal>
    <Meta :title="$store.state.siteName + ` | لیست دسترسی ها `"/>
    <Loading :loading="loading" />

    <Modal title="مشاهده دسترسی ها" ref="permissionsModal">
      <div>
        <ul class="flex items-center flex-wrap gap-2">
          <li class="bg-gray-200 rounded-lg px-2 py-1 fm:text-sm" v-for="(perm,index) in persianPermissions" :key="index">{{perm}}</li>
        </ul>
      </div>
    </Modal>
    <Meta :title="$store.state.siteName + ` | لیست دسترسی ها `"/>
    <Loading :loading="loading" />
    <ValidationError />
  </div>
</template>

<script>
export default{name: "Permission"}
</script>

<script setup>
import {ref, onMounted, defineExpose} from 'vue';
import Meta from "@/components/Meta.vue";
import Td from "@/components/Td.vue";
import Thead from "@/components/Thead.vue";
import Input from "@/components/Input.vue";
import Modal from "@/components/Modal.vue";
import Loading from "@/components/Loading.vue";
import PermissionComponent from "@/components/PermissionComponent.vue";
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
let model = ref('permission');
let search = ref('')
let role = ref('')
let allData = ref([])
let allRoles = ref([])
let openModal = ref(null)
let permissionsModal = ref(null)
let persianPermissions = ref([]);
onMounted(async()=>{
  store.state.allPermissions = [];
  await getData();
});
defineExpose({getData});

async function getData(_loading = true,page=1, isRefresh=false){
  if(isRefresh) search.value = '';
  refresh.value = true;
  loading.value = _loading;
  await axios.get(`${store.state.api}${model.value}?page=${page}&search=${search.value}`).then(resp=>{
    let data = resp.data.data;
    allData.value = data.data;
    store.commit('paginate',data);
  }).catch(err=>{
    store.commit('handleError',err)
  })
  loading.value = refresh.value = false;
}

function searchData(text){
  search.value = text
  getData(false)
}

async function create(){
  await getRoles();
  clearData();
}

async function getRoles(){
  await axios.get(`${store.state.api}get-roles`).then(resp=>{
    allRoles.value = resp.data.data;
  }).catch(err=>{
    store.commit('handleError',err)
  })
}

async function insert(){
  btnLoading.value = true;
  await axios.post(`${store.state.api}create/${role.value}`,formData()).then(resp=>{
    clearData();
    Toast.success();
    getData(false, store.state.current)
  }).catch(err=>{
    store.commit('handleError',err)
  })
  btnLoading.value = false;
}

async function show(postId){
  await getRoles();
  clearData();
  isUpdate.value = loading.value = true;
  await axios.get(`${store.state.api}${model.value}/${postId}`).then(async resp=>{
    let data = resp.data.data;
    role.value = data.id;
    store.state.permissions = data.permissions.map(item=>item.name)
  }).catch(err=>{
    store.commit('handleError',err)
  })
  loading.value = false;
}

async function update(){
  isUpdate.value = btnLoading.value =  true;
  await axios.post(`${store.state.api}update`,formData(true)).then(resp=>{
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
  await store.dispatch('deleteRecord',[`role-permission/${postId}`])
  await getData(false,store.state.current)
  loading.value = false

}


function formData(isUpdate){
  let frm = {
    permissions:store.state.permissions,
    role_name:role.value
  }
  if(isUpdate){
    frm['_method'] = 'PATCH'
  }
  return frm;
}

function clearData(){
  role.value = '';
  isUpdate.value = false;
  openModal.value.toggleModal();
  store.state.permissions = [];
}

function convertToPersian(permission){
  let faPermissions = [];
  for(let i = 0; i<permission.length; i++){
    let perm = store.state.faPermissions.filter(item=>item[permission[i].name])[0]
    faPermissions.push(perm[permission[i].name])
  }
  return faPermissions;
}

function showPermissions(permissions){
  persianPermissions.value = convertToPersian(permissions)
  permissionsModal.value.toggleModal();
}
</script>