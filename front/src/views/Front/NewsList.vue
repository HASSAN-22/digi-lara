<template>
  <div>
    <div class="container mx-auto">
      <div class="mt-20 mx-10">
        <div class="flex justify-between items-center">
          <div class="flex items-center gap-1">
            <span>دسته:</span>
            <h3 class="!font-medium fm:text-md fd:text-lg" >{{category ? category.title : slugQuery}}</h3>
          </div>
          <div>
            <input v-model="search" v-debounce="searchData" :debounce-events="['keydown']" placeholder="جستجو ..." type="search" class="outline-none text-sm rounded-lg border-none bg-gray-100 w-full p-3" />
          </div>
        </div>
        <div class="border-b border-gray-200 my-2 w-full"></div>
        <div class="flex flex-wrap justify-evenly gap-5 mt-5">
          <routerLink :to="{name:'NewsDetail',params:{slug:news.slug}}" class="w-[20rem] rounded overflow-hidden shadow-lg flex flex-col gap-3 justify-between" v-for="news in allData" :key="news.id">
            <div class="relative group">
              <img class="w-full" :src="$store.state.url + news.image" :alt="news.title">
              <div class="absolute bg-black  h-full w-full top-0 opacity-0 group-hover:opacity-50 transition ease-in-out delay-150">

              </div>
            </div>
            <div class="font-bold text-xl fm:text-md px-6">{{news.title}}</div>
            <div class="px-6">
              <p class="text-gray-700 text-base fm:text-sm">
                {{news.short_description}}
              </p>
            </div>
            <div class="flex items-center justify-between mx-3 my-2">
              <div class="flex items-center gap-1">
                <span class="text-sm"><i class="far fa-pen text-sm"></i></span>
                <span class="text-sm">{{news.user.name}}</span>
              </div>
              <div class="flex items-center gap-1">
                <span class="text-sm">{{news.ir_created_at}}</span>
                <span class="text-sm"><i class="far fa-calendar-clock text-sm"></i></span>
              </div>
            </div>
          </routerLink>
        </div>
        <div v-if="allData.length > 0" class="mt-10">
          <Paginate :current="$store.state.current" :next="$store.state.next" :previous="$store.state.previous" :total="$store.state.total" @callGetPage="getData"/>
        </div>
      </div>
    </div>
    <Meta v-if="category" type="name" title="author" :description="store.state.siteName" />
    <Meta v-if="category" type="property" title="og:title" :description="category.title" />
    <Meta v-if="category" type="property" title="og:url" :description="url" />
    <Meta v-if="category" type="name" title="keywords" :description="`${category.title} | ${category.slug}`" />
    <Meta v-if="category" type="property" title="og:image" :description="$store.state.url + category.image" />
    <Meta v-if="category" type="property" title="article:published_time" :description="category.created_at" />
    <Meta v-if="category" type="property" title="article:modified_time" :description="category.updated_at" />
    <Meta v-if="category || slugQuery" :title="$store.state.siteName + ` | ${category ? category.title : slugQuery} `"/>
    <Loading :loading="loading" />
  </div>
</template>

<script>
export default{name: "NewsList"}
</script>

<script setup>
import {onMounted, ref, watch} from "vue";
import Meta from "@/components/Meta.vue";
import Loading from "@/components/Loading";
import axios from "@/plugins/axios";
import store from "@/store";
import Paginate from "@/components/Paginate";
import { useRoute } from "vue-router"

const route = useRoute();
let loading = ref(false);
let allData = ref([]);
let category = ref(null)
let search = ref('');
let slug = ref(route.params['slug'])
let slugQuery = ref(route.query.slug??'')
onMounted(async ()=>{
  await getData()
})

watch(route, async ( current ) => {
  if(current.name === 'NewsList'){
    slug.value = current.params['slug'];
    await getData()
  }
})

async function getData(_loading = true,page=1){
  loading.value = _loading;
  await axios.get(`${store.state.api}get-news-list/${slug.value}?page=${page}&search=${search.value}`).then(resp=>{
    let data = resp.data.data;
    allData.value = data.news.data;
    category.value = data.category
    store.commit('paginate',data.news);
  }).catch(err=>{})
  search.value = '';
  loading.value = false;
}


async function searchData(text){
  search.value = text;
  await getData()
}
</script>

<style scoped>
#router-link {
  display: inline-block;
}
</style>