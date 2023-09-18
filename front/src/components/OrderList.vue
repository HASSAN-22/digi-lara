<template>
  <div>
    <div class="border border-gray-200 rounded-lg p-4 mb-4" v-for="order in props.orders" :key="order.id">

      <div class="flex flex-col fm:gap-4 fd:gap-12">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-1">
            <span class="flex" :style="{color:order.icon.color}" role="img"><i :class="[order.icon.icon,'text-2xl fm:text-sm']"></i></span>
            <span class="fm:text-xs">{{order.ir_shipping_status}}<b v-if="['returned','pending_delivery_to_store','pending_pay_back','success_pay_back'].includes(order.shipping_status)" class="text-blue-400">[{{order.ir_returned_status}}]</b></span>
          </div>
          <div class="relative group" v-if="order.shipping_status !== 'delivered_to_the_customer' && !['returned','pending_delivery_to_store','pending_pay_back','success_pay_back'].includes(order.shipping_status)">
            <span class="cursor-pointer"><i class="far fa-ellipsis text-2xl fm:text-lg"></i></span>
            <div class="absolute top-[1rem] hidden group-hover:block right-[-10rem] w-[12rem] bg-white p-3 rounded-lg shadow-lg">
              <div class="text-center" v-if="($store.state.user.access === 'admin')">
                <Button text="ویرایش سفارش" @click="show(order.id)" :btnLoading="btnLoading" my_class="!text-blue-400 !p-0 !border-none !bg-white"/>
              </div>
              <div class="border-b border-gray-200 my-2 w-full" v-if="['leaving_center','order_preparation','order_confirmation','review_queue','payment_pending'].includes(order.shipping_status)"></div>
              <div class="text-center" v-if="['leaving_center','order_preparation','order_confirmation','review_queue','payment_pending'].includes(order.shipping_status)">
                <Button text="لغو سفارش" @click="cancel(order.id)" :btnLoading="btnLoading" my_class="!text-red-500 !p-0 !border-none !bg-white"/>
              </div>
            </div>
          </div>
          <div class="relative group" v-else-if="order.shipping_status === 'delivered_to_the_customer' && order.can_returned && order.user_id === $store.state.user.id">
            <span class="cursor-pointer"><i class="far fa-ellipsis text-2xl fm:text-lg"></i></span>
            <div class="absolute top-[1rem] hidden group-hover:block right-[-10rem] w-[12rem] bg-white p-3 rounded-lg shadow-lg">
              <div class="text-center">
                <Button text="مرجوع کردن کالا" @click="showReturned(order.id)" :btnLoading="btnLoading" my_class="!text-blue-400 !p-0 !border-none !bg-white"/>
              </div>
            </div>
          </div>

        </div>
        <routerLink :to="{name:'OrderDetail',params:{order_id:order.id}}">
          <div class="flex fm:flex-col justify-between items-center">
            <div class="flex fm:flex-col fd:items-center gap-4 w-full">
              <span class="text-xs text-gray-500">{{order.created_at}}</span>
              <div class="flex items-center gap-5 justify-between">
                <div class="flex items-center gap-1">
                  <span class="fm:text-xs text-gray-500">کد سفارش</span>
                  <span class="text-xs !font-medium">{{order.id}}</span>
                </div>
                <div class="flex items-center gap-1">
                  <span class="fm:text-xs text-gray-500">مبلغ</span>
                  <span class="text-xs !font-medium">{{order.amount}} ریال</span>
                </div>
              </div>
            </div>
            <div class="flex flex-col gap-2 w-full fm:hidden" v-if="props.showPercentage">
              <div class="flex justify-between items-center">
                <span class="text-xs" :style="{color:order.icon.color}">{{order.ir_shipping_status}}</span>
                <div class="flex items-center gap-2" v-if="order.shipping_status !== 'delivered_to_the_customer'">
                  <span class="text-gray-500 text-xs !font-medium">مبلغ قابل پرداخت: </span>
                  <span class="text-xs !font-medium">{{order.amount}} ریال</span>
                </div>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-1.5 mb-4">
                <div class="h-1.5 rounded-full dark:bg-blue-500" :style="{background:order.icon.color,width:`${parseInt(order.icon.percent)}%`}"></div>
              </div>
            </div>
          </div>
        </routerLink>
      </div>
      <div class="border-b border-gray-200 w-full my-4"></div>
      <routerLink :to="{name:'OrderDetail',params:{order_id:order.id}}">
        <div class="flex flex-wrap gap-5 items-center">
          <div v-for="(detail,index) in order.order_details" :key="index">
            <img :src="$store.state.url + detail" class="w-[80px] h-[80px] fm:w-[50px] fm:h-[50px]"/>
          </div>
        </div>
      </routerLink>
      <div class="flex fm:flex-col fm:gap-3 justify-between items-center w-full mt-3" v-if="order.shipping_status === 'payment_pending'">
        <div class="flex items-center gap-1 w-full">
          <span><i class="far fa-exclamation-circle fm:text-xs text-orange-500"></i></span>
          <span class="text-orange-500 fm:text-xs">سفارش در صورت عدم پرداخت تا {{order.time_left}} دقیقه دیگر لغو خواهد شد.</span>
        </div>
        <div class="w-full fd:flex fd:justify-end">
          <Button @click="pay(order.id)" text="پرداخت" my_class="w-full fd:w-auto fd:!text-sm !p-2"/>
        </div>
      </div>
      <div v-else-if="order.shipping_status === 'delivered_to_the_customer'" class="flex justify-end mt-6">
          <a :href="`/invoice/${order.id}`" class="text-blue-400 fm:text-sm !font-medium">
            مشاهده فاکتور
          </a>
      </div>
    </div>
    <Modal title="ویرایش وضعیت سفارش" save="ثبت تغییرات" permission="update_orders" :btnLoading="btnLoading" @callback="update()" ref="openModal">

      <div class="mt-5">
        <label class="fm:text-sm">وضعیت سفارش<b class="text-red-500 !font-bold">*</b></label>
        <select v-model="shippingStatus" class="fm:text-sm border border-gray-200 p-2 outline-none rounded-lg w-full">
          <option value="" selected>--- انتخاب کنید ---</option>
          <option value="payment_pending">در انتظار پرداخت</option>
          <option value="review_queue">در صف بررسی</option>
          <option value="order_confirmation">تایید سفارش</option>
          <option value="order_preparation">آماده‌سازی سفارش</option>
          <option value="leaving_center">خروج از مرکز پردازش</option>
          <option value="pick_up_at_distribution_centers">دریافت در مرکز توزیع</option>
          <option value="delivery_to_sender_agent">تحویل به مامور ارسال</option>
          <option value="delivered_to_the_customer">تحویل داده شد</option>
          <option value="cancelled">لغو شد</option>
          <option value="auto_cancelled">لغو سیستمی</option>
          <option value="returned">مرجوع شد</option>
        </select>
      </div>

    </Modal>

    <Modal title="مرجوع کردن کالا" save="ثبت" :btnLoading="btnLoading" width="w-[55%]" @callback="returned()" ref="returnedModal">
      <div class="mb-5">
        <div class="flex fd:flex-wrap fm:flex-col justify-between gap-2 items-center">
          <div v-for="order in orderDetails" :key="order.id" class="mb-2 fd:w-[47%] fm:w-full">
            <div>
              <input type="checkbox" @change="orderDetailSelectChange(order.id)" :checked="orderDetailsSelected.filter(item=>item === order.id).length > 0"/>
            </div>
            <div class="flex gap-3 items-center border border-gray-100 h-[9rem] hover:shadow p-3">
              <div class="w-[35%]">
                <img :src="$store.state.url + order.image" />
              </div>
              <div class="flex flex-col gap-2">
                <span class="text-sm !font-medium">{{order.name}}</span>
                <span class="text-sm !font-medium">{{$store.getters.numberFormat(order.amount)}} ریال</span>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="mt-5">
        <Textarea label="توضیح مشکلات" v-model="problemDescription" id="problemDescription" :maxlength="2000" :alert="problemDescription.length + '/2000'"/>
      </div>
    </Modal>


    <Loading :loading="loading" />
    <ValidationError />
  </div>
