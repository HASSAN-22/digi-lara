<template>
  <div>
    <div v-if="product" class="container mx-auto mt-6">
      <div class="mx-4">
        <div class="fm:mb-8">
          <ul class="flex gap-1 items-center">
            <li v-for="category in categories" :key="category.id">
              <routerLink :to="{name:'ProductsList',params:{slug:category.slug}}" class="text-gray-500 fm:text-sm">
                {{category.title}}<span> / </span>
              </routerLink>
            </li>
            <li>
              <h5 class="text-blue-400 fm:text-sm">
                {{product.ir_name}}
              </h5>
            </li>
          </ul>
        </div>
        <div class="flex fm:flex-col fm:gap-4 fd:items-center fm:mb-8">
          <div class="flex fm:justify-between items-center gap-8 fd:w-[40%]" v-if="product.amazing_offer_status === 'yes'">
            <div><img src="@/assets/images/IncredibleOffer.svg" /></div>
            <Countdown :expireAt="product.amazing_offer_expire" />
          </div>
          <div class="" v-else-if="product.amazing_price !== null ">
            <img src="@/assets/images/SpecialSell.svg" />
          </div>
          <div class="fm:hidden flex justify-center w-full mt-5">
            <span class="text-lg !font-medium">{{product.ir_name}}</span>
          </div>
        </div>
        <div :class="['flex fm:flex-col gap-2']">
          <div class="flex w-[35%] fm:flex-col fm:gap-4 fm:w-[100%] fd:gap-3">
            <ul class="flex flex-col fm:flex-row fm:items-center fm:gap-8 fd:space-y-8">
              <li class="cursor-pointer">
                <span data-title="اضافه به علاقه مندی" @click="toggleWishlist()">
                  <span v-if="isAddedToWishlist">
                    <i class="text-2xl !font-bolder text-black fa fa-heart text-red-500"></i>
                  </span>
                  <span v-else>
                    <i class="text-2xl !font-bolder text-black far fa-heart"></i>
                  </span>
                </span>
              </li>
              <li class="cursor-pointer">
                <span data-title="اشتراک کذاری"  @click="copy()"><i class="far fa-share-alt text-2xl !font-medium"></i></span>
              </li>
              <li class="cursor-pointer">
                <span data-title="اطلاع رسانی شگفت انگیز" @click="toggleAmazingAlert()">
                  <span v-if="isAddedToAmazingAlert"><i class="fa fa-bell text-2xl text-red-500 !font-medium"></i></span>
                  <span v-else><i class="far fa-bell text-2xl !font-medium"></i></span>
                </span>
              </li>
              <li class="cursor-pointer">
                <span data-title="نمودار قیمت" @click="showModalPriceHistory()"><i class="far fa-chart-line text-2xl !font-medium"></i></span>
              </li>
              <li class="cursor-pointer">
                <span data-title="مقایسه" @click="addCompare()"><i class="far fa-compress-alt text-2xl !font-medium"></i></span>
              </li>
            </ul>
            <div class="flex relative flex-col gap-4">
              <div>
                <img :src="productImagePath" :alt="product.ir_name" class="w-[400px] h-[400px]" />
              </div>
              <div>
                <ul class="flex gap-3 items-center">
                  <li class="border border-gray-300 rounded-lg w-[15%] relative h-[15%] p-1 cursor-pointer" @click="replaceImage($store.state.url + product.image)">
                    <img :src="$store.state.url + product.image" :alt="product.ir_name"/>
                  </li>
                  <li class="border border-gray-300 rounded-lg w-[15%] h-[15%] p-1 cursor-pointer" v-for="image in product.product_images" :key="image.id" @click="replaceImage($store.state.url + image.image)">
                    <img :src="$store.state.url + image.image" :alt="product.ir_name" />
                  </li>
                  <li class="border border-gray-300 rounded-lg w-[15%] relative h-[15%] p-1 cursor-pointer" @click="showProductImagesModal()">
                    <img :src="$store.state.url + product.image" :alt="product.ir_name" class="blur" />
                    <div class="absolute top-[20%] right-[27%]"><span class="!font-extrabold text-3xl">...</span></div>
                  </li>
                </ul>
              </div>

            </div>
            <div class="flex flex-col gap-4 fd:hidden">
              <div>
                <h5 class="text-lg !font-medium">{{product.ir_name}}</h5>
              </div>
            </div>
          </div>
          <div class="flex flex-col w-[33%] fm:w-[100%] gap-4 fd:mt-10 justify-between">
            <div class="fm:hidden">
              <h5 class="text-sm text-gray-400">{{product.en_name}}</h5>
            </div>
            <div class="flex items-center gap-3">
              <div class="flex items-center gap-1">
                <span><i class="far fa-star text-sm text-yellow-300"></i></span>
                <span class="!font-medium text-sm">{{parseFloat(product.rating.rating).toFixed(1)}}</span>
                <span class="text-gray-400 text-xs">({{product.rating.average}})</span>
              </div>
              <div>
                <span class="text-blue-400 text-sm">{{product.comment_count}} دیدگاه</span>
              </div>
              <div>
                <span class="text-blue-400 text-sm">{{product.product_questions_count}} پرسش</span>
              </div>
            </div>
            <div class="flex gap-2 items-center">
              <div>
                <span><i class="far fa-thumbs-up text-green-400"></i></span>
              </div>
              <div><span class="text-gray-500">{{product['suggestion']['percent']}}% ({{product['suggestion']['total']}} نفر) از خریداران، این کالا را پیشنهاد کرده‌اند</span></div>
              <div>
                <span><i class="far fa-info-circle text-lg text-gray-400"></i></span>
              </div>
            </div>
            <div class="flex items-center gap-2">
              <div class="flex items-center bg-green-100 w-full p-2 rounded-lg text-green-500">
                <span class="fm:text-sm">دسته:</span>
                <span class="fm:text-sm">{{category.title}}</span>
              </div>
              <div class="flex items-center bg-orange-100 w-full p-2 rounded-lg text-orange-500">
                <span class="fm:text-sm">برند:</span>
                <span class="fm:text-sm">{{product.brand.name}}</span>
              </div>
            </div>
            <div v-if="sizes.length > 0">
              <div>
                <span class="!font-bold">اندازه</span>
              </div>
              <div class="mt-4">
                <select class="border border-gray-300 p-2 rounded-lg w-[30%]" @change="changeSize($event)">
                  <option v-for="size in sizes" :key="size.id" :value="[size.id,size.price]">{{size.size.size}}</option>
                </select>
              </div>
            </div>

            <div class="flex flex-wrap items-center gap-2" v-if="colors.length > 0">
              <div v-for="color in colors" :key="color.id">
                <div
                    :class="['relative rounded-full h-[3rem] w-[3rem] border border-gray-200 flex justify-center items-center cursor-pointer', (colorSelected[0].id === color.id) ? 'bg-[#19bfd3]' : 'bg-white']"
                    @click="colorChange([{id:color.id, price:color.price}])"
                >
                  <div class="rounded-full h-[2rem] w-[2rem] flex justify-center items-center" :style="{background:color.color.color_code}">
                    <span v-if="colorSelected[0].id === color.id" class="flex"><i :class="['far fa-check text-lg',color.color.color === 'سفید' ? 'text-black' : 'text-white']"></i></span>
                  </div>
                </div>
              </div>
            </div>
            <div class="mt-8">
              <span class="!font-bold text-lg">ویژگی ها</span>
              <ul :class="['flex flex-col space-y-4 mt-4', showMoreProperty ? 'h-auto' : 'h-[7rem] overflow-hidden']">
                <li class="flex gap-3 items-center" v-for="(_properties,title) in properties" :key="title">
                  <span class="text-gray-500">{{title}} :</span>
                  <span class="!font-medium">{{_properties.map(item=>item.property_type.name).join(', ')}}</span>
                </li>
              </ul>
              <div class="mt-5 text-blue-400 text-sm" v-if="Object.values(properties).length > 4">
                <span @click="showMoreProperty=!showMoreProperty" class="cursor-pointer">مشاهده {{showMoreProperty ? 'کمتر' : 'بیشتر'}}</span>
              </div>
            </div>
            <div class="flex gap-2 items-center cursor-pointer bg-red-50 p-2 rounded-lg">
              <span><i class="far fa-info-circle text-red-400 text-sm"></i></span>
              <div class="flex items-end gap-4">
                <span class="text-sm text-red-400 !font-medium cursor-pointer" @click="openReportModal('گزارش کالا', product.id,'Product')">گزارش کالا</span>
                <span class="text-xs text-blue-400">{{product.sku}}</span>
              </div>
            </div>
          </div>
          <div class="flex w-[31%] fm:w-[100%] h-full mt-10">
            <div class="border border-gray-200 rounded-lg fd:bg-gray-100 p-6  w-full">
              <div class="flex flex-col h-full gap-4" v-if="product.count > 0">
                <div class="flex flex-col gap-8 border-b border-gray-300 pb-4">
                  <span class="!font-bold text-lg">فروشنده</span>
                  <div class="flex flex-col gap-2">
                    <div class="flex items-center gap-2">
                      <div class="w-[12%]">
                        <img :src="$store.state.url + product.user.avatar">
                      </div>
                      <span class="text-lg text-gray-600">{{product.user.name}}</span>
                    </div>
                    <div class="flex items-center gap-2 px-14">
                      <div class="flex items-center gap-1">
                        <span class="text-gray-500 text-sm">عضویت از: </span>
                        <span class="text-green-400 text-sm">{{product.user.ir_created_at}}</span>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="flex gap-4 border-b justify-between border-gray-300 pb-4">
                  <div class="flex items-center gap-4">
                    <span><i class="far fa-shield-check text-2xl"></i></span>
                    <span v-if="product.guarantee_month === 1">
                    گارانتی مادام العمر {{product.guarantee.guarantee}}
                    </span>
                    <span v-else-if="product.guarantee_month === 0">
                     {{product.guarantee.guarantee}}
                    </span>
                    <span v-else>
                    گارانتی {{product.guarantee_month}} ماهه {{product.guarantee.guarantee}}
                    </span>
                  </div>
                  <div class="flex flex-col gap-2 p-2 bg-red-100 rounded items-center">
                    <span><i class="far fa-certificate text-4xl text-red-400"></i></span>
                    <span class="text-red-400 fm:text-sm !font-medium">{{product.is_original === 'yes' ? 'اصل' : 'کارکرده'}}</span>
                  </div>
                </div>
                <div class="flex flex-col gap-4 border-b border-gray-300 pb-4">
                  <div class="flex items-center gap-4">
                    <span><i class="far fa-file-invoice text-2xl"></i></span>
                    <span>موجود در انبار:</span>
                    <span class="text-green-500">{{product.count}} عدد</span>
                  </div>
                  <div class="flex gap-2 items-center">
                    <span><i class="far fa-truck scale-x-[-1] text-lg fm:text-md text-green-300"></i></span>
                    <span class="text-sm text-gray-500">{{product.transport_cost}}</span>
                  </div>
                </div>
                <div>
                  <div class="flex items-center gap-4 justify-end">
                    <div class="flex items-end flex-col gap-2">
                      <span class="text-red-500 text-sm" v-if="productPrice.discount.length > 0">تخفیف {{productPrice.discount}} ریال</span>
                      <span class="!font-medium">{{$store.getters.numberFormat(price)}} ریال</span>
                    </div>
                  </div>
                  <div class="w-full mt-8">
                    <Button text="افزودن به سبد" :btnLoading="$store.state.addBasketLoading" @click="!$store.state.addBasketLoading ? addBasket() : ''" my_class="!w-full" class="w-full"/>
                  </div>
                </div>
              </div>
              <div v-else class="flex flex-col items-center h-full gap-4">
                <div class="flex flex-row items-center w-full">
                  <div class="border border-b border-gray-400 w-full"></div>
                  <div class="w-full text-center"><span class="text-gray-400 text-lg !font-medium">کالا ناموجود</span></div>
                  <div class="border border-b border-gray-400 w-full"></div>
                </div>
                <div>
                  <p class="fm:text-sm text-gray-700">
                    این کالا فعلا موجود نیست اما می‌توانید زنگوله را بزنید تا به محض موجود شدن، به شما خبر دهیم.
                  </p>
                </div>
                <Button :my_class="isAddedExistNotfy ? '!bg-white !border !border-red-300 !text-red-500 !text-red-500 w-full !text-sm' : 'w-full !text-sm'" :text="isAddedExistNotfy ? 'دیگر لازم نیست خبرم کنید' : 'خبرم کنید'" class="!w-full" @click="toggleExistNotify()"> <span class="flex"><i class="far fa-bell-on"></i></span> </Button>
              </div>
            </div>
          </div>
        </div>

        <div class="border border-gray-200 rounded-lg p-2 mt-12 h-full">
          <div>
            <span class="border-b-2 border-red-500 text-lg !font-medium">کالاهای مشابه</span>
          </div>
          <div class="h-full">
            <Slider :slides="(displaySize < 769) ? 2 : 5">
              <SwiperSlide v-for="product in relatedProducts" :key="product.id">
                <div class="flex flex-col h-[19rem] justify-between gap-2 border-l border-gray-200 p-2">
                  <div>
                    <routerLink :to="{name:'ProductDetail', params:{slug:product.slug}}" class="flex justify-center">
                      <img :src="$store.state.url + product.image.replace(`${$store.state.largeSize}x${$store.state.largeSize}_`,`${$store.state.smallSize}x${$store.state.smallSize}_`)" class="fd:w-[150px] fd:h-[150px]"/>
                    </routerLink>
                  </div>
                  <div>
                    <routerLink :to="{name:'ProductDetail', params:{slug:product.slug}}" class="fm:text-sm">
                      <h5>{{product.ir_name}}</h5>
                    </routerLink>
                  </div>
                  <div>
                    <span class="text-sm text-red-400" v-if="product.count <= 3">تنها {{product.count}} عدد در انبار باقی مانده</span>
                  </div>
                  <div class="flex justify-between">
                    <div>
                      <span v-if="product.amazing_offer_status === 'yes'" class="bg-red-500 text-white rounded-lg px-2 fm:px-1 py-[1px] text-sm">{{product.amazing_offer_percent}}%</span>
                    </div>
                    <div class="flex flex-col gap-2">
                      <div class="flex flex-col gap-1" v-if="product.amazing_price !== null && product.amazing_offer_status !== 'yes'">
                        <span class="!font-medium fm:text-sm">{{$store.getters.numberFormat(product.amazing_price)}} ریال</span>
                        <span class="!font-medium text-xs text-gray-500"><del>{{$store.getters.numberFormat(product.price)}} ریال</del></span>
                      </div>
                      <div class="flex flex-col gap-1" v-else-if="product.amazing_offer_status === 'yes'">
                        <span class="!font-medium fm:text-sm">{{$store.getters.getDiscount(product.price, product.amazing_offer_percent)}} ریال</span>
                        <span class="!font-medium text-xs text-gray-500"><del>{{$store.getters.numberFormat(product.price)}} ریال</del></span>
                      </div>
                      <div class="flex flex-col gap-1" v-else>
                        <span class="!font-medium fm:text-sm">{{$store.getters.numberFormat(product.price)}} ریال</span>
                      </div>
                    </div>
                  </div>
                </div>
              </SwiperSlide>
            </Slider>
          </div>
        </div>

        <div class="mt-14">
          <div>
            <ul class="flex gap-8 items-center border-b border-gray-200 fm:flex-wrap">
              <li :class="['cursor-pointer text-gray-500 !font-medium border-b-4 fm:text-sm', tab === 1 ? 'border-red-500' : 'border-white']" @click="changeTab(1)">معرفی</li>
              <li :class="['cursor-pointer text-gray-500 !font-medium border-b-4 fm:text-sm', tab === 2 ? 'border-red-500' : 'border-white']" @click="changeTab(2)">بررسی تخصصی</li>
              <li :class="['cursor-pointer text-gray-500 !font-medium border-b-4 fm:text-sm', tab === 3 ? 'border-red-500' : 'border-white']" @click="changeTab(3)">مشخصات</li>
              <li :class="['cursor-pointer text-gray-500 !font-medium border-b-4 fm:text-sm', tab === 4 ? 'border-red-500' : 'border-white']" @click="changeTab(4)">دیدگاه‌ها</li>
              <li :class="['cursor-pointer text-gray-500 !font-medium border-b-4 fm:text-sm', tab === 5 ? 'border-red-500' : 'border-white']" @click="changeTab(5)">پرسش‌ها</li>
            </ul>
          </div>
          <div class="mt-4">
            <div :class="['p-2',tab === 1 ? 'block' : 'hidden']">
              <p class="break-words fm:text-sm" v-html="product.description"></p>
              <div class="mt-6 flex flex-col gap-5">
                <div class="flex items-center gap-2" v-if="JSON.parse(product.strengths).length > 0">
                  <span class="fm:text-sm">نکات مثبت</span>
                  <div class="bg-green-50 p-2 rounded">
                    <span class="text-green-500 text-sm !font-medium">{{JSON.parse(product.strengths).join(', ')}}</span>
                  </div>
                </div>
                <div class="flex items-center gap-2" v-if="JSON.parse(product.weak_points).length > 0">
                  <span class="fm:text-sm">نکات ضعف</span>
                  <div class="bg-red-50 p-2 rounded">
                    <span class="text-red-500 text-sm !font-medium">{{JSON.parse(product.weak_points).join(', ')}}</span>
                  </div>
                </div>
              </div>
            </div>
            <div :class="['p-2',tab === 2 ? 'block' : 'hidden']">
              <p class="break-words fm:text-sm" v-html="product.more_description"></p>
            </div>
            <div :class="['p-2',tab === 3 ? 'block' : 'hidden']">
              <ul>
                <li class="flex gap-2 mb-2" v-for="(_properties, title) in properties" :key="title">
                  <div class="w-[20%] rounded bg-gray-200 p-3"><span>{{title}}</span></div>
                  <div class="w-[80%] rounded bg-gray-200 p-3"><span>{{_properties.map(item=>item.property_type.name).join(', ')}}</span></div>
                </li>
                <li class="flex gap-2 mb-2" v-for="specification in product.product_specifications" :key="specification.id">
                  <div class="w-[20%] rounded bg-gray-200 p-3"><span>{{specification.name}}</span></div>
                  <div class="w-[80%] rounded bg-gray-200 p-3"><p class="break-words">{{specification.description}}</p></div>
                </li>
              </ul>
            </div>
            <div :class="['p-2',tab === 4 ? 'block' : 'hidden']">
              <div class="flex gap-8 fm:flex-col">
                <div class="w-[25%] fm:w-full bg-white">
                  <div class="flex flex-col gap-4 bg-white">
                    <div class="flex gap-1 items-center">
                      <span class="text-2xl fm:lg !font-medium">{{parseFloat(product.rating.rating).toFixed(1)}}</span>
                      <span class="text-sm text-gray-500">از</span>
                      <span class="text-sm text-gray-500">5</span>
                    </div>
                    <div class="flex gap-2">
                      <div class="flex items-center">
                        <span v-for="star in 5" :key="star">
                          <span v-if="parseInt(product.rating.rating) >= star"><i class="fa fa-star text-yellow-400"></i></span>
                          <span v-else><i class="far fa-star text-yellow-400"></i></span>
                        </span>

                      </div>
                      <div>
                        <span class="text-xs text-gray-500">از مجموع {{product.rating.average}} امتیاز</span>
                      </div>
                    </div>

                    <div class="flex flex-col gap-4 mt-4 border-t-2 pt-4">
                      <span class="text-sm text-gray-500 !font-medium">شما هم درباره این کالا دیدگاه ثبت کنید</span>
                      <Button @click="openCommentModal()" text="ثبت دیدگاه" my_class="w-full"></Button>
                    </div>
                  </div>
                </div>
                <div class="w-[75%] fm:w-full mt-24 fd:mr-12">
                  <div class="flex gap-2 items-center">
                    <div>
                      <span><i class="far fa-thumbs-up text-green-400"></i></span>
                    </div>
                    <div><span class="text-gray-500 fm:text-sm">{{product['suggestion']['percent']}}% ({{product['suggestion']['total']}} نفر) از خریداران، این کالا را پیشنهاد کرده‌اند</span></div>
                    <div>
                      <span><i class="far fa-info-circle text-lg text-gray-400"></i></span>
                    </div>
                  </div>
                  <div class="mt-10">
                    <Slider :slides="(displaySize < 769) ? 5 : 10">
                      <SwiperSlide v-for="image in [...new Map(product.product_comment_images.map(item => [item['productcomment_id'], item])).values()]" :key="image.id">
                        <div class="cursor-pointer" @click="showCustomerImageModal(image.productcomment_id)">
                          <img :src="$store.state.url + image.image" class="rounded-lg"/>
                        </div>
                      </SwiperSlide>
                    </Slider>
                  </div>
                  <div class="border-b border-gray-200 my-8"></div>
                  <div class="mt-8 flex flex-col gap-8">
                    <div class="flex fd:items-center justify-between">
                        <div class="flex gap-4 fd:items-center fm:flex-col">
                          <div class="flex items-center gap-2">
                            <span class="flex" @click="showCommentFilter = !showCommentFilter"><i class="far fa-sort-amount-down text-lg"></i></span>
                            <span class="!font-medium fm:hidden">مرتب سازی:</span>
                          </div>
                          <ul :class="['flex items-center gap-4 fm:flex-col', showCommentFilter ? 'fm:block' : 'fm:hidden']">
                            <li class="text-sm text-gray-500 cursor-pointer" @click="getComments(true, 1, 'desc')">جدیدترین</li>
                            <li class="text-sm text-gray-500 cursor-pointer" @click="getComments(true, 1, 'asc')">قدیمی ترین</li>
                          </ul>
                        </div>
                        <div>
                          <span class="text-gray-500 fm:text-sm">{{comments.length}} دیدگاه</span>
                        </div>
                      </div>
                      <div v-if="comments.length > 0">
                        <div class="mt-8 flex flex-col gap-8" v-for="_comment in comments" :key="_comment.id">
                          <div>
                            <div class="flex justify-between items-center">
                              <div class="flex items-center gap-2">
                                <span class="bg-green-600 text-xs !font-medium px-2 py-1 rounded-lg text-white">{{_comment.rating}}.0</span>
                                <span class="text-gray-500 text-xs">{{_comment.ir_created_at}}</span>
                                <span class="text-gray-500 text-xs">{{_comment.user.name}}</span>
                              </div>
                              <span class="text-gray-500 text-sm cursor-pointer" @click="openReportModal('گزارش دیدگاه', _comment.id,'Productcomment')">گزارش این دیدگاه</span>
                            </div>
                          </div>
                          <div class="flex items-center gap-2" v-if="_comment.suggest === 'yes'">
                            <span><i class="far fa-thumbs-up text-green-500"></i></span>
                            <span class="text-green-500 text-sm !font-medium">پیشنهاد می‌کنم</span>
                          </div>
                          <div class="flex items-center gap-2" v-else-if="_comment.suggest === 'no'">
                            <span><i class="far fa-thumbs-down text-red-500"></i></span>
                            <span class="text-red-500 text-sm !font-medium">پیشنهاد نمی‌کنم</span>
                          </div>
                          <div class="flex items-center gap-2" v-else>
                            <div class="flex items-center">
                              <span><i class="far fa-question text-gray-500"></i></span>
                              <span><i class="far fa-exclamation text-gray-500"></i></span>
                            </div>
                            <span class="text-gray-500 text-sm !font-medium">مطمعن نیستم</span>
                          </div>
                          <div>
                            <p class="break-words fm:text-sm">
                              {{_comment.comment}}
                            </p>
                          </div>
                          <div>
                            <ul class="flex flex-col gap-2">
                              <li class="flex items-center gap-2" v-for="(strength,index) in JSON.parse(_comment.strengths)" :key="index">
                                <span><i class="far fa-plus text-green-500"></i></span>
                                <span class="text-sm">{{strength}}</span>
                              </li>
                              <li class="flex items-center gap-2" v-for="(weak_point,index) in JSON.parse(_comment.weak_points)" :key="index">
                                <span><i class="far fa-window-minimize text-red-500"></i></span>
                                <span class="text-sm">{{weak_point}}</span>
                              </li>
                            </ul>
                          </div>
                          <div class="flex items-center gap-1 flex-wrap">
                            <a href="" target="_blank" v-for="image in _comment.product_comment_images" :key="image.id">
                              <img :src="$store.state.url + image.image" class="w-[50px] h-[50px] fm:w-[25px] fm:h-[25px]"/>
                            </a>
                          </div>
                          <div class="flex items-center gap-8 justify-end">
                            <span class="text-gray-500 text-sm">آیا این دیدگاه مفید بود؟</span>
                            <div class="flex items-center gap-3">
                              <div class="flex items-center gap-1">
                                <span class="text-gray-500 text-sm cursor-pointer" @click="likeOrDislike('yes',_comment.id,'Productcomment')"><i class="far fa-thumbs-up text-green-500"></i></span>
                                <span class="text-gray-500 text-sm text-green-500">{{_comment.likes.filter(item=>item.is_like === 'yes').length}}</span>
                              </div>
                              <div class="flex items-center gap-1">
                                <span class="text-gray-500 text-sm cursor-pointer" @click="likeOrDislike('no',_comment.id,'Productcomment')"><i class="far fa-thumbs-down text-red-500"></i></span>
                                <span class="text-gray-500 text-sm text-red-500">{{_comment.likes.filter(item=>item.is_like === 'no').length}}</span>
                              </div>
                            </div>
                          </div>
                          <div class="border-b border-gray-200 mt-3"></div>
                          <div v-if="comments.length > 0">
                            <Paginate :current="$store.state.current" :next="$store.state.next" :previous="$store.state.previous" :total="$store.state.total" @callGetPage="getComments"/>
                          </div>
                        </div>
                      </div>
                      <div v-else class="flex justify-center">
                        <img src="@/assets/images/no-data.svg" class="w-[300px] fm:w-[200px]"/>
                      </div>

                  </div>
                </div>
              </div>
            </div>
            <div :class="['p-2',tab === 5 ? 'block' : 'hidden']">
              <div class="flex gap-4 fm:flex-col fm:gap-8">
                <div class="w-[20%] fm:w-full fd:mt-12">
                  <div class="flex flex-col gap-4">
                    <span class="text-sm text-gray-500">شما هم درباره این کالا پرسش ثبت کنید</span>
                    <Button text="ثبت پرسش" @click="openQuestionModal()" my_class="!w-full"></Button>
                  </div>
                </div>
                <div class="w-[80%] fm:w-full fd:mt-12">
                  <div class="flex flex-col gap-8 fm:gap-4 fd:mr-14">
                    <div class="flex gap-4 items-center">
                      <div class="flex items-center gap-2">
                        <span class="flex"><i class="far fa-sort-amount-down text-lg"></i></span>
                        <span class="!font-medium fm:text-sm">مرتب سازی:</span>
                      </div>
                      <ul class="flex items-center gap-4">
                        <li class="text-sm text-gray-500 cursor-pointer" @click="getQuestions(true, 1, 'desc')">جدیدترین</li>
                        <li class="text-sm text-gray-500 cursor-pointer" @click="getQuestions(true, 1, 'asc')">مفیدترین</li>
                      </ul>
                    </div>
                    <div class="mt-8">
                      <div  v-for="_question in questions" :key="_question.id" :class="['py-8', _question.id < 4 ? 'border-b border-gray-300' : '']">
                        <div class="flex gap-3 items-center">
                          <span><i class="far fa-question-circle text-3xl fm:text-xl text-blue-400"></i></span>
                          <div>
                            <p class="break-words fm:text-sm">{{_question.question}}</p>
                          </div>
                          <button class="border border-red-500 text-sm fm:text-xs text-red-500 p-1 rounded" @click="openAnswerModal(_question.id)">پاسخ</button>
                        </div>
                        <div v-for="_answer in _question.product_question_answers" :key="_answer.id" :class="['mr-10 mt-4 py-2', _answer.id < 4 ? 'border-b border-gray-200 ' : '']">
                          <div class="flex items-center justify-between">
                            <div class="flex items-center gap-1">
                              <span class="text-gray-500 text-sm fm:text-xs">پاسخ توسط:</span>
                              <span class="text-gray-500 text-sm fm:text-xs">{{_answer.user.name}}</span>
                            </div>
                            <div class="flex items-center gap-1">
                              <span class="text-gray-500 text-sm fm:text-xs">{{_answer.ir_created_at}}</span>
                              <span class="flex text-gray-500 text-sm fm:text-xs"><i class="far fa-clock"></i></span>
                            </div>
                          </div>
                          <div class="fm:mt-2">
                            <p class="break-words fm:text-sm">{{_answer.answer}}</p>
                          </div>
                          <div class="flex items-center gap-8 mt-4 justify-end">
                            <span class="text-gray-500 text-sm">آیا این پاسخ مفید بود؟</span>
                            <div class="flex items-center gap-3">
                              <div class="flex items-center gap-1">
                                <span class="text-gray-500 text-sm cursor-pointer" @click="likeOrDislike('yes',_answer.id,'Productquestionanswer')"><i class="far fa-thumbs-up text-green-500"></i></span>
                                <span class="text-gray-500 text-sm text-green-500">{{_answer.likes.filter(item=>item.is_like === 'yes').length}}</span>
                              </div>
                              <div class="flex items-center gap-1">
                                <span class="text-gray-500 text-sm cursor-pointer" @click="likeOrDislike('no',_answer.id,'Productquestionanswer')"><i class="far fa-thumbs-down text-red-500"></i></span>
                                <span class="text-gray-500 text-sm text-red-500">{{_answer.likes.filter(item=>item.is_like === 'no').length}}</span>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>

                      <div v-if="questions.length > 0">
                        <Paginate :current="$store.state.current" :next="$store.state.next" :previous="$store.state.previous" :total="$store.state.total" @callGetPage="getQuestions"/>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <Meta v-if="product" type="name" title="author" :description="product.user.name" />
      <Meta v-if="product" type="property" title="og:title" :description="product.ir_name" />
      <Meta v-if="product" type="property" title="og:url" :description="url" />
      <Meta v-if="product" type="name" title="keywords" :description="`${product.ir_name} | ${product.slug}کالا | کالای `" />
      <Meta v-if="product" type="property" title="og:image" :description="$store.state.url + product.image" />
      <Meta v-if="product" type="property" title="article:published_time" :description="product.created_at" />
      <Meta v-if="product" type="property" title="article:modified_time" :description="product.updated_at" />
      <Meta v-if="product" :title="$store.state.siteName + ` | ${product.ir_name} `"/>

      <Modal title="تصاویر کالا" ref="productImageModal" width="w-[70%] fm:w-full">
        <div class="flex items-center gap-8 fm:mb-8">
          <span :class="['fm:text-sm text-gray-600 text-lg !font-medium border-b-4 cursor-pointer', productGalleryTitle === 1 ? 'border-red-500' : 'border-white']" @click="changeProductGalleryTitle(1)">تصاویر رسمی</span>
          <span :class="['fm:text-sm text-gray-600 text-lg !font-medium border-b-4 cursor-pointer', productGalleryTitle === 2 ? 'border-red-500' : 'border-white']" @click="changeProductGalleryTitle(2)">تصاویر خریداران</span>
        </div>
        <div class="fd:flex fd:gap-4" v-if="productGalleryTitle === 1">
          <ProductImage :key="productGalleryTitle" :images="product.product_images" :defaultImage="$store.state.url + product.image"  />
        </div>
        <div class="fd:flex fd:gap-4" v-if="productGalleryTitle === 2">
          <ProductImage :key="productGalleryTitle" :images="product.product_comment_images" :defaultImage="product.product_comment_images.length > 0 ? $store.state.url + product.product_comment_images[0].image : null" />
        </div>
      </Modal>
