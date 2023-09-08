<template>
  <div class="mt-10 px-8">
    <div class="flex justify-center">
      <span class="!font-medium text-lg">لیست کالاها</span>
    </div>
    <div class="mt-10 px-3 mb-3">
      <div class="mb-3 mr-2 flex justify-between fm:flex-col items-center">
        <div class="flex gap-2 items-center">
          <UserCanAction action="create" @create="create()" permission="create_products" />
          <Button @click="getData(false, 1, true)" my_class="!bg-white !py-2 !px-2" :btnLoading="refresh"><i class="far fa-sync text-2xl fm:text-lg text-gray-700"></i></Button>
          <Button v-if="isAdmin" text="ایمپورت کردن کالا" @click="showImport()" my_class="!bg-white !border !border-blue-400 !text-blue-400 !py-2 !px-2"></Button>
        </div>
        <div class="ml-2 fm:mt-3">
          <Input type="search" v-debounce="searchData" :debounce-events="['keydown']" id="search" placeholder="جستجو: متن خود را وارد کنید" :required="false" />
        </div>
      </div>
      <div v-if="allData.length <= 0" class="border border-b w-full my-2"></div>
      <div v-if="allData.length > 0">
        <table class="w-full border-collapse lg:table 2xl:table xl:lg:table md:table">
          <Thead :titles="[
            'نام کالا',
            'فروشنده',
            'تصویر',
            'قیمت (ریال)',
            'تعداد در انبار',
            'شگفت انگیز میباشد',
            'وضعیت',
            'عملیات'
            ]" :except="[$store.state.user.access !== 'admin' ? 'فروشنده' : '']" />
          <tbody class="block lg:table-row-group xl:table-row-group 2xl:table-row-group md:table-row-group">
          <tr v-for="item in allData" :key="item.id" class="border border-gray-200 block lg:table-row xl:table-row 2xl:table-row md:table-row">
            <Td title="نام کالا" class="fm:text-sm">
              <Notification :notifications="allNotifications" :postId="item.id" type="product">
                <div class="flex flex-col">
                  <span>{{item.name}}</span>
                  <span class="text-xs text-gray-400">شناسه: {{item.sku}}</span>
                </div>
              </Notification>
            </Td>
            <Td v-if="isAdmin" title="فروشنده" class="fm:text-sm">{{item.user}}</Td>
            <Td title="تصویر" class="fm:text-sm w-[80px] h-[80px]"><img :src="$store.state.url + item.image" class="fm:mr-[5rem]" /></Td>
            <Td title="قیمت (ریال)" class="fm:text-sm flex flex-col">
              <div v-if="item.amazing_price !== null && item.amazing_price.length > 0" class="flex flex-col gap-1">
                <span class="fm:text-sm">{{item.amazing_price}}</span>
                <del class="text-gray-400 text-xs">{{item.price}}</del>
              </div>
              <div v-else>
                <span>{{item.price}}</span>
              </div>
            </Td>
            <Td title="تعداد در انبار" class="fm:text-sm">{{item.count}} عدد</Td>
            <Td title="شگفت انگیز میباشد" class="fm:text-sm flex flex-col">
              <div v-if="item.amazing_offer_percent !== null && item.amazing_offer_status === 'yes'">
                <span class="fm:text-sm">{{item.amazing_offer_status}} <span class="bg-red-500 px-1 text-xs rounded-full text-white">{{item.amazing_offer_percent}}%</span></span>
              </div>
              <div v-else>
                <span>{{item.amazing_offer_status}}</span>
              </div>
            </Td>
            <Td title="وضعیت" class="fm:text-sm">{{item.publish}}</Td>

            <Td title="عملیات">
              <div class="flex items-center justify-center gap-2">
                <UserCanAction action="show" @show="show(item.id)" permission="view_products" :postId="item.id" />
                <UserCanAction action="destroy" @destroy="destroy(item.id)" permission="update_products" :postId="item.id" />
              </div>
            </Td>
          </tr>
          </tbody>
        </table>
      </div>
      <div v-else class="flex justify-center">
        <img src="@/assets/images/no-data.svg" class="w-[300px] fm:w-[200px]"/>
      </div>
      <div v-if="allData.length > 0">
        <Paginate :current="$store.state.current" :next="$store.state.next" :previous="$store.state.previous" :total="$store.state.total" @callGetPage="getData"/>
      </div>
    </div>
    <Modal :title="isUpdate ? 'ویرایش کالا' : 'ثبت کالا'" :save="isUpdate ? 'ثبت تغییرات' : 'ثبت'" :permission="isUpdate ? 'update_products' : 'create_products'" :btnLoading="btnLoading" @callback="isUpdate ? update() : insert()" ref="openModal" width="w-[90%]">
      <div class="flex fm:flex-col gap-4">
        <div class="fd:w-[65%] bg-white shadow p-2 h-full">
          <div class="mt-5 flex fm:flex-col fd:items-center gap-2">
            <Input label="نام کالا" placeholder="نام کالا با رعایت ترتیب ماهیت کالا + برند + مدل" v-model="ir_name" id="ir_name" />
            <Input label="نام انگلیسی کالا" :required="false" placeholder="Name of the product according to the order of the nature of the product + brand + model" v-model="en_name" id="en_name" />
          </div>
          <div class="mt-5 flex fm:flex-col fd:items-center gap-2">
            <Input label="شناسه (SKU)" placeholder="شناسه (SKU)" v-model="sku" id="sku" />
            <Input label="تعداد در انبار" placeholder="تعداد در انبار" v-model="count" id="count" />
          </div>
          <div class="mt-5 flex fm:flex-col gap-2">
            <Input label="قیمت (ریال)" placeholder="قیمت (ریال)" v-model="price" id="price" />
            <Input label="قیمت ویژه (ریال)" :required="false" placeholder="قیمت ویژه (ریال)" alert="اگر قیمت ویژه را وارد نمایید کالا با قیمت ویژه به فروش خواهد رفت" v-model="amazing_price" id="amazing_price" />
          </div>
          <div class="mt-5">
            <span class="fm:text-sm text-gray-700">ابعاد بسته بندی (سانتیمتر)</span>
            <div class="mt-5 flex fm:flex-col gap-2">
              <Input type="number" label="طول" placeholder="" v-model="packing_length" id="packing_length" />
              <Input type="number" label="عرض" placeholder="" v-model="packing_width" id="packing_width" />
              <Input type="number" label="ارتفاع" placeholder="" v-model="packing_height" id="packing_height" />
              <Input type="number" label="وزن (گرم)" placeholder="مثال: 200 گرم" v-model="packing_weight" id="packing_weight" />
            </div>
          </div>
          <div class="mt-5">
            <span class="fm:text-sm text-gray-700">مشخصات فیزکی کالا (سانتیمتر)</span>
            <div class="mt-5 flex fm:flex-col gap-2">
              <Input type="number" label="طول" placeholder="" v-model="physical_length" id="physical_length" />
              <Input type="number" label="عرض" placeholder="" v-model="physical_width" id="physical_width" />
              <Input type="number" label="ارتفاع" placeholder="" v-model="physical_height" id="physical_height" />
              <Input type="number" label="وزن (گرم)" placeholder="مثال: 200 گرم" v-model="physical_weight" id="physical_weight" />
            </div>
          </div>
          <div class="mt-5">
            <Textarea label="شرح کالا" id="description" placeholder="شرح کالا بیشتر از 200 کاراکتر" v-model="description" :maxlength="2000" :alert="description.length + '/2000'" />
          </div>
          <div class="mt-5 flex fm:flex-col gap-2">
            <div class="flex flex-col gap-3 w-full">
              <Input label="نکات مثبت" placeholder="متن را وارد کرده و اینتر بزند" :required="false" @keyup.enter="addStrengths($event)" id="strengths" />
              <div class="flex flex-wrap gap-2">
                <div class="flex items-center gap-1 bg-green-100 rounded px-2 py-1" v-for="(strength,index) in strengths" :key="index">
                  <span class="text-green-500 text-sm">{{strength}}</span>
                  <span class="cursor-pointer" @click="removeStrengths(index)"><i class="far fa-trash text-red-500 text-sm"></i></span>
                </div>
              </div>
            </div>
            <div class="flex flex-col gap-3 w-full">
              <Input label="نکات ضعف" placeholder="متن را وارد کرده و اینتر بزند" :required="false" @keyup.enter="addWeakPoints($event)" id="property" />
              <div class="flex flex-wrap gap-2">
                <div class="flex items-center gap-1 bg-red-100 rounded px-2 py-1" v-for="(weakPoint,index) in weakPoints" :key="index">
                  <span class="text-red-500 text-sm">{{weakPoint}}</span>
                  <span class="cursor-pointer" @click="removeWeakPoints(index)"><i class="far fa-trash text-red-500 text-sm"></i></span>
                </div>
              </div>
            </div>
            <div>
            </div>
          </div>
          <div class="mt-5">
            <Editor :key="editorKey" contentType="html" v-model="more_description" placeholder="سایر توضیحات"></Editor>
