<template>
  <div>
    <div class="container mx-auto">
      <div class="mt-20 mx-10">
        <div class="flex justify-between items-center">
          <div class="flex items-center gap-1">
            <span>دسته:</span>
            <h3 class="!font-medium fm:text-md fd:text-lg">{{categoryTitle}}</h3>
          </div>
          <div>
            <input v-model="search" v-debounce="searchData" :debounce-events="['keydown']" placeholder="جستجو ..." type="search" class="outline-none text-sm rounded-lg border-none bg-gray-100 w-full p-3" />
          </div>
        </div>
        <div class="border-b border-gray-200 my-2 w-full"></div>
        <div class="flex fm:flex-col gap-8 mt-5">
          <div class="fd:w-[70%]" v-if="news !== null">
            <div class="flex flex-col gap-10">
              <div>
                <h1 class="!font-medium text-3xl fm:text-xl">{{news.title}}</h1>
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
              <div><img class="w-full" :src="$store.state.url +  news.image" :alt="news.title"></div>
              <div>
                <p class="break-words fm:text-sm">
                  {{news.description}}
                </p>
              </div>
              <div class="flex items-end justify-end">
                <ul class="flex items-center gap-4">
                  <li><a target="_blank" :href="`https://telegram.me/share/url?url=${currentUrl}&text=${news.title}`"><i class="fab fa-telegram text-4xl text-blue-400"></i></a></li>
                  <li><a target="_blank" :href="`whatsapp://send?text=${currentUrl}`"><i class="fab fa-whatsapp text-4xl text-green-400"></i></a></li>
                  <li><a target="_blank" :href="`http://www.facebook.com/sharer/sharer.php?m2w&s=100&p[url]=${currentUrl}&p[images][0]=${$store.state.url + news.image}&p[title]=${news.title}&p[summary]=${news.short_description}`"><i class="fab fa-facebook text-4xl text-indigo-500"></i></a></li>
                  <li><a target="_blank" :href="`http://twitter.com/intent/tweet/?text=${news.title}&url=${currentUrl}`"><i class="fab fa-twitter text-4xl text-cyan-400"></i></a></li>
                  <li><a target="_blank" :href="`http://www.linkedin.com/shareArticle?mini=true&url=${currentUrl}}&title=${news.title}&source=${host}`"><i class="fab fa-linkedin text-4xl text-red-400"></i></a></li>
                </ul>
              </div>
            </div>
          </div>
          <div class="fd:w-[30%]">
            <div class="text-center">
              <span>اخبار تصادفی</span>
            </div>
            <div class="border-b border-gray-200 my-3 w-full"></div>
            <div class="flex flex-col" v-for="_news in randomNews" :key="_news.id">
              <div class="flex gap-3">
                <routerLink :to="{name:'NewsDetail',params:{slug:_news.slug}}" class="w-[30%]">
                  <img class="w-full" :src="$store.state.url + _news.image" :alt="_news.title">
                </routerLink>
                <routerLink :to="{name:'NewsDetail',params:{slug:_news.slug}}" class="flex flex-col items-end w-[70%]">
                  <div>
                    <span class="fm:text-sm text-gray-700">
                        {{_news.title}}
                    </span>
                  </div>
                  <div class="flex items-center gap-1">
                    <span class="text-sm text-gray-500">{{news.ir_created_at}}</span>
                    <span class="text-sm"><i class="far fa-calendar-clock text-sm text-gray-500"></i></span>
                  </div>
                </routerLink>
              </div>
              <div class="border-b border-gray-100 my-3 w-full"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <Meta v-if="news" type="name" title="author" :description="news.user.anem" />
    <Meta v-if="news" type="property" title="og:title" :description="news.title" />
    <Meta v-if="news" type="property" title="og:url" :description="currentUrl" />
    <Meta v-if="news" type="name" title="keywords" :description="`${news.title} | ${news.slug}`" />
    <Meta v-if="news" type="property" title="og:image" :description="$store.state.url + news.image" />
    <Meta v-if="news" type="property" title="article:published_time" :description="news.created_at" />
    <Meta v-if="news" type="property" title="article:modified_time" :description="news.updated_at" />
    <Meta v-if="news" :title="$store.state.siteName + ` | ${news.title} `"/>
    <Loading :loading="loading" />
    <ValidationError/>
  </div>
</template>

<script>
export default{name: "NewsDetail"}
</script>

<script setup>
import {onMounted, ref, watch} from "vue";
import Meta from "@/components/Meta.vue";
import Loading from "@/components/Loading";
import ValidationError from "@/components/ValidationError";
import axios from "@/plugins/axios";
import store from "@/store";
import { useRoute } from "vue-router"

const route = useRoute();
let loading = ref(false);
let randomNews = ref([]);
let news = ref(null);
let search = ref('');
let slug = ref(route.params['slug'])
let currentUrl = ref('');
let categoryTitle = ref('');
let host = ref(window.location.origin)
onMounted(async ()=>{
  await getData();
})

watch(route, async ( current ) => {
  if(current.name === 'NewsDetail'){
    slug.value = current.params['slug'];
    await getData()
  }
})

async function getData(_loading = true){
  loading.value = _loading;
  await axios.get(`${store.state.api}get-news-detail/${slug.value}?search=${search.value}`).then(resp=>{
    let data = resp.data.data;
    news.value = data.news;
    randomNews.value = data.random_news;
    categoryTitle.value = data.category_title;
    currentUrl.value = window.location.href;
  }).catch(err=>{})
  search.value = '';
  loading.value = false;
}

async function searchData(text){
  search.value = text;
  await getData();
}
</script>

<style scoped>
#router-link {
  display: inline-block;
}
</style>