<!--      Show single comment -->
      <Modal title="تصاویر کالا" ref="customerImageModal" width="w-[80%] fm:w-full">
        <div>
          <span>تصاویر خریداران</span>
        </div>
        <div class="fd:flex fd:gap-4" v-if="currentComment !== null">
          <div class="w-[60%]">
            <Slider :slides="1" :navigation="true">
              <SwiperSlide v-for="image in currentComment.product_comment_images" :key="image.id">
                <div class="cursor-pointer">
                  <img :src="$store.state.url + image.image" class="rounded-lg w-[580px] h-[550px]"/>
                </div>
              </SwiperSlide>
            </Slider>
          </div>
          <div  class="w-[40%]">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-2">
                <span class="bg-green-500 px-2 py-1 text-xs text-white !font-medium rounded-lg">{{currentComment.rating}}.0</span>
                <span class="text-gray-500 text-xs">{{currentComment.ir_created_at}}</span>
                <span class="text-gray-500 text-xs">{{currentComment.user.access}}</span>
              </div>
              <span class="text-gray-500 fm:text-sm cursor-pointer">گزارش این دیدگاه</span>
            </div>
            <div class="border-b border-gray-200 my-8"></div>
            <div class="flex flex-col gap-6">
              <div class="flex items-center gap-2" v-if="currentComment.suggest === 'yes'">
                <span><i class="far fa-thumbs-up text-green-500"></i></span>
                <span class="text-green-500 text-sm !font-medium">پیشنهاد می‌کنم</span>
              </div>
              <div class="flex items-center gap-2" v-else-if="currentComment.suggest === 'no'">
                <span><i class="far fa-thumbs-down text-red-500"></i></span>
                <span class="text-red-500 text-sm !font-medium">پیشنهاد نمی‌کنم</span>
              </div>
              <div class="flex items-center gap-2" v-else>
                <div class="flex items-center">
                  <span><i class="far fa-question text-gray-500"></i></span>
                  <span><i class="far fa-exclamation text-gray-500"></i></span>
                </div>
                <span class="text-gray-500 text-sm !font-medium">مطمعن نیستم</span>
              </div>
              <div>
                <p class="break-words text-lg fm:text-md tracking-wider">{{currentComment.comment}}</p>
              </div>
            </div>
          </div>
        </div>
      </Modal>

      <Modal title="تاریخچه قیمت" ref="priceHistoryModal" width="w-[70%] fm:w-full">
        <Line :key="priceHistoryChartKey" :data="priceHistoryData" :options="options" />
      </Modal>

