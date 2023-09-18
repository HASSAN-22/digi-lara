<template>
  <div class="mt-10 px-20 relative fm:w-[990px]">
    <div class="flex justify-between items-center w-full fm:w-[990px]" id="hiddenForPrint">
      <div v-if="logo != null">
        <img :src="logo" class="w-[55px] h-[55px]"/>
      </div>
      <Button @click="print()" text="پرینت" />
    </div>
    <div class="border-b-4 border-gray-200 my-6 fm:w-[990px]"></div>
    <div class="fm:w-[990px] flex flex-col" id="print" v-if="order !== null">
      <div class="flex flex-col gap-3 fm:w-[990px]">
        <div class="flex items-center justify-between gap-2">
          <div class="border border-gray-200 bg-gray-100 h-[6.6rem] w-[5rem] flex items-center justify-center">
            <div class="rotate-[270deg] text-center">
              <span>حق‌العمل کار</span>
            </div>
          </div>
          <div class="flex flex-col gap-8 border border-gray-200 p-4 w-[77%]" v-if="shop_info !== null">
            <div>
              <ul class="flex justify-between">
                <li class="flex items-center gap-2">
                  <span class="!font-medium text-sm">فروشنده:</span>
                  <span class="text-sm">{{shopInfo.company_name}} ({{shopInfo.ir_company_type}})</span>
                </li>
                <li class="flex items-center gap-2">
                  <span class="!font-medium text-sm">شناسه ملی:</span>
                  <span class="text-sm">{{shopInfo.national_identity_number}}</span>
                </li>
                <li class="flex items-center gap-2">
                  <span class="!font-medium text-sm">شماره ثبت:</span>
                  <span class="text-sm">{{shopInfo.registration_number}}</span>
                </li>
                <li class="flex items-center gap-2">
                  <span class="!font-medium text-sm">شماره اقتصادی:</span>
                  <span class="text-sm">{{shopInfo.economic_number}}</span>
                </li>
              </ul>
            </div>
            <div>
              <ul class="flex justify-between">
                <li class="flex items-center gap-2">
                  <span class="!font-medium text-sm">نشانی شرکت:</span>
                  <span class="text-sm">{{shopInfo.address}}</span>
                </li>
                <li class="flex items-center gap-2">
                  <span class="!font-medium text-sm">کدپستی:</span>
                  <span class="text-sm">{{shopInfo.postal_code}}</span>
                </li>
                <li class="flex items-center gap-2">
                  <span class="!font-medium text-sm">تلفن :</span>
                  <span class="text-sm">{{shopInfo.phone}}</span>
                </li>
                <li class="flex items-center gap-2">
                  <span class="!font-medium text-sm">فکس:</span>
                  <span class="text-sm">{{shopInfo.fax}}</span>
                </li>
              </ul>
            </div>
          </div>
          <div class="flex items-center justify-between border border-gray-200 p-4 w-[15%] h-[6.6rem] text-center">
            <span>شماره فاکتور:</span>
            <span>{{order.id}}</span>
          </div>
        </div>
        <div class="flex items-center justify-between gap-2" v-if="buyer !== null">
          <div class="border border-gray-200 bg-gray-100 h-[6.6rem] w-[5rem] flex items-center justify-center">
            <div class="rotate-[270deg] text-center">
              <span>خریدار</span>
            </div>
          </div>
          <div class="flex flex-col gap-8 border border-gray-200 p-4 w-[77%]">
            <div>
              <ul class="flex justify-between">
                <li class="flex items-center gap-2">
                  <span class="!font-medium text-sm">خریدار:</span>
                  <span class="text-sm">{{buyer.name}}</span>
                </li>
                <li class="flex items-center gap-2">
                  <span class="!font-medium text-sm">	شماره‌اقتصادی / شماره‌ملی:</span>
                  <span class="text-sm">{{buyer.economic_national_id}}</span>
                </li>
                <li class="flex items-center gap-2">
                  <span class="!font-medium text-sm">شناسه ملی:</span>
                  <span class="text-sm">{{buyer.national_id}}</span>
                </li>
                <li class="flex items-center gap-2">
                  <span class="!font-medium text-sm">شماره ثبت:</span>
                  <span class="text-sm">{{buyer.registration_id}}</span>
                </li>
              </ul>
            </div>
            <div>
              <ul class="flex justify-between">
                <li class="flex items-center gap-2">
                  <span class="!font-medium text-sm">نشانی :</span>
                  <span class="text-sm">{{buyer.address.address}}</span>
                </li>
                <li class="flex items-center gap-2">
                  <span class="!font-medium text-sm">کدپستی:</span>
                  <span class="text-sm">{{buyer.address.postal_code}}</span>
                </li>
                <li class="flex items-center gap-2">
                  <span class="!font-medium text-sm">شماره تماس:</span>
                  <span class="text-sm">{{buyer.address.mobile}}</span>
                </li>
              </ul>
            </div>
          </div>
          <div class="flex flex-col justify-between border border-gray-200 p-4 w-[15%] h-[6.6rem]">
            <div class="flex justify-between">
              <span>تاریخ:</span>
              <span>{{order.ir_created_at}}</span>
            </div>
            <div class="flex justify-between">
              <span>شماره فاکتور:</span>
              <span>{{order.id}}</span>
            </div>
          </div>
        </div>
      </div>
      <div class="flex flex-col gap-3 fm:w-[990px]">
        <div class="flex flex-col">
          <div class="overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 sm:px-6 lg:px-8">
              <div class="overflow-hidden fm:w-[990px]">
                <table class="min-w-full border text-center text-sm font-light fm:w-[990px]">
                  <thead class="border-b font-medium">
                    <tr class="bg-gray-100">
                      <th scope="col" class="border-r border-gray-300">ردیف</th>
                      <th scope="col" class="border-r border-gray-300">شناسه کالا</th>
                      <th class="border-r border-gray-300">شرح کالا</th>
                      <th scope="col" class="border-r border-gray-300">آمر</th>
                      <th scope="col" class="border-r border-gray-300">تعداد</th>
                      <th scope="col" class="border-r border-gray-300">مبلغ واحد (ریال)</th>
                      <th scope="col" class="border-r border-gray-300">مبلغ کل (ریال)</th>
                      <th scope="col" class="border-r border-gray-300">تخفیف (ریال)</th>
