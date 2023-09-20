<template>
  <div class="mt-10 px-8">

    <div v-if="order !== null">
      <div class="flex justify-between items-center">
        <div class="flex items-center gap-2">
          <routerLink :to="{name:'Order'}" class="flex cursor-pointer"><i class="far fa-arrow-right text-3xl fm:text-lg"></i></routerLink>
          <span class="!font-medium text-lg">جزئیات سفارش</span>
        </div>
        <a v-if="order.shipping_status === 'delivered_to_the_customer'" :href="`/invoice/${order.id}`" class="text-blue-400 fm:text-sm !font-medium">
          مشاهده فاکتور
        </a>
      </div>
      <div class="mt-10 px-3 mb-3 border border-gray-200 rounded-lg p-4" >
        <div class="flex flex-col gap-4">
          <div class="flex fm:flex-col fm:gap-3 justify-between items-center w-full mt-3" v-if="order.shipping_status === 'payment_pending'">
            <div class="flex items-center gap-1 w-full">
              <span><i class="far fa-exclamation-circle fm:text-xs text-orange-500"></i></span>
              <span class="text-orange-500 fm:text-xs">سفارش در صورت عدم پرداخت تا {{order.left_time}} دقیقه دیگر لغو خواهد شد.</span>
            </div>
            <div class="w-full fd:flex fd:justify-end">
              <Button @click="pay(order.id)" text="پرداخت" my_class="w-full fd:w-auto fd:!text-sm !p-2"/>
            </div>
          </div>

          <div class="border-b border-gray-200 py-2 w-full" v-if="(order.shipping_status === 'payment_pending' && order.let_time > 0)"></div>
          <div class="flex fm:flex-col fd:items-center gap-8">
            <div class="flex items-center fm:justify-between gap-3">
              <span class="text-gray-500 fm:text-sm">کد پیگیری سفارش</span>
              <span class="fm:text-sm !font-medium">{{order.id}}</span>
            </div>
            <div class="flex fm:justify-between items-center gap-3">
              <span class="text-gray-500 fm:text-sm">تاریخ ثبت سفارش</span>
              <span class="fm:text-sm !font-medium">{{order.ir_created_at}}</span>
            </div>
          </div>
          <div class="border-b border-gray-200 py-2 w-full"></div>
          <div class="flex fm:flex-col fd:items-center gap-8">
            <div class="flex items-center fm:justify-between gap-3">
              <span class="text-gray-500 fm:text-sm">تحویل گیرنده</span>
              <span class="fm:text-sm !font-medium">{{order.address.receiver}}</span>
            </div>
            <div class="flex items-center fm:justify-between gap-3">
              <span class="text-gray-500 fm:text-sm">شماره موبایل</span>
              <span class="fm:text-sm !font-medium">{{order.address.mobile}}</span>
            </div>
          </div>
          <div class="border-b border-gray-200 py-2 w-full"></div>
          <div class="flex fm:flex-col fd:items-center fd:justify-between gap-8">
            <div class="flex items-center fm:justify-between gap-3">
              <span class="text-gray-500 fm:text-sm">آدرس</span>
              <span class="fm:text-sm !font-medium">{{order.address.address}}</span>
            </div>
            <div v-if="order.shipping_status === 'payment_pending'">
              <Button text="تغییر آدرس تحویل" @click="openAddressListModal" my_class="!bg-white !border-none !text-blue-400 !fm:text-sm"/>
            </div>
          </div>
        </div>
        <div class="border-b border-gray-200 py-4 w-full"></div>
        <div class="flex flex-col gap-8 mt-8">
          <div class="flex fm:flex-col fd:items-center gap-8">
            <div class="flex fm:justify-between items-center gap-2">
              <span class="text-gray-500 fm:text-sm">مبلغ</span>
              <span class="fm:text-sm !font-medium">{{order.amount}} ریال</span>
            </div>
            <div class="flex fm:justify-between items-center gap-2">
              <span class="text-gray-500 fm:text-sm">نوع پرداخت</span>
              <span class="fm:text-sm !font-medium">اینترنتی</span>
            </div>
          </div>
          <div class="flex fm:flex-col fd:items-center gap-8" v-if="order.transaction !== null">
            <div class="flex fm:justify-between items-center gap-2">
              <span class="text-gray-500 fm:text-sm">هزینه ارسال (بر اساس وزن و حجم)</span>
              <span class="fm:text-sm !font-medium">{{$store.getters.numberFormat(order.transport_cost)}} ریال</span>
            </div>
          </div>
          <div class="border border-gray-200 rounded-lg p-4 flex fm:flex-col fm:gap-4 fd:items-center justify-between">
            <div class="flex fm:justify-between items-center gap-2">
              <span class="flex fm:hidden" v-if="order.transaction !== null"><i class="far fa-exclamation-circle text-green-500 text-lg fm:text-md"></i></span>
              <span class="flex fm:hidden" v-else><i class="far fa-circle-dot text-red-500 text-lg fm:text-md"></i></span>
              <span class="fd:hidden text-gray-500 fm:text-sm">توضیحات</span>
              <div class="flex items-center gap-2">
                <span class="!font-medium fm:text-sm"> مبلغ سفارش </span>
                <span class="!font-medium fm:text-sm" v-if="['cancelled','auto_cancelled','returned'].includes(order.shipping_status)"> - پرداخت {{order.transaction !== null ? 'موفق' : 'ناموفق'}}</span>
              </div>
            </div>
            <div class="flex fm:flex-col fd:items-center gap-4">
              <div class="flex items-center justify-between">
                <span class="fd:hidden text-gray-500 fm:text-sm">زمان</span>
                <span class="!font-medium  fm:text-sm">{{order.transaction !== null ? order.ir_transaction_created_at : order.ir_created_at}}</span>
              </div>
              <div class="flex items-center justify-between">
                <span class="fd:hidden text-gray-500 fm:text-sm">مبلغ</span>
                <span class="!font-medium  fm:text-sm">{{order.amount}} ریال</span>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="mt-5 px-3 mb-3 border border-gray-200 rounded-lg p-4" v-for="(orderDetail,index) in order.orderDetails" :key="index">
      <div>
        <div class="flex fd:items-center justify-between mb-5">
          <div class="flex fd:items-center fm:justify-between gap-12 mb-5">
            <div class="flex items-center gap-1">
              <span class="text-sm">مرسوله</span>
              <span class="text-sm">{{index+1}}</span>
              <span class="text-sm">از</span>
              <span class="text-sm">{{order.orderDetails.length}}</span>
            </div>
            <div class="flex items-center gap-1">
              <span><i class="far fa-truck-fast text-red-400 -scale-x-100"></i></span>
              <span class="text-sm text-red-400">{{orderDetail.transport_name}}</span>
            </div>
          </div>

          <div class="relative group" v-if="!['delivered_to_the_customer','returned','pending_delivery_to_store','pending_pay_back','success_pay_back','cancelled','auto_cancelled'].includes(orderDetail.shipping_status)">
            <span class="cursor-pointer"><i class="far fa-ellipsis text-2xl fm:text-lg"></i></span>
            <div class="absolute top-[1rem] hidden group-hover:block right-[-10rem] w-[12rem] bg-white p-3 rounded-lg shadow-lg">
              <div class="text-center" v-if="($store.state.user.access === 'admin')">
                <Button text="ویرایش سفارش" @click="show(orderDetail.id,orderDetail.weight_type)" :btnLoading="btnLoading" my_class="!text-blue-400 !p-0 !border-none !bg-white"/>
              </div>
              <div class="border-b border-gray-200 my-2 w-full" v-if="['leaving_center','order_preparation','order_confirmation','review_queue','payment_pending'].includes(orderDetail.shipping_status)"></div>
              <div class="text-center" v-if="['leaving_center','order_preparation','order_confirmation','review_queue','payment_pending'].includes(orderDetail.shipping_status)">
                <Button text="لغو سفارش" @click="cancel(orderDetail.id,orderDetail.weight_type)" :btnLoading="btnLoading" my_class="!text-red-500 !p-0 !border-none !bg-white"/>
              </div>
            </div>
          </div>
        </div>
        <div class="flex fd:items-center fm:justify-between gap-2 mb-5">
          <span class="text-gray-500 fm:text-sm">زمان تحویل</span>
          <span class="!font-medium fm:text-sm">زمان تقریبی تحویل از {{orderDetail.from_day}} تا {{orderDetail.to_day}} روز</span>
        </div>
        <div class="flex fm:flex-col fm:gap-4 fd:justify-between">
          <div class="flex fm:flex-col fd:items-center gap-8">
            <div class="flex fm:justify-between items-center gap-2">
              <span class="text-gray-500 fm:text-sm">هزینه ارسال</span>
              <span class="fm:text-sm !font-medium">{{$store.getters.numberFormat(orderDetail.transport_cost)}} ریال</span>
            </div>
          </div>
          <div class="flex fm:flex-col fd:items-center gap-8">
            <div class="flex fm:justify-between items-center gap-2">
              <span class="text-gray-500 fm:text-sm">مبلغ مرسوله</span>
              <span class="fm:text-sm !font-medium">{{orderDetail.amount}} ریال</span>
            </div>
          </div>
        </div>
        <div class="border-b border-gray-200 py-4 w-full"></div>
        <div v-for="detail in orderDetail.data" :key="detail.id">
          <div class="flex justify-between mt-10">
            <div class="flex gap-[1rem]">
              <div class="flex flex-col">
                <div class="flex flex-col justify-between gap-3">
                  <div class="w-[120px] h-[120px] fm:w-[80px] fm:h-[80px]">
                    <img :src="$store.state.url + detail.image"/>
                  </div>
                  <div class="flex justify-end">
                    <span class="text-gray-500 text-sm">{{detail.count}}</span>
                  </div>
                </div>
              </div>
              <div class="flex flex-col gap-3">
                <routerLink :to="{name:'ProductDetail', params:{slug:detail.product.slug}}" class="text-md !font-medium fm:text-sm">{{detail.product.ir_name}}</routerLink>

                <div class="flex flex-col items-start justify-start gap-2">
                  <div class="flex items-center gap-2" v-if="detail.property_type === 'size'">
                    <span class="text-gray-500 text-lg fm:text-sm"><i class="far fa-ruler-vertical"></i></span>
                    <span class="text-gray-500 text-sm fm:text-xs">{{detail.property_name}}</span>
                    <small class="text-gray-500 text-xs" v-if="detail.property_price > 0">{{$store.getters.numberFormat(detail.property_price)}} ریال</small>
                  </div>
                  <div class="flex items-center gap-2" v-else-if="detail.property_type === 'color'">
                    <div class="rounded-full border w-[1.7rem] h-[1.7rem] fm:w-[1.3rem] fm:h-[1.3rem] flex justify-center items-center" :style="{background:detail.property_color}">
                    </div>
                    <span class="text-gray-500 text-sm fm:text-xs">{{detail.property_name}}</span>
                    <small class="text-gray-500 text-xs" v-if="detail.property_price > 0">{{$store.getters.numberFormat(detail.property_price)}} ریال</small>
                  </div>

                  <div class="flex items-center gap-2">
                    <span class="text-gray-500 text-lg fm:text-sm"><i class="far fa-shield-check"></i></span>
                    <span class="text-gray-500 text-sm fm:text-xs">{{detail.product.guarantee.guarantee}}</span>
                  </div>
                  <div class="flex items-center gap-2">
                    <span class="text-gray-500 text-lg fm:text-sm"><i class="far fa-shop"></i></span>
                    <span class="text-gray-500 text-sm fm:text-xs">{{detail.product.brand.name}}</span>
                  </div>
                  <div class="flex items-center gap-2">
                    <span class="!font-medium text-sm fm:text-xs">{{$store.getters.numberFormat(detail.amount)}} ریال</span>
                  </div>
                </div>
              </div>
            </div>
            <div class="flex flex-col justify-between items-center">
              <div>
                <div class="flex fm:justify-between items-center gap-2">
                  <span class="text-gray-500 fm:text-sm">وضعیت:</span>
                  <span class="fm:text-sm !font-medium" :style="{color:detail.shipping_status_icon.color}">{{detail.ir_shipping_status}}</span>
                </div>
              </div>
              <div  @click="openCommentModal(detail.product.id)" class="flex fm:flex-col fd:items-center gap-2 cursor-pointer">
                <span class="!text-blue-400 !border-none !bg-white fm:hidden">ثبت دیدگاه</span>
                <span class="text-blue-400 text-sm"><i class="far fa-comment"></i></span>
              </div>
            </div>

          </div>
          <div class="border-b border-gray-200 py-4 w-full" v-if="i < 5"></div>
        </div>
      </div>
    </div>
      <Comment :purchased="order.transaction !== null" :productId="productId" ref="commentRef"/>
      <AddressComponent :key="$store.state.addressComponentKey" :orderId="order.id" saveToOrder="yes" :addressId="order.address.address_id" ref="addressComponentRef"/>
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
    <Meta :title="$store.state.siteName + ` | جزئیات سفارش `"/>
    <Loading :loading="loading" />
    <ValidationError />
  </div>