<!--            <Textarea label="سایر توضیحات" :required="false" id="more_description" placeholder="سایر توضیحات" :maxlength="10000" v-model="more_description" :alert="more_description.length + '/10000'"/>-->
          </div>
        </div>
        <div class="fd:w-[35%] flex flex-col gap-4">
          <div class="w-full bg-white shadow rounded py-4 px-2 flex flex-col gap-4">
            <div v-if="isAdmin">
              <label class="fm:text-sm" for="sellerId"> فروشنده<b class="text-red-500 !font-bold">*</b></label>
              <select v-model="sellerId" class="fm:text-sm border border-gray-200 p-2 outline-none rounded-lg w-full">
                <option value="" selected>--- انتخاب کنید ---</option>
                <option v-for="user in allUsers" :key="user.id" :value="user.id">{{user.name}}</option>
              </select>
            </div>
            <div class="flex flex-col gap-2">
<!--              <div class="mt-2">-->
<!--                <label class="fm:text-sm">دسته</label>-->
<!--                <Multiselect-->
<!--                    v-model="categoryId"-->
<!--                    :options="allCategories"-->
<!--                    :rtl="true"-->
<!--                    :close-on-select="true"-->
<!--                    placeholder="-&#45;&#45; انتخاب کنید -&#45;&#45;"-->
<!--                    :create-option="false"-->
<!--                    :searchable="true"-->
<!--                    :allow-absent="false"-->
<!--                    :resolve-on-load="false"-->
<!--                    :object="true"-->
<!--                    @select="getProperties"-->
<!--                />-->
<!--              </div>-->

              <div>
                <TreeSelect :key="treeSelectKey" @selectItem="selectItem" title="دسته" :multi="false" :categories="allCategories" :clickChild="true" :headCategory="false"/>

              </div>
              <div class="px-2 py-1 text-blue-600 bg-blue-100 rounded" v-if="commission !== null">
                <span class="fm:text-sm">کارمزد این دسته {{commission}}% میباشد</span>
              </div>
            </div>
            <div>
              <label class="fm:text-sm" for="brandId">برند<b class="text-red-500 !font-bold">*</b></label>
              <select v-model="brandId" class="fm:text-sm border border-gray-200 p-2 outline-none rounded-lg w-full">
                <option value="" selected>--- انتخاب کنید ---</option>
                <option v-for="brand in allBrands" :key="brand.id" :value="brand.id">{{brand.name}}</option>
              </select>
            </div>
            <div>
              <label class="fm:text-sm" for="guaranteeId">گارانتی<b class="text-red-500 !font-bold">*</b></label>
              <select v-model="guaranteeId" class="fm:text-sm border border-gray-200 p-2 outline-none rounded-lg w-full">
                <option value="" selected>--- انتخاب کنید ---</option>
                <option v-for="guarantee in allGuarantees" :key="guarantee.id" :value="guarantee.id">{{guarantee.guarantee}}</option>
              </select>
            </div>
            <div>
              <label class="fm:text-sm" for="sellerId"> مدت گارانتی<b class="text-red-500 !font-bold">*</b></label>
              <select v-model="guarantee_month" class="fm:text-sm border border-gray-200 p-2 outline-none rounded-lg w-full">
                <option value="" selected>--- انتخاب کنید ---</option>
                <option value="6">6 ماه</option>
                <option value="12">12 ماه</option>
                <option value="18">18 ماه</option>
                <option value="24">24 ماه</option>
                <option value="1">مادام العمر</option>
                <option value="0">هیچکدام</option>
              </select>
            </div>
          </div>
          <div class="w-full bg-white shadow rounded py-4 px-2 flex flex-col gap-4">
            <div class="flex flex-col gap-2">
              <span class="fd:text-lg !bold-medium">رنگ کالا (اختیاری)</span>
            </div>
            <div>
              <Button text="افزودن رنگ" my_class="!bg-white !border !border-red-400 !text-red-500 !p-2 !text-sm" @click="colorCount++"/>
              <div v-for="count in colorCount" :key="count-1" class="flex gap-2 justify-evenly items-center">
                <div class="w-full flex flex-col">
                  <label class="fm:text-sm" for="is_original">رنگ<b class="text-red-500 !font-bold">*</b></label>
                  <select v-model="productColors[count-1]" class="fm:text-sm border border-gray-200 p-2 outline-none rounded-lg w-full">
                    <option value="">--- انتخاب کنید ---</option>
                    <option v-for="color in allColors" :key="color.id" :value="color.id">{{color.color}}</option>
                  </select>
                </div>
                <div class="w-full">
                  <Input class="" label="قیمت (ریال)" v-model="productPriceColors[count-1]" :id="`color_${count-1}`"/>
                </div>
                <div class="">
                  <span class="cursor-pointer" @click="removeColor(count-1)"><i class="far fa-trash text-sm text-red-500"></i></span>
                </div>
              </div>
            </div>
          </div>
          <div class="w-full bg-white shadow rounded py-4 px-2 flex flex-col gap-4">
            <div class="flex flex-col gap-2">
              <span class="fd:text-lg !bold-medium">اندازه کالا (اختیاری)</span>
            </div>
            <div>
              <Button text="افزودن اندازه" my_class="!bg-white !border !border-red-400 !text-red-500 !p-2 !text-sm" @click="sizeCount++"/>
              <div v-for="count in sizeCount" :key="count-1" class="flex gap-2 justify-evenly items-center">
                <div class="w-full flex flex-col">
                  <label class="fm:text-sm" for="is_original">اندازه<b class="text-red-500 !font-bold">*</b></label>
                  <select v-model="productSizes[count-1]" class="fm:text-sm border border-gray-200 p-2 outline-none rounded-lg w-full">
                    <option value="">--- انتخاب کنید ---</option>
                    <option v-for="sizes in allSizes" :key="sizes.id" :value="sizes.id">{{sizes.size}}</option>
                  </select>
                </div>
                <div class="w-full">
                  <Input class="" label="قیمت (ریال)" v-model="productPriceSizes[count-1]" :id="`color_${count-1}`"/>
                </div>
                <div class="">
                  <span class="cursor-pointer" @click="removeSize(count-1)"><i class="far fa-trash text-sm text-red-500"></i></span>
                </div>
              </div>
            </div>
          </div>
          <div class="w-full bg-white shadow rounded py-4 px-2 flex flex-col gap-4">
            <div class="flex flex-col gap-2">
              <span class="fd:text-lg !bold-medium">ویژگی ها (اختیاری)</span>
              <span class="text-sm">این لیست برای شناخت کالا به خریدار کمک میکند و هچنین در قسمت فیلتر کردن کالا تاثیر مستقیم دارد </span>
            </div>
            <div class="flex justify-between items-center">
              <Button my_class="!bg-white !border !border-red-400 !text-red-500 !p-2 !text-sm" text="اضافه کردن" @click="addProperty()" />
            </div>
            <div class="mt-2 flex gap-2"  v-for="count in propertyCount" :key="count-1">
              <div class="w-[50%]">
                <label class="fm:text-sm" :for="`property_ids_${count-1}`">عنوان<b class="text-red-500 !font-bold">*</b></label>
                <select v-model="propertyIds[count-1]" @change="getPropertyType($event,count-1)" :id="`property_ids_${count-1}`" class="fm:text-sm border border-gray-200 p-2 outline-none rounded-lg w-full">
                  <option value="" selected disabled>--- انتخاب کنید ---</option>
                  <option v-for="property in allProperties" :key="property.value" :value="property.value">{{property.label}}</option>
                </select>
              </div>
              <div class="w-[50%]">
                <label class="fm:text-sm">مقدار</label>
                <Multiselect
                    v-model="propertyTypeIds[count-1]"
                    :options="propertyTypes[count-1]"
                    :rtl="true"
                    mode="tags"
                    :close-on-select="true"
                    placeholder="--- انتخاب کنید ---"
                    :create-option="false"
                    :searchable="true"
                    :allow-absent="false"
                    :resolve-on-load="false"
                    :object="true"
                />
              </div>
              <span class="cursor-pointer" @click="removeProperty(count-1)"><i class="far fa-trash text-red-500 text-sm"></i></span>
            </div>
          </div>
          <div class="w-full bg-white shadow rounded py-4 px-2 flex flex-col gap-4">
            <div class="flex flex-col gap-2">
              <span class="fd:text-lg !bold-medium">مشخصات کلی (اختیاری)</span>
              <span class="text-sm">این لیست در قسمت مشخصات محصول نمایش داده خواهد شد. </span>
            </div>
            <div class="flex justify-between items-center">
              <Button my_class="!bg-white !border !border-red-400 !text-red-500 !p-2 !text-sm" text="اضافه کردن" @click="addSpecification()" />
            </div>
            <div class="mt-2 flex gap-2 items-center"  v-for="count in specificationCount" :key="count-1">
              <Input label="عنوان" v-model="specificationNames[count-1]" :id="`specification_names_${count-1}`" />
              <Input label="توضیح" v-model="specificationDescriptions[count-1]" :id="`specification_description_${count-1}`" />
              <span class="cursor-pointer" @click="removeSpecification(count-1)"><i class="far fa-trash text-red-500 text-sm"></i></span>
            </div>
          </div>
          <div class="w-full bg-white shadow rounded py-4 px-2 flex flex-col gap-4">
            <div class="flex flex-col gap-2">
              <span class="fd:text-lg !bold-medium">تصاویر کالا</span>
              <span class="text-sm text-red-500 !font-medium">فرمت های مجاز: JPG,JPEG,PNG</span>
              <span class="text-sm text-red-500 !font-medium">حداقل حجم مجاز هر تصویر : 5 مگابایت</span>
              <span class="text-sm text-red-500 !font-medium">پس زمینه تصویر شاخص سفید باشد</span>
              <span class="text-sm text-red-500 !font-medium">سایز هر تصویر 600x600 یا 2500x2500</span>
              <span class="text-sm text-red-500 !font-medium">تصویر نباید لوگو نوشته یا واترمارکی داشته باشد</span>
            </div>
            <div class="my-2 flex gap-2 items-center">
              <Input type="file" label="تصویر شاخص" @change="getFile($event, count-1, true)" :key="originalImageKey" :id="`image`" />
              <div v-if="previewOriginalImage">
                <img :src="previewOriginalImage" width="100" height="100" />
              </div>
            </div>
            <Button my_class="!bg-white !border !border-red-400 !text-red-500 !p-2 !text-sm" text="اضافه کردن" @click="addImage()" />
            <div class="mt-2 flex gap-2 items-center" v-for="count in imageCount" :key="count-1">
              <Input type="file" label="تصویر" @change="getFile($event, count-1)" :key="imageKeys[count-1]" :required="false" :id="`images_${count-1}`" />
              <div v-if="previewImages[count-1]">
                <img :src="previewImages[count-1]" width="100" height="100" />
              </div>
              <span class="cursor-pointer" @click="removeImage(count-1)"><i class="far fa-trash text-red-500 text-sm"></i></span>
            </div>
          </div>
          <div class="w-full bg-white shadow rounded py-4 px-2 flex flex-col gap-4">
            <label class="fm:text-sm" for="is_original">کالا اصل میباشد<b class="text-red-500 !font-bold">*</b></label>
            <select v-model="is_original" class="fm:text-sm border border-gray-200 p-2 outline-none rounded-lg w-full">
              <option value="yes">بله</option>
              <option value="no">خیر</option>
            </select>
          </div>
          <div class="w-full bg-white shadow rounded py-4 px-2 flex flex-col gap-4">
            <div>
              <label class="fm:text-sm" for="is_original">به عنوان شگفت انگیز<b class="text-red-500 !font-bold">*</b></label>
              <select v-model="is_amazing_offer" @change="checkAmazingPrice()" class="fm:text-sm border border-gray-200 p-2 outline-none rounded-lg w-full">
                <option value="yes">بله</option>
                <option value="no">خیر</option>
              </select>
            </div>
            <div v-if="is_amazing_offer === 'yes'" class="w-full">
              <div class="mb-5" v-if="!isAdmin">
                <Input v-if="isUpdate && (amazingOfferStatus === 'pending' || amazingOfferStatus === null || amazingOfferStatus === 'no')" type="number" v-model="amazing_offer_percent" label="درصد تخفیف شگفت انگیز" placeholder="بین 0 - 100" alert="محاسبه قیمت براساس( قیمت اصلی - درصد تخفیف ) میباشد"/>
                <Input v-else :disabled="true" type="number" v-model="amazing_offer_percent" label="درصد تخفیف شگفت انگیز" placeholder="بین 0 - 100" alert="محاسبه قیمت براساس( قیمت اصلی - درصد تخفیف ) میباشد"/>
              </div>
              <div class="mb-5" v-else>
                <Input type="number" v-model="amazing_offer_percent" label="درصد تخفیف شگفت انگیز" placeholder="بین 0 - 100" alert="محاسبه قیمت براساس( قیمت اصلی - درصد تخفیف ) میباشد"/>
              </div>
              <div v-if="isUpdate && isAdmin" class="flex gap-2 items-center">
                <label for="amazing_offer_update">
                  به روز رسانی انقضاء
                </label>
                <input type="checkbox" v-model="amazing_offer_update" id="amazing_offer_update" />
              </div>
            </div>
          </div>

          <div class="w-full bg-white shadow rounded py-4 px-2 flex flex-col gap-4" v-if="isAdmin">
            <label class="fm:text-sm" for="publish">وضعیت<b class="text-red-500 !font-bold">*</b></label>
            <select v-model="publish" class="fm:text-sm border border-gray-200 p-2 outline-none rounded-lg w-full">
              <option value="published">انتشار</option>
              <option value="draft">پیشنویس</option>
            </select>
          </div>
        </div>
      </div>
    </Modal>

    <Modal title="ایمپورت کردن کالا" save="ایمپورت"  :btnLoading="importLoading" @callback="importProduct()" ref="importModal">
      <div class="mb-8 flex flex-col gap-8">
        <div class="flex items-center gap-2">
          <span class="fm:text-sm">نمونه فایل:</span>
          <a :href="`${$store.state.url}/product.xlsx`" target="_blank" class="text-blue-400 fm:text-sm">دانلود</a>
        </div>
        <ul class="flex items-center gap-10">
          <li class="flex items-center gap-2 text-green-500">
            <span class="fm:text-sm">موفق:</span>
            <span class="fm:text-sm">{{importSuccess}}</span>
          </li>
          <li class="flex items-center gap-2 text-red-500">
            <span class="fm:text-sm">خطا:</span>
            <span class="fm:text-sm">{{importError}}</span>
          </li>
        </ul>
      </div>
      <Input :key="importFileKey" type="file" label="انتخاب فایل" @change="getImportFile($event)" id="importFile" alert="فرمت مجاز: xlsx,xls" />
    </Modal>
    <Meta :title="$store.state.siteName + ` | لیست ویژگی ها `"/>
    <Loading :loading="loading" />
    <ValidationError />
  </div>
