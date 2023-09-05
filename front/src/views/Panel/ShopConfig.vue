<template>
  <div class="mt-10 px-8">
    <div class="flex justify-center">
      <span class="!font-medium text-lg"> تنظیمات سایت</span>
    </div>
    <div class="mt-10 px-3 mb-3">
      <div>
        <ul class="flex gap-8 items-center">
          <li @click="changeTypeTab(1)" :class="['fm:text-sm cursor-pointer', tabSelected === 1 ? 'border-b-2 border-red-500 text-red-500' : 'text-gray-500']">اطلاعات حقوقی</li>
          <li @click="changeTypeTab(2)" :class="['fm:text-sm cursor-pointer', tabSelected === 2 ? 'border-b-2 border-red-500 text-red-500' : 'text-gray-500']">مشخصات فروشگاه</li>
          <li @click="changeTypeTab(3)" :class="['fm:text-sm cursor-pointer', tabSelected === 3 ? 'border-b-2 border-red-500 text-red-500' : 'text-gray-500']">شبکه های اجتماعی</li>
          <li @click="changeTypeTab(4)" :class="['fm:text-sm cursor-pointer', tabSelected === 4 ? 'border-b-2 border-red-500 text-red-500' : 'text-gray-500']">فوتر باکس ها</li>
          <li @click="changeTypeTab(5)" :class="['fm:text-sm cursor-pointer', tabSelected === 5 ? 'border-b-2 border-red-500 text-red-500' : 'text-gray-500']">تنظیمات پیامک</li>
          <li @click="changeTypeTab(6)" :class="['fm:text-sm cursor-pointer', tabSelected === 6 ? 'border-b-2 border-red-500 text-red-500' : 'text-gray-500']">دیگر تنظیمات</li>
        </ul>
      </div>
      <div v-if="tabSelected === 1" class="mt-8">
        <div class="flex items-center gap-5 mb-5">
          <div class="fd:w-[50%]">
            <Input label="نام شرکت" v-model="companyName" id="companyName"/>
          </div>
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
        </div>
        <div class="flex items-center gap-5 mb-5">
            <Input type="number" label="شناسه ملی" v-model="nationalIdentityNumber" id="nationalIdentityNumber"/>
            <Input type="number" label="شماره ثبت" v-model="registrationNumber" id="registrationNumber"/>
            <Input type="number" label="کد اقتصادی" v-model="economicNumber" id="registrationNumber"/>
        </div>
        <div class="mb-5">
          <Textarea label="ادرس" :cols="3" :rows="3" v-model="address" :maxlength="400" :alert="address.length + '/400'" id="address"/>
        </div>
        <div class="flex items-center gap-5 mb-5">
            <Input type="number" label="کد پستی" v-model="postalCode" id="address"/>
            <Input type="number" label="فکس" v-model="fax" id="fax"/>
            <Input type="number" label="تلفن" v-model="phone" id="phone"/>
        </div>
        <div class="mb-5">
          <Input type="file" label="مهر یا امضاء" @change="getFile($event,'legal')" :key="signatureKey" alert="فرمت تصویر: JPG,JPEG,PNG | پس زمینه تصویر سفید باشد" id="signature"/>
          <div v-if="signaturePreview">
            <img :src="signaturePreview" class="w-[100px] h-[100px]"/>
          </div>
        </div>
        <div>
          <Button @click="insertLegal" :btnLoading="btnLoading" text="ثبت" />
        </div>
      </div>
      <div v-if="tabSelected === 2" class="mt-8">
        <div class="mb-5">
          <div class="flex fm:flex-col justify-between gap-5 mb-5">
            <div class="fd:w-[50%]">
              <Input label="نام انگلیسی فروشگاه" v-model="shopName" id="shopName"/>
            </div>
            <div class="fd:w-[50%]">
              <Input label="نام پارسی فروشگاه" v-model="shopNameIr" id="shopNameIr"/>
            </div>
          </div>
          <div class="flex fm:flex-col justify-between gap-5 mb-5">
            <div class="fd:w-[50%]">
              <Input label="لینک بک اند فروشگاه" placeholder="https://shop.com" v-model="shopUrl" id="shopUrl"/>
            </div>
            <div class="fd:w-[50%]">
              <Input label="لینک فرانت فروشگاه" placeholder="https://shop.com" v-model="shopFrontUrl" id="shopFrontUrl"/>
            </div>
          </div>
          <div>
            <Textarea label="ادرس فروشگاه" :cols="3" :rows="3" v-model="shopAddress" :maxlength="400" :alert="shopAddress.length + '/400'" id="shopAddress"/>
          </div>
          <div class="flex fm:flex-col justify-between gap-5 mb-5">
            <div class="fd:w-[50%]">
              <Input type="file" label="لوگو" @change="getFile($event, 'logo')" :key="logoKey"  alert="فرمت تصویر: JPG,JPEG,PNG,SVG" id="logo"/>
              <div v-if="logoPreview">
                <img :src="logoPreview" class="w-[100px] h-[100px]"/>
              </div>
            </div>
            <div class="fd:w-[50%]">
              <Input type="file" label="لوگو فوتر" @change="getFile($event, 'footer_logo')" :key="footerLogoKey"  alert="فرمت تصویر: JPG,JPEG,PNG,SVG" id="logo"/>
              <div v-if="footerLogoPreview">
                <img :src="footerLogoPreview" class="w-[100px] h-[100px]"/>
              </div>
            </div>
          </div>
          <div class="flex fm:flex-col fd:items-center justify-between gap-5 mb-5">
            <Input type="number" label="تلفن پشتیبانی" v-model="supportPhone" id="supportPhone"/>
            <Input type="email" label="ایمیل" v-model="email" id="email"/>
          </div>
          <div class="flex fm:flex-col fd:items-center justify-between gap-5 mb-5">
            <div class="fd:w-[50%]">
              <Input label="لینک اینماد" v-model="enamad" id="enamad" :required="false"/>
            </div>
            <div class="fd:w-[50%]">
              <Input  label="لینک ستاد ساماندهی" v-model="samandehi" id="samandehi" :required="false"/>
            </div>
            <div class="fd:w-[50%]">
              <Input label="لینک مجوز کشوری" v-model="mojavez" id="mojavez" :required="false"/>
            </div>
          </div>
          <div class="flex fm:flex-col fd:items-center justify-between gap-5 mb-5">
            <Textarea label="درباره فروشگاه" v-model="shopBio" :maxlength="10000" :cols="4" :rows="4" :alert="shopBio.length + '/10000'" id="shopBio"/>
            <Textarea label="کپی رایت" v-model="copyRight" :maxlength="400" :cols="4" :rows="4" :alert="copyRight.length + '/400'" id="copyRight"/>
          </div>
          <div class="flex fm:flex-col fd:items-center justify-between gap-5 mb-5">
            <Editor :key="commentRuleKey" contentType="html" v-model="commentRule" placeholder="قوانین ثبت دیدگاه"></Editor>
          </div>
        </div>
        <div>
          <Button @click="insertStoreDetail()" text="ثبت" :btnLoading="btnLoading" />
        </div>
      </div>
      <div v-if="tabSelected === 3" class="mt-8">
        <div class="mb-5">
          <div class="flex fm:flex-col fd:items-center justify-between gap-5 mb-5">
            <Input label="تلگرام" :required="false" v-model="telegram" id="telegram"/>
            <Input label="واتساپ" :required="false" v-model="whatsapp" id="whatsapp"/>
          </div>
          <div class="flex fm:flex-col fd:items-center justify-between gap-5 mb-5">
            <Input label="اینستاگرام" :required="false" v-model="instagram" id="instagram"/>
            <Input label="فیسبوک" :required="false" v-model="facebook" id="facebook"/>
          </div>
          <div class="flex fm:flex-col fd:items-center justify-between gap-5 mb-5">
            <Input label="توییتر" :required="false" v-model="twitter" id="companyName"/>
          </div>
        </div>
        <div>
          <Button text="ثبت" @click="insertSocialMedia()" :btnLoading="btnLoading" />
        </div>
      </div>
      <div v-if="tabSelected === 4" class="mt-8">
        <div class="mb-5">
          <div class="flex fm:flex-col justify-between gap-5 mb-5">
            <div class="fd:w-[50%]">
              <Input label="متن اﻣﮑﺎن ﺗﺤﻮﯾﻞ اﮐﺴﭙﺮس" v-model="expressName" id="expressName"/>
            </div>
            <div class="fd:w-[50%]">
              <Input type="file" label="تصویر اﻣﮑﺎن ﺗﺤﻮﯾﻞ اﮐﺴﭙﺮس" @change="getFile($event, 'express')" alert="فرمت تصویر: JPG,JPEG,PNG,SVG | سایز:55x55px" :key="expressKey" id="expressImage"/>
              <div v-if="expressPreview">
                <img :src="expressPreview" class="w-[100px] h-[100px]"/>
              </div>
            </div>
          </div>
          <div class="flex fm:flex-col justify-between gap-5 mb-5">
            <div class="fd:w-[50%]">
              <Input label="متن ۷ روز ﻫﻔﺘﻪ، ۲۴ ﺳﺎﻋﺘﻪ" v-model="supportEveryDay" id="supportEveryDay"/>
            </div>
            <div class="fd:w-[50%]">
              <Input type="file" label="تصویر ۷ روز ﻫﻔﺘﻪ، ۲۴ ﺳﺎﻋﺘﻪ" @change="getFile($event, 'supportEveryDay')" alert="فرمت تصویر: JPG,JPEG,PNG,SVG | سایز:55x55px" :key="supportEveryDayKey" id="supportEveryDayImage"/>
              <div v-if="supportEveryDayPreview">
                <img :src="supportEveryDayPreview" class="w-[100px] h-[100px]"/>
              </div>
            </div>
          </div>
          <div class="flex fm:flex-col justify-between gap-5 mb-5">
            <div class="fd:w-[50%]">
              <Input label="متن هفت روز ضمانت بازگشت کالا" v-model="guarantee" id="guarantee"/>
            </div>
            <div class="fd:w-[50%]">
              <Input type="file" label="تصویر هفت روز ضمانت بازگشت کالا" @change="getFile($event, 'guarantee')" alert="فرمت تصویر: JPG,JPEG,PNG,SVG | سایز:55x55px" :key="guaranteeKey" id="guaranteeImage"/>
              <div v-if="guaranteePreview">
                <img :src="guaranteePreview" class="w-[100px] h-[100px]"/>
              </div>
            </div>
          </div>
          <div class="flex fm:flex-col justify-between gap-5 mb-5">
            <div class="fd:w-[50%]">
              <Input label="متن ﺿﻤﺎﻧﺖ اﺻﻞ ﺑﻮدن ﮐﺎﻟﺎ" v-model="original" id="original"/>
            </div>
            <div class="fd:w-[50%]">
              <Input type="file" label="تصویر ﺿﻤﺎﻧﺖ اﺻﻞ ﺑﻮدن ﮐﺎﻟﺎ" @change="getFile($event, 'original')" alert="فرمت تصویر: JPG,JPEG,PNG,SVG | سایز:55x55px" :key="originalKey" id="original"/>
              <div v-if="originalPreview">
                <img :src="originalPreview" class="w-[100px] h-[100px]"/>
              </div>
            </div>
          </div>
        </div>
        <div>
          <Button text="ثبت" @click="insertFooterBox()" :btnLoading="btnLoading" />
        </div>
      </div>
      <div v-if="tabSelected === 5" class="mt-8">
        <div class="mb-5">
          <label class="fm:text-sm">پنل پیامک<b class="text-red-500 !font-bold">*</b></label>
          <select v-model="smsDriver" @change="changeSmsDriver($event)" id="companyType" class="fm:text-sm border border-gray-200 p-2 outline-none rounded-lg w-full">
            <option value="" selected disabled>--- انتخاب کنید ---</option>
            <option value="Ippanel">Ippanel</option>
          </select>
        </div>
        <div v-if="smsDriver !== ''">
          <div class="mb-5">
            <Input :label="smsDriver === 'Ippanel' ? 'api key' : 'نام کاربری'" v-model="nameKey" id="nameKey"/>
          </div>
          <div class="mb-5">
            <Input type="number" label="شماره تلفن" v-model="smsPhone" id="smsPhone" placeholder="مثال: 981000123456"/>
          </div>
          <div class="mb-5" v-if="smsDriver !== 'Ippanel'">
            <Input type="password" label="رمز عبور" v-model="smsPassword" id="smsPassword"/>
          </div>
        </div>

        <div>
          <Button text="ثبت" @click="insertSMS()" :btnLoading="btnLoading" />
        </div>
      </div>
      <div v-if="tabSelected === 6" class="mt-8">
        <div class="flex items-center gap-5 mb-5">
            <Input type="number" label="حداکثر مبلغ شارژ کیف پول (ریال)" v-model="maxAmount" id="maxAmount"/>
            <Input type="number" label="حداقل مبلغ شارژ کیف پول (ریال)" v-model="minAmount" id="minAmount"/>
        </div>
        <div class="flex items-center gap-5 mb-5">
          <Input type="number" label="مهلت زمان برای مرجوع کردن سفارش" v-model="returnedDays" id="returnedDays"/>
        </div>
        <div class="flex flex-col border p-3 rounded-lg mb-5">
          <div class="mb-5">
            <span class="fm:text-sm !font-medium">تنظیمات ایمیل</span>
          </div>
          <div class="flex flex-col gap-5 mb-5">
            <div class="flex items-center gap-5 mb-5">
              <Input label="Mail mailer" v-model="mailMailer" placeholder="مثال: smtp" id="mailMailer"/>
              <Input label="هاست" v-model="host" id="host"/>
              <Input label="پورت" placeholder="مثال: 587, 2525, 25" v-model="port" id="port"/>
            </div>
            <div class="flex items-center gap-5 mb-5">
              <Input label="نام کاربری" v-model="mailUsername" id="mailUsername"/>
              <Input type="password" label="رمز عبور" v-model="mailPassword" id="mailPassword"/>
              <Input label="ادرس" placeholder="example@example.com" v-model="mailAddress" id="mailAddress"/>
            </div>
          </div>
        </div>

        <div>
          <Button text="ثبت" @click="insertOtherSetting()" :btnLoading="btnLoading" />
        </div>

      </div>
    </div>
    <Meta :title="$store.state.siteName + ` |  تنظیمات سایت `"/>
    <Loading :loading="loading" />
    <ValidationError />
  </div>
