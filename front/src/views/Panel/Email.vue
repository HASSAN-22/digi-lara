<template>
  <div class="mt-10 px-8">
    <div class="flex justify-center">
      <span class="!font-medium text-lg">لیست ایمیل ها</span>
    </div>
    <div class="mt-10 px-3 mb-3">
      <div class="mb-3 mr-2 flex justify-between fm:flex-col items-center">
        <div class="flex gap-2 items-center">
          <UserCanAction action="create" @create="create()" permission="create_emails" />
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
              'توسط',
              'به ایمیل',
              'موضوع',
              'پیام',
              'وضعیت',
              'ارسال دوباره'
          ]" />
          <tbody class="block lg:table-row-group xl:table-row-group 2xl:table-row-group md:table-row-group">
          <tr v-for="item in allData" :key="item.id" class="border border-gray-200 block lg:table-row xl:table-row 2xl:table-row md:table-row">
            <Td title="تویط" class="fm:text-sm">{{item.user}}</Td>
            <Td title="به ایمیل" class="fm:text-sm">{{item.email}}</Td>
            <Td title="موضوع" class="fm:text-sm">{{item.issue}}</Td>
            <Td title="پیام" class="fm:text-sm">
              <Button :my_class="'!bg-white border border-blue-500 !py-2 !px-2 fm:py-1 fm:px-1'" @click="showMessage(item.message)"><i class="far fm:text-sm fa-eye text-blue-500"></i></Button>
            </Td>
            <Td title="وضعیت" class="fm:text-sm">{{item.ir_status}}</Td>
            <Td title="ارسال دوباره">
              <div class="flex items-center justify-center gap-2" v-if="['no','pending'].includes(item.status)">
                <Button v-if="$store.getters.userCan('create_emails')" :my_class="'!bg-white border border-green-500 !py-2 !px-2 fm:py-1 fm:px-1'" @click="reSend(item.id)"><i class="far fm:text-sm fa-sync text-green-500"></i></Button>
                <Button v-else :my_class="'!cursor-not-allowed !bg-white border border-black !py-2 !px-2 fm:py-1 fm:px-1 '"><i class="far fm:text-sm fa-sync text-black"></i></Button>
              </div>
              <div class="flex items-center justify-center gap-2" v-else>
                ---
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
    <Modal title="مشاهده پیام" ref="showMessageRef">
      <div>
        <p class="break-words">
          {{emailMessage}}
        </p>
      </div>
    </Modal>

    <Modal title="ارسال ایمیل" save="ارسال" permission="create_sms" :btnLoading="btnLoading" @callback="insert()" ref="openModal">
      <div class="mb-5">
        <Input label="موضوع" placeholder="موضوع" v-model="issue" id="issue" />
      </div>
      <div class="mb-5 w-full">
        <label class="fm:text-sm" for="type">نوع<b class="text-red-500 !font-bold">*</b></label>
        <select v-model="type" id="type" @change="getEmails($event)" class="fm:text-sm border border-gray-200 p-2 outline-none rounded-lg w-full">
          <option value="" selected disabled>--- انتخاب کنید ---</option>
          <option value="newsletter">خبرنامه</option>
          <option value="user">کاربران</option>
          <option value="single">تکی</option>
        </select>
      </div>
      <div class="mb-5 w-full" v-if="['user','newsletter'].includes(type)">
        <label class="fm:text-sm">ایمیل ها</label>
        <Multiselect
            v-model="emails"
            :options="allEmail"
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
      <div class="mb-5" v-else-if="type === 'single'">
        <Input label="ایمیل" placeholder="ایمیل" v-model="email" id="email" />
      </div>
      <div class="mb-5">
        <Textarea label="پیام" v-model="message" id="message" :maxlength="500" :alert="message.length + '/500'"/>
      </div>
    </Modal>
    <Meta :title="$store.state.siteName + ` | لیست ایمیل ها `"/>
    <Loading :loading="loading" />
    <ValidationError />
  </div>
</template>

<script>
export default{name: "Color"}
</script>

<script setup>
import {ref, onMounted, defineExpose} from 'vue';
import Meta from "@/components/Meta.vue";
import Td from "@/components/Td.vue";
import Thead from "@/components/Thead.vue";
import UserCanAction from "@/components/UserCanAction.vue";
import Input from "@/components/Input.vue";
import Textarea from "@/components/Textarea.vue";
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
let model = ref('email')
let search = ref('')
let emailMessage = ref('')

let issue = ref('');
let message = ref('');
let type = ref('');
let email = ref('');
let emails = ref([]);

let allEmail = ref([]);
let allData = ref([]);
let openModal = ref(null)
let showMessageRef = ref(null)

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
    Toast.success("با موفقیت ثبت شد و در بکگراند ارسال انجام میشود");
    getData(false, store.state.current)
    clearData();
  }).catch(err=>{
    store.commit('handleError',err)
  })
  btnLoading.value = false;
}

async function getEmails(event){
  let type = event.target.value;
  loading.value = true;
  await axios.get(`${store.state.api}${model.value}/get-emails?type=${type}`,formData()).then(resp=>{
    allEmail.value = resp.data.data;
  }).catch(err=>{
    store.commit('handleError',err)
  })
  loading.value = false;
}

function formData(){
  let _emails = [];
  if(type.value === 'single'){
    _emails = [email.value]
  }else{
    _emails= emails.value.map(item=>item.label)
  }
  return {
    message:message.value,
    issue:issue.value,
    emails:_emails,
  };
}

function clearData(){
  message.value = '';
  issue.value = '';
  emails.value = [];
  email.value = '';
  type.value = '';
  openModal.value.toggleModal();
}

function showMessage(_message){
  emailMessage.value = _message;
  showMessageRef.value.toggleModal();
}

</script>
<style src="@vueform/multiselect/themes/default.css"></style>