<!--      Send Comment -->
      <Comment :purchased="purchased" :productId="product.id" @parentMethod="getComments" ref="commentRef"/>

<!--    Send question-->
      <Modal title="ثبت پرسش" save="ثبت" :btnLoading="btnLoading" @callback="addQuestion()" ref="questionModal" width="w-[70%] fm:w-full">
        <div>
          <Textarea label="پرسش خود را درباره این کالا ثبت کنید" v-model="question" placeholder="پرسش خود را درباره این کالا ثبت کنید" :maxlength="500" :alert="question.length + '/500'"/>
        </div>
      </Modal>

      <!--    Send answer to question-->
      <Modal title="ثبت پاسخ" save="ثبت" :btnLoading="btnLoading" @callback="addAnswer()" ref="answerModal" width="w-[70%] fm:w-full">
        <div>
          <Textarea label="پاسخ شما" v-model="answer" :maxlength="500" :alert="answer.length + '/500'"/>
        </div>
      </Modal>

      <!--    Report -->
      <Modal :title="reportTitle" save="ثبت" :btnLoading="btnLoading" @callback="sendReport()" ref="reportModal" width="w-[70%] fm:w-full">
        <div>
          <Textarea label="شرح مشکل" placeholder="لطفا به طور کامل مشکل را شرح دهید" v-model="reportDescription" :maxlength="5000" :alert="reportDescription.length + '/5000'"/>
        </div>
      </Modal>
    </div>


    <ValidationError />
    <Loading :loading="loading"/>
  </div>

