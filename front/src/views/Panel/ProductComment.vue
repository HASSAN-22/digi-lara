<template>
  <div class="mt-10 px-8">
    <div class="flex justify-center">
      <span class="!font-medium text-lg">لیست دیدگاه های کالا</span>
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
              'کاربر',
              'برای کالا',
              'دیدگاه',
              'وضعیت',
              'تاریخ',
              'عملیات'
          ]" />
          <tbody class="block lg:table-row-group xl:table-row-group 2xl:table-row-group md:table-row-group">
          <tr v-for="item in allData" :key="item.id" class="border border-gray-200 block lg:table-row xl:table-row 2xl:table-row md:table-row">
            <Td title="کاربر" class="fm:text-sm">{{item.user}}</Td>
            <Td title="برای کالا" class="fm:text-sm">{{item.product}}</Td>
            <Td title="دیدگاه">
              <Button :my_class="'!bg-white border border-green-500 !py-2 !px-2 fm:py-1 fm:px-1'" @click="showComment(item.comment)"><i class="far fm:text-sm fa-eye text-green-500"></i></Button>
            </Td>
            <Td title="وضعیت" class="fm:text-sm">{{item.ir_status}}</Td>
            <Td title="تاریخ" class="fm:text-sm">{{item.created_at}}</Td>
            <Td title="عملیات">
              <div class="flex items-center justify-center gap-2">
                <UserCanAction action="show" @show="show(item.id,item.status)" permission="view_productcomments" :postId="item.id" />
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
    <Modal title="ویرایش وضعیت دیدگاه" save="ثبت" permission="update_productcomments" :btnLoading="btnLoading" @callback="update()" ref="openModal">
      <div class="mb-5 w-full">
        <label class="fm:text-sm" for="status">وضعیت<b class="text-red-500 !font-bold">*</b></label>
        <select v-model="status" id="status" class="fm:text-sm border border-gray-200 p-2 outline-none rounded-lg w-full">
          <option value="" selected disabled>--- انتخاب کنید ---</option>
          <option value="yes">تایید</option>
          <option value="no">رد</option>
          <option value="pending">در حال بررسی</option>
        </select>
      </div>
    </Modal>
    <Modal title="نمایش دیدگاه" ref="openCommentModal">
      <div class="mb-5" v-if="comment !== null">
        <p class="break-words overflow-hidden">{{comment}}</p>
      </div>
    </Modal>
    <Meta :title="$store.state.siteName + ` | لیست  دیدگاه های کالا`"/>
    <Loading :loading="loading" />
    <ValidationError />
  </div>
</template>

<script>
export default{name: "ProductComment"}
</script>

<script setup>
import {ref, onMounted, defineExpose} from 'vue';
import Meta from "@/components/Meta.vue";
import Td from "@/components/Td.vue";
import Thead from "@/components/Thead.vue";
import Input from "@/components/Input.vue";
import UserCanAction from "@/components/UserCanAction.vue";
import Loading from "@/components/Loading.vue";
import Button from "@/components/Button.vue";
import Modal from "@/components/Modal.vue";
import ValidationError from "@/components/ValidationError.vue";
import Paginate from "@/components/Paginate.vue";
import axios from "@/plugins/axios";
import store from "@/store";
import Toast from "@/plugins/toast";


let refresh = ref(false)
let btnLoading = ref(false)
let loading = ref(false)
let search = ref('')
let status = ref('')
let comment = ref('')
let postId = ref(0)
let model = ref('productcomment')
let openModal = ref(null)
let openCommentModal = ref(null)
let allData = ref([])

onMounted(async()=>{
  await getData();
});
defineExpose({getData});


async function getData(_loading = true,page=1, isRefresh=false){
  if(isRefresh) search.value = '';
  refresh.value = true;
  loading.value = _loading;
  await axios.get(`${store.state.api}${model.value}?page=${page}&search=${search.value}`).then(resp=>{
    let data = resp.data
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

function show(_postId, _status){
  postId.value = _postId;
  status.value = _status;
  openModal.value.toggleModal();
}


async function update(){
  btnLoading.value =  true;
  await axios.patch(`${store.state.api}${model.value}/${postId.value}`,{status:status.value}).then(resp=>{
    Toast.success("تغییرات با موفقیت ثبت شد");
    getData(false,store.state.current)
    openModal.value.toggleModal();
  }).catch(err=>{
    store.commit('handleError',err)
  })
  btnLoading.value =  false;
}

function showComment(_comment){
  comment.value = _comment;
  openCommentModal.value.toggleModal();
}
</script>