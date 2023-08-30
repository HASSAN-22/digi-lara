<template>
  <div class="mt-10 px-8">
    <div class="flex justify-center">
      <span class="!font-medium text-lg">پروفایل</span>
    </div>
    <div class="flex">
      <div class="w-[50%]">
        <div class="mt-10 px-3 mb-3 flex flex-col border border-gray-200 p-4 rounded-r-lg">
          <div class="flex items-center justify-between">
            <div class="flex flex-col gap-2">
              <span class="text-gray-500 fm:text-sm">نام و نام خانوادگی</span>
              <span :class="['fm:text-sm', hasName ? '' : 'text-white']">{{username.length <= 0 ? '---' : username}}</span>
            </div>
            <span class="flex cursor-pointer" v-if="hasName" @click="openNameModal(true)"><i class="far fa-pen-line fm:text-lg text-2xl"></i></span>
            <span class="flex cursor-pointer" v-else @click="openNameModal()"><i class="far fa-plus fm:text-lg text-2xl"></i></span>
          </div>
          <div class="border-b my-5"></div>
          <div class="flex items-center justify-between">
            <div class="flex flex-col gap-2">
              <span class="text-gray-500 fm:text-sm">کد ملی</span>
              <span :class="['fm:text-sm', hasNationalId ? '' : 'text-white']">{{nationalIdNumber.length <= 0 ? '---' : nationalIdNumber}}</span>
            </div>
            <span class="flex cursor-pointer" v-if="hasNationalId" @click="openNationalIdModal(true)"><i class="far fa-pen-line fm:text-lg text-2xl"></i></span>
            <span class="flex cursor-pointer" v-else @click="openNationalIdModal()"><i class="far fa-plus fm:text-lg text-2xl"></i></span>
          </div>
          <div class="border-b my-5"></div>
          <div class="flex items-center justify-between">
            <div class="flex flex-col gap-2">
              <span class="text-gray-500 fm:text-sm">رمز عبور</span>
              <span :class="['fm:text-sm', hasPassword ? '' : 'text-white']">******</span>
            </div>
            <span class="flex cursor-pointer" v-if="hasPassword" @click="openPasswordModal(true)"><i class="far fa-pen-line fm:text-lg text-2xl"></i></span>
            <span class="flex cursor-pointer" v-else @click="openPasswordModal()"><i class="far fa-plus fm:text-lg text-2xl"></i></span>
          </div>
          <div class="border-b my-5"></div>
          <div class="flex items-center justify-between">
            <div class="flex flex-col gap-2">
              <span class="text-gray-500 fm:text-sm">تاریخ تولد</span>
              <span :class="['fm:text-sm', hasBirthDay ? '' : 'text-white']">{{irBirthDay.length <= 0 ? '---' : irBirthDay}}</span>
            </div>
            <span class="flex cursor-pointer" v-if="hasBirthDay" @click="openBirthDayModal(true)"><i class="far fa-pen-line fm:text-lg text-2xl"></i></span>
            <span class="flex cursor-pointer" v-else @click="openBirthDayModal()"><i class="far fa-plus fm:text-lg text-2xl"></i></span>
          </div>
        </div>
      </div>
      <div class="w-[50%]">
        <div class="mt-10 px-3 mb-3 flex flex-col border-t border-l border-b border-gray-200 p-4 rounded-l-lg">
          <div class="flex items-center justify-between">
            <div class="flex flex-col gap-2">
              <span class="text-gray-500 fm:text-sm">شماره موبایل</span>
              <span :class="['fm:text-sm', hasMobile ? '' : 'text-white']">{{mobileNumber.length <= 0 ? '---' : mobileNumber}}</span>
            </div>
            <span class="flex cursor-pointer" v-if="hasMobile" @click="openMobileModal(true)"><i class="far fa-pen-line fm:text-lg text-2xl"></i></span>
            <span class="flex cursor-pointer" v-else @click="openMobileModal()"><i class="far fa-plus fm:text-lg text-2xl"></i></span>
          </div>
          <div class="border-b my-5"></div>
          <div class="flex items-center justify-between">
            <div class="flex flex-col gap-2">
              <span class="text-gray-500 fm:text-sm">ایمیل</span>
              <span :class="['fm:text-sm', hasEmail ? '' : 'text-white']">{{myEmail.length <= 0 ? '---' : myEmail}}</span>
            </div>
            <span class="flex cursor-pointer" v-if="hasEmail" @click="openEmailModal(true)"><i class="far fa-pen-line fm:text-lg text-2xl"></i></span>
            <span class="flex cursor-pointer" v-else @click="openEmailModal()"><i class="far fa-plus fm:text-lg text-2xl"></i></span>
          </div>
          <div class="border-b my-5"></div>
          <div class="flex items-center justify-between">
            <div class="flex flex-col gap-2">
              <span class="text-gray-500 fm:text-sm">روش بازگرداندن پول من</span>
              <span :class="['fm:text-sm', hasRefundMethod ? '' : 'text-white']">{{refundMethodName.length <= 0 ? '---' : refundMethodName}}</span>
            </div>
            <span class="flex cursor-pointer" v-if="hasRefundMethod" @click="openRefundMethodModal(true)"><i class="far fa-pen-line fm:text-lg text-2xl"></i></span>
            <span class="flex cursor-pointer" v-else @click="openRefundMethodModal()"><i class="far fa-plus fm:text-lg text-2xl"></i></span>
          </div>
          <div class="border-b my-5"></div>
          <div class="flex items-center justify-between">
            <div class="flex flex-col gap-2">
              <span class="text-gray-500 fm:text-sm">شغل</span>
              <span :class="['fm:text-sm', hasWork ? '' : 'text-white']">{{workName.length <= 0 ? '---' : workName}}</span>
            </div>
            <span class="flex cursor-pointer" v-if="hasWork" @click="openWorkModal(true)"><i class="far fa-pen-line fm:text-lg text-2xl"></i></span>
            <span class="flex cursor-pointer" v-else @click="openWorkModal()"><i class="far fa-plus fm:text-lg text-2xl"></i></span>
          </div>
        </div>
      </div>
    </div>
    <div class="border border-gray-200 p-4 rounded-lg">
      <div class="mb-12">
        <h1 class="text-medium text-lg fm:text-md">اطلاعات حقوقی</h1>
      </div>
      <div class="flex flex-col gap-2">
        <span class="text-gray-500 fm:text-sm">
          این گزینه برای کسانی است که نیاز به خرید سازمانی (با فاکتور رسمی) دارند.
        </span>
        <Button :text="organizationName !== null ? 'ویرایش اطلاعات حقوقی' : 'ثبت اطلاعات حقوقی'" @click="openModalLegalInfo()" my_class="!border-none !bg-white !text-blue-400 fm:text-sm" />
      </div>
    </div>

