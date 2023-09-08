<template>
  <div class="mt-10 px-8">
    <div class="flex justify-center">
      <span class="!font-medium text-lg">لیست ویجت‌ها</span>
    </div>
    <div class="mt-10 px-3 mb-3">
      <div class="flex gap-3 fm:flex-col gap-3">
        <div class="fd:w-[30%] h-full flex flex-col gap-4" >
          <div v-for="item in dragItems" :key="item.id" class="mt-5 flex items-center gap-1">
            <div class="w-full"><img :src="require(`../../assets/images/widgets/`+item.image)" class="w-full h-[50px]" /></div>
            <span class="cursor-pointer" @click="dragItem(item)"><i class="far fa-plus text-lg fm:text-sm text-green-500"></i></span>
          </div>
        </div>
        <div class="fd:w-[70%] flex flex-col justify-between gap-4">
          <div v-for="item in dropItems" :key="item.id" class="mt-5 p-2 bg-gray-50 p-2 rounded-lg shadow-sm">
            <div class="relative flex gap-2 flex-col">
              <div class="flex justify-end">
                <span class="cursor-pointer" @click="removeItem(item.id)"><i class="far fa-trash text-red-500 fm:text-sm" ></i></span>
              </div>
              <img :src="require(`../../assets/images/widgets/`+item.image)" class="w-full" @click="toggleOption(item)"/>
              <div v-if="item.name === 'amazing_supermarket' && item.showOption && item.id === currentToggleId">
                <div class="flex fm:flex-col justify-between gap-2">
                  <div class="flex flex-col gap-1">
                    <Input @change="getFile($event, item.id, 'basket_image','basket_key',['svg','jpg','jpeg','png','gif','webp'], $store.state.oneMgByte)" :key="item.basket_key" type="file" label="تصویر سبد" :alert="`فرمت مجار: svg,jpg,jpeg,png,gif,webp | حداکثر حجم: ${$store.state.imageSize} | سایز: 66x64px`"/>
                    <img v-if="item.basket_image" :src="item.basket_image" class="w-[100px] h-[100px] fm:h-[50px] fm:w-[50px]" />
                  </div>
                  <div class="flex flex-col gap-1">
                    <Input @change="getFile($event, item.id, 'amazing_image', 'amazing_key',['svg','jpg','jpeg','png','gif','webp'], $store.state.oneMgByte)" :key="item.amazing_key" type="file" label="تصویر شگفت انگیز سوپر مارکتی" :id="`amazing_image_${item.id}`" :alert="`فرمت مجار: svg,jpg,jpeg,png,gif,webp | حداکثر حجم: ${$store.state.imageSize} | سایز: 273x28px`" />
                    <img v-if="item.amazing_image" :src="item.amazing_image" class="w-[100px] h-[100px] fm:h-[50px] fm:w-[50px]" />
                  </div>
                </div>
                <div class="w-full">
                  <Input type="color" v-model="item.background" label="رنگ پس زمینه" />
                </div>
                <div class="flex fm:flex-col justify-between items-center gap-2 mt-5">
                  <div class="w-[32%] fm:w-[100%]">
                    <Input v-model="item.discount_text" label="تخفیف تا 60 درصد" placeholder="تخفیف تا 60 درصد" />
                  </div>
                  <div class="w-[35%] fm:w-[100%]">
                    <Input type="color" v-model="item.discount_bg" label="رنگ پس زمینه تخفیف تا 60 درصد" />
                  </div>
                  <div class="w-[32%] fm:w-[100%]">
                    <Input type="color" v-model="item.discount_color" label="رنگ متن تخفیف تا 60 درصد" />
                  </div>
               </div>
                <div class="flex fm:flex-col justify-between items-center gap-2 mt-5">
                  <div class="w-[50%] fm:w-[100%]">
                    <Input type="color" v-model="item.product_bg" label="رنگ پس زمینه بیش از 100 کالا" />
                  </div>
                  <div class="w-[50%] fm:w-[100%]">
                    <Input type="color" v-model="item.product_color" label="رنگ متن بیش از 100 کالا" />
                  </div>
                </div>
                <div class="flex fd:justify-between fm:flex-col gap-2 mt-5">
                  <div class="fm:mt-3 w-[50%] fm:w-[100%]">
                    <label class="fm:text-sm">دسته</label>
                    <Multiselect
                        v-model="item.category_id"
                        :options="allCategoryHaveProducts"
                        :rtl="true"
                        :close-on-select="true"
                        placeholder="--- انتخاب کنید ---"
                        :searchable="true"
                        :allow-absent="true"
                        :resolve-on-load="false"
                        :object="true"
                        @select="getProducts"
                        @deselect="deselectProduct($event, item)"
                    />
                  </div>
                  <div class="fm:mt-3 w-[50%] fm:w-[100%]">
                    <label class="fm:text-sm">کالاها</label>
                    <Multiselect
                        v-model="item.product_ids"
                        :options="products"
                        :rtl="true"
                        mode="tags"
                        :close-on-select="false"
                        placeholder="--- انتخاب کنید ---"
                        :searchable="true"
                        :allow-absent="true"
                        :resolve-on-load="false"
                        :object="true"
                        :max="5"
                    />
                  </div>
                </div>

              </div>
              <div v-else-if="item.name === 'amazing_offer' && item.showOption && item.id === currentToggleId">
                <div class="flex justify-between fm:flex-col gap-2">
                  <div class="flex flex-col gap-1">
                    <Input @change="getFile($event, item.id, 'amazing_image','amazing_key',['svg','jpg','jpeg','png','gif','webp'], $store.state.oneMgByte)" :key="item.amazing_key" type="file" label="تصویر پشنهاد شگغت انگیز" :alert="`فرمت مجار: svg,jpg,jpeg,png,gif,webp | حداکثر حجم: ${$store.state.imageSize} | سایز: 120x120px`"/>
                    <img v-if="item.amazing_image" :src="item.amazing_image" class="w-[100px] h-[100px] fm:w-[50px] fm:h-[50px]" />
                  </div>
                  <div class="flex flex-col gap-1">
                    <Input @change="getFile($event, item.id, 'box_image', 'box_key',['svg','jpg','jpeg','png','gif','webp'], $store.state.oneMgByte)" :key="item.box_key" type="file" label="تصویر باکس" :id="`box_image_${item.id}`" :alert="`فرمت مجار: svg,jpg,jpeg,png,gif,webp | حداکثر حجم: ${$store.state.imageSize} | سایز: 165x150px`" />
                    <img v-if="item.box_image" :src="item.box_image" class="w-[100px] h-[100px] fm:w-[50px] fm:h-[50px]" />
                  </div>
                </div>
                <div class="flex fd:justify-between fm:flex-col gap-2 mt-5">
                  <div class="w-[50%] fm:w-[100%]">
                    <Input type="color" v-model="item.box_bg" label="رنگ پس زمینه تخفیف تا 60 درصد" />
                  </div>
                  <div class="w-[50%] fm:w-[100%]">
                    <label class="fm:text-sm">کالاها</label>
                    <Multiselect
                        v-model="item.product_ids"
                        :options="allProducts"
                        :rtl="true"
                        mode="tags"
                        :close-on-select="false"
                        placeholder="--- انتخاب کنید ---"
                        :searchable="true"
                        :allow-absent="true"
                        :resolve-on-load="false"
                        :object="true"
                    />
                  </div>
                </div>
              </div>
              <div v-else-if="item.name === 'category' && item.showOption && item.id === currentToggleId">
                <div class="flex fm:flex-col justify-between gap-2">
                  <div class="w-[50%] fm:w-[100%]">
                    <Input @change="getFile($event, item.id, 'image_one','key_one',['svg','jpg','jpeg','png','gif','webp'], $store.state.oneMgByte)" :key="item.first_key" type="file" label="تصویر اول" :id="`image_one_${item.id}`" :alert="`فرمت مجار: svg,jpg,jpeg,png,gif,webp | حداکثر حجم: ${$store.state.imageSize} | سایز: 247x617px`" />
                    <img v-if="item.image_one" :src="item.image_one" class="w-[100px] h-[100px]  fm:w-[50px] fm:h-[50px]" />
                  </div>
                  <div class="w-[50%] fm:w-[100%]">
                    <Input v-model="item.link_one" label="لینک" :id="`link_one_${item.id}`"/>
                  </div>
                </div>
                <div class="flex fm:flex-col justify-between gap-2">
                  <div class="w-[50%] fm:w-[100%]">
                    <Input @change="getFile($event, item.id, 'image_two','key_two',['svg','jpg','jpeg','png','gif','webp'], $store.state.oneMgByte)" :key="item.first_key" type="file" label="تصویر دوم" :id="`image_two_${item.id}`" :alert="`فرمت مجار: svg,jpg,jpeg,png,gif,webp | حداکثر حجم: ${$store.state.imageSize} | سایز: 247x617px`" />
                    <img v-if="item.image_two" :src="item.image_two" class="w-[100px] h-[100px]  fm:w-[50px] fm:h-[50px]" />
                  </div>
                  <div class="w-[50%] fm:w-[100%]">
                    <Input v-model="item.link_two" label="لینک" :id="`first_link_${item.id}`"/>
                  </div>
                </div>
                <div class="flex fm:flex-col justify-between gap-2">
                  <div class="w-[50%] fm:w-[100%]">
                    <Input @change="getFile($event, item.id, 'image_three','key_three',['svg','jpg','jpeg','png','gif','webp'], $store.state.oneMgByte)" :key="item.first_key" type="file" label="تصویر سوم" :id="`image_three_${item.id}`" :alert="`فرمت مجار: svg,jpg,jpeg,png,gif,webp | حداکثر حجم: ${$store.state.imageSize} | سایز: 247x617px`" />
                    <img v-if="item.image_three" :src="item.image_three" class="w-[100px] h-[100px]  fm:w-[50px] fm:h-[50px]" />
                  </div>
                  <div class="w-[50%] fm:w-[100%]">
                    <Input v-model="item.link_three" label="لینک" :id="`first_link_${item.id}`"/>
                  </div>
                </div>
                <div class="flex fm:flex-col justify-between gap-2">
                  <div class="w-[50%] fm:w-[100%]">
                    <Input @change="getFile($event, item.id, 'image_four','key_four',['svg','jpg','jpeg','png','gif','webp'], $store.state.oneMgByte)" :key="item.first_key" type="file" label="تصویر چهارم" :id="`image_four_${item.id}`" :alert="`فرمت مجار: svg,jpg,jpeg,png,gif,webp | حداکثر حجم: ${$store.state.imageSize} | سایز: 247x617px`" />
                    <img v-if="item.image_four" :src="item.image_four" class="w-[100px] h-[100px]  fm:w-[50px] fm:h-[50px]" />
                  </div>
                  <div class="w-[50%] fm:w-[100%]">
                    <Input v-model="item.link_four" label="لینک" :id="`first_link_${item.id}`"/>
                  </div>
                </div>
              </div>
              <div v-else-if="item.name === 'shop_category' && item.showOption && item.id === currentToggleId">
                <div class="flex fm:flex-col justify-between gap-2">
                  <div class="w-[50%] fm:w-[100%]">
                    <Input v-model="item.title" label="عنوان" :id="`title_${item.id}`"/>
                  </div>
                  <div class="w-[50%] fm:w-[100%]">
                    <label class="fm:text-sm">دسته ها</label>
                    <Multiselect
                        class="multiselect-tags"
                        v-model="item.category_ids"
                        :options="allParents"
                        :rtl="true"
                        mode="tags"
                        :close-on-select="false"
                        placeholder="--- انتخاب کنید ---"
                        :searchable="true"
                        :allow-absent="true"
                        :resolve-on-load="false"
                        :object="true"
                        :max="12"
                    />
                  </div>
                </div>
              </div>
              <div v-else-if="item.name === 'banner' && item.showOption && item.id === currentToggleId">
                <div class="flex flex-col gap-2">
                  <div class="flex fm:flex-col justify-between gap-2">
                    <div class="w-[50%] fm:w-[100%]">
                      <Input @change="getFile($event, item.id, 'first_image','first_key',['svg','jpg','jpeg','png','gif','webp'], $store.state.oneMgByte)" :key="item.first_key" type="file" label="تصویر سمت راست" :id="`first_image_${item.id}`" :alert="`فرمت مجار: svg,jpg,jpeg,png,gif,webp | حداکثر حجم: ${$store.state.imageSize} | سایز: 247x617px`" />
                      <img v-if="item.first_image" :src="item.first_image" class="w-[100px] h-[100px]  fm:w-[50px] fm:h-[50px]" />
                    </div>
                    <div class="w-[50%] fm:w-[100%]">
                      <Input v-model="item.first_link" label="لینک" :id="`first_link_${item.id}`"/>
                    </div>
                  </div>
                  <div class="flex fm:flex-col justify-between gap-2 mt-5">
                    <div class="w-[50%] fm:w-[100%]">
                      <Input @change="getFile($event, item.id, 'second_image', 'second_key',['svg','jpg','jpeg','png','gif','webp'], $store.state.oneMgByte)" :key="item.second_key" type="file" label="تصویر سمت جپ" :id="`second_link_${item.id}`" :alert="`فرمت مجار: svg,jpg,jpeg,png,gif,webp | حداکثر حجم: ${$store.state.imageSize} | سایز: 247x617px`"/>
                      <img v-if="item.second_image" :src="item.second_image" class="w-[100px] h-[100px]  fm:w-[50px] fm:h-[50px]" />
                    </div>
                    <div class="w-[50%] fm:w-[100%]">
                      <Input v-model="item.second_link" label="لینک" :id="`second_link_${item.id}`"/>
                    </div>
                  </div>
                </div>
              </div>
              <div v-else-if="item.name === 'brand' && item.showOption && item.id === currentToggleId">
                <div>
                  <label class="fm:text-sm">ّبرند ها</label>
                  <Multiselect
                      v-model="item.brand_ids"
                      :options="allBrands"
                      :rtl="true"
                      mode="tags"
                      :close-on-select="false"
                      placeholder="--- انتخاب کنید ---"
                      :searchable="true"
                      :allow-absent="true"
                      :resolve-on-load="false"
                      :object="true"
                  />
                </div>
              </div>
            </div>
          </div>
          <Button v-if="dropItems.length > 0 && $store.getters.userCan('create_widgets')" :btnLoading="btnLoading" @click="update()" text="ذخیره" my_class="w-full" />
          <Button v-else text="ذخیره" my_class="!w-full !bg-black"/>
        </div>
      </div>
    </div>
    <Meta :title="$store.state.siteName + ` | لیست ویجت‌ها `"/>
    <Loading :loading="loading" />
    <ValidationError />
  </div>