</template>

<script>
export default{name: "Product"}
</script>

<script setup>
import {ref, onMounted, defineExpose} from 'vue';
import Meta from "@/components/Meta.vue";
import Td from "@/components/Td.vue";
import Thead from "@/components/Thead.vue";
import UserCanAction from "@/components/UserCanAction.vue";
import Input from "@/components/Input.vue";
import Editor from "@/components/Editor.vue";
import Modal from "@/components/Modal.vue";
import Loading from "@/components/Loading.vue";
import Button from "@/components/Button.vue";
import ValidationError from "@/components/ValidationError.vue";
import Paginate from "@/components/Paginate.vue";
import Textarea from "@/components/Textarea.vue";
import TreeSelect from "@/components/TreeSelect.vue";
import Notification from "@/components/Notification.vue";
import axios from "@/plugins/axios";
import store from "@/store";
import Toast from "@/plugins/toast";
import Multiselect from '@vueform/multiselect'


let isAdmin = ref(store.state.user.access === 'admin')
let refresh = ref(false)
let btnLoading = ref(false)
let loading = ref(false)
let isUpdate = ref(false)
let postId = ref(null)
let model = ref('product')
let search = ref('')
let importModal = ref(null);
let importLoading = ref(false);
let importFile = ref('');
let importFileKey = ref(0);
let importSuccess = ref(0);
let importError = ref(0);