<!--    Legal information modal-->
    <Modal :title="isUpdateName ? 'ویرایش اطلاعات حقوقی' : 'ثبت اطلاعات حقوقی'" :save="isUpdateName ? 'ثبت تغییرات' : 'ثبت'" :btnLoading="btnLoading" @callback="insertLegalInfo()" ref="legalInfoModal">
      <div class="mb-5">
        <Input label="نام سازمان" v-model="organizationName" id="organizationName"/>
      </div>
      <div class="mb-5">
        <Input type="number" label="کد اقتصادی" v-model="economicCode" id="economicCode" :required="false"/>
      </div>
      <div class="mb-5">
        <Input type="number" label="شناسه ملی" v-model="legalNationalId" id="legalNationalId"/>
      </div>
      <div class="mb-5">
        <Input type="number" label="شناسه ثبت" v-model="registrationId" id="registrationId"/>
      </div>
      <div class="mb-5">
        <label class="fm:text-sm" for="province">استان<b class="text-red-500 !font-bold">*</b></label>
        <select v-model="province" id="province" @change="getCities($event)" class="fm:text-sm border border-gray-200 p-2 outline-none rounded-lg w-full">
          <option value="" selected disabled>--- انتخاب کنید ---</option>
          <option v-for="_province in provinces" :key="_province.id" :value="_province.id">{{_province.name}}</option>
        </select>
      </div>
      <div class="mb-5">
        <label class="fm:text-sm" for="city">شهرستان<b class="text-red-500 !font-bold">*</b></label>
        <select v-model="city" id="city" class="fm:text-sm border border-gray-200 p-2 outline-none rounded-lg w-full">
          <option value="" selected disabled>--- انتخاب کنید ---</option>
          <option v-for="_city in cities" :key="_city.id" :value="_city.id">{{_city.name}}</option>
        </select>
      </div>
      <div class="mb-5">
        <Input type="number" label="شماره تلفن ثابت" v-model="phone" id="phone"/>
      </div>

    </Modal>


    <Modal :title="isUpdateName ? 'ویرایش نام و نام خانوادگی' : 'ثبت نام و نام خانوادگی'" :save="isUpdateName ? 'ثبت تغییرات' : 'ثبت'" :btnLoading="btnLoading" @callback="insertName()" ref="nameModal">
      <Input label="نام و نام خانوادگی" v-model="name" id="name"/>
    </Modal>

    <Modal :title="isUpdateMobile ? 'ویرایش موبایل' : 'ثبت موبایل'" ref="mobileModal">
      <div>
        <Input type="number" label="موبایل" v-model="mobile" id="mobile"/>
        <Input v-if="showConfirmCode" type="number" label="کد تایید" v-model="confirmCode" id="confirmCode"/>
      </div>
      <div class="mt-6">
        <div v-if="firstSendCode === false">
          <Button text="ارسال کد تایید" my_class="!bg-white border border-blue-500 !text-blue-500  py-1 px-2" :btnLoading="sendCodeLoading" @click="sendCode" />
        </div>
        <div v-else class="flex items-center gap-3">
          <Button :text="isUpdateMobile ? 'ویرایش موبایل' : 'ثبت موبایل'" my_class="!bg-white border border-green-500 !text-green-500 py-1 px-2" :btnLoading="btnLoading" @click="insertMobile" />
          <Button v-if="timer <= 0" text="ارسال کد تایید" my_class="!bg-white border border-blue-500 !text-blue-500  py-1 px-2" :btnLoading="reSendBtnLoading" @click="reSendCode" />
          <Button v-else :text="' ارسال دوباره کد تایید در ' + timer" my_class="!bg-white border border-gray-300 !text-gray-400  py-1 px-2" />
        </div>
      </div>
    </Modal>

    <Modal :title="isUpdateNationalId ? 'ویرایش کد ملی' : 'ثبت کد ملی'" :save="isUpdateNationalId ? 'ثبت تغییرات' : 'ثبت'" :btnLoading="btnLoading" @callback="insertNationalId()" ref="nationalIdModal">
      <Input type="number" label="کد ملی" v-model="nationalId" id="nationalId"/>
    </Modal>

    <Modal :title="isUpdateEmail ? 'ویرایش ایمیل' : 'ثبت ایمیل'" :save="isUpdateEmail ? 'ثبت تغییرات' : 'ثبت'" :btnLoading="btnLoading" @callback="insertEmail()" ref="emailModal">
      <Input type="email" label="ایمیل" v-model="email" id="email"/>
    </Modal>

    <Modal :title="isUpdatePassword ? 'ویرایش رمز عبور' : 'ثبت رمز عبور'" :save="isUpdatePassword ? 'ثبت تغییرات' : 'ثبت'" :btnLoading="btnLoading" @callback="insertPassword()" ref="passwordModal">
      <div class="mb-5">
        <Input type="password" label="رمز عبور قبلی" v-model="prevPassword" id="prevPassword"/>
      </div>
      <div class="mb-5">
        <Input type="password" label="رمز عبور جدید" v-model="password" id="password"/>
      </div>
      <div class="mb-5">
        <Input type="password" label="تکرار رمز عبور" v-model="confirmPassword" id="confirmPassword"/>
      </div>
    </Modal>

    <Modal :title="isUpdateRefundMethod ? 'ویرایش روش بازگرداندن پول من' : 'ثبت روش بازگرداندن پول من'" :save="isUpdateRefundMethod ? 'ثبت تغییرات' : 'ثبت'" :btnLoading="btnLoading" @callback="insertRefundMethod()" ref="refundMethodModal">
        <div class="flex flex-col gap-10">
          <div class="flex items-center gap-5">
            <div>
              <input type="radio" v-model="refundMethod" value="wallet" id="refundMethod" />
            </div>
            <div class="flex flex-col gap-2">
              <h1 class="!font-medium text-lg fm:text-md">واریز به کیف پول {{$store.state.siteName}}</h1>
              <span class="text-gray-500 fm:text-sm">
                شما می‌توانید با انتخاب کیف پول به عنوان روش بازگشت وجه، از مزایای انتقال سریع تر وجوه درخواستی بهره‌مند شوید.
              </span>
            </div>
          </div>
          <div class="flex items-center gap-5">
            <div>
              <input type="radio" v-model="refundMethod" value="bank" id="refundMethod" />
            </div>
            <div class="flex flex-col gap-2">
              <h1 class="!font-medium text-lg fm:text-md">واریز به حساب بانکی</h1>
              <span class="text-gray-500 fm:text-sm">
                در این روش، {{$store.state.siteName}} به شماره شبا حساب بانکی شما نیاز دارد. در صورتی که شماره شبا خود را نمیدانید شما می‌توانید در مرحله بعد با شماره حساب و یا شماره کارت بانکی خود نسبت به محاسبه شبا اقدام نمایید.
              </span>
            </div>
          </div>
          <div v-if="refundMethod === 'bank'">
            <Input label="شماره شبا" v-model="shaba" id="shaba" alert="بدونه IR وارد شود"/>
          </div>
        </div>
    </Modal>

    <Modal :title="isUpdateBirthDay ? 'ویرایش تاریخ تولد' : 'ثبت تاریخ تولد'" :save="isUpdateBirthDay ? 'ثبت تغییرات' : 'ثبت'" :btnLoading="btnLoading" @callback="insertBirthDay()" ref="birthDayModal">
      <div class="mb-5">
        <label class="fm:text-sm" for="expire_at">تاریخ تولد<b class="text-red-500 !font-bold">*</b></label>
        <date-picker v-model="birthDay" id="birthDay"></date-picker>
      </div>
    </Modal>

    <Modal :title="isUpdateJob ? 'ویرایش شغل' : 'ثبت شغل'" :save="isUpdateJob ? 'ثبت تغییرات' : 'ثبت'" :btnLoading="btnLoading" @callback="insertWork()" ref="workModal">
      <div class="mb-5 w-full flex flex-col gap-4">
        <label class="fm:text-sm" for="work">شغل<b class="text-red-500 !font-bold">*</b></label>
        <select v-model="work" id="work" class="fm:text-sm border border-gray-200 p-2 outline-none rounded-lg w-full">
          <option value="" selected disabled>--- انتخاب کنید ---</option>
          <option v-for="_work in works" :key="_work.id" :value="_work.id">{{_work.name}}</option>
        </select>
      </div>
    </Modal>

    <Meta :title="$store.state.siteName + ` | پروفایل `"/>
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
import Input from "@/components/Input.vue";
import Modal from "@/components/Modal.vue";
import Loading from "@/components/Loading.vue";
import Button from "@/components/Button.vue";
import ValidationError from "@/components/ValidationError.vue";
import Textarea from "@/components/Textarea.vue";
import axios from "@/plugins/axios";
import store from "@/store";
import Toast from "@/plugins/toast";

