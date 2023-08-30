<template>
  <div>
    <div class="container mx-auto fd:mt-12">
      <div class="mx-4">
        <div class="flex justify-center">
          <div class="w-[30%] fm:w-full border border-gray-200 px-4 py-8 flex flex-col gap-5 rounded-lg">
            <div class="flex justify-between">
              <div class="w-full flex justify-center">
                <img :src="$store.state.configSite.logo" class="w-[115px] h-[30px]" />
              </div>
              <span class="flex items-center cursor-pointer" v-if="steep===2" @click="steep = 1"><i class="far fa-arrow-left text-lg text-blue-400"></i></span>
            </div>
<!--            Get phone number-->
            <div :class="['flex flex-col gap-5', steep === 1 ? 'block' : 'hidden']">
              <div>
                <span class="!font-medium text-xl">ورود | ثبت‌ نام</span>
              </div>
              <div>
                <span class="text-sm text-gray-500">سلام!</span>
              </div>
              <div>
                <Input
                    v-model="mobile"
                    type="number"
                    id="mobile"
                    label="لطفا شماره موبایل خود را وارد کنید"
                    my_class="!p-3"
                    placeholder="شماره موبایل"
                    :required="false"
                />
              </div>
              <div>
                <Button text="ادامه" my_class="!w-full" :btnLoading="btnLoading" @callback="sendCode()"></Button>
              </div>
              <div>
              <span class="text-sm text-gray-500">
                ورود شما به معنای پذیرش <routerLink :to="{name:'PageDetail', params:{id:6}}" target="_blank" class="text-blue-400">شرایط {{$store.state.configSite.shop_name_ir}}</routerLink> و <routerLink :to="{name:'PageDetail', params:{id:7}}" target="_blank" class="text-blue-400">قوانین حریم‌ خصوصی</routerLink> است
              </span>
              </div>
            </div>
<!--            End getting phone number-->
            <!--            Send code-->
            <div :class="['flex flex-col gap-5', steep === 2 ? 'block' : 'hidden']">
              <div>
                <span class="!font-medium text-xl" v-if="!userExist">کد تایید و </span>
                <span class="!font-medium text-xl">رمز عبور </span>
                <span class="!font-medium text-xl">را وارد کنید </span>
              </div>

              <div v-if="!userExist">
                <Input
                    v-model="smsCode"
                    type="number"
                    id="smsCode"
                    :label="`کد تایید برای شماره ${mobile} پیامک شد`"
                    :required="false"
                    my_class="!p-3"
                />
              </div>
              <div class="flex items-center gap-1" v-if="!userExist">
                <span class="text-sm text-blue-400">ارسال دوباره کد تایید در: </span>
                <span class="text-sm text-yellow-400 !font-medium" v-if="time > 0">{{time}}</span>
                <Button v-if="time <= 0" text="ارسال دوباره کد" my_class="!bg-white !text-red-500 !p-1 !text-sm" :btnLoading="reSendBtnLoading" @callback="reSendCode()" />
              </div>

              <div>
                <Input
                    v-model="password"
                    type="password"
                    id="password"
                    label="رمز عبور"
                    :required="false"
                    my_class="!p-3"
                />
              </div>

              <div v-if="userExist">
                <span class="text-blue-400 !font-medium text-sm">
                  <routerLink :to="{name:'ForgotPassword'}">آیا رمز عبور خود را فراموش کرده اید؟</routerLink>
                </span>
              </div>
              <div>
                <Button text="تایید" my_class="!w-full" :btnLoading="btnLoading" @callback="loginRegister()"></Button>
              </div>
            </div>
            <!--            End sending code -->
          </div>
        </div>
      </div>
    </div>
    <Meta :title="$store.state.siteName + ` | ورود | ثبت نام `"/>
  </div>
</template>

<script>
export default{name: "Login"}
</script>

<script setup>
import {ref} from 'vue';
import store from '@/store';
import Input from "@/components/Input.vue";
import Meta from "@/components/Meta.vue";
import Button from "@/components/Button.vue";
import axios from '@/plugins/axios';
import Toast from "@/plugins/toast";
import { useRouter } from "vue-router";
import route from "@/plugins/route";
const router = useRouter();

const timer = 60;
let time = ref(timer)
let steep = ref(1)
let mobile = ref('')
let smsCode = ref('')
let password = ref('')
let userExist = ref(true)
let btnLoading = ref(false)
let reSendBtnLoading = ref(false)

async function sendCode(){
  btnLoading.value = true;
  await axios.post(`${store.state.api}send-code`,{mobile:mobile.value,pattern:process.env.VUE_LOGIN_REGISTER_PATTERN}).then(resp=>{
    userExist.value = resp.data.user_exists;
    steep.value = 2;
    if(!userExist.value){
      _timeOut();
    }

  }).catch(err=>{
    let response = err.response;
    if(response !== undefined && response.status === 422){
      let errors = Object.values(response.data.errors);
      Toast.error(errors[0][0])
    }else{
      Toast.error('یک خطای غیرمنتظره رخ داده است')
    }

  })
  btnLoading.value = false;
}

async function reSendCode(){
  reSendBtnLoading.value = true;
  time.value = timer;
  await axios.post(`${store.state.api}resend-code`,{mobile:mobile.value,pattern:process.env.VUE_LOGIN_REGISTER_PATTERN}).then(resp=>{
    Toast.success("کد با موفقیت ارسال شد")
    _timeOut();
  }).catch(err=>{
    let response = err.response;
    if(response !== undefined && response.status === 422){
      let errors = Object.values(response.data.errors);
      Toast.error(errors[0][0])
    }else{
      Toast.error('یک خطای غیرمنتظره رخ داده است')
    }

  })
  reSendBtnLoading.value = false;
}

async function loginRegister(){
  btnLoading.value = true;
  await axios.post(`${store.state.api}login-register`,{mobile:mobile.value, password:password.value, code:smsCode.value}).then(async resp=>{
    const data = resp.data.data
    if(data.status === 'no'){
      router.push({name:'HandleError',params:{code:403,msg:'دسترسی شما غیر فعال میباشد برای بررسی بیشتر با ما تماس بگیرید'}})
    }else{
      await store.commit('setStorage',[true,data])
      Toast.success("با موفقیت انجام شد")
      setTimeout(()=>{
        router.push({name:'Dashboard'})
      },1000)
    }
  }).catch(err=>{
    let response = err.response;
    if(response !== undefined){
      if(response.data.error === 'invalid code'){
        Toast.error('کد تایید وارد شده معتبر نیست')
      }else if(response.status === 422){
        let errors = Object.values(response.data.errors);
        Toast.error(errors[0][0])
      }else if(response.data.error === "invalid login"){
        Toast.error('رمز عبور یا نام کاربری اشتباه است')
      }
    }else{
      Toast.error('یک خطای غیرمنتظره رخ داده است')
    }
    store.commit('clearStorage')
  })
  btnLoading.value = false;
}

function _timeOut(){
  let timeInterVal = setInterval(()=>{
    if(time.value <= 0){
      clearInterval(timeInterVal);
    }else{
      time.value--;
    }
  },1000)
}
</script>