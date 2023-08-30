<template>
  <div class="mt-10 px-8">
    <div class="mt-10 px-3 mb-3">
      <div class="flex flex-col gap-5 border border-gray-200 rounded-lg p-3 mb-5" v-if="!isUser">
        <div class="flex justify-between items-center">
          <div class="w-[25%] flex gap-8 bg-green-500 items-center justify-between rounded-lg border border-gray-200 p-3">
            <div class="flex flex-col items-center">
              <span class="!font-medium fm:text-sm text-white">سفارشات</span>
              <div class="flex flex-col">
                <span class="fm:text-sm text-white">{{orderCount}} سفارش</span>
              </div>
            </div>
            <span><i class="far fa-bag-shopping text-2xl fm:text-lg text-white"></i></span>
          </div>
          <div class="w-[25%] flex gap-8 bg-blue-500 items-center justify-between rounded-lg border border-gray-200 p-3">
            <div class="flex flex-col items-center">
              <span class="!font-medium fm:text-sm text-white">کالاها</span>
              <div class="flex flex-col">
                <span class="fm:text-sm text-white">{{productCount}} کالا</span>
              </div>
            </div>
            <span><i class="far fa-cube text-2xl fm:text-lg text-white"></i></span>
          </div>
          <div class="w-[25%] flex gap-8 bg-yellow-500 items-center justify-between rounded-lg border border-gray-200 p-3" v-if="isAdmin">
            <div class="flex flex-col items-center">
              <span class="!font-medium fm:text-sm text-white">کاربران</span>
              <div class="flex flex-col">
                <span class="fm:text-sm text-white">{{userCount}} کاربر</span>
              </div>
            </div>
            <span><i class="far fa-users text-2xl fm:text-lg text-white"></i></span>
          </div>
          <div class="w-[25%] flex gap-8 bg-red-500 items-center justify-between rounded-lg border border-gray-200 p-3">
            <div class="flex flex-col items-center">
              <span class="!font-medium fm:text-sm text-white" v-if="isAdmin">بستانکاری</span>
              <span class="!font-medium fm:text-sm text-white" v-else>بدهکاری</span>
              <div class="flex flex-col">
                <span class="fm:text-sm text-white">{{ debtor }} ریال</span>
              </div>
            </div>
            <span><i class="far fa-money-bill text-2xl fm:text-lg text-white"></i></span>
          </div>
        </div>
      </div>

      <div class="flex flex-col gap-5 border border-gray-200 rounded-lg p-3 mb-5">
        <h4 class="!font-medium text-2xl fm:text-lg">سفارشات</h4>
        <div class="flex justify-between items-center">
          <div class="flex gap-2 items-center">
            <div>
              <img src="@/assets/images/dashboard/status-processing.svg" />
            </div>
            <div class="flex flex-col">
              <span class="fm:text-sm !font-medium">{{current}} سفارش</span>
              <span class="fm:text-sm">جاری</span>
            </div>
          </div>
          <div class="flex gap-2 items-center">
            <div>
              <img src="@/assets/images/dashboard/status-delivered.svg" />
            </div>
            <div class="flex flex-col">
              <span class="fm:text-sm !font-medium">{{delivered}} سفارش</span>
              <span class="fm:text-sm">تحویل شده</span>
            </div>
          </div>

          <div class="flex gap-2 items-center">
            <div>
              <img src="@/assets/images/dashboard/status-returned.svg" />
            </div>
            <div class="flex flex-col">
              <span class="fm:text-sm !font-medium">{{returned}} سفارش</span>
              <span class="fm:text-sm">مرجوع شده</span>
            </div>
          </div>
        </div>
      </div>

      <div class="flex flex-col gap-5 border border-gray-200 rounded-lg p-3 mb-5">
        <h4 class="!font-medium text-2xl fm:text-lg">اخرین سفارشات</h4>
        <div class="flex justify-between items-center">
          <div v-for="order in latestOrders" :key="order.id">
            <routerLink :to="{name:'ProductDetail', params:{slug:order.product.slug}}" class="flex flex-col gap-3 items-center border border-gray-100 hover:shadow p-3">
              <div class="w-[75%]">
                <img :src="$store.state.url + order.image" />
              </div>
              <span class="text-sm !font-medium">{{order.name}}</span>
              <span class="text-sm !font-medium">{{$store.getters.numberFormat(order.amount)}} ریال</span>
            </routerLink>
          </div>
        </div>
      </div>
    </div>

    <Meta :title="$store.state.siteName + ` | داشبورد `"/>
  </div>
</template>

<script>
export default{name: "Dashboard"}
</script>

<script setup>
import Meta from "@/components/Meta";
import {onMounted, ref} from "vue";
import store from "@/store";
import axios from "@/plugins/axios";

let isUser = ref(store.state.user.access === 'user')
let isAdmin = ref(store.state.user.access === 'admin')
let current = ref(0)
let debtor = ref(0)
let delivered = ref(0)
let orderCount = ref(0)
let productCount = ref(0)
let returned = ref(0)
let userCount = ref(0)
let latestOrders = ref([])

onMounted(async()=>{
  await getContent()
})

async function getContent(){
  await axios(`${store.state.api}dashboard/content`).then(resp=>{
    let data = resp.data;
    current.value = data.current;
    debtor.value = data.debtor;
    delivered.value = data.delivered;
    orderCount.value = data.order_count;
    productCount.value = data.product_count;
    returned.value = data.returned;
    userCount.value = data.user_count;
    latestOrders.value = data.latest_orders;
  })
}

</script>