let defaultTimer = ref(10);
let timer = ref(defaultTimer.value)
let reSendBtnLoading = ref(false)
let sendCodeLoading = ref(false);
let firstSendCode = ref(false)
let showConfirmCode = ref(false)

let hasName = ref(false)
let nameModal = ref(false)
let isUpdateName = ref(false)

let hasMobile = ref(false)
let mobileModal = ref(false)
let isUpdateMobile = ref(false)

let hasNationalId = ref(false)
let nationalIdModal = ref(false)
let isUpdateNationalId = ref(false)

let hasEmail = ref(false)
let emailModal = ref(false)
let isUpdateEmail = ref(false)

let hasPassword = ref(false)
let passwordModal = ref(false)
let isUpdatePassword = ref(false)

let hasRefundMethod = ref(false)
let refundMethodModal = ref(false)
let isUpdateRefundMethod = ref(false)

let hasBirthDay = ref(false)
let birthDayModal = ref(false)
let isUpdateBirthDay = ref(false)

let hasWork = ref(false)
let workModal = ref(false)
let isUpdateWork = ref(false)

let btnLoading = ref(false)
let loading = ref(false)

let confirmCode = ref('');
let model = ref('profile');
let name = ref('');
let username = ref('');
let mobile = ref('');
let mobileNumber = ref('');
let nationalId = ref('');
let nationalIdNumber = ref('');
let email = ref('');
let myEmail = ref('');
let password = ref('');
let prevPassword = ref('');
let confirmPassword = ref('');
let refundMethod = ref('');
let refundMethodName = ref('');
let shaba = ref('');
let irBirthDay = ref('');
let birthDay = ref('');
let work = ref('');
let workName = ref('');