</template>

<script>
export default {name: "ProductDetail"}
</script>

<script setup>
import {ref, onMounted, onBeforeUnmount, watch, computed} from "vue";
import store from "@/store";
import Meta from "@/components/Meta.vue";
import Paginate from "@/components/Paginate.vue";
import Modal from "@/components/Modal.vue";
import Comment from "@/components/Comment.vue";
import ValidationError from "@/components/ValidationError.vue";
import Button from "@/components/Button.vue";
import ProductImage from "@/components/ProductImage.vue";
import Slider from "@/components/Slider.vue";
import Countdown from "@/components/Countdown.vue";
import { SwiperSlide } from 'swiper/vue';
import Loading from "@/components/Loading";
import { useRoute } from "vue-router"
import axios from "@/plugins/axios"
import Toast from "@/plugins/toast";

import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Filler,
  Legend
} from 'chart.js'
import { Line } from 'vue-chartjs'
import Textarea from "@/components/Textarea";

ChartJS.register(CategoryScale, LinearScale, Filler , PointElement, LineElement, Title, Tooltip, Legend)

let priceHistoryData = ref({
  labels: [],
  datasets: [{
    borderColor: '#0fabc6',
    pointBackgroundColor: '#0fabc6',
    borderWidth: 1,
    radius: 2,
    fill: true,
    pointBorderColor: '#0fabc6',
    backgroundColor: (ctx) => {
      const canvas = ctx.chart.ctx;
      const gradient = canvas.createLinearGradient(0,0,0,160);

      gradient.addColorStop(0, 'rgba(159,229,246,0.35)');
      gradient.addColorStop(.5, 'rgba(159,229,246,0.35)');
      gradient.addColorStop(1, 'rgba(159,229,246,0.35)');

      return gradient;
    },
    tension: 0.25,
    label: '',
    data: []
  }]
})
let options = ref({
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      display: false
    }
  }

})