let sellerId = ref('')
let brandId = ref('')
let categoryId = ref('')
let guaranteeId = ref('')
let ir_name = ref('')
let en_name = ref('')
let sku = ref('')
let price = ref('')
let is_amazing_offer = ref('no')
let amazing_offer_percent = ref('')
let amazingOfferStatus = ref('')
let amazing_price = ref('')
let packing_length = ref('')
let packing_width = ref('')
let packing_height = ref('')
let packing_weight = ref('')
let physical_length = ref('')
let physical_width = ref('')
let physical_height = ref('')
let physical_weight = ref('')
let publish = ref('published')
let count = ref('')
let guarantee_month = ref('18')
let is_original = ref('yes')
let more_description = ref('')
let description = ref('')
let weakPoints = ref([])
let strengths = ref([])
let properties = ref([])
let allProperties = ref([])

let propertyCount = ref(0);
let propertyIds = ref([]);
let propertyTypeIds = ref([]);
let propertyTypes = ref([]);

let specificationNames = ref([])
let specificationDescriptions = ref([])
let specificationPropertyTypes = ref([])
let specificationCount = ref(0)
let colorCount = ref(0)
let productColors = ref([])
let productPriceColors = ref([])
let sizeCount = ref(0)
let productSizes = ref([])
let productPriceSizes = ref([])
let imageCount = ref(0);
let images = ref([]);
let previewImages = ref([]);
let imageKeys = ref([]);
let originalImage = ref('');
let previewOriginalImage = ref(null);
let originalImageKey = ref(0);
let commission = ref(null)
let treeSelectKey = ref(0)
let propertyTypeCount = ref([])
let allPropertyTypes = ref([])
let imageList = ref([])
let amazing_offer_update = ref(false)