let works = ref([]);
let provinces = ref([]);
let cities = ref([]);

let legalInfoModal = ref(false)

let organizationName = ref('')
let economicCode = ref('')
let legalNationalId = ref('')
let registrationId = ref('')
let province = ref('')
let city = ref('')
let phone = ref('')


onMounted(async()=>{
  await getData();
});
defineExpose({getData});

async function getData(_loading = true){
  loading.value = _loading;
  await axios.get(`${store.state.api}${model.value}/get-profile`).then(resp=>{
    let data = resp.data.data;
    let user = data.user;
    let profile = user.profile;
    let legalInfo = user.legal_info;
    works.value = data.works;
    provinces.value = data.provinces

    mobile.value = user.mobile;
    mobileNumber.value = mobile.value;
    hasMobile.value = true;
    hasPassword.value = true;

    if(profile){
      if(profile.name){
        name.value = profile.name;
        username.value = name.value;
        hasName.value = true;
      }
      if(profile.national_id){
        nationalId.value = profile.national_id;
        nationalIdNumber.value = nationalId.value;
        hasNationalId.value = true;
      }
      if(profile.email){
        email.value = profile.email;
        myEmail.value = email.value;
        hasEmail.value = true;
      }
      if(profile.refund_method){
        refundMethod.value = profile.refund_method;
        refundMethodName.value = refundMethod.value === 'bank' ? 'بانک' : 'کیف پول';
        hasRefundMethod.value = true;
      }
      if(profile.birth_day){
        birthDay.value = profile.birth_day;
        irBirthDay.value = profile.ir_birth_day;
        hasBirthDay.value = true;
      }
      if(profile.shaba){
        shaba.value = profile.shaba;
      }
      if(profile.work){
        work.value = profile.work_id;
        workName.value = profile.work.name;
        hasWork.value = true;
      }
    }

    if(legalInfo){
      organizationName = legalInfo.organization_name
      economicCode = legalInfo.economic_code === null ? '' : legalInfo.economic_code;
      legalNationalId = legalInfo.national_id
      registrationId = legalInfo.registration_id
      province = legalInfo.province_id
      city = legalInfo.city_id
      phone = legalInfo.phone
    }
  }).catch(err=>{
    store.commit('handleError',err)
  })
  loading.value = false;
}

