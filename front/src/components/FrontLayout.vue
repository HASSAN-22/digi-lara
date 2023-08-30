<template>
  <div class="relative">
    <header>
      <div class="container-fluid border-b border-gray-200 pr-8 pl-8 fm:pr-4 fm:pl-4 fd:fixed z-100 w-full bg-white pt-4">
        <!--    Nav bar-->
        <div class="flex flex-col gap-8 container mx-auto">
          <div :class="['flex flex-row justify-between items-center', scrollY >= 400 && scrollIsDown ? 'pb-5' : '']">
            <span class="fd:hidden" @click="showNav = true"><i class="fal fa-bars text-2xl"></i></span>

            <div class="flex flex-row  fd:justify-between gap-8 fm:gap-2 items-center">
              <routerLink :to="{name:'Index'}"><img :src="$store.state.configSite.logo" :alt="$store.state.configSite.shop_name" class="fm:w-[100px] w-[10rem]"></routerLink>
              <!--          Search product-->
              <Search class="fm:hidden" />
              <!--          End search Product-->
            </div>
            <div class="flex flex-row justify-between items-center fd:gap-4 fm:gap-8">
              <div v-if="!$store.state.user.token">
                <routerLink :to="{name:'Login'}" class="flex flex-row items-center gap-2 fd:border fd:border-gray-300 fd:border-solid fd:p-2 fd:rounded-lg">
                  <span class="rotate-180 flex"><i class="fal fa-sign-in-alt text-xl"></i></span>
                  <span class="fm:text-sm">ورود</span>
                  <span class="!font-bold text-xl fm:hidden">|</span>
                  <span class="fm:text-sm fm:hidden">ثبت نام</span>
                </routerLink>
              </div>
              <div v-else>
                <routerLink :to="{name:'Dashboard'}" class="flex flex-row items-center gap-2 fd:border fd:border-gray-300 fd:border-solid fd:p-2 fd:rounded-lg">
                  <span><img src="@/assets/images/dashboard.svg" class="w-[20px]"/></span>
                  <span class="fm:text-sm">داشبورد</span>
                </routerLink>
              </div>
              <span class="text-gray-300 text-xl fm:hidden">|</span>
              <BasketComponent/>
            </div>

          </div>
          <!--          Search product-->
          <Search class="fd:hidden" />
          <!--          End search Product-->
          <div :class="['fm:hidden flex justify-between items-center relative z-50', scrollY >= 400 && scrollIsDown ? 'hidden' : 'block']">
            <nav>
              <ul class="flex items-center gap-4 list-none">
                <li class="flex items-center group gap-2 cursor-pointer border-b-2 border-white hover:!border-crimson-200 transition ease-in-out duration-300">
                  <span><i class="fal fa-bars text-2xl"></i></span>
                  <span class="!font-bold text-gray-700">دسته بندی کالاها</span>
                  <div class="hidden group-hover:block absolute w-full bg-white top-[22px] shadow-lg">
                    <div class="flex">
                      <div class="w-[22%] border-l border-gray-300 ml-4 h-[22rem] overflow-y-scroll scroll p-4">
                        <ul class="flex flex-col">
                          <div v-for="category in categories" :key="category.id">
                            <li v-if="category.parent_id === '0'" class="flex items-center gap-2 hover:bg-gray-50 rounded p-3 text-gray-700 text-sm !font-medium" @mouseover="setCategoryId(category.id, category.title)">
                              <span><i :class="category.icon"></i></span>
                              <span>{{category.title}}</span>
                            </li>
                          </div>
                        </ul>
                      </div>
                      <div class="w-[100%] h-[22rem] overflow-y-scroll scroll">
                        <div v-for="category in categories" :key="category.id">
                          <div v-if="category.id === categoryId" class="p-4">
                            <div class="flex items-center gap-4 mt-3">
                              <routerLink :to="{name:'ProductsList',params:{slug:category.slug}}" class="text-sm text-gray-700 !font-medium">همه کالاهای {{categoryTitle}}</routerLink>
                              <span class="flex"><i class="far fa-chevron-left text-xs"></i></span>
                            </div>
                            <div class="flex flex-wrap gap-12">
                              <div class="mt-12" v-for="children in category.children" :key="children.id">
                                <div class="flex items-center gap-2">
                                  <span class="text-crimson-200 text-2xl !font-bold">|</span>
                                  <routerLink :to="{name:'ProductsList',params:{slug:children.slug}}" class="text-sm text-gray-700 !font-medium">{{children.title}}</routerLink>
                                  <span class="flex"><i class="far fa-chevron-left text-xs"></i></span>
                                </div>
                                <div>
                                  <ul class="flex flex-col gap-2">
                                    <li  v-for="child in children.children" :key="child.id" class="mt-2 mr-2">
                                      <div class="flex items-center gap-2" v-if="child.children.length > 0">
                                        <span class="text-crimson-200 text-2xl !font-bold">|</span>
                                        <routerLink :to="{name:'ProductsList',params:{slug:child.slug}}" class="text-sm text-gray-700 !font-medium">{{child.title}}</routerLink>
                                        <span class="flex"><i class="far fa-chevron-left text-xs"></i></span>
                                      </div>
                                      <div class="flex items-center gap-2" v-else>
                                        <routerLink :to="{name:'ProductsList',params:{slug:child.slug}}" class="text-sm text-gray-700">{{child.title}}</routerLink>
                                      </div>
                                      <ul class="flex flex-col gap-2">
                                        <li  v-for="_child in child.children" :key="_child.id" class="mr-2">
                                          <routerLink :to="{name:'ProductsList',params:{slug:_child.slug}}" class="text-gray-700 text-sm">{{_child.title}}</routerLink>
                                        </li>
                                      </ul>
                                    </li>
                                  </ul>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>

                      </div>
                    </div>
                  </div>
                </li>
                <li class="flex relative dropdown group items-center gap-2 cursor-pointer border-b-2 border-white hover:!border-crimson-200 transition ease-in-out duration-300 text-gray-600"
                  v-for="category in newsCategories" :key="category.id"
                >
                  <div class="flex gap-2 items-center">
                    <span><i :class="['text-xl text-gray-400',category.icon]"></i></span>
                    <span>{{category.title}}</span>
                  </div>
                  <NavbarDropdown :childrens="category.children" :isFirstCall="true" />

                </li>
                <li class="hover:text-crimson-200 transition ease-in-out duration-300">
                  <routerLink :to="{name:'BestSelling'}" class="flex items-center gap-2 text-gray-600">
                    <span><i class="fal fa-fire text-gray-400"></i></span>
                    <span>پرفروش ترین ها</span>
                  </routerLink>
                </li>
                <li class="hover:text-crimson-200 transition ease-in-out duration-300">
                  <routerLink :to="{name:'SpecialProduct'}" class="flex items-center gap-2 text-gray-600">
                    <span><i class="fal fa-wand-magic-sparkles text-gray-400"></i></span>
                    <span>کالاهای ویژه</span>
                  </routerLink>
                </li>
                <li class="hover:text-crimson-200 transition ease-in-out duration-300">
                  <routerLink :to="{name:'IncredibleOffer'}" class="flex items-center gap-2 text-gray-600">
                    <span><i class="fal fa-badge-percent text-gray-400"></i></span>
                    <span>کالاهای شگفت انگیز</span>
                  </routerLink>
                </li>
              </ul>
            </nav>
          </div>
          <div class="fd:hidden">
            <!--            Mobile screen-->
            <div :class="['fixed overflow-y-scroll scroll bg-white w-[90%] shadow w-full top-0 h-screen z-50 ', showNav ? 'right-0' : 'right-[-100%]']">
              <div class="text-left p-3 flex items-center justify-between border-b border-gray-200">
                <div class="w-[36%]">
                  <img src="@/assets/images/digi.svg" />
                </div>
                <span @click="showNav = false"><i class="fal fa-times text-2xl"></i></span>
              </div>
              <ul class="mt-3 flex flex-col gap-8 mr-4">
                <li class="cursor-pointer relative">
                  <div>
                    <span class="!font-bold text-gray-700 pr-3 ">دسته بندی کالاها</span>
                  </div>
                  <ul class="w-full bg-white flex flex-col space-y-1 mt-4 relative">
                    <li class="group" v-for="category in categories" :key="category.id">
                      <div v-if="category.parent_id === '0'" class="flex items-center justify-between px-3">
                        <span>{{category.title}}</span>
                        <span><i class="fal fa-chevron-left group-hover:rotate-[270deg] text-sm text-gray-400 transition ease-in-out duration-300"></i></span>
                      </div>
                      <ul :class="['w-full top-[20px] bg-gray-200 flex flex-col space-y-4 py-4 hidden group-hover:block']">
                        <li class="relative" v-for="children in category.children" :key="children.id" @click="toggleCategory(children.id)" >
                          <div class="flex items-center justify-between px-8">
                            <span>{{children.title}}</span>
                            <span :class="[showCategory && categoryItem === children.id ? 'rotate-[270deg]' : '']"><i class="fal fa-chevron-left text-sm text-gray-400 transition ease-in-out duration-300"></i></span>
                          </div>
                          <ul :class="['w-full top-[20px] bg-white bg-gray-300 flex flex-col space-y-4 py-4', showCategory && categoryItem === j ? 'block' : 'hidden']">
                            <li class="px-16" v-for="child in children.children" :key="child.id">
                              <span>{{child.title}}</span>
                            </li>
                          </ul>
                        </li>
                      </ul>
                    </li>
                  </ul>
                </li>
                <li class="flex relative fm:flex-col dropdown fd:items-center gap-2 cursor-pointer border-b-2 border-white hover:!border-crimson-200 transition ease-in-out duration-300 text-gray-600"
                    v-for="category in newsCategories" :key="category.id"
                >
                  <div class="flex gap-2 items-center">
                    <span><i class="far fa-newspaper text-xl text-gray-400"></i></span>
                    <span>{{category.title}}</span>
                  </div>
                  <NavbarDropdown :childrens="category.children" :isFirstCall="true" />

                </li>

                <li class="hover:text-crimson-200 transition ease-in-out duration-300">
                  <routerLink :to="{name:'BestSelling'}" class="flex items-center gap-2 text-gray-600">
                    <span><i class="fal fa-fire text-gray-400"></i></span>
                    <span>پرفروش ترین ها</span>
                  </routerLink>
                </li>
                <li class="hover:text-crimson-200 transition ease-in-out duration-300">
                  <routerLink :to="{name:'SpecialProduct'}" class="flex items-center gap-2 text-gray-600">
                    <span><i class="fal fa-wand-magic-sparkles text-gray-400"></i></span>
                    <span>کالاهای ویژه</span>
                  </routerLink>
                </li>
                <li class="hover:text-crimson-200 transition ease-in-out duration-300">
                  <routerLink :to="{name:'IncredibleOffer'}" class="flex items-center gap-2 text-gray-600">
                    <span><i class="fal fa-badge-percent text-gray-400"></i></span>
                    <span>کالاهای شگفت انگیز</span>
                  </routerLink>
                </li>
              </ul>
            </div>
            <!--            End mobile screen-->
          </div>
        </div>
        <!--    End nav bar-->
      </div>
    </header>
    <main class="fd:pt-[7.8rem]" id="main">
      <router-view></router-view>
    </main>

    <footer class="mt-16 container mx-auto">
      <div class="mx-4 border p-3 rounded-lg">
        <div class="flex flex-col gap-5">
          <div>
            <img :src="$store.state.configSite.footer_logo" class="fm:w-[40%] w-[14rem]" />
          </div>
          <div class="flex flex-row gap-3 fm:flex-col fd:items-center">
            <span class="fm:text-sm">تلفن پشتیبانی {{$store.state.configSite.support_phone}}</span>
            <span class="fm:hidden">|</span>
            <span class="fm:text-sm">۷ روز هفته، ۲۴ ساعته پاسخگوی شما هستیم</span>
          </div>
        </div>
        <div class="flex justify-around  mt-20 fm:hidden">
          <div class="flex flex-col gap-2 items-center">
            <div>
              <img :src="$store.state.configSite.footer_box.express_image"/>
            </div>
            <div>
              <span class="text-sm">{{$store.state.configSite.footer_box.express_name}}</span>
            </div>
          </div>
          <div class="flex flex-col gap-2 items-center">
            <div>
              <img :src="$store.state.configSite.footer_box.support_every_day_image"/>
            </div>
            <div>
              <span class="text-sm">{{$store.state.configSite.footer_box.support_every_day}}</span>
            </div>
          </div>
          <div class="flex flex-col gap-2 items-center">
            <div>
              <img :src="$store.state.configSite.footer_box.guarantee_image"/>
            </div>
            <div>
              <span class="text-sm">{{$store.state.configSite.footer_box.guarantee}}</span>
            </div>
          </div>
          <div class="flex flex-col gap-2 items-center">
            <div>
              <img :src="$store.state.configSite.footer_box.original_image"/>
            </div>
            <div>
              <span class="text-sm">{{$store.state.configSite.footer_box.original}}</span>
            </div>
          </div>
        </div>
        <div class="flex justify-around fm:flex-wrap mt-20">
          <div>
            <div>
              <span class="text-2xl !font-bold fm:text-lg">با {{$store.state.configSite.shop_name_ir}}</span>
            </div>
            <ul class="flex flex-col space-y-4 mt-6">
              <li class="text-gray-500"><routerLink :to="{name:'PageDetail', params:{id:1}}">اتاق خبر {{$store.state.configSite.shop_name_ir}}</routerLink></li>
              <li class="text-gray-500"><routerLink :to="{name:'PageDetail', params:{id:2}}">فروش در {{$store.state.configSite.shop_name_ir}}</routerLink></li>
              <li class="text-gray-500"><routerLink :to="{name:'PageDetail', params:{id:3}}">گزارش تخلف در {{$store.state.configSite.shop_name_ir}}</routerLink></li>
              <li class="text-gray-500"><routerLink :to="{name:'ContactUs'}">تماس با {{$store.state.configSite.shop_name_ir}}</routerLink></li>
              <li class="text-gray-500"><routerLink :to="{name:'PageDetail', params:{id:4}}">درباره {{$store.state.configSite.shop_name_ir}}</routerLink></li>
            </ul>
          </div>
          <div class="fm:hidden">
            <div>
              <span class="text-2xl !font-bold fm:text-lg">خدمات مشتریان</span>
            </div>
            <ul class="flex flex-col space-y-4 mt-6">
              <li class="text-gray-500"><routerLink :to="{name:'FaqCategory'}">پرسش‌ و پاسخ های متداول</routerLink></li>
              <li class="text-gray-500"><routerLink :to="{name:'PageDetail', params:{id:5}}">رویه‌های بازگرداندن کالا</routerLink></li>
              <li class="text-gray-500"><routerLink :to="{name:'PageDetail', params:{id:6}}">شرایط استفاده</routerLink></li>
              <li class="text-gray-500"><routerLink :to="{name:'PageDetail', params:{id:7}}">حریم خصوصی</routerLink></li>
            </ul>
          </div>
          <div>
            <div>
              <span class="text-2xl !font-bold fm:text-lg">راهنمای خرید از {{$store.state.configSite.shop_name_ir}}</span>
            </div>

            <ul class="flex flex-col space-y-4 mt-6">
              <li class="text-gray-500"><routerLink :to="{name:'PageDetail', params:{id:8}}">نحوه ثبت سفارش</routerLink></li>
              <li class="text-gray-500"><routerLink :to="{name:'PageDetail', params:{id:9}}">رویه ارسال سفارش</routerLink></li>
            </ul>
          </div>
          <div class="fm:mt-10">
            <div class="">
              <span class="text-2xl !font-bold fm:text-lg">همراه ما باشید!</span>
            </div>
            <div class="flex flex-col gap-6 mt-6">
              <div>
                <ul class="flex flex-row gap-4 items-center">
                  <li>
                    <a :href="$store.state.configSite.instagram">
                      <span><i class="text-gray-500 text-4xl fab fa-instagram"></i></span>
                    </a>
                  </li>
                  <li>
                    <a :href="$store.state.configSite.twitter">
                      <span><i class="text-gray-500 text-4xl fab fa-twitter"></i></span>
                    </a>
                  </li>
                  <li>
                    <a :href="$store.state.configSite.telegram">
                      <span><i class="text-gray-500 text-4xl fab fa-telegram"></i></span>
                    </a>
                  </li>
                  <li>
                    <a :href="$store.state.configSite.whatsapp">
                      <span><i class="text-gray-500 text-4xl fab fa-whatsapp"></i></span>
                    </a>
                  </li>
                  <li>
                    <a :href="$store.state.configSite.facebook">
                      <span><i class="text-gray-500 text-4xl fab fa-facebook"></i></span>
                    </a>
                  </li>
                </ul>
              </div>
              <div class="flex flex-col gap-3">
                <div>
                <span class="fm:text-sm">
                  همراه ما باشید!
                  با ثبت ایمیل، از جدید‌ترین های ما با‌خبر شوید
                </span>
                </div>
                <div class="flex items-center justify-between gap-4">
                  <input type="text" class="w-full p-3 rounded-lg outline-none bg-gray-100" v-model="newsletter" placeholder="ایمیل شما"/>
                  <div v-if="newsletter.length > 0">
                    <Button text="ثبت" :btnLoading="btnLoading" @callback="sendNewsletter()"></Button>
                  </div>
                  <div v-else>
                    <Button text="ثبت" :btnLoading="false" my_class="!bg-gray-300"></Button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="flex justify-between fm:flex-col gap-8 mt-20">
          <div class="flex flex-col gap-3 fd:w-[50%]">
            <div>
              <span class="text-2xl fm:text-lg !font-bold">{{$store.state.configSite.shop_name_ir}}</span>
            </div>
            <div>
              <p class="break-all fm:text-sm">
                {{$store.state.configSite.shop_bio}}
              </p>
            </div>
          </div>
          <div class="flex justify-end gap-3 fd:w-[50%]">
            <div class="">
              <div class="border border-gray-200 rounded p-3">{{$store.state.configSite.enamad}}</div>
            </div>
            <div class="">
              <div class="border border-gray-200 rounded p-3">{{$store.state.configSite.samandehi}}</div>
            </div>
            <div class="">
              <div class="border border-gray-200 rounded p-3">{{$store.state.configSite.mojavez}}</div>
            </div>
          </div>
        </div>
      </div>
      <div class="mt-10 text-center p-5">
        <span class="text-md fm:text-sm">{{$store.state.configSite.copy_right}}</span>
      </div>
    </footer>
    <ValidationError />
  </div>