</template>

<script>
export default{name: "Widget"}
</script>

<script setup>
import {ref, onMounted} from 'vue';
import Meta from "@/components/Meta.vue";
import Input from "@/components/Input.vue";
import Loading from "@/components/Loading.vue";
import Button from "@/components/Button.vue";
import ValidationError from "@/components/ValidationError.vue";
import axios from "@/plugins/axios";
import store from "@/store";
import Toast from "@/plugins/toast";
import Multiselect from '@vueform/multiselect'


let btnLoading = ref(false)
let loading = ref(false)
let model = ref('widget')
let products = ref([])
let allProducts = ref([])
let allParents = ref([])
let allCategoryHaveProducts = ref([])
let allBrands = ref([])
let dropItems = ref([])
let currentToggleId = ref(0)

let dragItems = ref([
  {tab:1, id:0,
    image:'amazing_supermarket.png',
    name:'amazing_supermarket',
    showOption:false,
    basket_image:'',
    basket_key:0,
    amazing_image:'',
    amazing_key:0,
    discount_text:'',
    discount_bg:'',
    discount_color:'',
    product_color:'',
    background:'',
    product_bg:'',
    product_ids:[],
    category_id:[]
  },

  {tab:2, id:0,
    image:'amazing_offer.png',
    name:'amazing_offer',
    showOption:false,
    box_bg:'',
    product_ids:[],
    amazing_image:'',
    amazing_key:0,
    box_image:'',
    box_preview:'',
    box_key:0,
  },
  {tab:3, id:0,
    image:'category.png',
    name:'category',
    showOption:false,
    image_one:'',
    key_one:'',
    link_one:'',
    image_two:'',
    key_two:'',
    link_two:'',
    image_three:'',
    key_three:'',
    link_three:'',
    image_four:'',
    key_four:'',
    link_four:''
  },
  {tab:4, id:0,
    image:'shop_category.png',
    name:'shop_category',
    showOption:false,
    category_ids: [],
    title: '',
  },
  {tab:5, id:0,
    image:'banner.png',
    name:'banner',
    showOption:false,
    first_image:'',
    first_link:'',
    first_key:'',
    second_image:'',
    second_link:'',
    second_key:'',
  },
  {tab:7, id:0,
    image:'brand.png',
    name:'brand',
    showOption:false,
    brand_ids:[]
  },
])