const route = useRoute();
let loading = ref(false)
let btnLoading = ref(false)
let showMoreProperty = ref(false)
let showCommentFilter = ref(false)
let productImageModal = ref(null)
let commentRef = ref(null)
let priceHistoryModal = ref(null)
let questionModal = ref(null)
let reportModal = ref(null)
let answerModal = ref(null)
let customerImageModal = ref(null)
let productGalleryTitle = ref(1)
let url = ref(window.location.href)
let productImagePath = ref('')
let galleryImagePath = ref("")
let gallerySellerImagePath = ref("")
let displaySize = ref(window.innerWidth);
let tab = ref(1);

let reportDescription = ref('');
let reportType = ref('');
let reportId = ref('');
let reportTitle = ref('');


let purchased = ref(false);
let price = ref(0);
let defalutPrice = ref(0);
let product = ref(null);
let relatedProducts = ref([]);
let categories = ref([]);
let category = ref(null);
let isAddedToWishlist = ref(false);
let isAddedToAmazingAlert = ref(false);
let isAddedExistNotify=ref(false)
let colors = ref([]);
let sizes = ref([]);
let colorSelected = ref([]);
let sizeSelected = ref([]);
let properties = ref([]);
let comments = ref([]);
let currentComment = ref(null);
let questions = ref([]);
let question = ref('');
let answer = ref('');
let questionId = ref('');
let priceHistories = ref([]);
let priceHistoryChartKey = ref(0);
let slug = ref(route.params['slug']);
onBeforeUnmount(() => {
  window.removeEventListener('resize', onResize)
})

