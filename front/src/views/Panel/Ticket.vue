<template>
  <div class="mt-10 px-8">
    <div class="flex justify-center">
      <span class="!font-medium text-lg">لیست تیکت ها</span>
    </div>
    <div class="mt-10 px-3 mb-3">
      <div class="mb-3 mr-2 flex justify-between fm:flex-col items-center">
        <div class="flex gap-2 items-center">
          <div v-if="!isAdmin">
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
              'عنوان',
              'توسط',
              'موضوع',
              'اولویت',
              'وضعیت',
              'تاریخ',
              'عملیات'
          ]" :except="!isAdmin ? ['توسط'] : []"/>
          <tbody class="block lg:table-row-group xl:table-row-group 2xl:table-row-group md:table-row-group">
          <tr v-for="item in allData" :key="item.id" class="border border-gray-200 block lg:table-row xl:table-row 2xl:table-row md:table-row">

            <Td title="عنوان" class="fm:text-sm">
              <Notification :notifications="notifications" :postId="item.id" type="ticket">
                <span>{{item.title}}</span>
              </Notification>
            </Td>
            <Td v-if="isAdmin" title="توسط'" class="fm:text-sm">{{item.user}}</Td>
            <Td title="موضوع" class="fm:text-sm">{{item.ticket_category}}</Td>
            <Td title="اولویت" class="fm:text-sm">{{item.priority}}</Td>
            <Td title="وضعیت" class="fm:text-sm">{{item.ir_is_locked}}</Td>
            <Td title="تاریخ" class="fm:text-sm">{{item.created_at}}</Td>
            <Td title="عملیات">
              <div class="flex items-center justify-center gap-2">
                <Button :my_class="'!bg-white border border-green-500 !py-2 !px-2 fm:py-1 fm:px-1'" @click="getMessages(item.id)"><i class="far fm:text-sm fa-eye text-green-500"></i></Button>
                <UserCanAction v-if="isAdmin" action="show" @show="show(item.id,item.is_locked)" permission="update_tickets" :postId="item.id" />
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
    <Modal title="تغییر وضعیت تیکت" save="ثبت" permission="update_tickets" :btnLoading="btnLoading" @callback="update()" ref="openLockedModal">
      <div class="mb-5 w-full">
        <label class="fm:text-sm" for="isLocked">وضعیت<b class="text-red-500 !font-bold">*</b></label>
        <select v-model="isLocked" id="isLocked" class="fm:text-sm border border-gray-200 p-2 outline-none rounded-lg w-full">
          <option value="" selected disabled>--- انتخاب کنید ---</option>
          <option value="open">باز</option>
          <option value="locked">بسته</option>
        </select>
      </div>
    </Modal>
    <Modal title="ثبت تیکت" save="ثبت" :btnLoading="btnLoading" @callback="insert()" ref="openModal">
      <div class="mb-5">
        <Input label="عنوان" v-model="title" id="title" />
      </div>
      <div class="mb-5 w-full">
        <label class="fm:text-sm" for="ticketCategoryId">موضوع<b class="text-red-500 !font-bold">*</b></label>
        <select v-model="ticketCategoryId" id="ticketCategoryId" class="fm:text-sm border border-gray-200 p-2 outline-none rounded-lg w-full">
          <option value="" selected disabled>--- انتخاب کنید ---</option>
          <option v-for="category in ticketCategories" :key="category.id" :value="category.id">{{category.title}}</option>
        </select>
      </div>
      <div class="mb-5 w-full">
        <label class="fm:text-sm" for="priority">اولویت<b class="text-red-500 !font-bold">*</b></label>
        <select v-model="priority" id="priority" class="fm:text-sm border border-gray-200 p-2 outline-none rounded-lg w-full">
          <option value="" selected disabled>--- انتخاب کنید ---</option>
          <option value="high">بالا</option>
          <option value="medium">متوسط</option>
          <option value="low">کم</option>
        </select>
      </div>
      <div class="mb-5">
        <Textarea label="متن پیام" :cols="5" :rows="5" v-model="message" id="message" :maxlength="10000" :alert="message.length + '/10000'" />
      </div>
    </Modal>
    <Modal title="مشاهده پیام ها" save="ثبت" :btnLoading="btnLoading" @callback="sendMessage()" ref="openMessageModal">
      <div>
        <div class="border border-gray-200 rounded-lg p-3">
          <div v-for="message in ticketMessages" :key="message.id" class="mb-5">
            <div :class="['flex flex-col gap-2 p-2 rounded-lg text-white',message.is_admin ? 'bg-[#adb7b5]' : 'bg-[#74c7c2]']">
              <div class="flex items-center gap-2">
                <div class="w-[4rem] h-[4rem] rounded-full">
                  <img :src="$store.state.url + message.avatar" class="rounded-full"/>
                </div>
                <div class="flex items-center gap-6">
                  <div class="flex items-center gap-1">
                    <span class="text-sm"><i class="far fa-user text-sm"></i></span>
                    <span class="text-sm">{{message.username}}</span>
                  </div>
                  <div class="flex items-center gap-1">
                    <span class="text-sm"><i class="far fa-clock text-sm"></i></span>
                    <span class="text-sm">{{message.created_at}}</span>
                  </div>
                </div>
              </div>
              <div><p class="text-sm break-words">{{message.message}}</p></div>
            </div>
          </div>
        </div>
        <div class="mt-5" v-if="ticketIsOpen">
          <Textarea label="پیام شما" :cols="5" :rows="5" v-model="message" id="message" :maxlength="10000" :alert="message.length + '/10000'" />
        </div>
      </div>
    </Modal>
    <Meta :title="$store.state.siteName + ` | لیست تیکت ها `"/>
    <Loading :loading="loading" />
    <ValidationError />
  </div>