</template>

<script>
export default{name: "ShopConfig"}
</script>

<script setup>
import {ref, onMounted} from 'vue';
import Meta from "@/components/Meta.vue";
import Editor from "@/components/Editor.vue";
import Input from "@/components/Input.vue";
import Loading from "@/components/Loading.vue";
import Textarea from "@/components/Textarea.vue";
import Button from "@/components/Button.vue";
import ValidationError from "@/components/ValidationError.vue";
import axios from "@/plugins/axios";
import store from "@/store";
import Toast from "@/plugins/toast";


let btnLoading = ref(false)
let loading = ref(false)
let model = ref('config')

let tabSelected = ref(1);

let companyName = ref('');
let companyType = ref('');
let nationalIdentityNumber = ref('');
let registrationNumber = ref('');
let economicNumber = ref('');
let address = ref('');
let postalCode = ref('');
let phone = ref('');
let fax = ref('');
let signature = ref('');
let signaturePreview = ref(null);
let signatureKey = ref(0);


let shopName = ref('');
let shopNameIr = ref('');
let shopAddress = ref('');
let shopUrl = ref('');
let shopFrontUrl = ref('');
let supportPhone = ref('');
let email = ref('');
let logo = ref('');
let logoPreview = ref('');
let logoKey = ref('');
let footerLogo = ref('');
let footerLogoPreview = ref('');
let footerLogoKey = ref('');
let enamad = ref('');
let samandehi = ref('');
let mojavez = ref('');
let copyRight = ref('');
let shopBio = ref('');
let commentRule = ref('');
let commentRuleKey = ref(0)

