<template>
  <div>
    <div v-if="category" class="container mx-auto fd:mt-12">
      <div class="mx-4">
        <div>
          <span class="text-lg !font-medium">{{category.title}}</span>
        </div>
        <div class="flex fm:flex-col-reverse fm:flex-col gap-4 mt-8">
          <div :class="['w-[20%] fm:w-full border border-gray-200 rounded-lg p-3', filter ? 'fixed overflow-y-scroll scroll right-0 w-screen h-screen top-0 bg-white z-100' : 'h-full fm:hidden sticky top-[8rem]']">
            <div v-if="btnLoading === false" class="flex flex-col gap-8">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                  <span class="fd:hidden text-xl !font-medium" @click="toggleFilter"><i class="far fa-times"></i></span>
                  <span class="text-xl !font-medium">فیلترها</span>
                </div>
                <span class="text-blue-400 cursor-pointer" @click="removeFilter('all')">حذف فیلترها</span>
              </div>
              <div class="flex items-center gap-2 flex-wrap">
                <div v-for="filter in filterSelected" :key="filter.id">
                  <div class="flex items-center gap-1 bg-blue-100 rounded-lg px-2 py-1">
                    <span class="text-sm text-blue-500">{{filter.name}}</span>
                    <span class="text-red-500 text-sm cursor-pointer" @click="removeFilter('single',filter.id)"><i class="far fa-trash"></i></span>
                  </div>
                </div>
                <div v-for="filter in sizeSelected" :key="filter.id">
                  <div class="flex items-center gap-1 bg-blue-100 rounded-lg px-2 py-1">
                    <span class="text-sm text-blue-500">{{filter.name}}</span>
                    <span class="text-red-500 text-sm cursor-pointer" @click="removeFilter('size',filter.id)"><i class="far fa-trash"></i></span>
                  </div>
                </div>
              </div>

              <!--            Color -->
              <div>
                <div class="flex flex-col justify-between items-center">
                  <span>رنگ</span>
                  <div class="flex flex-wrap items-center justify-center gap-2" v-if="allColors.length > 0">
                    <div v-for="color in allColors" :key="color.id">
                      <div
                          :class="['relative rounded-full h-[3rem] w-[3rem] border border-gray-200 flex justify-center items-center cursor-pointer', (colorSelected.find(item=>item.id === color.id) !== undefined) ? 'bg-[#19bfd3]' : 'bg-white']"
                          @click="colorSelect(color.id, color.color_code)"
                      >
                        <div class="rounded-full h-[2rem] w-[2rem] flex justify-center items-center" :style="{background:color.color_code}">
                          <span v-if="colorSelected.find(item=>item.id === color.id) !== undefined" class="flex"><i :class="['far fa-check text-lg',color.color === 'سفید' ? 'text-black' : 'text-white']"></i></span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!--            End color -->

              <!--            Exist in store -->
              <div>
                <div class="flex justify-between items-center">
                  <span>فقط کالاهای موجود</span>
                  <div class="flex items-center justify-center">
                    <label for="toggleB" class="flex items-center cursor-pointer">
                      <!-- toggle -->
                      <div class="relative">
                        <!-- input -->
                        <input type="checkbox" @change="getData(true, 1, route.query.sort)" v-model="onlyAvailableProducts" id="toggleB" class="sr-only">
                        <!-- line -->
                        <div class="block bg-white border border-gray-400 w-12 h-6 dot-line rounded-full"></div>
                        <!-- dot -->
                        <div class="dot absolute left-1 top-1 bg-gray-300 w-4 h-4 rounded-full transition"></div>
                      </div>
                    </label>
                  </div>
                </div>
              </div>
              <!--            End exist in store -->

              <!--            Amount -->
              <div class="flex flex-col gap-3">
                <button type="button" class="flex justify-between cursor-pointer" @click="toggleTab('priceRange')">
                  <span>محدوده قیمت</span>
                  <span><i class="text-sm fal fa-chevron-down"></i></span>
                </button>
                <div :class="['flex flex-col gap-4',tab === 'priceRange' ? 'block' : 'hidden']">
                  <div class="flex flex-col gap-2">
                    <div class="flex flex-row justify-between">
                      <span>از</span>
                      <span>{{ $store.getters.numberFormat(fromPrice) }}</span>
                      <span>ریال</span>
                    </div>
                    <div>
                      <input id="default-range" min="0" max="1000000000" @change="getFilterSelected()" type="range" v-model="fromPrice"
                             class="w-full h-1 bg-gray-100 rounded-lg appearance-none cursor-pointer">
                    </div>
                  </div>
                  <div class="flex flex-col gap-2">
                    <div class="flex flex-row justify-between">
                      <span>تا</span>
                      <span>{{ $store.getters.numberFormat(toPrice) }}</span>
                      <span>ریال</span>
                    </div>
                    <div>
                      <input id="default-range" type="range" min="0" @change="getFilterSelected()" max="1000000000" v-model="toPrice"
                             class="w-full h-1 bg-gray-100 rounded-lg appearance-none cursor-pointer">
                    </div>
                  </div>
                </div>
              </div>
              <!--            End amount -->

              <!--            Properties -->
              <div>
                <div class="flex flex-col gap-3">
                  <button type="button" class="flex justify-between cursor-pointer" @click="sizeTab = !sizeTab">
                    <span>اندازه</span>
                    <span><i class="text-sm fal fa-chevron-down"></i></span>
                  </button>
                  <div :class="['flex flex-col gap-4',sizeTab ? 'block' : 'hidden']">
                    <div class="flex flex-col gap-2" v-for="size in allSizes" :key="size.id">
                      <div class="flex flex-row items-center gap-4">
                        <div>
                          <input type="checkbox" :checked="sizeSelected.filter(item=>item.id === size.id).length > 0" @change="sizeSelect(size.id,size.size)"/>
                        </div>
                        <div>
                          <span>{{size.size}}</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="flex flex-col gap-3" v-for="property in allProperties" :key="property.id">
                <button type="button" class="flex justify-between cursor-pointer" @click="toggleTab(property.id)">
                  <span> {{ property.property.property }}</span>
                  <span><i class="text-sm fal fa-chevron-down"></i></span>
                </button>
                <div :class="['flex flex-col gap-4',tab === property.id ? 'block' : 'hidden']">
                  <div class="flex flex-col gap-2" v-for="propertyType in property.property.property_types" :key="propertyType.id">
                    <div class="flex flex-row items-center gap-4">
                      <div>
                        <input type="checkbox" :checked="filterSelected.filter(item=>item.id === propertyType.id).length > 0" @change="getFilterSelected(true,1,{id:propertyType.id, name:propertyType.name})"/>
                      </div>
                      <div>
                        <span>{{propertyType.name}}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!--            End properties -->
            </div>

            <div v-else class="flex justify-center">
              <span v-if="btnLoading">
                  <svg aria-hidden="true" class="w-[1.2rem] h-[1.2rem] text-white animate-spin fill-crimson-200 !font-bold" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
                      <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
                  </svg>
              </span>
            </div>

          </div>



          <div class="w-[80%] fm:w-full flex flex-col">
            <div class="pb-6 fd:px-6">
              <div class="flex justify-between fd:items-center">
                <div class="flex fd:items-center gap-3">
                  <div class="fd:hidden" @click="toggleFilter()">
                    <span class="fd:flex">
                      <img src="@/assets/images/filter.svg" class="w-[1.6rem]" />
                    </span>
                  </div>
                  <div class="flex fm:flex-col fd:items-center gap-3">
                    <div class="flex fd:items-center gap-2" @click="toggleSort()">
                      <span class="flex"><i class="fal fa-sort-amount-down text-2xl !font-bold"></i></span>
                      <span class="text-sm !font-medium fm:hidden">مرتب سازی:</span>
                    </div>
                    <div>
                      <ul :class="['flex fd:items-center gap-4 fm:flex-col', sort ? 'block' : 'fm:hidden']">
                        <li class="text-sm text-gray-500 cursor-pointer" @click="getData(true, 1, 'top_visit')">پربازدیدترین</li>
                        <li class="text-sm text-gray-500 cursor-pointer" @click="getData(true, 1, 'newest')">جدیدترین</li>
                        <li class="text-sm text-gray-500 cursor-pointer" @click="getData(true, 1, 'bestselling')">پرفروش‌ترین‌</li>
                        <li class="text-sm text-gray-500 cursor-pointer" @click="getData(true, 1, 'cheapest')">ارزان‌ترین</li>
                        <li class="text-sm text-gray-500 cursor-pointer" @click="getData(true, 1, 'expensive')">گران‌ترین</li>
                      </ul>
                    </div>
                  </div>


                </div>
                <div>
                  <span class="text-gray-500">{{allProducts.length}} کالا</span>
                </div>
              </div>
            </div>
            <div>
              <ProductComponent :products="allProducts" />
            </div>
            <div v-if="allProducts.length > 0" class="mt-10">
              <Paginate v-if="isFilter" :current="$store.state.current" :next="$store.state.next" :previous="$store.state.previous" :total="$store.state.total" @callGetPage="getFilterSelected"/>
              <Paginate v-else :current="$store.state.current" :next="$store.state.next" :previous="$store.state.previous" :total="$store.state.total" @callGetPage="getData"/>
            </div>
          </div>
        </div>
      </div>
      <Meta v-if="category" type="name" title="author" :description="$store.state.siteName" />
      <Meta v-if="category" type="property" title="og:title" :description="category.title" />
      <Meta v-if="category" type="property" title="og:url" :description="url" />
      <Meta v-if="category" type="name" title="keywords" :description="`${category.title} | ${category.title}دسته ها | دسته بندی `" />
      <Meta v-if="category" type="property" title="og:image" :description="$store.state.url + category.image" />
      <Meta v-if="category" type="property" title="article:published_time" :description="category.created_at" />
      <Meta v-if="category" type="property" title="article:modified_time" :description="category.updated_at" />
      <Meta :title="$store.state.siteName + ` | ${category.title} `"/>
    </div>

    <Loading :loading="loading" />
  </div>

