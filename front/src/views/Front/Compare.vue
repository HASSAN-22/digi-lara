<template>
  <div>
    <div class="container mx-auto">
      <div class="mt-20 mx-10 relative">
        <div class="flex justify-evenly gap-1">
          <div class="fd:w-[21rem] fm:w-full" v-for="(product,index) in products" :key="product.id">
            <div>
              <div :class="['border border-gray-200 rounded-lg fd:p-3 h-[22rem] fm:p-1 flex flex-col fd:w-[21rem] gap-5 items-center bg-white']">
                <div class="flex">
                  <div :class="['w-[10rem] fd:w-[10rem]']">
                    <img :src="$store.state.url + product.image" />
                  </div>
                  <span class="cursor-pointer" @click="removeCompare(product.id)"><i class="far fa-times"></i></span>
                </div>
                <div class="text-center">
                  <routerLink :to="{name:'ProductDetail', params:{slug:product.slug}}">{{product.ir_name}}</routerLink>
                </div>
                <div class="flex items-center gap-1">
                  <span class="fm:text-sm"><i class="far fa-star text-yellow-300 fm:text-sm"></i></span>
                  <span class="fm:text-sm">{{fixedRating(product.rating)}}</span>
                </div>
                <div>
                  <span class="fm:text-sm">{{productPrice(product).amount}} ریال</span>
                </div>
              </div>
            </div>
            {{}}
            <div class="flex flex-col gap-4 mt-5 border-b border-gray-200 pb-2" v-for="(productProperty,i) in mergeProperty(product.product_properties)" :key="i">
              <span class="text-gray-500 fm:text-sm">{{properties[i]}}</span>
              <div class="flex gap-2">
                <span class="fm:text-sm !font-medium">{{productProperty}}</span>
              </div>
            </div>
          </div>

          <div :class="['mr-5', (isMobile && products.length >= 2) ? 'hidden' : (products.length >= 4 ? 'hidden' : '')]">
            <Button text="انتخاب کالا" @click="showModal()" />
          </div>
        </div>
      </div>
    </div>
    <Modal title="انتخاب کالا" ref="openModal" width="w-[60%]">
      <div>
        <ProductComponent :products="moreProducts" boxClass="w-auto" @addCompare="addCompare" ref="productComponentRef" :compare="true" />
      </div>
    </Modal>
    <Meta :title="$store.state.siteName + ` مقایسه ها `"/>
    <Loading :loading="loading" />
  </div>
</template>

<script>
export default{name: "Compare"}
</script>

<script setup>
import {computed, onMounted, ref} from "vue";
import Meta from "@/components/Meta.vue";
import Loading from "@/components/Loading";
import ProductComponent from "@/components/ProductComponent";
import Modal from "@/components/Modal";
import Button from "@/components/Button";
import axios from "@/plugins/axios";
import store from "@/store";
import Toast from "@/plugins/toast";

let productComponentRef = ref(null);
let openModal = ref(null);
let loading = ref(false);
let products = ref([]);
let moreProducts = ref([])
let properties = ref([]);
let isMobile = ref(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent));
onMounted(async ()=>{
  await getData();
  window.addEventListener('resize', onResize)
})

function mergeProperty(_properties){
  let data = [];
  for(let i = 0; i < properties.value.length; i++){
    let allProperties = _properties.filter(item=>item.property.property === properties.value[i]);
    let names = allProperties.length > 0 ? allProperties.map(item=>item.property_type.name) : ['---']
    data.push(names.join(','))
  }
  return data
}

async function getData(_loading=true){
  loading.value = _loading;
  properties.value = [];
  await axios.get(`${store.state.api}get-compares`).then(resp=>{
    let data = resp.data.data;
    products.value = data.compares.sort((a, b) => b.product_properties.length - a.product_properties.length);
    moreProducts.value = data.products;
    for(let i = 0; i<products.value.length;i++){
      let productProperties = products.value[i].product_properties
      properties.value.push(productProperties.map(item=>item.property.property))
    }
    properties.value = properties.value.reduce((r, e) => (r.push(...e), r), []);
    properties.value = [...new Set(properties.value)]
  })
  loading.value = false;
}

const fixedRating =  computed(()=>(_rating)=>{
  return _rating.toFixed(1)
})

const productPrice = computed(()=>(product)=>{
  let amount = 0;
  if(product.amazing_offer_status === 'yes'){
    amount = store.getters.getDiscount(product.price, product.amazing_offer_percent,false);
  }else if(product.amazing_price !== null && product.amazing_price > 0){
    amount = product.amazing_price
  }else{
    amount = product.price
  }
  return {amount:store.getters.numberFormat(amount)}
})

async function addCompare(productId){
  productComponentRef.value.btnLoading = true;
  if(store.state.user.token){
    await axios.post(`${store.state.api}add-compare/${productId}`).then(async resp=>{
      await getData(false);
      Toast.success('با موفقیت اضافه شد')
      openModal.value.toggleModal()
    }).catch(err=>{
      Toast.error('یک خطای غیر منظره رخ داده است')
    })
  }else{
    Toast.error('لطفا وارد حساب کاربری خود شوید')
  }
  productComponentRef.value.btnLoading = true;
}

async function removeCompare(productId){
  loading.value = true;
  if(store.state.user.token){
    await axios.post(`${store.state.api}remove-compare/${productId}`).then(async resp=>{
      await getData(false);
      Toast.success('با موفقیت حذف شد')
    }).catch(err=>{
      Toast.error('یک خطای غیر منظره رخ داده است')
    })
  }else{
    Toast.error('لطفا وارد حساب کاربری خود شوید')
  }
  loading.value = false;
}


function showModal(){
  openModal.value.toggleModal()
}

function onResize() {
  isMobile.value = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
}

</script>

<style scoped>
#router-link {
  display: inline-block;
}
</style>