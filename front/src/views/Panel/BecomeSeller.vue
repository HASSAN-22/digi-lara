<template>
  <div class="mt-10 px-8">
    <div class="flex justify-center">
      <span class="!font-medium text-lg" v-if="!isAdmin">فروشنده شوید</span>
      <span class="!font-medium text-lg" v-else>لیست درخواست های فروشندفروشندگی</span>
    </div>
    <div class="mt-10 px-3 mb-3">
      <div class="mb-3 mr-2 flex justify-between fm:flex-col items-center">
        <div class="flex gap-2 items-center">
          <Button v-if="($store.state.user.access !== 'admin') && !alreadyRegistered && !requestAlreadySent" text="اضافه کردن" @click="create()"></Button>
          <Button @click="getData(false, 1, true)" my_class="!bg-white !py-2 !px-2" :btnLoading="refresh"><i class="far fa-sync text-2xl fm:text-lg text-gray-700"></i></Button>
        </div>
        <div class="ml-2 fm:mt-3">
          <Input type="search" v-debounce="searchData" :debounce-events="['keydown']" id="search" placeholder="جستجو: متن خود را وارد کنید" :required="false" />
        </div>
      </div>
      <div v-if="allData.length <= 0" class="border border-b w-full my-2"></div>
      <div v-if="allData.length > 0" class=" overflow-x-scroll scroll">
        <table class="w-full border-collapse lg:table 2xl:table xl:lg:table md:table">
          <Thead :titles="[
              'نام فروشگاه',
              'نوع فروشندگی',
              'موبایل',
              'شبا',
              'وضعیت',
              'دلیل تایید نشدن',
              'تاریخ ثبت',
              'عملیات'
          ]" />
          <tbody class="block lg:table-row-group xl:table-row-group 2xl:table-row-group md:table-row-group ">
          <tr class="border border-gray-200 block lg:table-row xl:table-row 2xl:table-row md:table-row" v-for="item in allData" :key="item.id">
            <Td title="نام فروشگاه" class="fm:text-sm">
              <Notification :notifications="notifications" :postId="item.id" type="become_seller">
                <span>{{item.shop_name}}</span>
              </Notification>
            </Td>
            <Td title="نوع فروشندگی" class="fm:text-sm">{{item.type}}</Td>
            <Td title="موبایل" class="fm:text-sm">{{item.mobile}}</Td>
            <Td title="شبا" class="fm:text-sm">{{item.shaba}}</Td>
            <Td title="وضعیت" class="fm:text-sm">{{item.ir_status}}</Td>
            <Td title="دلیل تایید نشدن" class="fm:text-sm"><p class="break-all">{{item.reject_reason}}</p></Td>
            <Td title="تاریخ ثبت" class="fm:text-sm">{{item.ir_created_at}}</Td>
            <Td title="عملیات">
              <div class="flex items-center justify-center gap-2">
                <Button v-if="isAdmin" :my_class="'!bg-white border border-blue-500 !py-2 !px-2 fm:py-1 fm:px-1'" @click="changeStatus(item.id,item.status)"><i class="far fm:text-sm fa-edit text-blue-500"></i></Button>
                <Button :my_class="'!bg-white border border-blue-500 !py-2 !px-2 fm:py-1 fm:px-1'" @click="show(item.id)"><i class="far fm:text-sm fa-eye text-green-500"></i></Button>
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
    <Modal :title="isUpdate ? 'ویرایش اطلاعات' : 'ثبت نام'" :save="isUpdate ? 'ثبت تغییرات' : 'ثبت'" :btnLoading="btnLoading" @callback="isUpdate ? update() : insert()" ref="openModal">
      <div class="mb-5 w-full">
        <label class="fm:text-sm" for="seller_type">چه نوع فروشنده ای هستید؟<b class="text-red-500 !font-bold">*</b></label>
        <select v-model="sellerType" id="seller_type" class="fm:text-sm border border-gray-200 p-2 outline-none rounded-lg w-full">
          <option value="" selected disabled>--- انتخاب کنید ---</option>
          <option value="legal">شخص حقوقی</option>
          <option value="real">شخص حقیقی</option>
        </select>
        <div class="flex flex-col gap-5 mt-5">
          <p v-if="sellerType === 'real'">
            <b class="!font-medium">شخص حقیقی</b> فردی است که دارای خصوصیاتی مختص به خود مانند نام، نام خانوادگی، تاریخ تولد، کد ملی، شماره شناسنامه و غیره می باشد.
          </p>
          <p v-if="sellerType === 'legal'">
            <b class="!font-medium"> شخص حقوقی </b>موسسات یا شرکت هایی هستند که پس از طی مراحل قانونی به ثبت می‌رسند و دارای مشخصاتی مانند نام شخص حقوقی، تاریخ ثبت، شماره ثبت، کد شناسایی، کد اقتصادی، موضوع فعالیت و غیره می باشند.
          </p>
        </div>
      </div>
      <div class="mt-8" v-if="sellerType !== ''">
          <div v-if="sellerType === 'legal'">
            <div class="mb-5 mb-5">
              <Input label="نام شرکت" v-model="companyName" />
            </div>
            <div class="flex items-center gap-5 mb-5">
              <div class="fd:w-[50%]">
                <label class="fm:text-sm">نوع شرکت<b class="text-red-500 !font-bold">*</b></label>
                <select v-model="companyType" id="companyType" class="fm:text-sm border border-gray-200 p-2 outline-none rounded-lg w-full">
                  <option value="" selected>--- انتخاب کنید ---</option>
                  <option value="public" data-select2-id="11">سهامی عام</option>
                  <option value="joint_stock" data-select2-id="43">سهامی خاص</option>
                  <option value="ltd" data-select2-id="44">مسولیت محدود</option>
                  <option value="coop" data-select2-id="45">تعاونی</option>
                  <option value="solidarity" data-select2-id="46">تضامنی</option>
                  <option value="institution" data-select2-id="47">موسسه</option>
                </select>
              </div>
              <div class="fd:w-[50%]">
                <Input type="number" label="شماره ثبت" v-model="registrationNumber" />
              </div>
            </div>
            <div class="flex items-center gap-5 mb-5">
              <div class="fd:w-[50%]">
                <Input type="number" label="شناسه ملی" v-model="nationalIdentityNumber" />
              </div>
              <div class="fd:w-[50%]">
                <Input type="number" label="کد اقتصادی" v-model="economicNumber" />
              </div>
            </div>
            <div class="flex items-center gap-5 mb-5">
              <Input label="صاحبان حق امضا" v-model="authorizedRepresentative" placeholder="مثال: حسن بهرامی - مهشید رضایی" />
            </div>
          </div>
          <div v-if="sellerType === 'real'">
            <div class="flex items-center gap-5 mb-5">
              <div class="fd:w-[50%]">
                <Input label="نام" v-model="name" />
              </div>
              <div class="fd:w-[50%]">
                <Input label="نام خانوادگی" v-model="lastName" />
              </div>
            </div>
            <div class="flex items-center gap-5 mb-5">
              <div class="fd:w-[50%]">
                <Input type="number" label="شماره شناسنامه" v-model="nationalIdentityNumber" />
              </div>
              <div class="fd:w-[50%]">
                <Input type="number" label="کد ملی" v-model="nationalIdentityCartNumber" />
              </div>
            </div>
            <div class="mb-5">
              <label class="fm:text-sm" for="expire_at">تاریخ تولد<b class="text-red-500 !font-bold">*</b></label>
              <date-picker v-model="birthday" id="birthDay"></date-picker>
            </div>
            <div class="mb-5">
              <label class="fm:text-sm">جنسیت<b class="text-red-500 !font-bold">*</b></label>
              <select v-model="gender" id="gender" class="fm:text-sm border border-gray-200 p-2 outline-none rounded-lg w-full">
                <option value="" selected>--- انتخاب کنید ---</option>
                <option value="man" data-select2-id="11">مرد</option>
                <option value="woman" data-select2-id="43">زن</option>
              </select>
            </div>
          </div>

          <div class="flex items-center gap-5 mb-5">
            <div class="fd:w-[50%]">
              <label class="fm:text-sm" for="province">استان<b class="text-red-500 !font-bold">*</b></label>
              <select v-model="province" id="province" @change="getCities($event)" class="fm:text-sm border border-gray-200 p-2 outline-none rounded-lg w-full">
                <option value="" selected disabled>--- انتخاب کنید ---</option>
                <option v-for="_province in provinces" :key="_province.id" :value="_province.id">{{_province.name}}</option>
              </select>
            </div>
            <div class="fd:w-[50%]">
              <label class="fm:text-sm" for="city">شهرستان<b class="text-red-500 !font-bold">*</b></label>
              <select v-model="city" id="city" class="fm:text-sm border border-gray-200 p-2 outline-none rounded-lg w-full">
                <option value="" selected disabled>--- انتخاب کنید ---</option>
                <option v-for="_city in cities" :key="_city.id" :value="_city.id">{{_city.name}}</option>
              </select>
            </div>
          </div>
          <div class="flex items-center gap-5 mb-5">
            <Input label="آدرس" v-model="address" placeholder="آدرس خود را به صورت کامل (محله - خیابان اصلی - کوچه - پلاک – واحد ) وارد نمایید" />
          </div>
          <div class="flex items-center gap-5 mb-5">
            <Input type="number" label="کد پستی" v-model="postalCode" />
          </div>
          <div class="flex items-center gap-5 mb-5">
            <div class="fd:w-[50%]">
              <Input type="number" label="تلفن ثابت" placeholder="مثال: 0216655" v-model="phone" />
            </div>
            <div class="fd:w-[50%]">
              <Input type="number" label="تلفن همراه" placeholder="مثال: 09221234567" v-model="mobile" />
            </div>
          </div>
          <div class="flex items-center gap-5 mb-5">
            <Input label="نام فروشگاه" v-model="shopName" />
          </div>
          <div class="flex items-center gap-5 mb-5">
            <Input type="number" label="شماره شبا (به نام شخص یا شرکت ثبت نام کننده)" placeholder="810150000000000000000000" v-model="shaba" />
          </div>
          <div class="flex gap-5 mb-5">
            <div class="fd:w-[50%]">
              <Input type="file" label="تصویر کارت ملی" @change="getFile($event, 'front')" :key="identityCardFrontKey" alert="فرمت: png |حداکثر سایز 2 MB"/>
              <div v-if="identityCardFrontPreview">
                <img :src="identityCardFrontPreview" class="w[70px] h-[70px]" />
              </div>
            </div>
            <div class="fd:w-[50%]">
              <Input type="file" label="تصویر پشت کارت ملی" @change="getFile($event, 'backend')" :key="identityCardBackKey" alert="فرمت: png |حداکثر سایز 2 MB"  />
              <div v-if="identityCardBackPreview">
                <img :src="identityCardBackPreview" class="w[70px] h-[70px]" />
              </div>
            </div>
          </div>
      </div>
    </Modal>

    <Modal title="تغییر وضعیت" save="ثبت" :btnLoading="btnLoading" @callback="update()" ref="openUpdateModal">
      <div class="mb-5 w-full">
        <label class="fm:text-sm" for="status">وضعیت<b class="text-red-500 !font-bold">*</b></label>
        <select v-model="status" id="status" class="fm:text-sm border border-gray-200 p-2 outline-none rounded-lg w-full">
          <option value="" selected disabled>--- انتخاب کنید ---</option>
          <option value="yes">تایید</option>
          <option value="no">رد</option>
          <option value="pending">در حال بررسی</option>
        </select>
      </div>
      <div v-if="status === 'no'">
        <Textarea label="دلیل رد" :cols="3" :rows="3" :maxlength="5000" :alert="rejectReason.length+'/5000'" v-model="rejectReason" id="rejectReason" />
      </div>
    </Modal>

    <Modal title="نمایش اطلاعات" ref="openInfoModal">
      <div v-if="becomeSeller !== null">
        <div class="mb-3">
          <ul class="flex flex-col gap-4">
            <li class="flex justify-between items-center gap-5 border border-gray-200 rounded-lg bg-gray-100 p-2">
              <span class="fm:text-sm !font-medium">نام فروشگاه:</span>
              <span class="fm:text-sm">{{becomeSeller.shop_name}}</span>
            </li>
          </ul>
        </div>
        <div class="mb-3">
          <ul class="flex flex-col gap-4">
            <li class="flex justify-between items-center gap-5 border border-gray-200 rounded-lg bg-gray-100 p-2">
              <span class="fm:text-sm !font-medium">نوع فروشندگی:</span>
              <span class="fm:text-sm">{{becomeSeller.type}}</span>
            </li>
          </ul>
        </div>
        <div v-if="becomeSeller.become_seller_legal !== null" class="mb-5">
          <ul class="flex flex-col gap-4">
            <li class="flex justify-between items-center gap-5 border border-gray-200 rounded-lg bg-gray-100 p-2">
              <span class="fm:text-sm !font-medium">نام شرکت:</span>
              <span class="fm:text-sm">{{becomeSeller.become_seller_legal.company_name}}</span>
            </li>
            <li class="flex justify-between items-center gap-5 border border-gray-200 rounded-lg bg-gray-100 p-2">
              <span class="fm:text-sm !font-medium">نوع شرکت:</span>
              <span class="fm:text-sm">{{becomeSeller.become_seller_legal.company_type}}</span>
            </li>
            <li class="flex justify-between items-center gap-5 border border-gray-200 rounded-lg bg-gray-100 p-2">
              <span class="fm:text-sm !font-medium">شماره ثبت:</span>
              <span class="fm:text-sm">{{becomeSeller.become_seller_legal.registration_number}}</span>
            </li>
            <li class="flex justify-between items-center gap-5 border border-gray-200 rounded-lg bg-gray-100 p-2">
              <span class="fm:text-sm !font-medium">شناسه ملی:</span>
              <span class="fm:text-sm">{{becomeSeller.become_seller_legal.national_identity_number}}</span>
            </li>
            <li class="flex justify-between items-center gap-5 border border-gray-200 rounded-lg bg-gray-100 p-2">
              <span class="fm:text-sm !font-medium">کد اقتصادی:</span>
              <span class="fm:text-sm">{{becomeSeller.become_seller_legal.economic_number}}</span>
            </li>
            <li class="flex justify-between items-center gap-5 border border-gray-200 rounded-lg bg-gray-100 p-2">
              <span class="fm:text-sm !font-medium">صاحبان حق امضا:</span>
              <span class="fm:text-sm">{{becomeSeller.become_seller_legal.authorized_representative}}</span>
            </li>
          </ul>
        </div>
        <div v-if="becomeSeller.become_seller_real !== null" class="mb-5">
          <ul class="flex flex-col gap-4">
            <li class="flex justify-between items-center gap-5 border border-gray-200 rounded-lg bg-gray-100 p-2">
              <span class="fm:text-sm !font-medium">نام:</span>
              <span class="fm:text-sm">{{becomeSeller.become_seller_real.name}}</span>
            </li>
            <li class="flex justify-between items-center gap-5 border border-gray-200 rounded-lg bg-gray-100 p-2">
              <span class="fm:text-sm !font-medium">نام خانوادگی:</span>
              <span class="fm:text-sm">{{becomeSeller.become_seller_real.last_name}}</span>
            </li>
            <li class="flex justify-between items-center gap-5 border border-gray-200 rounded-lg bg-gray-100 p-2">
              <span class="fm:text-sm !font-medium">شماره شناسنامه:</span>
              <span class="fm:text-sm">{{becomeSeller.become_seller_real.identity_card_number }}</span>
            </li>
            <li class="flex justify-between items-center gap-5 border border-gray-200 rounded-lg bg-gray-100 p-2">
              <span class="fm:text-sm !font-medium">کد ملی:</span>
              <span class="fm:text-sm">{{becomeSeller.become_seller_real.national_identity_number}}</span>
            </li>
            <li class="flex justify-between items-center gap-5 border border-gray-200 rounded-lg bg-gray-100 p-2">
              <span class="fm:text-sm !font-medium">تاریخ تولد:</span>
              <span class="fm:text-sm">{{becomeSeller.become_seller_real.birth_day}}</span>
            </li>
            <li class="flex justify-between items-center gap-5 border border-gray-200 rounded-lg bg-gray-100 p-2">
              <span class="fm:text-sm !font-medium">جنسیت:</span>
              <span class="fm:text-sm">{{becomeSeller.become_seller_real.gender}}  </span>
            </li>
          </ul>
        </div>
        <div>
          <ul class="flex flex-col gap-4">
            <li class="flex justify-between items-center gap-5 border border-gray-200 rounded-lg bg-gray-100 p-2">
              <span class="fm:text-sm !font-medium">استان و شهرستان:</span>
              <span class="fm:text-sm">{{becomeSeller.province.name + ',' + becomeSeller.city.name}}</span>
            </li>
            <li class="flex justify-between items-center gap-5 border border-gray-200 rounded-lg bg-gray-100 p-2">
              <span class="fm:text-sm !font-medium">آدرس:</span>
              <span class="fm:text-sm">{{becomeSeller.address}}</span>
            </li>
            <li class="flex justify-between items-center gap-5 border border-gray-200 rounded-lg bg-gray-100 p-2">
              <span class="fm:text-sm !font-medium">کد پستی:</span>
              <span class="fm:text-sm">{{becomeSeller.postal_code}}</span>
            </li>
            <li class="flex justify-between items-center gap-5 border border-gray-200 rounded-lg bg-gray-100 p-2">
              <span class="fm:text-sm !font-medium">تلفن ثابت:</span>
              <span class="fm:text-sm">{{becomeSeller.phone}}</span>
            </li>
            <li class="flex justify-between items-center gap-5 border border-gray-200 rounded-lg bg-gray-100 p-2">
              <span class="fm:text-sm !font-medium">تلفن همراه:</span>
              <span class="fm:text-sm">{{becomeSeller.mobile}}</span>
            </li>

            <li class="flex justify-between items-center gap-5 border border-gray-200 rounded-lg bg-gray-100 p-2">
              <span class="fm:text-sm !font-medium">شماره شبا:</span>
              <span class="fm:text-sm">{{becomeSeller.shaba}}</span>
            </li>
            <li class="flex justify-between items-center gap-5 border border-gray-200 rounded-lg bg-gray-100 p-2">
              <span class="fm:text-sm !font-medium">وضعیت:</span>
              <span class="fm:text-sm">{{becomeSeller.status}}</span>
            </li>
            <li class="flex flex-col gap-5 border border-gray-200 rounded-lg bg-gray-100 p-2">
              <span class="fm:text-sm !font-medium">دلیل رد:</span>
              <p class="fm:text-sm break-all">{{becomeSeller.reject_reason ?? '---'}}</p>
            </li>
            <li class="flex justify-between items-center gap-5 border border-gray-200 rounded-lg bg-gray-100 p-2">
              <span class="fm:text-sm !font-medium">تصویر کارت ملی:</span>
              <span class="fm:text-sm"><img :src="$store.state.url + becomeSeller.identity_card_front" class="w-[100px] h-[100px]"/></span>
            </li>
            <li class="flex justify-between items-center gap-5 border border-gray-200 rounded-lg bg-gray-100 p-2">
              <span class="fm:text-sm !font-medium">تصویر پشت کارت ملی:</span>
              <span class="fm:text-sm"><img :src="$store.state.url + becomeSeller.identity_card_back"  class="w-[100px] h-[100px]"/></span>
            </li>
          </ul>
        </div>
      </div>
    </Modal>

    <Meta :title="$store.state.siteName + ` | فروشندفروشندگی `"/>
    <Loading :loading="loading" />
    <ValidationError />
  </div>