let allData = ref([])
let allUsers = ref([])
let allBrands = ref([])
let allCategories = ref([])
let allGuarantees = ref([])
let allSizes = ref([])
let allColors = ref([])
let allNotifications = ref([])
let openModal = ref(null)
let amazingOfferDay = ref('')
let editorKey = ref(0)
onMounted(async()=>{
  await getData();
});

defineExpose({getData});

async function getData(_loading = true,page=1, isRefresh=false){
  if(isRefresh) search.value = '';
  refresh.value = true;
  loading.value = _loading;
  await axios.get(`${store.state.api}${model.value}?page=${page}&search=${search.value}`).then(resp=>{
    let data = resp.data;
    allData.value = data.data;
    allUsers.value = data.users;
    allBrands.value = data.brands;
    allCategories.value = data.categories;
    allGuarantees.value = data.guarantees;
    allColors.value = data.colors;
    allSizes.value = data.sizes;
    allNotifications.value = data.notifications;
    amazingOfferDay.value = data.amazing_offer_day
    store.commit('paginate',data.meta);
  }).catch(err=>{
    store.commit('handleError',err)
  })
  loading.value = refresh.value = false;
}

function removeSize(index){
  productSizes.value = productSizes.value.filter((i,k)=>k !== index)
  productPriceSizes.value = productPriceSizes.value.filter((i,k)=>k !== index)
  sizeCount.value --;
}