onMounted(async () => {
  store.state.searchModal = false;
  store.state.searchText = '';
  await getData()
  await getUserData();
  window.addEventListener('resize', onResize)
})

watch(route, async ( current ) => {
  store.state.searchModal = false;
  store.state.searchText = '';
  if(current.name === 'ProductDetail'){
    slug.value = current.params['slug'];
    await getData()
    await getUserData();
  }
})

const productPrice = computed(()=>{
  let amount = 0;
  let discount = 0;
  if(product.value.amazing_offer_status === 'yes'){
    amount = store.getters.getDiscount(product.value.price, product.value.amazing_offer_percent,false);
    discount = product.value.price - amount
  }else if(product.value.amazing_price !== null && product.value.amazing_price > 0){
    discount = product.value.price - product.value.amazing_price
    amount = product.value.amazing_price
  }else{
    amount = product.value.price
  }
  price.value = amount;
  defalutPrice.value = amount;
  return {discount:discount > 0 ? store.getters.numberFormat(discount) : ''}
})

async function getData(_loading=true){
  loading.value = _loading;
  await axios.get(`${store.state.api}get-product-detail/${slug.value}`).then(resp=>{
    let data = resp.data.data;
    product.value = data['product'];
    relatedProducts.value = data.related_products;
    categories.value = data.category_parents;
    category.value = data.category;
    colors.value = product.value.product_colors.sort((a, b) => a.price > b.price ? 1 : -1)
    sizes.value = product.value.product_sizes.sort((a, b) => a.price > b.price ? 1 : -1)
    if(colors.value.length > 0){
      let color = colors.value[0];
      colorChange([{id:color.id, price:color.price}])
    }
    if(sizes.value.length > 0){
      changeSize([sizes.value[0].id, sizes.value[0].price], false)
    }

    priceHistories.value = product.value.product_price_histories
    productImagePath.value = store.state.url + product.value.image
    // Separation of similar properties
    properties.value = product.value.product_properties.reduce((a,v) => ((a[v.property.property] = a[v.property.property] || []).push(v), a), {});

    // Create chart line data
    chartData();

  }).catch(err=>{})
  loading.value = false;
}