function openNameModal(isUpdate){
  isUpdateName.value = isUpdate;
  nameModal.value.toggleModal()
}

async function insertName(){
  btnLoading.value = true;
  await axios.post(`${store.state.api}${model.value}/insert-name`,{name:name.value}).then(resp=>{
    hasName.value = true;
    username.value = name.value;
    Toast.success();
  }).catch(err=>{
    store.commit('handleError',err)
  })
  btnLoading.value = false;
}

function openNationalIdModal(isUpdate){
  isUpdateNationalId.value = isUpdate;
  nationalIdModal.value.toggleModal()
}

async function insertNationalId(){
  btnLoading.value = true;
  await axios.post(`${store.state.api}${model.value}/insert-national-id`,{national_id:nationalId.value}).then(resp=>{
    hasNationalId.value = true;
    Toast.success();
  }).catch(err=>{
    store.commit('handleError',err)
  })
  btnLoading.value = false;
}

function openPasswordModal(isUpdate){
  prevPassword.value = password.value = confirmPassword.value = '';
  isUpdatePassword.value = isUpdate;
  passwordModal.value.toggleModal()
}

async function insertPassword(){
  btnLoading.value = true;
  await axios.post(`${store.state.api}${model.value}/insert-password`,{prev_password:prevPassword.value, password:password.value, password_confirmation:confirmPassword.value}).then(resp=>{
    hasPassword.value = true;
    Toast.success();
  }).catch(err=>{
    store.commit('handleError',err)
  })
  btnLoading.value = false;
}

function openBirthDayModal(isUpdate){
  isUpdateBirthDay.value = isUpdate;
  birthDayModal.value.toggleModal()
}

async function insertBirthDay(){
  btnLoading.value = true;
  await axios.post(`${store.state.api}${model.value}/insert-birthday`,{birth_day:birthDay.value}).then(resp=>{
    hasBirthDay.value = true;
    Toast.success();
  }).catch(err=>{
    store.commit('handleError',err)
  })
  btnLoading.value = false;
}

