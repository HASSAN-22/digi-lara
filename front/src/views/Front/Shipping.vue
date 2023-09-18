<template>
  <div>
    <div class="container mx-auto">
      <div class="mx-4 mt-10">
        <div class="text-center mb-9">
          <h1 class="text-lg fm:text-md !font-medium">نهایی کردن سبد خرید</h1>
        </div>
        <div class="flex fm:flex-col relative" v-if="$store.state.baskets.length > 0">

          <div class="fd:w-[75%] flex flex-col p-2">
            <div v-if="currentAddress.length > 0" class="border border-gray-200 p-8 rounded-lg">
              <div class="flex flex-col gap-2 items-start">
                <span class="text-sm font-gray-400">آدرس تحویل سفارش</span>
                <div class="flex items-center gap-2">
                  <span class="flex"><i class="far fa-location-dot text-xl !font-medium"></i></span>
                  <span class="text-lg !font-medium">{{currentAddress[0].address}}</span>
                </div>
                <span class="text-gray-500 text-lg">{{currentAddress[0].receiver_name + ' '+ currentAddress[0].receiver_last_name}}</span>
              </div>
              <div class="flex items-end justify-end">
                <button class="text-blue-400 cursor-pointer" @click="openAddressListModal">تغییر یا ویرایش آدرس</button>
              </div>
            </div>
            <div v-else>
              <div class="mb-8 flex items-center gap-3 cursor-pointer" @click="openAddressModal(false)">
                <span><i class="far fa-location-plus"></i></span>
                <h1 class="text-lg !font-medium">افزودن آدرس جدید</h1>
                <span class="flex"><i class="far fa-plus"></i></span>
              </div>
            </div>
            <div class="my-8 flex items-center gap-2" v-if="$store.state.baskets.filter(item=>item.product.category.weight_type.includes('heavy','super_heavy'))">
              <span class="fm:text-sm text-gray-500"><i class="far fa-exclamation-circle"></i></span>
              <span class="fm:text-sm text-gray-500">این سفارش در چند نوبت (مرسوله) ارسال می‌شود چون شامل کالای سوپرمارکتی، سنگین یا روش ارسال متفاوت است</span>
            </div>
            <ProductWeightSplit :transports="transports" :baskets="$store.state.baskets.filter(item=>item.product.category.weight_type === 'style')" weightType="style"/>
            <ProductWeightSplit :transports="transports" :baskets="$store.state.baskets.filter(item=>item.product.category.weight_type === 'heavy')" weightType="heavy"/>
            <ProductWeightSplit :transports="transports" :baskets="$store.state.baskets.filter(item=>item.product.category.weight_type === 'super_heavy')" weightType="super_heavy"/>


            <div class="mt-3 text-left">
              <routerLink :to="{name:'Basket'}" id="router-link" class="text-blue-400 text-sm">بازگشت به سبد خرید</routerLink>
            </div>
          </div>
          <div class="fd:w-[25%] relative">
            <div class="fixed sticky top-[9rem] flex-col gap-5">
              <div class="flex flex-col mb-5 border border-gray-200 rounded-lg p-4">
                <div class="flex items-center gap-2 ">
                  <div class="w-full">
                    <Input placeholder="کد تخفیف" v-model="couponCode" id="couponCode" :required="false"/>
                  </div>
                  <div>
                    <Button text="اعمال" @click="btnLoadingCheckCoupon ? '' : checkCoupon()" :btnLoading="btnLoadingCheckCoupon" my_class="!py-2 !px-2 w-full !text-sm" />
                  </div>
                </div>
                <span v-if="couponMessage.length > 0" class="text-red-500 text-sm">{{couponMessage}}</span>
              </div>
              <div class="flex flex-col gap-6 border border-gray-200 rounded-lg p-4">
                <div class="flex items-start justify-between">
                  <span class="text-gray-500 fm:text-sm">قیمت کالاها ({{$store.state.baskets.length}})</span>
                  <span class="text-gray-500 fm:text-sm">{{productAmount}} ریال</span>
                </div>

                <div class="border-b border-gray-200 w-full" v-if="$store.state.couponDiscount > 0"></div>
                <div class="flex items-start justify-between" v-if="$store.state.couponDiscount > 0">
                  <span class="!font-medium fm:text-sm">تخفیف</span>
                  <span class="!font-medium fm:text-sm">{{$store.getters.numberFormat($store.state.couponDiscount)}} ریال</span>
                </div>
                <div class="border-b border-gray-200 w-full"></div>
                <div class="flex items-start justify-between">
                  <span class="!font-medium fm:text-sm">جمع کل</span>
                  <span class="!font-medium fm:text-sm">{{$store.getters.numberFormat(fullAmount)}} ریال</span>
                </div>
                <div class="border-b border-gray-200 w-full" v-if="calcPercent > 0"></div>
                <div class="flex flex-col gap-5" v-if="calcPercent > 0">
                  <div class="flex items-start justify-between">
                    <span class="!font-medium text-red-500 fm:text-sm">سود شما از خرید</span>
                    <span class="!font-medium text-red-500 fm:text-sm"> ({{calcPercent}}٪) {{$store.getters.numberFormat(price - fullPrice)}} ریال</span>
                  </div>
                </div>
                <div class="border-b border-gray-200 w-full"></div>
                <div class="flex items-start justify-between">
                  <span class="!font-medium fm:text-sm">هزینه ارسال</span>
                  <span class="!font-medium fm:text-sm" v-if="transportAmount > 0">{{$store.getters.numberFormat(transportAmount)}} ریال</span>
                  <span class="!font-medium fm:text-sm" v-else>رایگان</span>
                </div>
                <div class="border-b border-gray-200 w-full"></div>
                <div class="flex items-start justify-between">
                  <span class="!font-medium text-gray-500 text-sm">قابل پرداخت</span>
                  <span class="!font-medium text-sm"> {{$store.getters.numberFormat(totalAmount)}} ریال</span>
                </div>

                <div v-if="walletAmount > 0" class="flex flex-col items-start">
                  <button @click="calcWalletPrice()" class="text-xs text-blue-400">کم کردن موجودی کیف پول از مبلغ پرداختی ({{$store.getters.numberFormat(walletAmount)}}) ریال</button>
                </div>

                <div class="w-full" v-if="$store.state.baskets.length > 0 && selectedAddressId !== null">
                  <Button @click="addOrder()" :btnLoading="btnLoadingAddOrder" my_class="w-full" text="ثبت سفارش" />
                </div>
                <div class="w-full" v-else>
                  <button class="bg-gray-200 p-3 text-gray-500 w-full text-sm text-center rounded-lg">
                    {{currentAddress.length <= 0 ? 'لطفا آدرس تحویل سفارش را وارد کنید' : 'ثبت سفارش'}}
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div v-else class="flex justify-center">
          <img src="@/assets/images/no-data.svg" class="w-[300px] fm:w-[200px]"/>
        </div>
      </div>
    </div>

    <AddressComponent ref="addressComponentRef"/>

    <Meta :title="$store.state.siteName + ` زمان و نحوه ارسال | صفحه اصلی `"/>
    <Loading :loading="loading" />
  </div>