</template>

<script>
export default{name: "BecomeSeller"}
</script>

<script setup>
import {ref, onMounted} from 'vue';
import Meta from "@/components/Meta.vue";
import Td from "@/components/Td.vue";
import Thead from "@/components/Thead.vue";
import Input from "@/components/Input.vue";
import Modal from "@/components/Modal.vue";
import Loading from "@/components/Loading.vue";
import Button from "@/components/Button.vue";
import ValidationError from "@/components/ValidationError.vue";
import Paginate from "@/components/Paginate.vue";
import axios from "@/plugins/axios";
import store from "@/store";
import Toast from "@/plugins/toast";
import Textarea from "@/components/Textarea";
import Notification from "@/components/Notification";


let refresh = ref(false)
let btnLoading = ref(false)
let loading = ref(false)
let isUpdate = ref(false)
let postId = ref(null)
let model = ref('become-seller')
let search = ref('')
let allData = ref([])
let notifications = ref([])
let provinces = ref([])
let cities = ref([])
let openModal = ref(null)
let openInfoModal = ref(null)
let openUpdateModal = ref(null)
let alreadyRegistered = ref(false)
let requestAlreadySent = ref(false);
let becomeSeller = ref(null)
let isAdmin = ref(store.state.user.access === 'admin');
let status = ref('')
let rejectReason = ref('')

