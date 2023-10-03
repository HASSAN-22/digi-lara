<template>
  <div>
    <div class="container mx-auto">
      <div class="flex">
        <aside :class="['w-[23%] border-l border-gray-200 min-h-screen fd:mb-10', toggleAside ? 'fm:block fm:fixed fm:bg-white fm:w-[90%] fm:z-100' : 'fm:hidden']">
          <div class="flex flex-col gap-8 p-4">
            <div class="flex justify-between">
              <div class="flex items-center gap-2">
                <routerLink to="/">
                  <img :src="$store.state.url + $store.state.user.avatar" class="rounded-full w-[100px] h-[100px] fm:w-[60px] fm:h-[60px]"/>
                </routerLink>
                <div class="flex flex-col gap-2">
                  <span class="text-gray-500 !font-medium fm:text-sm">{{$store.state.user.name}}</span>
                  <span class="text-gray-500 !font-medium fm:text-sm">وضعیت دسترسی:
                    <b class="text-green-500" v-if="$store.state.user.status === 'yes'">فعال</b>
                    <b class="text-red-500" v-else>غیر فعال</b>
                  </span>
                </div>
              </div>
              <div class="fd:hidden">
                <span class="cursor-pointer" @click="toggleAside = false"><i class="far fa-times text-lg"></i></span>
              </div>
            </div>
            <div class="flex gap-1 items-center">
              <span class="!font-medium fm:text-sm">موجودی کیف پول شما:</span>
              <span class="text-green-500 !font-medium fm:text-sm">{{walletAmount}}</span>
              <span class="!font-medium fm:text-sm">ریال</span>
            </div>
          </div>
          <div class="border-b border-gray-200 w-full py-3"></div>
          <div class="overflow-y-scroll scroll h-[30rem]">
            <ul class="flex flex-col" v-if="store.state.userPermissions.length > 0 && $store.state.user.access === 'admin'">
              <DashboardAside
                  v-if="hasPermission(['categories','properties','commentsubjects'])"
                  icon="far fa-cubes"
                  :tab="1"
                  menu="دسته بندی ها"
                  :submenus="[
                      {id:1, title:'لیست دسته بندی ها', route:{name:'Category'}, perm:'categories'},
                      {id:1, title:'لیست ویژگی ها', route:{name:'Property'}, perm:'properties'},
                  ]"
              />
              <DashboardAside
                  v-if="hasPermission(['brands','guarantees','colors','sizes'])"
                  icon="far fa-chart-network"
                  :tab="2"
                  menu="تاکسونومی"
                  :submenus="[
                      {id:1, title:'لیست برندها', route:{name:'Brand'}, perm:'brands'},
                      {id:2, title:'لیست گارانتی ها', route:{name:'Guarantee'}, perm:'guarantees'},
                      {id:3, title:'لیست رنگ ها', route:{name:'Color'}, perm:'colors'},
                      {id:4, title:'لیست اندازه ها', route:{name:'Size'}, perm:'sizes'},
                  ]"
              />
              <DashboardAside
                  v-if="hasPermission(['products','coupons','amazing_products','productcomments','productquestions'])"
                  icon="far fa-cube"
                  :tab="3"
                  menu="کالاها"
                  :submenus="[
                      {id:1, title:'لیست کالاها', route:{name:'Product'}, perm:'products'},
                      {id:2, title:'کالاهای شگفت انگیز', route:{name:'AmazingProduct'}, perm:'amazing_products'},
                      {id:3, title:'تخفیف ها', route:{name:'Coupon'}, perm:'coupons'},
                      {id:4, title:'گزارشات کالاها', route:{name:'ProductReport'}},
                      {id:5, title:'گزارشات دیدگاه های کالا', route:{name:'CommentReport'}},
                      {id:6, title:'دیدگاه های کالا', route:{name:'ProductComment'}, perm:'productcomments'},
                      {id:7, title:'پرسش های کالا', route:{name:'ProductQuestion'}, perm:'productquestions'},
                  ]"
              />
              <DashboardAside
                  icon="far fa-link"
                  :tab="4"
                  menu="نقش  و دسترسی ها"
                  :submenus="[
                      {id:1, title:'لیست نقش ها', route:{name:'Role'}},
                      {id:2, title:'لیست دسترسی ها', route:{name:'Permission'}}
                  ]"
              />
              <DashboardAside
                  v-if="hasPermission(['users'])"
                  icon="far fa-user"
                  :tab="5"
                  menu="کاربران"
                  :submenus="[{id:1, title:'لیست کاربران', route:{name:'User'}, perm:'users'}]"
              />
              <DashboardAside
                  v-if="hasPermission(['widgets'])"
                  icon="far fa-th-large"
                  :tab="6"
                  menu="ویجت‌ها"
                  :submenus="[{id:1, title:'ویجت‌ها', route:{name:'Widget'}, perm:'widgets'}]"
              />

              <DashboardAside
                  v-if="hasPermission(['sliders','works','transports','faqs'])"
                  icon="far fa-cog"
                  :tab="7"
                  menu="تنظیمات"
                  :submenus="[
                      {id:1, title:'اسلایدر', route:{name:'Slider'}, perm:'sliders'},
                      {id:2, title:'شغل ها', route:{name:'Work'}, perm:'works'},
                      {id:3, title:'حمل و نقل', route:{name:'Transport'}, perm:'transports'},
                      {id:4, title:'تنظیمات سایت', route:{name:'ShopConfig'}},
                      {id:5, title:'سوالات متداول', route:{name:'Faq'},perm:'faqs'},
                  ]"
              />
              <DashboardAside
                  v-if="hasPermission(['becomesellers'])"
                  icon="far fa-trumpet"
                  :tab="12"
                  menu="لیست درخواست های فروشندگی"
                  :submenus="[
                      {id:1, title:'فروشنده شوید', route:{name:'BecomeSeller'}, perm:'becomesellers'},
                  ]"
              />
              <DashboardAside
                  v-if="hasPermission(['news'])"
                  icon="far fa-newspaper"
                  :tab="13"
                  menu="اخبار"
                  :submenus="[
                      {id:1, title:'لیست اخبار', route:{name:'News'}, perm:'news'},
                  ]"
              />
              <DashboardAside
                  v-if="hasPermission(['pages'])"
                  icon="far fa-memo"
                  :tab="14"
                  menu="برگه ها"
                  :submenus="[
                      {id:1, title:'لیست برگه ها', route:{name:'Page'}, perm:'pages'},
                  ]"
              />
              <DashboardAside
                  v-if="hasPermission(['withdrawwallets','debtors'])"
                  icon="far fa-wallet"
                  :tab="9"
                  menu="کیف پول"
                  :submenus="[
                      {id:1, title:'لیست کیف پول', route:{name:'Wallet'}},
                      {id:2, title:'لیست برداشت ها', route:{name:'Withdraw'},perm:'withdrawwallets'},
                      {id:3, title:'لیست بستانکاری ها', route:{name:'Debtor'},perm:'debtors'}
                  ]"
              />
              <DashboardAside
                  v-if="hasPermission(['ticketcategories','tickets'])"
                  icon="far fa-messages"
                  :tab="15"
                  menu="تیکت ها"
                  :submenus="[
                      {id:1, title:'لیست موضوعات تیکت ها', route:{name:'TicketCategory'}, perm:'ticketcategories'},
                      {id:2, title:'لیست تیکت ها', route:{name:'Ticket'}, perm:'tickets'},
                  ]"
              />
              <DashboardAside
                  v-if="hasPermission(['contacts'])"
                  icon="far fa-phone"
                  :tab="15"
                  menu="تماس با ما"
                  :submenus="[
                      {id:1, title:'لیست تماس با ما', route:{name:'Contact'}, perm:'contacts'},
                  ]"
              />
              <DashboardAside
                  v-if="hasPermission(['sms','emails'])"
                  icon="far fa-message-sms"
                  :tab="16"
                  menu="ارسال ایمیل یا پیامک"
                  :submenus="[
                      {id:1, title:'لیست پیامک ها', route:{name:'SMS'}, perm:'sms'},
                      {id:2, title:'لیست ایمیل ها', route:{name:'Email'}, perm:'emails'},
                  ]"
              />
              <DashboardAside
                  v-if="hasPermission(['newsletters'])"
                  icon="far fa-message-middle"
                  :tab="17"
                  menu="خبرنامه"
                  :submenus="[
                      {id:1, title:'لیست خبرنامه ها', route:{name:'Newsletter'}, perm:'newsletters'},
                  ]"
              />
            </ul>

            <ul v-if="$store.state.user.access === 'seller' || $store.state.user.access === 'admin' || $store.state.user.access === 'user'">
              <DashboardAside
                  icon="far fa-user-check"
                  :tab="8"
                  menu="اطلاعات شخصی"
                  :submenus="[
                      {id:1, title:'پروفایل', route:{name:'Profile'}},
                      {id:2, title:'ادرس ها', route:{name:'Address'}},
                  ]"
              />

              <DashboardAside
                  icon="far fa-bag-shopping"
                  :tab="10"
                  menu="سفارش ها"
                  :submenus="[
                      {id:1, title:'لیست سفارش ها', route:{name:'Order'}}
                  ]"
              />

              <DashboardAside
                  icon="far fa-heart"
                  :tab="16"
                  menu="لیست من"
                  :submenus="[
                      {id:1, title:'لیست من', route:{name:'Wishlist'}}
                  ]"
              />

            </ul>

            <ul v-if="$store.state.user.access === 'seller' || $store.state.user.access === 'user'">

              <DashboardAside
                  icon="far fa-messages"
                  :tab="16"
                  menu="تیکت ها"
                  :submenus="[
                      {id:1, title:'لیست تیکت ها', route:{name:'Ticket'}},
                  ]"
              />
              <DashboardAside
                  icon="far fa-trumpet"
                  :tab="12"
                  menu="فروشنده شوید"
                  :submenus="[
                      {id:1, title:'فروشنده شوید', route:{name:'BecomeSeller'}},
                  ]"
              />
            </ul>

            <ul v-if="$store.state.user.access === 'seller'">
              <DashboardAside
                  icon="far fa-flag"
                  :tab="2"
                  menu="برند ها"
                  :submenus="[
                      {id:1, title:'لیست برندها', route:{name:'Brand'}},
                  ]"
              />

              <DashboardAside
                  icon="far fa-cube"
                  :tab="11"
                  menu="کالاها"
                  :submenus="[
                      {id:1, title:'لیست کالاها', route:{name:'Product'}},
                      {id:2, title:'کالاهای شگفت انگیز', route:{name:'AmazingProduct'}},
                      {id:3, title:'پرسش های کالا', route:{name:'ProductQuestion'}},

                  ]"
              />

              <DashboardAside
                  icon="far fa-wallet"
                  :tab="9"
                  menu="کیف پول"
                  :submenus="[
                      {id:1, title:'شارژ کیف پول', route:{name:'Wallet'}},
                      {id:2, title:'برداشت از کیف پول', route:{name:'Withdraw'}},
                      {id:3, title:'بدهکاری ها', route:{name:'Debtor'}}
                  ]"
              />
            </ul>
            <ul v-if="$store.state.user.access === 'user'">
              <DashboardAside
                  icon="far fa-wallet"
                  :tab="9"
                  menu="کیف پول"
                  :submenus="[
                      {id:1, title:'شارژ کیف پول', route:{name:'Wallet'}},
                      {id:2, title:'برداشت از کیف پول', route:{name:'Withdraw'}}
                  ]"
              />
            </ul>

          </div>
        </aside>
        <div :class="['flex flex-col fm:w-full fd:w-[77%]']">
          <header class="border-b border-gray-200 px-4 py-2">
            <div class="flex items-center justify-between fd:justify-end">
              <div class="fd:hidden">
                <span class="cursor-pointer" @click="toggleAside = !toggleAside"><i class="far fa-bars text-xl fm:text-sm"></i></span>
              </div>
              <div class="flex items-center gap-1 cursor-pointer group relative">
                <div>
                  <img :src="$store.state.url + $store.state.user.avatar" class="rounded-full w-[40px] h-[40px]"/>
                </div>
                <div class="group-hover:block hidden bg-white shadow absolute top-[3rem] w-[12rem] left-0 rounded">
                  <ul>
                    <li class="bg-gray-100 p-2 hover:bg-gray-200 text-gray-600 fm:text-sm">کیف پول</li>
                    <li class="bg-gray-100 p-2 hover:bg-gray-200 text-gray-600 fm:text-sm" @click="logout()">خروج</li>
                  </ul>
                </div>
              </div>
            </div>
          </header>
          <main class="p-4 overflow-y-scroll scroll h-[35rem]">
            <router-view></router-view>
          </main>
          <footer class="mt-8 fd:w-full fixed bottom-0 bg-white">
            <div class="px-3 py-5 border-t">
              <p class="text-sm break-all">{{$store.state.configSite.copy_right}}</p>
            </div>
          </footer>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default{name: "DashboardLayout"}