function openMobileModal(isUpdate){
  showConfirmCode.value = false;
  firstSendCode.value = false;
  confirmCode.value = '';
  timer.value = defaultTimer.value;
  isUpdateMobile.value = isUpdate;
  mobileModal.value.toggleModal()
}

async function insertMobile(){
  btnLoading.value = true;
  await axios.post(`${store.state.api}${model.value}/insert-mobile`,{mobile:mobile.value,confirm_code:confirmCode.value}).then(resp=>{
    hasMobile.value = true;
    Toast.success();
  }).catch(err=>{
    store.commit('handleError',err)
  })
  btnLoading.value = false;
}

function openEmailModal(isUpdate){
  isUpdateEmail.value = isUpdate;
  emailModal.value.toggleModal()
}

async function insertEmail(){
  btnLoading.value = true;
  await axios.post(`${store.state.api}${model.value}/insert-email`,{email:email.value}).then(resp=>{
    hasEmail.value = true;
    Toast.success();
  }).catch(err=>{
    store.commit('handleError',err)
  })
  btnLoading.value = false;
}

function openRefundMethodModal(isUpdate){
  isUpdateRefundMethod.value = isUpdate;
  refundMethodModal.value.toggleModal()
}

async function insertRefundMethod(){
  btnLoading.value = true;
  await axios.post(`${store.state.api}${model.value}/insert-refund-method`,{refund_method:refundMethod.value, shaba:shaba.value}).then(resp=>{
    hasRefundMethod.value = true;
    refundMethodName.value = refundMethod.value === 'bank' ? 'بانک' : 'کیف پول';
    Toast.success();
  }).catch(err=>{
    store.commit('handleError',err)
  })
  btnLoading.value = false;
}

function openWorkModal(isUpdate){
  isUpdateWork.value = isUpdate;
  workModal.value.toggleModal()
}

async function insertWork(){
  btnLoading.value = true;
  await axios.post(`${store.state.api}${model.value}/insert-work`,{work_id:work.value}).then(resp=>{
    hasWork.value = true;
    workName.value = works.value.filter(item=>item.id === work.value)[0].name;
    Toast.success();
  }).catch(err=>{
    store.commit('handleError',err)
  })
  btnLoading.value = false;
}

async function insertLegalInfo(){
  btnLoading.value = true;
  await axios.post(`${store.state.api}${model.value}/insert-legal-info`,setLegalInfo()).then(async resp=>{
    await getData(false);
    Toast.success();
    openModalLegalInfo()
  }).catch(err=>{
    store.commit('handleError',err)
  })
  btnLoading.value = false;
}

function setLegalInfo(){
  return {
    province_id:province.value,
    city_id:city.value,
    organization_name:organizationName.value,
    economic_code:economicCode.value,
    national_id:legalNationalId.value,
    registration_id:registrationId.value,
    phone:phone.value,
  }
}

async function getCities(event, _cityId=null, _loading=true){
  loading.value = _loading;
  let provinceId = _cityId === null ? event.target.value : _cityId;
  await axios.get(`${store.state.api}get-cities/${provinceId}`).then(resp=>{
    cities.value = resp.data.data;
  })
  loading.value = false;
}

function openModalLegalInfo(){
  legalInfoModal.value.toggleModal();
}

async function sendCode(){
  sendCodeLoading.value = showConfirmCode.value = true;
  await axios.post(`${store.state.api}send-code`,{mobile:mobile.value,pattern:process.env.VUE_CHAGE_PHONE_NUMBER_PATTERN,force_send:'yes'}).then(resp=>{
    firstSendCode.value = true;
    _timeOut();
  }).catch(err=>{
    store.commit('handleError',err)
  })
  sendCodeLoading.value = false;
}

async function reSendCode(){
  reSendBtnLoading.value = showConfirmCode.value = true;
  timer.value = defaultTimer.value;
  await axios.post(`${store.state.api}resend-code`,{mobile:mobile.value,pattern:process.env.VUE_CHAGE_PHONE_NUMBER_PATTERN}).then(resp=>{
    Toast.success("کد با موفقیت ارسال شد")
    _timeOut();
  }).catch(err=>{
    store.commit('handleError',err)
  })
  reSendBtnLoading.value = false;
}

function _timeOut(){
  let timerInterVal = setInterval(()=>{
    if(timer.value <= 0){
      clearInterval(timerInterVal);
    }else{
      timer.value--;
    }

  },1000)
}


</script>