let telegram = ref('');
let whatsapp = ref('');
let instagram = ref('');
let facebook = ref('');
let twitter = ref('');

let expressName = ref('');
let expressImage = ref('');
let expressPreview = ref(null);
let expressKey = ref(0);
let supportEveryDay = ref('');
let supportEveryDayImage = ref('');
let supportEveryDayPreview = ref(null);
let supportEveryDayKey = ref(0);
let guarantee = ref('');
let guaranteeImage = ref('');
let guaranteePreview = ref(null);
let guaranteeKey = ref(0);
let original = ref('');
let originalImage = ref('');
let originalPreview = ref(null);
let originalKey = ref(0);

let smsDriver = ref('');
let nameKey = ref('');
let smsPhone = ref('');
let smsPassword = ref('');

let maxAmount = ref('');
let minAmount = ref('');
let returnedDays = ref('');
let mailMailer = ref('');
let host = ref('');
let port = ref('');
let mailUsername = ref('');
let mailPassword = ref('');
let mailAddress = ref('');

onMounted(async()=>{
  await showLegal();
});

async function showLegal(_loading=true){
  loading.value = _loading;
  await axios.get(`${store.state.api}${model.value}/legal`).then(resp=>{
    let data = resp.data.data;
    if(data !== null){
      data = JSON.parse(data)
      companyName.value=data.company_name;
      companyType.value=data.company_type;
      nationalIdentityNumber.value=data.national_identity_number;
      registrationNumber.value=data.registration_number;
      economicNumber.value=data.economic_number;
      address.value=data.address;
      postalCode.value=data.postal_code;
      phone.value=data.phone;
      fax.value=data.fax;
      signature.value=data.signature;
      signaturePreview.value=data.signature;
      signatureKey.value++;
    }
  }).catch(err=>{})
  loading.value = false;
}
async function insertLegal(){
  btnLoading.value = true;
  let data = {
    company_name:companyName.value,
    company_type:companyType.value,
    national_identity_number:nationalIdentityNumber.value,
    registration_number:registrationNumber.value,
    economic_number:economicNumber.value,
    address:address.value,
    postal_code:postalCode.value,
    phone:phone.value,
    fax:fax.value,
    signature:signature.value,
  }
  await axios.post(`${store.state.api}${model.value}/legal`,data).then(async resp=>{
    Toast.success();
    await showLegal(false)
  }).catch(err=>{
    store.commit('handleError',err)
  })
  btnLoading.value = false;
}