</template>

<script>
  export default{name: "FrontLayout"}
</script>
<script setup>
import {onMounted, ref} from "vue"
import Button from "@/components/Button.vue"
import ValidationError from "@/components/ValidationError.vue"
import Search from "@/components/Search.vue"
import BasketComponent from "@/components/BasketComponent.vue";
import axios from "@/plugins/axios";
import store from "@/store";
import Toast from "@/plugins/toast";
import NavbarDropdown from "@/components/NavbarDropdown";

let newsletter = ref('');
let btnLoading = ref(false);
let showNav = ref(false);
let showCategory = ref(false);
let categoryItem = ref(0);
let categoryId = ref(1);
let scrollY = ref(0);
let scrollIsDown = ref(false);
let prevScroll = 0;
let categoryTitle = ref('');
let categories = ref([]);
let newsCategories = ref([]);
let pages = ref([]);
onMounted(async () => {
  await indexHeader()
  await index()
  await basketCount()
  window.addEventListener('scroll', yScroll)
  window.addEventListener('scroll', doesItScrollDown)
})

async function index(){
  await axios.get(`${store.state.api}index`).then(resp=>{
    let data = resp.data.data;
    pages.value = data.pages;
    if(data.store_detail){
      store.state.siteName = data.store_detail.shop_name_ir
      store.state.configSite = {...data.store_detail}
    }
    if(data.footer_box){
      store.state.configSite.footer_box = {...data.footer_box};
    }
    if(data.social_media){
      store.state.configSite.social_media = {...data.store_detail};
    }
  })
}

async function indexHeader(){
  await axios.get(`${store.state.api}index-header`).then(resp=>{
    let data = resp.data.data;
    categories.value = data.categories;
    newsCategories.value = data.news_categories;
    if(categories.value.length > 0){
      categoryTitle.value = categories.value[0].title;
    }
  }).catch(err=>{})
}

async function basketCount(){
  await axios.get(`${store.state.api}basket-count`).then(resp=>{
    store.state.basketCount = resp.data.data;
  }).catch(err=>{})
}


function setCategoryId(id, title){
  categoryId.value = id;
  categoryTitle.value = title;
}

function yScroll(){
  scrollY.value = window.scrollY
}

function toggleCategory(item){
  showCategory.value = categoryItem.value !== item && showCategory.value ? true : !showCategory.value;
  categoryItem.value = item;
}

async function sendNewsletter(){
  btnLoading.value = true;
  await axios.post(`${store.state.api}add-newsletter`,{email:newsletter.value}).then(res=>{
    Toast.success();
    newsletter.value = '';
  }).catch(err=>{
    store.commit('handleError',err)
  })
  btnLoading.value = false;
}
function doesItScrollDown() {
  scrollIsDown.value = prevScroll < window.scrollY;
  prevScroll = window.scrollY
}
</script>