</template>

<script>
export default {name: "ProductsList"}
</script>

<script setup>
import {ref, onMounted, watch} from "vue";
import Meta from "@/components/Meta.vue";
import ProductComponent from "@/components/ProductComponent.vue";
import Loading from "@/components/Loading.vue";
import Paginate from "@/components/Paginate.vue";
import store from "@/store";
import { useRoute } from "vue-router"
import axios from "@/plugins/axios";

const route = useRoute();
let tab = ref('')
let url = ref(window.location.href)
let category = ref(null);
let allProducts = ref([])
let allProperties = ref([])
let filterSelected = ref([])
let colorSelected = ref([])
let sizeSelected = ref([])
let fromPrice = ref(0)
let toPrice = ref(0)
let sizeTab = ref(false)
let onlyAvailableProducts = ref(true)
let isFilter = ref(false)
let loading = ref(false)
let btnLoading = ref(false)
let sort = ref(false)
let filter = ref(false)
let slug = ref(route.params['slug']);
let allColors = ref([]);
let allSizes = ref([]);
onMounted(async () => {
  removeFilter('all',null,false);
  await getData();
  await getProductProperties()
})


watch(route, async ( current ) => {
  slug.value = current.params['slug'];
  removeFilter('all',null,false);
  if(current.name === 'ProductsList'){
    await getData()
    await getProductProperties()
  }

})