</script>

<script setup>
import {ref ,onMounted} from "vue";
import axios from "@/plugins/axios";
import DashboardAside from "@/components/DashboardAside";
import store from "@/store";
import { useRouter } from "vue-router";

const router = useRouter();
let toggleAside = ref(false)
let walletAmount = ref(0)

onMounted(async ()=>{
  await dashboard();
})

async function logout(){
  await axios.post(`${store.state.api}logout`).then(resp=>{
    store.commit('clearStorage')
    router.push({name:'Index'})
  })
}

async function dashboard(){
  await axios.get(`${store.state.api}dashboard`).then(resp=>{
    let data = resp.data.data;
    walletAmount.value = data.wallet_amount;
    if(data.store_detail){
      store.state.siteName = data.store_detail.shop_name_ir
      store.state.url = data.store_detail.shop_url ? data.store_detail.shop_url : process.env.VUE_APP_BACKEND;
      store.state.api = data.store_detail.shop_url ? data.store_detail.shop_url : process.env.VUE_APP_BACKEND;
      store.state.api += '/api/';
      store.dispatch('setFavicon',data.store_detail.favicon)
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


function hasPermission(_permissions){
  let existPerms = [];
  for(let i = 0; i < _permissions.length; i++){
    for(let j = 0; j < store.state.userPermissions.length; j++){
      if(store.state.userPermissions[j].search(_permissions[i]) > 0){
        existPerms.push(true);
      }
    }
  }
  return existPerms.filter(item=>item).length > 0;
}
</script>