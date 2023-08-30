<template>
  <div class="mt-10 fd:px-8">
    <div class="flex justify-center">
      <span class="!font-medium text-lg">تاریخچه سفارشات</span>
    </div>
    <div class="mt-10 px-3 mb-3 border border-gray-200 rounded-lg p-4">
      <ul class="flex gap-8 items-center">
        <li @click="changeTypeTab(1)" :class="['fm:text-sm cursor-pointer', typeOrderSelect === 1 ? 'border-b-2 border-red-500 text-red-500' : 'text-gray-500']">سفارشات من</li>
        <li v-if="['seller','admin'].includes($store.state.user.access)" @click="changeTypeTab(2)" :class="['fm:text-sm cursor-pointer', typeOrderSelect === 2 ? 'border-b-2 border-red-500 text-red-500' : 'text-gray-500']">سفارشات خریداران</li>
      </ul>
      <div v-if="typeOrderSelect === 1" class="mt-12">
        <ul class="flex gap-8 items-center border-b border-gray-200 mb-10">
          <li @click="myOrderSelectTab(1)" :class="['cursor-pointer flex items-center fm:gap-[2px] fd:gap-1 pb-2', myOrdersTab === 1 ? 'border-b-2 border-red-500' : 'text-gray-500']">
            <span  :class="['fm:text-sm',myOrdersTab === 1 ? 'text-red-500' : 'text-gray-700']">جاری</span>
            <span :class="['text-xs p-1 fm:p-[3px] rounded',myOrdersTab === 1 ? 'bg-red-500 text-white' : 'bg-gray-200 text-gray-700']">{{currentOrders.length}}</span>
          </li>
          <li @click="myOrderSelectTab(2)" :class="['cursor-pointer flex items-center fm:gap-[2px] fd:gap-1 pb-2', myOrdersTab === 2 ? 'border-b-2 border-red-500' : 'text-gray-500']">
            <span  :class="['fm:text-sm',myOrdersTab === 2 ? 'text-red-500' : 'text-gray-700']">تحویل شده</span>
            <span :class="['text-xs p-1 fm:p-[3px] rounded',myOrdersTab === 2 ? 'bg-red-500 text-white' : 'bg-gray-200 text-gray-700']">{{deliveredOrders.length}}</span>
          </li>
          <li @click="myOrderSelectTab(3)" :class="['cursor-pointer flex items-center fm:gap-[2px] fd:gap-1 pb-2', myOrdersTab === 3 ? 'border-b-2 border-red-500' : 'text-gray-500']">
            <span  :class="['fm:text-sm',myOrdersTab === 3 ? 'text-red-500' : 'text-gray-700']">مرجوع شده</span>
            <span :class="['text-xs p-1 fm:p-[3px] rounded',myOrdersTab === 3 ? 'bg-red-500 text-white' : 'bg-gray-200 text-gray-700']">{{returnedOrders.length}}</span>
          </li>
          <li @click="myOrderSelectTab(4)" :class="['cursor-pointer flex items-center fm:gap-[2px] fd:gap-1 pb-2', myOrdersTab === 4 ? 'border-b-2 border-red-500' : 'text-gray-500']">
            <span  :class="['fm:text-sm',myOrdersTab === 4 ? 'text-red-500' : 'text-gray-700']">لغو شده</span>
            <span :class="['text-xs p-1 fm:p-[3px] rounded',myOrdersTab === 4 ? 'bg-red-500 text-white' : 'bg-gray-200 text-gray-700']">{{canceledOrders.length}}</span>
          </li>
        </ul>
        <div v-if="myOrdersTab === 1">
          <div v-if="currentOrders.length > 0">
            <OrderList :key="orderListKey" :orders="currentOrders" @parentMethod="getCurrent" />
            <div v-if="currentOrders.length > 0">
              <Paginate :current="$store.state.current" :next="$store.state.next" :previous="$store.state.previous" :total="$store.state.total" @callGetPage="getCurrent"/>
            </div>
          </div>
          <div v-else class="flex justify-center">
            <img src="@/assets/images/no-data.svg" class="w-[300px] fm:w-[200px]"/>
          </div>
        </div>
        <div v-if="myOrdersTab === 2">
          <div v-if="deliveredOrders.length > 0">
            <OrderList :key="orderListKey" :orders="deliveredOrders" @parentMethod="getDelivered"/>
            <div v-if="deliveredOrders.length > 0">
              <Paginate :current="$store.state.current" :next="$store.state.next" :previous="$store.state.previous" :total="$store.state.total" @callGetPage="getDelivered"/>
            </div>
          </div>
          <div v-else class="flex justify-center">
            <img src="@/assets/images/no-data.svg" class="w-[300px] fm:w-[200px]"/>
          </div>
        </div>
        <div v-if="myOrdersTab === 3">
          <div v-if="returnedOrders.length > 0">
            <OrderList :key="orderListKey" :orders="returnedOrders" :showPercentage="false" @parentMethod="getReturned"/>
            <div v-if="returnedOrders.length > 0">
              <Paginate :current="$store.state.current" :next="$store.state.next" :previous="$store.state.previous" :total="$store.state.total" @callGetPage="getReturned"/>
            </div>
          </div>
          <div v-else class="flex justify-center">
            <img src="@/assets/images/no-data.svg" class="w-[300px] fm:w-[200px]"/>
          </div>
        </div>
        <div v-if="myOrdersTab === 4">
          <div v-if="canceledOrders.length > 0">
            <OrderList :key="orderListKey" :orders="canceledOrders" :showPercentage="false" @parentMethod="getCanceled"/>
            <div v-if="canceledOrders.length > 0">
              <Paginate :current="$store.state.current" :next="$store.state.next" :previous="$store.state.previous" :total="$store.state.total" @callGetPage="getCanceled"/>
            </div>
          </div>
          <div v-else class="flex justify-center">
            <img src="@/assets/images/no-data.svg" class="w-[300px] fm:w-[200px]"/>
          </div>
        </div>
      </div>
      <div v-if="typeOrderSelect === 2" class="mt-12">
        <ul class="flex gap-8 items-center">
          <li @click="customerOrderSelectTab(1)" :class="['cursor-pointer flex items-center gap-1 pb-2', customerOrdersSelected === 1 ? 'border-b-2 border-red-500' : 'text-gray-500']">
            <span  :class="[customerOrdersSelected === 1 ? 'text-red-500' : 'text-gray-700']">جاری</span>
            <span :class="['text-xs p-1 rounded',customerOrdersSelected === 1 ? 'bg-red-500 text-white' : 'bg-gray-200 text-gray-700']">{{customerCurrentOrders.length}}</span>
          </li>
          <li @click="customerOrderSelectTab(2)" :class="['cursor-pointer flex items-center gap-1 pb-2', customerOrdersSelected === 2 ? 'border-b-2 border-red-500' : 'text-gray-500']">
            <span  :class="[customerOrdersSelected === 2 ? 'text-red-500' : 'text-gray-700']">تحویل شده</span>
            <span :class="['text-xs p-1 rounded',customerOrdersSelected === 2 ? 'bg-red-500 text-white' : 'bg-gray-200 text-gray-700']">{{customerDeliveredOrders.length}}</span>
          </li>
          <li @click="customerOrderSelectTab(3)" :class="['cursor-pointer flex items-center gap-1 pb-2', customerOrdersSelected === 3 ? 'border-b-2 border-red-500' : 'text-gray-500']">
            <span  :class="[customerOrdersSelected === 3 ? 'text-red-500' : 'text-gray-700']">مرجوع شده</span>
            <span :class="['text-xs p-1 rounded',customerOrdersSelected === 3 ? 'bg-red-500 text-white' : 'bg-gray-200 text-gray-700']">{{customerReturnedOrders.length}}</span>
          </li>
          <li @click="customerOrderSelectTab(4)" :class="['cursor-pointer flex items-center gap-1 pb-2', customerOrdersSelected === 4 ? 'border-b-2 border-red-500' : 'text-gray-500']">
            <span  :class="[customerOrdersSelected === 4 ? 'text-red-500' : 'text-gray-700']">لغو شده</span>
            <span :class="['text-xs p-1 rounded',customerOrdersSelected === 4 ? 'bg-red-500 text-white' : 'bg-gray-200 text-gray-700']">{{customerCanceledOrders.length}}</span>
          </li>
        </ul>
        <div v-if="customerOrdersSelected === 1">
          <div v-if="customerCurrentOrders.length > 0">
            <OrderList :key="customerOrderListKey" :orders="customerCurrentOrders" :showPercentage="false" @parentMethod="getCustomerCurrent"/>
            <div v-if="customerCurrentOrders.length > 0">
              <Paginate :current="$store.state.current" :next="$store.state.next" :previous="$store.state.previous" :total="$store.state.total" @callGetPage="getCustomerCurrent"/>
            </div>
          </div>
          <div v-else class="flex justify-center">
            <img src="@/assets/images/no-data.svg" class="w-[300px] fm:w-[200px]"/>
          </div>
        </div>
        <div v-if="customerOrdersSelected === 2">
          <div v-if="customerDeliveredOrders.length > 0">
            <OrderList :key="customerOrderListKey" :orders="customerDeliveredOrders" :showPercentage="false" @parentMethod="getCustomerDelivered"/>
            <div v-if="customerDeliveredOrders.length > 0">
              <Paginate :current="$store.state.current" :next="$store.state.next" :previous="$store.state.previous" :total="$store.state.total" @callGetPage="getCustomerDelivered"/>
            </div>
          </div>
          <div v-else class="flex justify-center">
            <img src="@/assets/images/no-data.svg" class="w-[300px] fm:w-[200px]"/>
          </div>
        </div>
        <div v-if="customerOrdersSelected === 3">
          <div v-if="customerReturnedOrders.length > 0">
            <OrderList :key="customerOrderListKey" :orders="customerReturnedOrders" :showPercentage="false" @parentMethod="getCustomerReturned"/>
            <div v-if="customerReturnedOrders.length > 0">
              <Paginate :current="$store.state.current" :next="$store.state.next" :previous="$store.state.previous" :total="$store.state.total" @callGetPage="getCustomerReturned"/>
            </div>
          </div>
          <div v-else class="flex justify-center">
            <img src="@/assets/images/no-data.svg" class="w-[300px] fm:w-[200px]"/>
          </div>
        </div>
        <div v-if="customerOrdersSelected === 4">
          <div v-if="customerCanceledOrders.length > 0">
            <OrderList :key="customerOrderListKey" :orders="customerCanceledOrders" :showPercentage="false" @parentMethod="getCustomerCanceled"/>
            <div v-if="customerCanceledOrders.length > 0">
              <Paginate :current="$store.state.current" :next="$store.state.next" :previous="$store.state.previous" :total="$store.state.total" @callGetPage="getCustomerCanceled"/>
            </div>
          </div>
          <div v-else class="flex justify-center">
            <img src="@/assets/images/no-data.svg" class="w-[300px] fm:w-[200px]"/>
          </div>
        </div>
      </div>
    </div>

    <Meta :title="$store.state.siteName + ` | تاریخچه سفارشات `"/>
    <Loading :loading="loading" />
  </div>