<!--                      <th scope="col" class="border-r border-gray-300">مبلغ کل پس از تخفیف (ریال)</th>-->
                      <th scope="col" class="border-r border-gray-300">جمع کل پس از تخفیف (ریال)</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr class="border-b" v-for="(detail,index) in order.order_details" :key="detail.id">
                      <td class="whitespace-nowrap border-r px-6 py-4 font-medium">{{index+1}}</td>
                      <td class="whitespace-nowrap border-r px-6 py-4">{{detail.sku}}</td>
                      <td class="border-r px-6 py-4 w-[20%]">
                        <span>{{detail.name}}</span>
                      </td>
                      <td class="whitespace-nowrap border-r px-6 py-4">{{detail.user.become_sellers[0].shop_name}}</td>
                      <td class="whitespace-nowrap border-r px-6 py-4">{{detail.count}}</td>
                      <td class="whitespace-nowrap border-r px-6 py-4">{{$store.getters.numberFormat(detail.price)}}</td>
                      <td class="whitespace-nowrap border-r px-6 py-4">{{$store.getters.numberFormat(detail.price*detail.count)}}</td>
                      <td class="whitespace-nowrap border-r px-6 py-4">{{$store.getters.numberFormat(Math.abs(detail.discount))}}</td>
<!--                      <td class="whitespace-nowrap border-r px-6 py-4">{{$store.getters.numberFormat(detail.amount)}}</td>-->
                      <td class="whitespace-nowrap border-r px-6 py-4">{{$store.getters.numberFormat(detail.amount)}}</td>
                    </tr>
                    <tr class="border-b">
                      <td colspan="4" class="whitespace-nowrap border-r px-6 py-4">جمع کل</td>
                      <td class="whitespace-nowrap border-r px-6 py-4">{{$store.getters.numberFormat(order.order_details.reduce((n, {count}) => n + count, 0))}}</td>
                      <td class="whitespace-nowrap border-r px-6 py-4">{{$store.getters.numberFormat(order.order_details.reduce((n, {price}) => n + price, 0))}}</td>
                      <td class="whitespace-nowrap border-r px-6 py-4">{{$store.getters.numberFormat(order.order_details.reduce((n, {price,count}) => n + (price*count), 0))}}</td>
                      <td class="whitespace-nowrap border-r px-6 py-4">{{$store.getters.numberFormat(Math.abs(order.order_details.reduce((n, {discount}) => n + discount, 0)))}}</td>