</template>

<script>
export  default {name:'OrderList'}
</script>

<script setup>
import {ref, defineProps, defineEmits} from 'vue';
import Button from "@/components/Button";
import axios from "@/plugins/axios";
import store from "@/store";
import Toast from "@/plugins/toast";
import Modal from "@/components/Modal.vue";
import Loading from "@/components/Loading.vue";
import Textarea from "@/components/Textarea.vue";
import ValidationError from "@/components/ValidationError.vue";

const props = defineProps({
  orders:{
    type:Object
  },
  showPercentage:{
    type:Boolean,
    default:true
  },
  cancelOrder:{
    type:Boolean,
    default:false,
  }
})

const emits = defineEmits(['parentMethod'])

let btnLoading = ref(false)
let loading = ref(false)
let isUpdate = ref(false)
let openModal = ref(null)
let returnedModal = ref(null)
let orderId = ref(null)

let problemDescription = ref('')
let shippingStatus = ref('')
let orderDetails = ref([]);
let orderDetailsSelected = ref([]);


async function showReturned(_orderId){
  orderDetailsSelected.value = [];
  loading.value = true;
  orderId.value = _orderId;
  await axios.get(`${store.state.api}returned/${orderId.value}`).then(async resp=>{
    orderDetails.value = resp.data.data;
    returnedModal.value.toggleModal();
  }).catch(err=>{
    Toast.error();
  })
  loading.value = false;
}