</template>

<script>
export default{name: "Order"}
</script>

<script setup>
import {ref, onMounted} from 'vue';
import Meta from "@/components/Meta.vue";
import Loading from "@/components/Loading.vue";
import Paginate from "@/components/Paginate.vue";
import axios from "@/plugins/axios";
import store from "@/store";
import OrderList from "@/components/OrderList.vue";
import UserCanAction from "@/components/UserCanAction.vue";


let model = ref('order')
let search = ref('')
let loading = ref(false)


let orderListKey = ref(0)
let customerOrderListKey = ref(0)
let typeOrderSelect = ref(1)
let myOrdersTab = ref(1)
let customerOrdersSelected = ref(1)

let currentOrders = ref([])
let deliveredOrders = ref([])
let canceledOrders = ref([])
let returnedOrders = ref([])

let customerCurrentOrders = ref([])
let customerDeliveredOrders = ref([])
let customerCanceledOrders = ref([])
let customerReturnedOrders = ref([])

onMounted(async()=>{
  orderListKey.value ++;
  customerOrderListKey.value ++;
  await myOrderSelectTab(myOrdersTab.value)
  await getDelivered(false);
  await getCanceled(false);
  await getReturned(false);
});

async function getCurrent(_loading = true,page=1){
  loading.value = _loading;
  await axios.get(`${store.state.api}${model.value}/current?page=${page}&search=${search.value}`).then(resp=>{
    let data = resp.data;
    currentOrders.value = data.data;
    store.commit('paginate',data.meta);
  }).catch(err=>{
    store.commit('handleError',err)
  })
  loading.value = false;
}