async function getData(_loading=true,page=1, sort=''){
  isFilter.value = false;
  loading.value = _loading;
  let url = `${store.state.api}product-list/?page=${page}&slug=${slug.value}&sort=${sort}&only_available_products=${onlyAvailableProducts.value}`
  if(colorSelected.value.length > 0){
    url+=makeQuery(colorSelected.value,'color');
  }else if(sizeSelected.value.length > 0){
    url+=makeQuery(sizeSelected.value,'size');
  }else if(filterSelected.value.length > 0){
    url+=makeQuery(filterSelected.value,'property');
  }else if(fromPrice.value > 0 || toPrice.value > 0){
    url+=`&from_price=${fromPrice.value}&to_price=${toPrice.value}`;
  }
  await axios.get(url).then(resp=>{
    let data = resp.data.data;
    allProducts.value = data.products.data;
    category.value = data.category;
    store.commit('paginate', data.products)
  }).catch(err=>{})
  loading.value = false;
}

async function getProductProperties(){
  btnLoading.value = true;
  await axios.get(`${store.state.api}get-product-properties/${slug.value}`).then(resp=>{
    let data = resp.data.data;
    allProperties.value = data.properties.reduce((r, a) => r.concat(a), []);
    allColors.value = data.colors;
    allSizes.value = data.sizes;
  }).catch(err=>{})
  btnLoading.value = false;
}

function makeQuery(data, queryName){
  let result = data.map(item=>item.id).map(function(item){
    return `&${queryName}[]=${item}`;
  });
  return result.join('')
}

async function getFilterSelected(_loading=true, page=1, propertyType=null){
  isFilter.value = true;
  if(propertyType !== null){
    let existPropertyType = filterSelected.value.filter(item=>item.id === propertyType.id).length > 0;
    if(existPropertyType){
      filterSelected.value = filterSelected.value.filter(item=>item.id !== propertyType.id)
    }else{
      filterSelected.value.push(propertyType)
    }
  }
  await getData(true,1,route.query.sort)
}

function removeFilter(action, id=null,runFilter=true){
  if(action === 'all'){
    filterSelected.value = [];
    toPrice.value = fromPrice.value = 0
    colorSelected.value = [];
    sizeSelected.value = [];
    onlyAvailableProducts.value = true;
  }else if(action === 'size'){
    sizeSelected.value = sizeSelected.value.filter(item=>item.id !== id)
  }else{
    filterSelected.value = filterSelected.value.filter(item=>item.id !== id)
  }
  runFilter ? getFilterSelected() : '';
}

function colorSelect(colorId, code){
  if(colorSelected.value.find(item=>item.id === colorId) === undefined){
    colorSelected.value.push({id:colorId, code:code})
  }else{
    colorSelected.value = colorSelected.value.filter(item=>item.id !== colorId)
  }
  getFilterSelected();
}

function sizeSelect(sizeId, sizeName){
  if(sizeSelected.value.find(item=>item.id === sizeId) === undefined){
    sizeSelected.value.push({id:sizeId, name:sizeName})
  }else{
    sizeSelected.value = sizeSelected.value.filter(item=>item.id !== sizeId)
  }
  getFilterSelected();
}

function toggleTab(tabName) {
  tab.value = tabName === tab.value ? "" : tabName;
}

function toggleSort() {
  sort.value = !sort.value;
}

function toggleFilter() {
  filter.value = !filter.value;
}
</script>