async function showStoreDetail(_loading=true){
  commentRuleKey.value ++;
  loading.value = _loading;
  await axios.get(`${store.state.api}${model.value}/store-detail`).then(resp=>{
    let data = resp.data.data;
    if(data !== null){
      data = JSON.parse(data)
      shopName.value=data.shop_name;
      shopNameIr.value=data.shop_name_ir;
      shopAddress.value=data.shop_address;
      shopUrl.value=data.shop_url;
      shopFrontUrl.value=data.shop_front_url;
      supportPhone.value=data.support_phone;
      email.value=data.email;
      copyRight.value=data.copy_right;
      shopBio.value=data.shop_bio;
      commentRule.value=data.comment_rule;
      enamad.value=data.enamad === null ? '' : data.enamad;
      samandehi.value=data.samandehi === null ? '' : data.samandehi;
      mojavez.value=data.mojavez === null ? '' : data.mojavez;
      logo.value=data.logo;
      logoPreview.value=data.logo;
      logoKey.value++;
      footerLogo.value=data.footer_logo;
      footerLogoPreview.value=data.footer_logo;
      footerLogoKey.value++;
    }
  }).catch(err=>{})
  loading.value = false;
}
async function insertStoreDetail(){
  btnLoading.value = true;
  let data = {
    shop_name:shopName.value,
    shop_name_ir:shopNameIr.value,
    shop_address:shopAddress.value,
    shop_url:shopUrl.value,
    shop_front_url:shopFrontUrl.value,
    support_phone:supportPhone.value,
    email:email.value,
    logo:logo.value,
    footer_logo:footerLogo.value,
    enamad:enamad.value,
    samandehi:samandehi.value,
    mojavez:mojavez.value,
    copy_right:copyRight.value,
    shop_bio:shopBio.value,
    comment_rule:commentRule.value,
  }
  await axios.post(`${store.state.api}${model.value}/insert-store-detail`,data).then(async resp=>{
    Toast.success();
    await showStoreDetail(false)
  }).catch(err=>{
    store.commit('handleError',err)
  })
  btnLoading.value = false;
}