async function getDelivered(_loading = true,page=1){
  loading.value = _loading;
  await axios.get(`${store.state.api}${model.value}/delivered?page=${page}&search=${search.value}`).then(resp=>{
    let data = resp.data;
    deliveredOrders.value = data.data;
    store.commit('paginate',data.meta);
  }).catch(err=>{
    store.commit('handleError',err)
  })
  loading.value = false;
}

async function getReturned(_loading = true,page=1){
  loading.value = _loading;
  await axios.get(`${store.state.api}${model.value}/returned?page=${page}&search=${search.value}`).then(resp=>{
    let data = resp.data;
    returnedOrders.value = data.data;
    store.commit('paginate',data.meta);
  }).catch(err=>{
    store.commit('handleError',err)
  })
  loading.value = false;
}

async function getCanceled(_loading = true,page=1){
  loading.value = _loading;
  await axios.get(`${store.state.api}${model.value}/canceled?page=${page}&search=${search.value}`).then(resp=>{
    let data = resp.data;
    canceledOrders.value = data.data;
    store.commit('paginate',data.meta);
  }).catch(err=>{
    store.commit('handleError',err)
  })
  loading.value = false;
}

// ############################## Customer ################################################

async function getCustomerCurrent(_loading = true,page=1){
  loading.value = _loading;
  await axios.get(`${store.state.api}${model.value}/customer-current?page=${page}&search=${search.value}`).then(resp=>{
    let data = resp.data;
    customerCurrentOrders.value = data.data;
    store.commit('paginate',data.meta);
  }).catch(err=>{
    store.commit('handleError',err)
  })
  loading.value = false;
}

