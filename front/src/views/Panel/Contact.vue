<template>
  <div class="mt-10 px-8">
    <div class="flex justify-center">
      <span class="!font-medium text-lg">لیست تماس با ما</span>
    </div>
    <div class="mt-10 px-3 mb-3">
      <div class="mb-3 mr-2 flex justify-between fm:flex-col items-center">
        <div class="flex gap-2 items-center">
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
              'نام و نام خانوادگی',
              'موضوع',
              'ایمیل',
              'شماره تماس',
              'وضعیت پاسخ',
              'تاریخ',
              'عملیات'
          ]" :except="$store.getters.userCan('update_contacts') ? [] : ['عملیات']" />
          <tbody class="block lg:table-row-group xl:table-row-group 2xl:table-row-group md:table-row-group">
          <tr v-for="item in allData" :key="item.id" class="border border-gray-200 block lg:table-row xl:table-row 2xl:table-row md:table-row">
            <Td title="نام و نام خانوادگی" class="fm:text-sm">{{item.username}}</Td>
            <Td title="موضوع" class="fm:text-sm">{{item.subject}}</Td>
            <Td title="ایمیل" class="fm:text-sm">{{item.email}}</Td>
            <Td title="شماره تماس" class="fm:text-sm">{{item.mobile}}</Td>
            <Td title="وضعیت پاسخ" class="fm:text-sm">{{item.answer_status}}</Td>
            <Td title="تاریخ" class="fm:text-sm">{{item.created_at}}</Td>
            <Td title="عملیات" v-if="$store.getters.userCan('update_contacts')">
              <div class="flex items-center justify-center gap-2">
                <Button :my_class="'!bg-white border border-blue-500 !py-2 !px-2 fm:py-1 fm:px-1'" @click="show(item.id)"><i class="far fm:text-sm fa-eye text-blue-500"></i></Button>
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
    <Modal title="پاسخ به تماس با ما" save="ثبت" permission="update_contacts" :btnLoading="btnLoading" @callback="update()" ref="openModal">
      <div class="mb-12 flex flex-col gap-2">
        <span class="fm:text-sm !font-medium">سوال کاربر:</span>
        <p class="break-words fm:text-sm">{{question}}</p>
      </div>
      <div class="mb-5">
        <Textarea label="پاسخ شما" v-model="answer" id="answer" :cols="4" :rows="4" :maxlength="1000" :alert="answer.length + '/1000'" />
      </div>
    </Modal>
    <Meta :title="$store.state.siteName + ` | لیست تماس با ما `"/>
    <Loading :loading="loading" />
    <ValidationError />
  </div>
</template>

<script>
export default{name: "Contact"}
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
import axios from "@/plugins/axios";
import store from "@/store";
import Toast from "@/plugins/toast";
import Textarea from "@/components/Textarea";


let refresh = ref(false)
let btnLoading = ref(false)
let loading = ref(false)
let postId = ref(null)
let model = ref('contact')
let search = ref('')
let answer = ref('')
let question = ref('')
let allData = ref([])
let openModal = ref(null)

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


async function show(_postId){
  openModal.value.toggleModal();
  postId.value = _postId;
  loading.value = true;
  await axios.get(`${store.state.api}${model.value}/${postId.value}`).then(async resp=>{
    let data = resp.data.data;
    question.value = data.message;
    answer.value = data.answer === null ? '' : data.answer;
  }).catch(err=>{
    store.commit('handleError',err)
  })
  loading.value = false;
}

async function update(){
  loading.value = btnLoading.value =  true;
  await axios.patch(`${store.state.api}${model.value}/${postId.value}`,{answer:answer.value}).then(resp=>{
    Toast.success("تغییرات با موفقیت ثبت شد");
    getData(false,store.state.current)
    answer.value = '';
    openModal.value.toggleModal();
  }).catch(err=>{
    store.commit('handleError',err)
  })
  loading.value = btnLoading.value =  false;
}

</script>