</template>

<script>
export default {name:'Shipping'}
</script>
<script setup>
import {computed, onMounted, ref, watch} from 'vue';
import Loading from "@/components/Loading";
import Meta from "@/components/Meta";
import Input from "@/components/Input";
import axios from "@/plugins/axios";
import store from "@/store";
import Toast from "@/plugins/toast";
import Button from "@/components/Button";
import AddressComponent from "@/components/AddressComponent";
import ProductWeightSplit from "@/components/ProductWeightSplit";


let price = ref(0)
let fullPrice = ref(0)
let transportAmount = ref(0);
let couponCode = ref('');
let couponMessage = ref('');
let walletAmount = ref(0);
let useWalletAmount = ref(false);
let totalAmount = ref(0);
let transports = ref([]);

let addressComponentRef = ref();
let loading = ref(false);
let btnLoadingCheckCoupon = ref(false);
let btnLoadingAddOrder = ref(false);

let selectedAddressId = ref(null);
let currentAddress = ref([]);

onMounted(async ()=>{
  await getData(true);
})

watch(() => store.state.addressComponentKey, async function() {
  await addressComponentRef.value.getAddresses(false)
  selectedAddressId.value = addressComponentRef.value.selectedAddressId;
  currentAddress.value = addressComponentRef.value.currentAddress;
});