</template>

<script>
export default{name: "OrderDetail"}
</script>

<script setup>
import {ref, onMounted, watch} from 'vue';
import Meta from "@/components/Meta.vue";
import Comment from "@/components/Comment.vue";
import Loading from "@/components/Loading.vue";
import AddressComponent from "@/components/AddressComponent.vue";
import Button from "@/components/Button.vue";
import ValidationError from "@/components/ValidationError.vue";
import Modal from "@/components/Modal.vue";
import axios from "@/plugins/axios";
import store from "@/store";
import { useRoute } from "vue-router"
import Toast from "@/plugins/toast";

const route = useRoute();
let addressComponentRef = ref();
let commentRef = ref();

let btnLoading = ref(false)
let refresh = ref(false)
let loading = ref(false)
let isUpdate = ref(false)
let openModal = ref(null)
let orderDetailId = ref(null)
let model = ref('order')
let productId = ref(0)
let search = ref('')

let shippingStatus = ref('')
let weightType = ref('')

let orderId = ref(0)
let order = ref(null);

onMounted(async()=>{
  orderId.value = route.params['order_id'];
  await getData();
});

watch(() => store.state.addressComponentKey, async function() {
  await getData(false);
});

async function getData(_loading = true){
  await getOrderDetail(_loading)
  await addressComponentRef.value.getAddresses()
}