let sellerType = ref('')
let companyName = ref('')
let companyType = ref('')
let registrationNumber = ref('')
let economicNumber = ref('')
let authorizedRepresentative = ref('')

let nationalIdentityCartNumber = ref('')
let nationalIdentityNumber = ref('')

let name = ref('')
let lastName = ref('')
let birthday = ref('')
let gender = ref('')
let province = ref('')
let city = ref('')

let shopName = ref('')
let shaba = ref('')
let address = ref('')
let postalCode = ref('')
let phone = ref('')
let mobile = ref('')
let identityCardFront = ref('')
let identityCardFrontPreview = ref(null)
let identityCardFrontKey = ref(0)
let identityCardBack = ref('')
let identityCardBackPreview = ref(null)
let identityCardBackKey = ref(0)

onMounted(async()=>{
  await getData();
});

async function getData(_loading = true,page=1, isRefresh=false){
  if(isRefresh) search.value = '';
  refresh.value = true;
  loading.value = _loading;
  await axios.get(`${store.state.api}${model.value}?page=${page}&search=${search.value}`).then(resp=>{
    let data = resp.data;
    allData.value = data.data;
    allData.value.filter(item=>{
      if(item.status === 'yes'){
        alreadyRegistered.value = true;
        return 0;
      }else if(item.status === 'pending'){
        requestAlreadySent.value = true;
        return 0;
      }
    });
    provinces.value = data.provinces;
    notifications.value = data.notifications;
    store.commit('paginate',data.meta);
  })
  loading.value = refresh.value = false;
}