async function showSocialMedia(_loading=true){
  loading.value = _loading;
  await axios.get(`${store.state.api}${model.value}/social-media`).then(resp=>{
    let data = resp.data.data;
    if(data !== null){
      data = JSON.parse(data)
      telegram.value=data.telegram;
      whatsapp.value=data.whatsapp;
      instagram.value=data.instagram;
      facebook.value=data.facebook;
      twitter.value=data.twitter;
    }
  }).catch(err=>{})
  loading.value = false;
}
async function insertSocialMedia(){
  btnLoading.value = true;
  let data = {
    telegram:telegram.value,
    whatsapp:whatsapp.value,
    instagram:instagram.value,
    facebook:facebook.value,
    twitter:twitter.value,
  }
  await axios.post(`${store.state.api}${model.value}/social-media`,data).then(async resp=>{
    Toast.success();
    await showSocialMedia(false)
  }).catch(err=>{
    store.commit('handleError',err)
  })
  btnLoading.value = false;
}

async function showFooterBox(_loading=true){
  loading.value = _loading;
  await axios.get(`${store.state.api}${model.value}/footer-box`).then(resp=>{
    let data = resp.data.data;
    if(data !== null){
      data = JSON.parse(data)

      expressName.value=data.express_name;
      expressImage.value=data.express_image;
      expressPreview.value=data.express_image;
      expressKey.value++;

      supportEveryDayKey.value=data.support_every_day;
      supportEveryDayImage.value=data.support_every_day_image;
      supportEveryDayPreview.value=data.support_every_day_image;
      supportEveryDayKey.value++;

      guarantee.value=data.guarantee;
      guaranteeImage.value=data.guarantee_image;
      guaranteePreview.value=data.guarantee_image;
      guaranteeKey.value++;

      original.value=data.original;
      originalImage.value=data.original_image;
      originalPreview.value=data.original_image;
      originalKey.value++;
    }
  }).catch(err=>{})
  loading.value = false;
}
async function insertFooterBox(){
  btnLoading.value = true;
  let data = {
    express_name:expressName.value,
    express_image:expressImage.value,
    support_every_day:supportEveryDay.value,
    support_every_day_image:supportEveryDayImage.value,
    guarantee:guarantee.value,
    guarantee_image:guaranteeImage.value,
    original:original.value,
    original_image:originalImage.value,

  }
  await axios.post(`${store.state.api}${model.value}/footer-box`,data).then(async resp=>{
    Toast.success();
    await showFooterBox(false)
  }).catch(err=>{
    store.commit('handleError',err)
  })
  btnLoading.value = false;
}