<!--                      <td class="whitespace-nowrap border-r px-6 py-4">{{$store.getters.numberFormat(order.order_details.reduce((n, {amount,discount}) => n + (amount-discount), 0))}}</td>-->
                      <td class="whitespace-nowrap border-r px-6 py-4">{{$store.getters.numberFormat(order.order_details.reduce((n, {amount}) => n + amount, 0))}}</td>
                    </tr>
                    <tr class="border-b">
                      <td colspan="4" class="whitespace-nowrap border-r px-6 py-4"></td>
                      <td colspan="4" class="whitespace-nowrap border-r px-6 py-4">جمع کل پس از تخفیف (ریال):</td>
                      <td class="whitespace-nowrap border-r px-6 py-4">{{$store.getters.numberFormat(((order.order_details.reduce((n, {amount}) => n + amount, 0))))}}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="flex flex-col gap-10 fm:w-[990px] border border-gray-200 mb-10 p-4">
        <ul class="flex justify-between">
          <li class="flex flex-col gap-8">
            <span>مهر و امضای فروشنده:</span>
            <img :src="shopInfo.signature" class="w-[120px] h-[120px]" />
          </li>
          <li class="flex flex-col gap-8">
            <span>مهر و امضای خریدار:</span>
          </li>
        </ul>
      </div>
    </div>
    <Loading :loading="loading" />
  </div>
</template>

<script>
export default{name: "Invoice"}
</script>

<script setup>

import Button from "@/components/Button";
import {onMounted, ref} from "vue";
import Loading from "@/components/Loading";
import axios from "@/plugins/axios";
import { useRoute } from "vue-router"
import store from "@/store";
import Toast from "@/plugins/toast";

let loading = ref(false);
const route = useRoute();

let orderId = ref(route.params['order'])
let shopInfo = ref(null)
let buyer = ref(null)
let order = ref(null)
let logo = ref(null)

onMounted(async ()=>{
  await getData()
})

async function getData(){
  loading.value = true;
  await axios.get(`${store.state.api}order/invoice/${orderId.value}`).then(resp=>{
    let data = resp.data.data
    shopInfo.value = data.shop_info;
    buyer.value = data.buyer;
    order.value = data.order;
    logo.value = data.logo;
  }).catch(err=>{
    Toast.error();
  })
  loading.value = false;
}

function print(){
  window.print();
}
</script>

<style scoped>
@media print {
  @page { margin-top: 0;margin-bottom: 0; }
  * {
    -webkit-print-color-adjust: exact !important;   /* Chrome, Safari 6 – 15.3, Edge */
    color-adjust: exact !important;                 /* Firefox 48 – 96 */
    print-color-adjust: exact !important;           /* Firefox 97+, Safari 15.4+ */
  }
  #hiddenForPrint * {
    visibility: hidden;
  }

}
</style>