</template>

<script>
export default{name: "Ticket"}
</script>

<script setup>
import {ref, onMounted, defineExpose} from 'vue';
import Meta from "@/components/Meta.vue";
import Td from "@/components/Td.vue";
import Thead from "@/components/Thead.vue";
import UserCanAction from "@/components/UserCanAction.vue";
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
let postId = ref(null)
let model = ref('ticket')
let openModal = ref(null)
let openMessageModal = ref(null)
let openLockedModal = ref(null)
let search = ref('')

let title = ref('')
let ticketCategoryId = ref('')
let priority = ref('')
let message = ref('')

let isLocked = ref('')
let allData = ref([])
let ticketCategories = ref([])
let notifications = ref([])
let ticketMessages = ref([])
let ticketIsOpen = ref(true)
let isAdmin = ref(store.state.user.access === 'admin')
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
    ticketCategories.value = data.ticket_categories;
    notifications.value = data.notifications;
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

async function show(_postId,_isLocked){
  isLocked.value = _isLocked;
  postId.value = _postId;
  openLockedModal.value.toggleModal()
}

async function update(){
  loading.value = btnLoading.value =  true;
  await axios.patch(`${store.state.api}${model.value}/${postId.value}`,{is_locked:isLocked.value}).then(resp=>{
    Toast.success("تغییرات با موفقیت ثبت شد");
    getData(false,store.state.current)
    openLockedModal.value.toggleModal()
  }).catch(err=>{
    store.commit('handleError',err)
  })
  loading.value = btnLoading.value =  false;
}

async function getMessages(_postId,_loading=true,modal=true){
  message.value = '';
  loading.value = _loading;
  postId.value = _postId;
  await axios.get(`${store.state.api}${model.value}/${postId.value}`).then(resp=>{
    let data = resp.data
    ticketIsOpen.value = data.is_locked;
    ticketMessages.value = data.data;
    if(modal){
      openMessageModal.value.toggleModal()
    }
  }).catch(err=>{
    Toast.error();
  })
  loading.value = false;
}
async function sendMessage(){
  loading.value = true;
  await axios.post(`${store.state.api}ticket-send-message/${postId.value}`,{message:message.value}).then(async resp=>{
    Toast.success()
    await getMessages(postId.value,false,false)
  }).catch(err=>{
    store.commit('handleError',err)
  })
  loading.value = false;
}

function formData(){
  return {
    title:title.value,
    ticketcategory_id:ticketCategoryId.value,
    priority:priority.value,
    message:message.value,
  };
}

function clearData(){
  title.value = '';
  ticketCategoryId.value = '';
  priority.value = '';
  message.value = '';
  openModal.value.toggleModal();
}

</script>