async function getCustomerDelivered(_loading = true,page=1){
  loading.value = _loading;
  await axios.get(`${store.state.api}${model.value}/customer-delivered?page=${page}&search=${search.value}`).then(resp=>{
    let data = resp.data;
    customerDeliveredOrders.value = data.data;
    store.commit('paginate',data.meta);
  }).catch(err=>{
    store.commit('handleError',err)
  })
  loading.value = false;
}

async function getCustomerReturned(_loading = true,page=1){
  loading.value = _loading;
  await axios.get(`${store.state.api}${model.value}/customer-returned?page=${page}&search=${search.value}`).then(resp=>{
    let data = resp.data;
    customerReturnedOrders.value = data.data;
    store.commit('paginate',data.meta);
  }).catch(err=>{
    store.commit('handleError',err)
  })
  loading.value = false;
}

async function getCustomerCanceled(_loading = true,page=1){
  loading.value = _loading;
  await axios.get(`${store.state.api}${model.value}/customer-canceled?page=${page}&search=${search.value}`).then(resp=>{
    let data = resp.data;
    customerCanceledOrders.value = data.data;
    store.commit('paginate',data.meta);
  }).catch(err=>{
    store.commit('handleError',err)
  })
  loading.value = false;
}

async function myOrderSelectTab(tab){
  orderListKey.value ++;
  myOrdersTab.value = tab;
  if(tab === 1){
    await getCurrent(true, store.state.current);
  }else if(tab === 2){
    await getDelivered(true, store.state.current);
  }else if(tab === 3){
    await getReturned(true, store.state.current);
  }else if(tab === 4){
    await getCanceled(true, store.state.current);
  }
}

async function customerOrderSelectTab(tab){
  customerOrderListKey.value ++;
  customerOrdersSelected.value = tab;
  if(tab === 1){
    await getCustomerCurrent(true, store.state.current);
  }else if(tab === 2){
    await getCustomerDelivered(true, store.state.current);
  }else if(tab === 3){
    await getCustomerReturned(true, store.state.current);
  }else if(tab === 4){
    await getCustomerCanceled(true, store.state.current);
  }
}

async function changeTypeTab(tab){
  typeOrderSelect.value = tab;
  if(tab === 1){
    await getCurrent(true, store.state.current);
    await getDelivered(false, store.state.current);
    await getReturned(false, store.state.current);
    await getCanceled(false, store.state.current);
  }else{
    await getCustomerCurrent(true, store.state.current);
    await getCustomerDelivered(false, store.state.current);
    await getCustomerReturned(false, store.state.current);
    await getCustomerCanceled(false, store.state.current);
  }
}

function searchData(text){
  search.value = text
}


</script>