function create(){
  clearData();
}

async function insert(){
  btnLoading.value = true;
  await axios.post(`${store.state.api}${model.value}/insert`,frmData()).then(async resp=>{
    Toast.success()
    clearData()
    await getData();
  }).catch(err =>{
    store.commit('handleError',err)
  })
  btnLoading.value = false;
}

async function show(_postId){
  loading.value = true;
  await axios.get(`${store.state.api}${model.value}/show/${_postId}`).then(async resp=>{
    becomeSeller.value = resp.data.data;
    openInfoModal.value.toggleModal();
  }).catch(err =>{
  })
  loading.value = false;
}

function changeStatus(_postId,_status){
  rejectReason.value = '';
  status.value = _status
  postId.value = _postId;
  openUpdateModal.value.toggleModal();
}

async function update(){
  loading.value = true;
  await axios.patch(`${store.state.api}${model.value}/update/${postId.value}`,{_method:'PATCH',status:status.value,reject_reason:rejectReason.value}).then(async resp=>{
    openUpdateModal.value.toggleModal();
    rejectReason.value = '';
    status.value = ''
    await getData(false,store.state.current);
    Toast.success();
  }).catch(err =>{
    store.commit('handleError',err)
  })
  loading.value = false;
}

async function getCities(event, _cityId=null, _loading=true){
  loading.value = _loading;
  let provinceId = _cityId === null ? event.target.value : _cityId;
  await axios.get(`${store.state.api}get-cities/${provinceId}`).then(resp=>{
    cities.value = resp.data.data;
  })
  loading.value = false;
}