function removeColor (index) {
  productColors.value = productColors.value.filter((i,k)=>k !== index)
  productPriceColors.value = productPriceColors.value.filter((i,k)=>k !== index)
  colorCount.value --;
}


async function getPropertyType(event, index, _loading=true){
  loading.value = _loading;
  let propertyId = event.target.value;
  propertyTypes.value[index] = [];
  await  axios.get(`${store.state.api}${model.value}/get-property-type/${propertyId}`).then(resp=>{
    propertyTypes.value[index] = resp.data.data.map(item=>{
      return {value:item.id, label:item.name}
    });
  }).catch(err=>{
    Toast.error('دریافت نوع های ویژگی با خطا مواجه شد')
  })
  loading.value = false;
}

function addProperty(){
  propertyCount.value ++;
}

function removeProperty(index){
  propertyIds.value = propertyIds.value.filter((v,k)=>k !== index)
  delete propertyTypes.value[index]
  propertyTypeIds.value = propertyTypeIds.value.filter((v,k)=>k !== index)
  propertyCount.value --;
}

async function selectItem(data){
  commission.value = data[2];
  categoryId.value = store.state.selectIds[0];
  if(categoryId.value){
    await getProperties();
  }
}

async function getProperties(_loading=true){
  loading.value = _loading;
  propertyTypeCount.value = [];
  allPropertyTypes.value = [];
  allPropertyTypes.value = [];
  properties.value = [];
  await  axios.get(`${store.state.api}${model.value}/get-property/${categoryId.value}`).then(resp=>{
    // let data = resp.data.data.reduce((r, a) => r.concat(a), []);
    allProperties.value = resp.data.data.map((item)=>{
      return {value:item.property_id, label:item.property.property}
    });
    allProperties.value = allProperties.value.filter((a, i) => allProperties.value.findIndex((s) => a.value === s.value) === i)
  }).catch(err=>{
    Toast.error('دریافت ویژگی ها با خطا مواجه شد')
  })
  loading.value = false;
}


function searchData(text){
  search.value = text
  getData(false)
}

function showImport(){
  importFile.value = '';
  importLoading.value = false;
  importSuccess.value = 0;
  importError.value = 0;
  importModal.value.toggleModal();
}

async function importProduct(){
  importLoading.value = true;
  let frm = new FormData();
  frm.append('import_file',importFile.value);
  await axios.post(`${store.state.api}${model.value}/import`,frm).then(resp=>{
    let data = resp.data.data;
    importSuccess.value = data.success;
    importError.value = data.error;
    importFile.value = '';
  }).catch(err=>{
    store.commit('handleError',err)
  })
  importLoading.value = false;
}
function create(){
  clearData();
}

async function checkAmazingPrice(){
  loading.value = true;
  await axios.get(`${store.state.api}${model.value}/check-amazing-offer`).then(resp=>{
    if(!resp.data.add_amazing){
      Toast.warning(`برای ثبت در شگفت انگیز باید ${amazingOfferDay.value} روز از تایید پیشنهاد شگفت‌انگیز قبلی بگذرد`,10000);
      is_amazing_offer.value = 'no';
      amazing_offer_percent.value = '';
      amazing_offer_update.value = false;
    }
  })
  loading.value = false;
}

