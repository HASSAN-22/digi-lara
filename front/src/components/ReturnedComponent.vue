<template>
  <div>
    <div class="flex flex-col gap-3 border border-gray-100 rounded-lg p-3 mb-3" v-for="item in props.returnedOrders" :key="item.id">
      <div class="flex justify-between items-center">
        <div class="flex items-center gap-1">
          <span>وضعیت:</span>
          <span>{{item.ir_shipping_status}} [{{item.ir_status}}]</span>
        </div>
        <div class="relative group" v-if="item.shipping_status === 'returned' && $store.state.user.access === 'admin'">
          <span class="cursor-pointer"><i class="far fa-ellipsis text-2xl fm:text-lg"></i></span>
          <div class="absolute top-[1rem] hidden group-hover:block right-[-10rem] w-[12rem] bg-white p-3 rounded-lg shadow-lg">
            <div class="text-center" v-if="item.status !== 'success_pay_back'">
              <Button text="ویرایش وضعیت مرجوعی" @click="showReturnedStatus(item.id,item.status)" :btnLoading="btnLoading" my_class="!text-blue-400 !p-0 !border-none !bg-white"/>
            </div>
            <div class="border-b border-gray-200 my-2 w-full"></div>
            <div class="text-center">
              <Button text="مشاهده توضیحات مشکل" @click="showReturnedProblem(item.id)" :btnLoading="btnLoading" my_class="!text-orange-400 !p-0 !border-none !bg-white"/>
            </div>
          </div>
        </div>
      </div>
      <div>
        <img :src="$store.state.url + item.image" class="w-[8%]"/>
      </div>
      <div class="flex items-center gap-3">
        <span class="text-sm text-gray-500">{{item.created_at}}</span>
        <span class="text-sm text-gray-500"><b>کد سفارش</b> {{item.order_id}}</span>
        <span class="text-sm text-gray-500"><b>کد مرجوعی</b> {{item.id}}</span>
        <span class="text-sm text-gray-500"><b>قیمت</b> {{item.amount}}</span>
      </div>
    </div>

    <Modal title="ویرایش وضعیت مرجوعی" save="ثبت تغییرات" permission="update_returneds" :btnLoading="btnLoading" @callback="updateReturnedStatus()" ref="returnedStatusModal">

      <div class="mt-5">
        <label class="fm:text-sm">وضعیت مرجوعی<b class="text-red-500 !font-bold">*</b></label>
        <select v-model="returnedStatus" class="fm:text-sm border border-gray-200 p-2 outline-none rounded-lg w-full">
          <option value="" selected disabled>--- انتخاب کنید ---</option>
          <option value="pending_delivery_to_store">درانتظار ارسال به فروشگاه</option>
          <option value="pending_pay_back">در انتظار بازگشت مبلغ سفارش</option>
          <option value="success_pay_back">مبلغ سفارش با موفقیت بازگردانده شد</option>
        </select>
      </div>

    </Modal>

    <Modal title="مشاهده مشکل" save="ثبت تغییرات" ref="showProblemDescriptionModal">
      <div class="mt-5">
        <p v-html="showProblemDescription" class="break-all"></p>
      </div>
    </Modal>

    <Loading :loading="loading" />
  </div>
</template>

<script>
export default {name:'ReturnedComponent'}
</script>

<script setup>
import {defineEmits, defineProps, ref} from 'vue';
import Button from "@/components/Button.vue";
import Modal from "@/components/Modal.vue";
import Loading from "@/components/Loading.vue";
import axios from "@/plugins/axios";
import store from "@/store";
import Toast from "@/plugins/toast";

const emits = defineEmits(['parentMethod'])
const props = defineProps(['returnedOrders'])

let btnLoading = ref(false)
let loading = ref(false)
let returnedStatusModal = ref(null)
let showProblemDescriptionModal = ref(null)

let showProblemDescription = ref('')
let returnedStatus = ref('')
let returnedId = ref(0)

async function showReturnedProblem(_returnedId){
  loading.value = true;
  await axios.get(`${store.state.api}returned-problem/${_returnedId}`).then(resp=>{
    showProblemDescription.value = resp.data.data.description;
    showProblemDescriptionModal.value.toggleModal();
  }).catch(err=>{
    Toast.error();
  })
  loading.value = false;
}

function showReturnedStatus(_returnedId, _status){
  returnedId.value = _returnedId;
  if(_status !== ''){
    returnedStatus.value = _status;
  }
  returnedStatusModal.value.toggleModal();
}

async function updateReturnedStatus(){
  btnLoading.value = true;
  await axios.post(`${store.state.api}returned-status/${returnedId.value}`,{status:returnedStatus.value}).then(async resp=>{
    await emits('parentMethod',false, store.state.current)
    Toast.success();
    returnedStatusModal.value.toggleModal();
  }).catch(err=>{
    Toast.error();
  })
  btnLoading.value = false;
}

</script>