onMounted(async()=>{
  await show();
});

async function getProducts(data){
  loading.value = true;
  await axios.get(`${store.state.api}widget/get-product/${data.value}`).then(resp=>{
    products.value = resp.data.data.map(item=>{
      return {value:item.id, label:item.ir_name}
    })
  })
  loading.value = false;
}

function deselectProduct(data, item){
  const objectIndex = dropItems.value.findIndex((_item)=>_item.id === item.id);
  dropItems.value[objectIndex].product_ids = []
}

async function show(_loading = true){
  clearData();
  loading.value = _loading;
  await axios.get(`${store.state.api}${model.value}`).then(async resp=>{
    let data = resp.data.data;
    allProducts.value = data.products.map(item=>{
      return {value:item.id, label:item.ir_name}
    });
    allCategoryHaveProducts.value = data.category_have_products.map(item=>{
      return {value:item.id, label:item.title}
    });
    allParents.value = data.parents.map(item=>{
      return {value:item.id, label:item.title}
    });

    allBrands.value = data.brands.map(item=>{
      return {value:item.id, label:item.name}
    });
    if(data.widgets){
      dropItems.value = JSON.parse(data.widgets.data);
    }
  }).catch(err=>{
    store.commit('handleError',err)
  })
  loading.value = false;
}

