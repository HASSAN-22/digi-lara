<template>
  <div class="mt-10 px-8">
    <div class="flex justify-center">
      <span class="!font-medium text-lg">لیست اس ام اس ها</span>
    </div>
    <div class="mt-10 px-3 mb-3">
      <div class="mb-3 mr-2 flex justify-between fm:flex-col items-center">
        <div class="flex gap-2 items-center">
          <UserCanAction action="create" @create="create()" permission="create_sms" />
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
              'موبایل',
              'پیام',
          ]" />
          <tbody class="block lg:table-row-group xl:table-row-group 2xl:table-row-group md:table-row-group">
          <tr v-for="item in allData" :key="item.id" class="border border-gray-200 block lg:table-row xl:table-row 2xl:table-row md:table-row">
            <Td title="موبایل" class="fm:text-sm">{{item.mobile}}</Td>
            <Td title="پیام" class="fm:text-sm">
              <Button :my_class="'!bg-white border border-blue-500 !py-2 !px-2 fm:py-1 fm:px-1'" @click="showMessage(item.message)"><i class="far fm:text-sm fa-eye text-blue-500"></i></Button>
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

    <Modal title="ارسال اس ام اس" save="ارسال" permission="create_sms" :btnLoading="btnLoading" @callback="insert()" ref="openModal">
      <div class="mb-5 w-full">
        <label class="fm:text-sm" for="type">نوع<b class="text-red-500 !font-bold">*</b></label>
        <select v-model="type" id="type" class="fm:text-sm border border-gray-200 p-2 outline-none rounded-lg w-full">
          <option value="" selected disabled>--- انتخاب کنید ---</option>
          <option value="mobile">موبایل ها</option>
          <option value="single">تکی</option>
        </select>
      </div>
      <div class="mb-5 w-full" v-if="type === 'mobile'">
        <label class="fm:text-sm">موبایل ها</label>
        <Multiselect
            v-model="mobiles"
            :options="allMobiles"
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
        <Input label="موبایل" placeholder="موبایل" v-model="mobile" id="mobile" />
      </div>
      <div class="mb-5">
        <Textarea label="پیام" v-model="message" id="message" :maxlength="500" :alert="message.length + '/500'"/>
      </div>
    </Modal>
    <Meta :title="$store.state.siteName + ` | لیست اس ام اس ها `"/>
    <Loading :loading="loading" />
    <ValidationError />
  </div>
</template>

<script>
export default{name: "SMS"}
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
let model = ref('sms')
let search = ref('')
let emailMessage = ref('')

let message = ref('');
let type = ref('');
let mobile = ref('');
let mobiles = ref([]);

let allMobiles = ref([]);
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
    let data = resp.data.data;
    allData.value = data.sms.data;
    allMobiles.value = data.mobiles;
    store.commit('paginate',data.sms);
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

function formData(){
  let _mobiles = [];
  if(type.value === 'single'){
    _mobiles = [mobile.value]
  }else{
    _mobiles= mobiles.value.map(item=>item.label)
  }
  return {
    message:message.value,
    mobiles:_mobiles,
  };
}

function clearData(){
  message.value = '';
  mobiles.value = [];
  mobile.value = '';
  type.value = '';
  openModal.value.toggleModal();
}

function showMessage(_message){
  emailMessage.value = _message;
  showMessageRef.value.toggleModal();
}

</script>
<style src="@vueform/multiselect/themes/default.css"></style>