async function insert(){
  btnLoading.value = true;

  await axios.post(`${store.state.api}${model.value}`,formData()).then(resp=>{
    clearData();
    Toast.success();
    getData(false, store.state.current)
  }).catch(err=>{
    store.commit('handleError',err)
  })
  btnLoading.value = false;
}

async function show(_postId){
  clearData();
  postId.value = _postId;
  isUpdate.value = loading.value = true;
  await axios.get(`${store.state.api}${model.value}/${postId.value}`).then(async resp=>{
    let data = resp.data.data;
    sellerId.value = data.user_id
    brandId.value = data.brand_id
    categoryId.value = data.category_id
    commission.value = data.category.commission;
    guaranteeId.value = data.guarantee_id
    ir_name.value = data.ir_name
    en_name.value = data.en_name === null ? '' : data.en_name
    sku.value = data.sku
    guarantee_month.value = data.guarantee_month
    previewOriginalImage.value = store.state.url + data.image
    price.value = data.price
    amazing_price.value = data.amazing_price === null ? '' : data.amazing_price;
    packing_length.value = data.packing_length
    packing_width.value = data.packing_width
    packing_height.value = data.packing_height
    packing_weight.value = data.packing_weight
    physical_length.value = data.physical_length
    physical_width.value = data.physical_width
    physical_height.value = data.physical_height
    physical_weight.value = data.physical_weight
    if(isAdmin.value){
      publish.value = data.publish
    }
    count.value = data.count
    is_original.value = data.is_original
    more_description.value = data.more_description === null ? '' : data.more_description
    description.value = data.description
    weakPoints.value = JSON.parse(data.weak_points)
    strengths.value = JSON.parse(data.strengths)
    amazingOfferStatus.value = data.amazing_offer_status
    amazing_offer_percent.value = data.amazing_offer_percent === null ? '' : data.amazing_offer_percent;
    if(['yes','pending'].includes(data.amazing_offer_status)){
      is_amazing_offer.value = 'yes'
    }

    await getProperties(true)

    colorCount.value = data.product_colors.length;
    data.product_colors.map(item=>{
      productColors.value.push(item.color_id);
      productPriceColors.value.push(item.price);
    })

    sizeCount.value = data.product_sizes.length;
    data.product_sizes.map(item=>{
      productSizes.value.push(item.size_id);
      productPriceSizes.value.push(item.price);
    })

    for(let i = 0; i < data.product_properties.length; i++){
      let property = data.product_properties[i];
      propertyTypeIds.value[i] = [];
      if(propertyIds.value.filter(item=>item === property.property_id).length <= 0){
        propertyIds.value.push(property.property_id)
        propertyCount.value++
      }
    }


    // Set format values `propertyTypeIds` fro `vueSelect`
    for(let i = 0; i < propertyIds.value.length; i++){
      await getPropertyType({target:{value:propertyIds.value[i]}},i,true)
      let properties = data.product_properties.filter(item=>item.property_id === propertyIds.value[i]);
      if(properties.length > 0){
        propertyTypeIds.value[i] = properties.map(property=>{
          return {value:property.property_type.id, label:property.property_type.name}
        })
      }
    }
    propertyTypeIds.value = propertyTypeIds.value.filter(item=>item.length > 0);


    imageCount.value = data.product_images.length;
    for(let i = 0; i < data.product_images.length; i++){
      previewImages.value.push(store.state.url + data.product_images[i].image)
      imageKeys.value[i] = 0;
    }
    imageList.value = data.product_images;


    specificationCount.value = data.product_specifications.length;
    data.product_specifications.map(item=>{
       specificationNames.value.push(item.name)
       specificationDescriptions.value.push(item.description)
    })

    store.state.selectIds = [parseInt(data.category_id)];
    treeSelectKey.value ++;

  }).catch(err=>{
    store.commit('handleError',err)
  })

  loading.value = false;
}

async function update(){
  isUpdate.value = btnLoading.value =  true;
  await axios.post(`${store.state.api}${model.value}/${postId.value}`,formData(true)).then(resp=>{
    clearData();
    Toast.success("تغییرات با موفقیت ثبت شد");
    getData(false,store.state.current)
  }).catch(err=>{
    store.commit('handleError',err)
  })
  btnLoading.value =  false;
}

async function destroy(postId){
  await store.dispatch('deleteRecord',[`${model.value}/${postId}`])
  await getData(false,store.state.current)

}

async function destroyImage(postId){
  await store.dispatch('deleteRecord',[`${model.value}/delete-image/${postId}`])
}


