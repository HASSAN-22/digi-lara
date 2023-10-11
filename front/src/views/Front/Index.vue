<template>
  <div>
    <div class="container mx-auto">
      <Slider :slides="1" :navigation="true" :paginate="true">
        <SwiperSlide v-for="slider in sliders" :key="slider.id">
          <div>
            <img :src="$store.state.url + slider.image" class="w-full h-[408px] fm:h-[208px]"/>
          </div>
        </SwiperSlide>
      </Slider>
    </div>
    <div class="container mx-auto">
      <div class="mx-4">

        <div v-for="widget in widgets" :key="widget.id">
          <!--    Special product-->
          <SpecialProduct class="my-8" v-if="widget.name === 'amazing_offer'" :key="widget.id" :widget="widget" />
          <!--    End special product-->

          <!--      Market special-->
          <MarketSpecial class="my-8" v-if="widget.name === 'amazing_supermarket'" :key="widget.id" :widget="widget" />
          <!--      end Market special-->

          <!--          Category -->
          <CategoryList class="my-8" v-if="widget.name === 'category'" :key="widget.id" :widget="widget" />
          <!--          End Category -->

          <!--        Selected Categories-->
          <SelectedCategory class="my-8" v-if="widget.name === 'shop_category'" :key="widget.id" :widget="widget" />
          <!--        End Selected Categories-->

          <div v-if="widget.name === 'banner'" class="mt-24">
            <div class="flex flex-row fd:gap-4">
              <a :href="widget.first_link" class="fm:w-[60%]">
                <img :src="widget.first_image" class="rounded-2xl" />
              </a>
              <a :href="widget.second_link" class="fm:w-[60%]">
                <img :src="widget.second_image" class="rounded-2xl" />
              </a>
            </div>
          </div>

          <!--          Brand -->
          <BrandSection v-if="widget.name === 'brand'" :key="widget.id" :widget="widget" />
          <!--          End Brand -->
        </div>

        <div class="mt-14 border border-gray-200 rounded-2xl p-4" v-if="bestSellingProducts.length > 0">
          <div class="flex justify-between">
            <div class="flex items-center gap-1 justify-center">
              <span class="flex"><i class="fal fa-fire text-yellow-400 text-lg !font-extrabold"></i></span>
              <span class="text-2xl fm:text-lg !font-medium">پرفروش‌ترین کالاها</span>
            </div>
            <div>
              <routerLink :to="{name:'BestSelling'}" target="_blank" class="text-lg text-blue-400 fm:text-sm">
                <span >مشاهده همه</span>
              </routerLink>
            </div>
          </div>

          <div class="expensive mt-8">
            <div class="flex flex-wrap gap-4 justify-around">
              <routerLink :to="{name:'ProductDetail', params:{slug:item.slug}}" :class="['w-[25%] flex items-center justify-between gap-3 border-b border-gray-200 mb-8']" v-for="(item,index) in bestSellingProducts" :key="item.id">
                <div class="w-[30%]">
                  <img :src="$store.state.url + item.image" class="rounded"/>
                </div>
                <div>
                  <span class="text-2xl !font-extrabold text-blue-400">{{(parseInt(index)+1)}}</span>
                </div>
                <div class="flex flex-col gap-2">
                  <span class="text-sm text-">{{item.ir_name}}</span>
                </div>

              </routerLink>
            </div>
          </div>
        </div>

        <div class="mt-14">
          <div class="flex justify-between items-center">
            <div>
              <span class="text-2xl fm:text-lg !font-medium">خواندی ها</span>
            </div>
            <div>
              <routerLink :to="{name:'NewsList',query:{slug:'مشاهده همه اخبار'}}">
                <span class="text-lg text-blue-400 fm:text-sm">مشاهده همه</span>
              </routerLink>
            </div>
          </div>

          <div class="mt-8">
            <div class="flex justify-evenly gap-3 fm:flex-col">
              <div v-for="item in news" :key="item.id" >
                <routerLink :to="{name:'NewsDetail',params:{slug:item.slug}}" class="flex flex-col gap-2 border border-gray-200">
                  <div>
                    <img :src="$store.state.url + item.image" :alt="item.title" class="rounded-lg" />
                  </div>
                  <div class="p-4">
                    <span class="">{{item.title}}</span>
                  </div>
                </routerLink>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <Meta :title="$store.state.siteName + ` | صفحه اصلی`"/>
    <Loading :loading="loading"/>
  </div>
</template>

<script>
export default{name: "Index"}
</script>

<script setup>
import {ref, onBeforeUnmount, onMounted} from "vue";
import Slider from "@/components/Slider.vue";
import Meta from "@/components/Meta.vue";
import Loading from "@/components/Loading.vue";
import { SwiperSlide } from 'swiper/vue';
import axios from "@/plugins/axios";
import store from "@/store";
import SpecialProduct from "@/components/IndexWidgets/SpecialProduct.vue";
import MarketSpecial from "@/components/IndexWidgets/MarketSpecial.vue";
import CategoryList from "@/components/IndexWidgets/CategoryList";
import SelectedCategory from "@/components/IndexWidgets/SelectedCategory";
import BrandSection from "@/components/IndexWidgets/BrandSection";

let displaySize = ref(window.innerWidth);
let sliders = ref([]);
let widgets = ref([]);
let news = ref([]);
let bestSellingProducts = ref([]);
let loading = ref(false)

onBeforeUnmount(() => {
  window.removeEventListener('resize', onResize)
})

onMounted(async () => {
  await index();
  window.addEventListener('resize', onResize)
})

async function index(){
  loading.value = true;
  await axios.get(`${store.state.api}content`).then(resp=>{
    let data = resp.data.data;
    sliders.value = data.sliders;
    widgets.value = data.widgets;
    bestSellingProducts.value = data.best_selling_products;
    news.value = data.news;
  })
  loading.value = false;
}

function onResize() {
  displaySize.value = window.innerWidth;
}

</script>