async function getUserData(){
  await axios.get(`${store.state.api}get-user-data/${product.value.id}`).then(resp=>{
    let data = resp.data.data;
    isAddedToWishlist.value = data.has_wishlist;
    isAddedToAmazingAlert.value = data.has_amazing_alert;
    isAddedExistNotify.value = data.has_product_notify_exists;
  }).catch(err=>{})
}

async function addBasket(){
  if(store.state.user.token) {
    let color = colorSelected.value.length > 0 ? colorSelected.value[0].id : '';
    let size = sizeSelected.value.length > 0 ? sizeSelected.value[0].id : '';
    await store.dispatch('addBasket', {productId: product.value.id, color: color, size: size})
  }else{
    Toast.error('لطفا وارد حساب کاربری خود شوید')
  }
}

async function toggleWishlist(){
  loading.value = true;
  if(store.state.user.token){
    await axios.post(`${store.state.api}toggle-wishlist/${product.value.id}`).then(resp=>{
      isAddedToWishlist.value = resp.data.data;
      Toast.success('با موفقیت انجام شد')
    }).catch(err=>{
      Toast.error('یک خطای غیر منظره رخ داده است')
    })
  }else{
    Toast.error('لطفا وارد حساب کاربری خود شوید')
  }
  loading.value = false;
}

async function toggleAmazingAlert(){
  loading.value = true;
  if(store.state.user.token){
    await axios.post(`${store.state.api}toggle-amazing-alert/${product.value.id}`).then(resp=>{
      isAddedToAmazingAlert.value = resp.data.data;
      Toast.success('با موفقیت انجام شد')
    }).catch(err=>{
      Toast.error('یک خطای غیر منظره رخ داده است')
    })
  }else{
    Toast.error('لطفا وارد حساب کاربری خود شوید')
  }
  loading.value = false;
}

async function addCompare(){
  loading.value = true;
  if(store.state.user.token){
    await axios.post(`${store.state.api}add-compare/${product.value.id}?compare=detail`).then(resp=>{
      Toast.success('با موفقیت ااضافه شد')
      setTimeout(()=>{
        window.location.href = window.location.origin +'/compares'
      },1000)
    }).catch(err=>{
      Toast.error('یک خطای غیر منظره رخ داده است')
    })
  }else{
    Toast.error('لطفا وارد حساب کاربری خود شوید')
  }
  loading.value = false;
}