async function showSMS(_loading=true){
  loading.value = _loading;
  await axios.get(`${store.state.api}${model.value}/sms`).then(resp=>{
    let data = resp.data.data;
    if(data !== null){
      data = JSON.parse(data)
      smsDriver.value=data.sms_driver;
      nameKey.value=data.name_key;
      smsPhone.value=data.sms_phone;
      smsPassword.value=data.sms_password;
    }
  }).catch(err=>{})
  loading.value = false;
}
async function insertSMS(){
  btnLoading.value = true;
  let data = {
    sms_driver:smsDriver.value,
    name_key:nameKey.value,
    sms_phone:smsPhone.value,
    sms_password:smsPassword.value,
  }
  await axios.post(`${store.state.api}${model.value}/sms`,data).then(async resp=>{
    Toast.success();
    await showSMS(false)
  }).catch(err=>{
    store.commit('handleError',err)
  })
  btnLoading.value = false;
}

async function showOtherSetting(_loading=true){
  loading.value = _loading;
  await axios.get(`${store.state.api}${model.value}/other-setting`).then(resp=>{
    let data = resp.data.data;
    if(data !== null){
      data = JSON.parse(data)
      maxAmount.value=data.max_amount;
      minAmount.value=data.min_amount;
      returnedDays.value=data.returned_days;
      mailMailer.value=data.mail_mailer;
      host.value=data.host;
      port.value=data.port;
      mailUsername.value=data.mail_username;
      mailPassword.value=data.mail_password;
      mailAddress.value=data.mail_address;
    }
  }).catch(err=>{})
  loading.value = false;
}
async function insertOtherSetting(){
  btnLoading.value = true;
  let data = {
    max_amount:maxAmount.value,
    min_amount:minAmount.value,
    returned_days:returnedDays.value,
    mail_mailer:mailMailer.value,
    host:host.value,
    port:port.value,
    mail_username:mailUsername.value,
    mail_password:mailPassword.value,
    mail_address:mailAddress.value,
  }
  await axios.post(`${store.state.api}${model.value}/other-setting`,data).then(async resp=>{
    Toast.success();
    await showOtherSetting(false)
  }).catch(err=>{
    store.commit('handleError',err)
  })
  btnLoading.value = false;
}