async function returned(){
  btnLoading.value = true;
  let _orderDetailIds = JSON.stringify(orderDetailsSelected.value)
  await axios.post(`${store.state.api}returned`,{problem_description:problemDescription.value,order_id:orderId.value,order_detail_ids:_orderDetailIds}).then(async resp=>{
    await emits('parentMethod',false, store.state.current)
    Toast.success();
    returnedModal.value.toggleModal();
  }).catch(err=>{
    store.commit('handleError',err)
  })
  btnLoading.value = false;
}

async function pay(_orderId){
  btnLoading.value = true;
  await axios.get(`${store.state.api}order/pay/${_orderId}`).then(async resp=>{
    let redirectUrl = resp.data.data.redirect_url;
    window.location.href = redirectUrl ? redirectUrl : '/payment-alert?status=success&msg=سفارش با موفقیت ثبت شد'
  }).catch(err=>{
    store.commit('handleError',err)
  })
  btnLoading.value = false;
}


async function show(_orderId){
  orderId.value = _orderId
  isUpdate.value = true;
  loading.value = true;
  openModal.value.toggleModal();
  await axios.get(`${store.state.api}order/show-order/${orderId.value}`).then(resp=>{
    shippingStatus.value = resp.data.data.shipping_status;
  }).catch(err=>{
    Toast.error();
  })
  loading.value = false;
}

async function update(){
  btnLoading.value = true;
  await axios.post(`${store.state.api}order/update-order/${orderId.value}`,{shipping_status:shippingStatus.value}).then(async resp=>{
    await emits('parentMethod',false, store.state.current)
    Toast.success();
    openModal.value.toggleModal();
  }).catch(err=>{
    Toast.error();
  })
  btnLoading.value = false;
}

async function cancel(orderId){
  btnLoading.value = true;
  await axios.post(`${store.state.api}order/cancel/${orderId}`).then(async resp=>{
    Toast.success('با موفقیت انجام شد')
    await emits('parentMethod',false, store.state.current)
  }).catch(err=>{
    Toast.error();
  })
  btnLoading.value = false;
}


function orderDetailSelectChange(_orderId){
  let exists = orderDetailsSelected.value.filter(item=>item === _orderId).length > 0;
  if(exists){
    orderDetailsSelected.value = orderDetailsSelected.value.filter(item=>item !== _orderId)
  }else{
    orderDetailsSelected.value.push(_orderId)
  }
}
</script>