async function getComments(_loading=true, page=1, sort='desc'){
  loading.value = _loading;
  await axios.get(`${store.state.api}get-product-comments/${product.value.id}?page=${page}&sort=${sort}`).then(resp=>{
    let data = resp.data.data;
    purchased.value = data.purchased
    comments.value = data.comments.data
    store.commit('paginate',data.comments);

  }).catch(err=>{
    Toast.error('یک خطای غیر منظره رخ داده است')
  })
  loading.value = false;
}

async function getComment(commentId){
  loading.value = true;
  await axios.get(`${store.state.api}get-comment/${commentId}`).then(resp=>{
    currentComment.value = resp.data.data
  })
  loading.value = false;
}

async function likeOrDislike(isLike,postId, type){
  loading.value = true;
  if(store.state.user.token){
    await axios.post(`${store.state.api}like-or-dislike?is_like=${isLike}&post_id=${postId}&type=${type}`).then(async resp=>{
      if(type === 'Productcomment'){
        await getComments(false, store.state.current)
      }else{
        await getQuestions(false, store.state.current)
      }
      Toast.success('با موفقیت ثبت شد')
    }).catch(err=>{
      store.commit('handleError',err)
    })
  }else{
    Toast.error('لطفا وارد حساب کاربری خود شوید')
  }
  loading.value = false;
}

async function getQuestions(_loading=true, page=1, sort='desc'){
  loading.value = _loading;
  await axios.get(`${store.state.api}get-product-questions/${product.value.id}?page=${page}&sort=${sort}`).then(resp=>{
    let data = resp.data.data;
    questions.value = data.data;
    store.commit('paginate',data);
  })
  loading.value = false;
}

async function addQuestion(){
  btnLoading.value = true;
  if(store.state.user.token){
    await axios.post(`${store.state.api}add-product-question/${product.value.id}`,{question:question.value}).then(async resp=>{
      Toast.success('با موفقیت ثبت شد بعد از تایید نمایش داده میشود')
      await getQuestions(false, store.state.current)
      question.value = '';
    }).catch(err=>{
      store.commit('handleError',err)
    })
  }else{
    Toast.error('لطفا وارد حساب کاربری خود شوید')
  }
  btnLoading.value = false;
}

async function addAnswer(){
  btnLoading.value = true;
  if(store.state.user.token){
    await axios.post(`${store.state.api}add-product-question-answer/${questionId.value}`,{answer:answer.value}).then(async resp=>{
      Toast.success('با موفقیت ثبت شد بعد از تایید نمایش داده میشود')
      await getQuestions(false, store.state.current)
      answer.value = '';
    }).catch(err=>{
      store.commit('handleError',err)
    })
  }else{
    Toast.error('لطفا وارد حساب کاربری خود شوید')
  }
  btnLoading.value = false;
}

async function sendReport(){
  btnLoading.value = true;
  if(store.state.user.token){
    await axios.post(`${store.state.api}send-report`,{type:reportType.value,post_id:reportId.value, report_description:reportDescription.value}).then(async resp=>{
      Toast.success()
      reportDescription.value = '';
      reportType.value = '';
      reportModal.value.toggleModal();
    }).catch(err=>{
      store.commit('handleError',err)
    })
  }else{
    Toast.error('لطفا وارد حساب کاربری خود شوید')
  }
  btnLoading.value = false;
}

function chartData() {
  priceHistoryData.value.labels = [];
  priceHistoryData.value.datasets[0].data = [];
  priceHistoryData.value.datasets[0].label = product.value.ir_name;
  for (let i = 0; i < priceHistories.value.length; i++) {
    let history = priceHistories.value[i];
    priceHistoryData.value.labels.push(history.ir_created_at);
    priceHistoryData.value.datasets[0].data.push(history.price);
  }
}

function replaceImage(_imagePath, type){
  if(type === "gallery"){
    galleryImagePath.value = _imagePath;
  }else if(type === "gallerySeller"){
    gallerySellerImagePath.value = _imagePath;
  }else{
    productImagePath.value = _imagePath;
  }
}

async function changeTab(_tab){
  tab.value = _tab;
  if(tab.value === 4){
    await getComments()
  }
  else if(tab.value === 5){
    await getQuestions()
  }
}

function changeProductGalleryTitle(title){
  productGalleryTitle.value = title;
}

function showProductImagesModal(){
  productImageModal.value.toggleModal();
}

function showCustomerImageModal(commentId){
  customerImageModal.value.toggleModal();
  getComment(commentId)
}

function changeSize(event, isEvent = true){
  let result = isEvent ? event.target.value.split(',') : event;
  sizeSelected.value = [{id:result[0], price:result[1]}]
  price.value = parseInt(defalutPrice.value) + parseInt(result[1]);
}

function colorChange(colorData){
  colorSelected.value = colorData;
  price.value = parseInt(defalutPrice.value) + parseInt(colorData[0].price);
}

async function toggleExistNotify(){
  loading.value = true;
  if(store.state.user.token) {
    await axios.post(`${store.state.api}toggle-exist-notify-product/${product.value.id}`).then(resp => {
      resp.data.data ? Toast.success('با موفقیت ثبت شد') : Toast.success('با موفقیت برداشته شد');
      isAddedExistNotify.value = resp.data.data;
    }).catch(err => {
      Toast.error('یک خطای غیر منتظره رخ داده است')
    })
  }else{
    Toast.error('لطفا وارد حساب کاربری خود شوید')
  }
  loading.value = false;
}

function onResize() {
  displaySize.value = window.innerWidth;
}

function copy(){
  navigator.clipboard.writeText(url.value);
  Toast.success('لینک با موفقیت کپی شد')
}

function showModalPriceHistory(){
  priceHistoryChartKey.value ++;
  priceHistoryModal.value.toggleModal();
}

function openCommentModal(){
  commentRef.value.openCommentModal();
}

function openQuestionModal(){
  questionModal.value.toggleModal();
}

function openAnswerModal(_questionId){
  answerModal.value.toggleModal();
  questionId.value = _questionId
}

function openReportModal(title, postId, _reportType){
  reportModal.value.toggleModal();
  reportTitle.value = title;
  reportId.value = postId;
  reportType.value = _reportType;
}


</script>