async function update(){
  loading.value = btnLoading.value =  true;
  await axios.post(`${store.state.api}${model.value}`,formData()).then(async resp=>{
    clearData();
    Toast.success("تغییرات با موفقیت ثبت شد");
    await show(false);
  }).catch(err=>{
    store.commit('handleError',err)
  })
  loading.value = btnLoading.value =  false;
}

function dragItem(item){
  let _item = dropItems.value[dropItems.value.length - 1];
  item.id = _item !== undefined ? _item.id+1 : item.id;
  dropItems.value.push({...item})
}

function removeItem(id){
  dropItems.value = dropItems.value.filter((item)=>item.id !== id);

}

function toggleOption(item){
  const objectIndex = dropItems.value.findIndex((_item)=>_item.id === item.id);
  currentToggleId.value = item.id;
  dropItems.value[objectIndex].showOption = !item.showOption
}


function formData(){
  return {
    'data':dropItems.value,
    'page':'root'
  };
}

function clearData(){
  dropItems.value = [];
}


async function getFile(event, id, imageKey, changeKey, imageMimes, imageSize){
  const image = event.target.files[0];
  const imageType = image.type.replace('image/','').replace('+xml','');
  const objectIndex = dropItems.value.findIndex((_item)=>_item.id === id);
  dropItems.value[objectIndex][changeKey] ++;
  if((imageMimes.indexOf(imageType) > -1) && imageSize <= image.size){
    dropItems.value[objectIndex][imageKey] = await makeBase64Image(image)
  }else{
    Toast.warning('فرمت یا سایز تصویر مجاز نیست')
  }


}

async function makeBase64Image(file){
  const blob = file;
  return new Promise((resolve) => {
    const reader = new FileReader();
    reader.readAsDataURL(blob);
    reader.onloadend = () => {
      const base64data = reader.result;
      resolve(base64data);
      return base64data
    }
  });
}

</script>
<style src="@vueform/multiselect/themes/default.css"></style>