function formData(isUpdate=false){
  let frm = new FormData();
  let _propertyTypeIds = [];
  for(let i = 0; i < propertyTypeIds.value.length; i++)
  {
    _propertyTypeIds[i] = [];
    let propertyType = propertyTypeIds.value[i];
    for(let j = 0; j < propertyType.length; j++)
    {
      _propertyTypeIds[i].push(propertyType[j]['value']);
    }
  }

  frm.append('seller_id',sellerId.value);
  frm.append('brand_id',brandId.value);
  frm.append('category_id',categoryId.value ?? '');
  frm.append('guarantee_id',guaranteeId.value);
  frm.append('ir_name',ir_name.value);
  frm.append('en_name',en_name.value);
  frm.append('sku',sku.value);
  frm.append('guarantee_month',guarantee_month.value);
  frm.append('amazing_offer_percent',amazing_offer_percent.value);
  frm.append('is_amazing_offer',is_amazing_offer.value);
  frm.append('amazing_offer_update',amazing_offer_update.value);

  frm.append('price',price.value);
  frm.append('amazing_price',amazing_price.value);
  frm.append('packing_length',packing_length.value);
  frm.append('packing_width',packing_width.value);
  frm.append('packing_height',packing_height.value);
  frm.append('packing_weight',packing_weight.value);
  frm.append('physical_length',physical_length.value);
  frm.append('physical_width',physical_width.value);
  frm.append('physical_height',physical_height.value);
  frm.append('physical_weight',physical_weight.value);
  frm.append('publish',publish.value);
  frm.append('count',count.value);
  frm.append('is_original',is_original.value);
  frm.append('is_original',is_original.value);
  frm.append('more_description',more_description.value);
  frm.append('description',description.value);
  frm.append('image',originalImage.value);
  frm.append('weak_points',JSON.stringify(weakPoints.value));
  frm.append('strengths',JSON.stringify(strengths.value));
  frm.append('color_ids',JSON.stringify(productColors.value));
  frm.append('colors_price',JSON.stringify(productPriceColors.value));
  frm.append('size_ids',JSON.stringify(productSizes.value));
  frm.append('sizes_price',JSON.stringify(productPriceSizes.value));
  frm.append('property_ids',JSON.stringify(propertyIds.value));
  frm.append('property_types',JSON.stringify(_propertyTypeIds));
  frm.append('specification_names',JSON.stringify(specificationNames.value));
  frm.append('specification_description',JSON.stringify(specificationDescriptions.value));
  if(images.value.length > 0){
    images.value.forEach( img => {
      frm.append('images[]', img);
    })
  }else{
    frm.append('images[]', []);
  }

  if(isUpdate){
    frm.append('_method','PATCH')
  }
  return frm;
}

function clearData(){

  editorKey.value ++;
  sellerId.value = ''
  brandId.value = ''
  categoryId.value = {}
  guaranteeId.value = ''
  ir_name.value = ''
  en_name.value = ''
  sku.value = ''
  price.value = ''
  guarantee_month.value = '18'
  is_amazing_offer.value = 'no'
  amazing_offer_percent.value = ''
  amazing_offer_update.value = false;
  amazing_price.value = ''
  packing_length.value = ''
  packing_width.value = ''
  packing_height.value = ''
  packing_weight.value = ''
  physical_length.value = ''
  physical_width.value = ''
  physical_height.value = ''
  physical_weight.value = ''
  publish.value = 'published'
  count.value = ''
  is_original.value = 'yes'
  more_description.value = ''
  description.value = ''
  weakPoints.value = []
  strengths.value = []
  allProperties.value = []
  properties.value = []
  propertyTypeCount.value = []
  allPropertyTypes.value = []
  specificationNames.value = []
  specificationDescriptions.value = []
  specificationPropertyTypes.value = [];
  specificationCount.value = 0
  colorCount.value = 0
  productColors.value = []
  productPriceColors.value = []
  sizeCount.value = 0
  productSizes.value = []
  productPriceSizes.value = []
  imageCount.value = 0;
  images.value = [];
  previewImages.value = [];
  imageKeys.value = [];
  commission.value = null
  treeSelectKey.value ++;
  isUpdate.value = false;
  postId.value = null;
  originalImage.value = '';
  previewOriginalImage.value = null;
  propertyCount.value = 0;
  propertyIds.value = [];
  propertyTypeIds.value = [];
  propertyTypes.value = [];
  originalImageKey.value ++;
  store.state.selectIds = [];
  openModal.value.toggleModal();
}


function addSpecification(){
  specificationCount.value ++;
}

function removeSpecification(index){
  specificationDescriptions.value = specificationDescriptions.value.filter((v,k)=>k !== index)
  specificationNames.value = specificationNames.value.filter((v,k)=>k !== index)
  specificationCount.value --;
}


function addImage(){
  imageCount.value ++;
}

async function removeImage(index){
  let img = imageList.value[index];
  if(img){
    await destroyImage(img.id)
  }else{
    images.value = images.value.filter((v,k)=>k !== index)
  }
  previewImages.value = previewImages.value.filter((v,k)=>k !== index)
  imageKeys.value = imageKeys.value.filter((v,k)=>k !== index)
  imageCount.value --;
}

function addStrengths(event){
  event.target.value.length > 0 ? strengths.value.push(event.target.value) : ''
  event.target.value = null
}

function removeStrengths(index){
  strengths.value = strengths.value.filter((v,k)=>k !== index)
}

function addWeakPoints(event){
  event.target.value.length > 0 ? weakPoints.value.push(event.target.value) : '';
  event.target.value = null
}

function removeWeakPoints(index){
  weakPoints.value = weakPoints.value.filter((v,k)=>k !== index)
}


function getFile(event, index, isOriginalImage=false){
  if(isOriginalImage){
    originalImage.value = event.target.files[0];
    previewOriginalImage.value = URL.createObjectURL(originalImage.value);
    originalImageKey.value ++;
  }else{
    images.value[index] = event.target.files[0];
    previewImages.value[index] = URL.createObjectURL(images.value[index]);
    imageKeys.value[index]++;
  }
}

function getImportFile(event){
  importFile.value = event.target.files[0];
  importFileKey.value ++;
}

</script>
<style src="@vueform/multiselect/themes/default.css"></style>