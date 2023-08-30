<template>
  <div>
    <div class="container mx-auto">
      <div class="mx-4 mt-10 border border-gray-100 rounded-lg p-4">
        <div>
          <h3 class="text-lg !font-medium fm:text-md"> تماس با {{$store.state.siteName}}</h3>
        </div>
        <div class="mt-14 flex justify-between items-center">
          <span class="fm:text-sm">لطفاً پیش از ارسال ایمیل یا تماس تلفنی، ابتدا <a href="" class="text-blue-400">پرسش های متداول</a> را مشاهده کنید.</span>
          <a href="" class="text-blue-400 fm:text-sm border border-blue-400 p-2 text-blue-400 rounded-lg">پرسش های متداول</a>
        </div>
        <div class="flex fm:flex-col gap-5 mt-20">
          <div class="fd:w-[50%]">
            <div>
              <h3 class="text-lg !font-medium fm:text-md"> اطلاعات {{$store.state.siteName}}</h3>
            </div>
            <div class="mt-8 flex flex-col gap-6">
              <div class="flex items-center gap-1">
                <span><i class="far fa-envelope text-2xl text-blue-500 !font-medium"></i></span>
                <span class="fm:text-sm">{{store.state.configSite.email}}</span>
              </div>
              <div class="flex items-center gap-1">
                <span><i class="far fa-mobile text-2xl text-blue-500 !font-medium"></i></span>
                <span class="fm:text-sm">{{store.state.configSite.support_phone}}</span>
              </div>
              <div class="flex items-center gap-1">
                <span><i class="far fa-location-dot text-2xl text-blue-500 !font-medium"></i></span>
                <span class="fm:text-sm">{{store.state.configSite.shop_address}}</span>
              </div>
            </div>
          </div>
          <div class="fd:w-[50%]">
            <div class="mb-5">
              <Input label="موضوع" v-model="subject" id="subject" />
            </div>
            <div class="mb-5">
              <Input type="email" label="ایمیل" v-model="email" id="email" />
            </div>
            <div class="mb-5">
              <Input label="نام و نام خانوادگی" v-model="username" id="username" />
            </div>
            <div class="mb-5">
              <Input type="numeric" label="تلفن تماس" v-model="mobile" id="mobile" />
            </div>
            <div class="mb-5">
              <Textarea label="متن پیام" v-model="message" id="message" :cols="4" :rows="4" :maxlength="1000" :alert="message.length + '/1000'" />
            </div>
            <div class="mb-5">
              <Button text="ارسال" :btnLoading="btnLoading" @click="sendContact()" my_class="w-full" />
            </div>
          </div>
        </div>
      </div>
    </div>
    <Meta :title="$store.state.siteName + ` تماس با ما `"/>
    <Loading :loading="loading" />
    <ValidationError/>
  </div>
</template>

<script>
export default{name: "ContactUs"}
</script>

<script setup>
import {ref} from "vue";
import Meta from "@/components/Meta.vue";
import Loading from "@/components/Loading";
import Input from "@/components/Input";
import Textarea from "@/components/Textarea";
import Button from "@/components/Button";
import ValidationError from "@/components/ValidationError";
import axios from "@/plugins/axios";
import store from "@/store";
import Toast from "@/plugins/toast";

let loading = ref(false);
let btnLoading = ref(false);
let subject = ref('');
let email = ref('');
let username = ref('');
let mobile = ref('');
let message = ref('');

async function sendContact(){
  btnLoading.value = true;
  let frm = {
    subject:subject.value,
    email:email.value,
    username:username.value,
    mobile:mobile.value,
    message:message.value,
  }
  await axios.post(`${store.state.api}send-contact-us`,frm).then(resp=>{
    Toast.success('با موفقیت ارسال شد');
    subject.value = '';
    email.value = '';
    username.value = '';
    mobile.value = '';
    message.value = '';
  }).catch(err=>{
    store.commit('handleError',err)
  })
  btnLoading.value = false;
}

</script>

<style scoped>
#router-link {
  display: inline-block;
}
</style>