async function changeTypeTab(tab){
  tabSelected.value = tab;
  if(tab === 1){
    await showLegal()
  }else if(tab === 2){
    await showStoreDetail();
  }else if(tab === 3){
    await showSocialMedia();
  }else if(tab === 4){
    await showFooterBox();
  }else if(tab === 5){
    await showSMS();
  }else if(tab === 6){
    await showOtherSetting();
  }
}


async function getFile(event, type){
  let image = event.target.files[0];
  if(type === 'legal'){
    signature.value = await makeBase64Image(image);
    signaturePreview.value = URL.createObjectURL(image);
    signatureKey.value ++;
  }else if(type === 'logo'){
    logo.value = await makeBase64Image(image);
    logoPreview.value = URL.createObjectURL(image);
    logoKey.value ++;
  }else if(type === 'footer_logo'){
    footerLogo.value = await makeBase64Image(image);
    footerLogoPreview.value = URL.createObjectURL(image);
    footerLogoKey.value ++;
  }else if(type === 'express'){
    expressImage.value = await makeBase64Image(image);
    expressPreview.value = URL.createObjectURL(image);
    expressKey.value ++;
  }else if(type === 'supportEveryDay'){
    supportEveryDayImage.value = await makeBase64Image(image);
    supportEveryDayPreview.value = URL.createObjectURL(image);
    supportEveryDayKey.value ++;
  }else if(type === 'guarantee'){
    guaranteeImage.value = await makeBase64Image(image);
    guaranteePreview.value = URL.createObjectURL(image);
    guaranteeKey.value ++;
  }else if(type === 'original'){
    originalImage.value = await makeBase64Image(image);
    originalPreview.value = URL.createObjectURL(image);
    originalKey.value ++;
  }
}

async function makeBase64Image(file){
  const blob = file;
  return new Promise((resolve) => {
    const reader = new FileReader();
    reader.readAsDataURL(blob);
    reader.onloadend = () => {
      const base64data = reader.result;
      resolve(base64data);
      return base64data
    }
  });
}

function changeSmsDriver(event){
  smsDriver.value = event.target.value;
  nameKey.value = '';
  smsPhone.value = '';
  smsPassword.value = '';
}

</script>