const calcTransportAmount = computed(()=>{
  let amount = 0;
  let baskets = [...new Map(Object.values(store.state.baskets).map(item => [item['product']['category']['weight_type'], item])).values()]
  for(let i =0; i < baskets.length; i++){
    let basket = baskets[i];
    let transport = transports.value.filter(item=>item.weight_type === basket.product.category.weight_type)[0];
    if(transport){
      amount += transport.fixed_price !== null ? parseInt(transport.fixed_price) : 0;
    }
  }
  return amount;
})

const productAmount = computed(()=>{
  price.value = store.state.basketOriginalAmount;
  return store.getters.numberFormat(store.state.basketOriginalAmount)
})

const fullAmount = computed(()=>{
  let amount = store.state.basketAmount
  fullPrice.value = amount;
  return amount;
})

const calcPercent = computed(()=>{
  let result = (price.value-fullPrice.value)/price.value * 100;
  return result.toFixed(2);
})

async function getData(_loading=true){
  loading.value = _loading;
  await addressComponentRef.value.getAddresses(_loading)
  selectedAddressId.value = addressComponentRef.value.selectedAddressId;
  currentAddress.value = addressComponentRef.value.currentAddress;
  await store.dispatch('getBasket', {loading:_loading,withCoupon:true,withCount:true,withProduct:true})
  await getBasketExtraData();
  transportAmount.value = calcTransportAmount.value;
  totalAmount.value = fullAmount.value + transportAmount.value;
  loading.value = false;
}

function calcWalletPrice(){
  totalAmount.value = walletAmount.value > totalAmount.value ? 0 : totalAmount.value - walletAmount.value;
  walletAmount.value = 0;
  useWalletAmount.value = true;
  Toast.success('با موفقییت اعمال شد')
}

async function getBasketExtraData(){
  await axios.get(`${store.state.api}get-basket-extra-data`).then(resp=>{
    let data = resp.data.data;
    walletAmount.value = data.amount;
    transports.value = data.transports;
  }).catch(err=>{})
}

async function checkCoupon(){
  couponMessage.value = '';
  btnLoadingCheckCoupon.value = true;
  await axios.post(`${store.state.api}check-coupon`,{'code':couponCode.value}).then(async resp=>{
    let data = resp.data.data;
    if(data.status){
      await getData(false);
      couponMessage.value = 'با موفقیت اعمال شد'
    }else{
      couponMessage.value = 'تخفیف معتبر نیست';
    }
  }).catch(err=>{
    Toast.error('یک خطای غیر منتظره رخ داده است')
  })
  btnLoadingCheckCoupon.value = false;
}

async function addOrder(){
  btnLoadingAddOrder.value = true;
  await axios.post(`${store.state.api}add-order`,{use_wallet:useWalletAmount.value}).then(resp=>{
    setTimeout(()=>{
      Toast.success()
    },2000)
    let redirectUrl = resp.data.data.redirect_url;
    window.location.href = redirectUrl ? redirectUrl : '/payment-alert?status=success&msg=سفارش با موفقیت ثبت شد'
  }).catch(err=>{
    Toast.error('یک خطای غیر منتظره رخ داده است')
  })
  btnLoadingAddOrder.value = false;
}

function openAddressModal(closeAddressList=true){
  addressComponentRef.value.openAddressModal(closeAddressList);
  if(closeAddressList){
    addressComponentRef.value.openAddressModal(closeAddressList);
  }
}

function openAddressListModal(){
  addressComponentRef.value.openAddressListModal();
}


</script>

<style scoped>
#router-link {
  display: inline-block;
}
</style>