async function getOrderDetail(_loading = true){
  loading.value = _loading;
  await axios.get(`${store.state.api}${model.value}/detail/${orderId.value}`).then(resp=>{
    order.value = resp.data.data;
  }).catch(err=>{
    store.commit('handleError',err)
  })
  loading.value = refresh.value = false;
}

async function openAddressListModal(){
  addressComponentRef.value.openAddressListModal();
}

function openCommentModal(_productId){
  productId.value = _productId;
  commentRef.value.openCommentModal();
}

async function pay(_orderId){
  let _confirm = confirm("هزینه این سفارش هنوز پرداخت نشده‌ و در صورت اتمام موجودی هر کدام از کالاها ان کالا خود کار از سبد حذف می‌شوند")
  if(_confirm){
    btnLoading.value = true;
    await axios.get(`${store.state.api}order/pay/${_orderId}`).then(async resp=>{
      let redirectUrl = resp.data.data.redirect_url;
      window.location.href = redirectUrl ? redirectUrl : '/payment-alert?status=error&msg=پرداخت با خطا مواجه شد'
    }).catch(err=>{
      store.commit('handleError',err)
    })
    btnLoading.value = false;
  }else{
    Toast.error("عملیات لغو شد")
  }
}
async function show(_orderDetailId,_weightType){
  orderDetailId.value = _orderDetailId
  weightType.value = _weightType;
  isUpdate.value = true;
  loading.value = true;
  openModal.value.toggleModal();
  await axios.get(`${store.state.api}order/show-order-detail/${orderDetailId.value}`).then(resp=>{
    shippingStatus.value = resp.data.data.shipping_status;
  }).catch(err=>{
    Toast.error();
  })
  loading.value = false;
}

async function update(){
  loading.value = true;
  await axios.post(`${store.state.api}order/update-order-detail/${orderDetailId.value}`,{shipping_status:shippingStatus.value,'weight_type':weightType.value}).then(async resp=>{
    await getOrderDetail(false)
    Toast.success();
    openModal.value.toggleModal();
  }).catch(err=>{
    Toast.error();
  })
  loading.value = false;
}

async function cancel(_orderDetailId,_weightType){
  btnLoading.value = true;
  await axios.post(`${store.state.api}order/cancel-detail/${_orderDetailId}`,{'weight_type':_weightType}).then(async resp=>{
    await getOrderDetail(false)
    Toast.success('با موفقیت انجام شد')
  }).catch(err=>{
    Toast.error();
  })
  btnLoading.value = false;
}
</script>