function searchData(text){
  search.value = text
  getData(false)
}

function frmData(){
  let frm = new FormData();
  frm.append('province_id',province.value);
  frm.append('city_id',city.value);
  frm.append('shop_name',shopName.value);
  frm.append('seller_type',sellerType.value);
  frm.append('postal_code',postalCode.value);
  frm.append('phone',phone.value);
  frm.append('mobile',mobile.value);
  frm.append('shaba',shaba.value);
  frm.append('identity_card_front',identityCardFront.value);
  frm.append('identity_card_back',identityCardBack.value);
  frm.append('address',address.value);
  if(sellerType.value === 'legal'){
    frm.append('company_name',companyName.value);
    frm.append('company_type',companyType.value);
    frm.append('registration_number',registrationNumber.value);
    frm.append('national_identity_number',nationalIdentityNumber.value);
    frm.append('economic_number',economicNumber.value);
    frm.append('authorized_representative',authorizedRepresentative.value);

  }else{
    frm.append('name',name.value);
    frm.append('last_name',lastName.value);
    frm.append('birth_day',birthday.value);
    frm.append('gender',gender.value);
    frm.append('identity_card_number',nationalIdentityCartNumber.value);
    frm.append('national_identity_number',nationalIdentityNumber.value);
  }
  return frm;
}

function clearData(){
  sellerType.value = ''
  companyName.value = ''
  companyType.value = ''
  registrationNumber.value = ''
  economicNumber.value = ''
  authorizedRepresentative.value = ''

  nationalIdentityNumber.value = ''
  nationalIdentityCartNumber.value = '';

  name.value = ''
  lastName.value = ''
  birthday.value = ''
  gender.value = ''
  province.value = ''
  city.value = ''

  shopName.value = ''
  address.value = ''
  postalCode.value = ''
  phone.value = ''
  mobile.value = ''
  identityCardFront.value = ''
  identityCardFrontPreview.value = null
  identityCardFrontKey.value++
  identityCardBack.value = ''
  identityCardBackPreview.value = null
  identityCardBackKey.value++

  isUpdate.value = false;
  postId.value = null;
  openModal.value.toggleModal();
}

function getFile(event, type){
  let image = event.target.files[0]
  if(type === 'front'){
    identityCardFront.value = image;
    identityCardFrontPreview.value = URL.createObjectURL(image);
    identityCardFrontKey.value ++;
  }else{
    identityCardBack.value = image;
    identityCardBackPreview.value = URL.createObjectURL(image);
    identityCardBackKey.value ++;
  }
}

</script>