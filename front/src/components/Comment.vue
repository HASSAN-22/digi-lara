<template>
  <div>
    <Modal title="ثبت دیدگاه" save="ثبت" :btnLoading="btnLoading" @callback="addComment()" ref="commentModal" width="w-[70%] fm:w-full">

      <div class="flex fm:flex-col">
        <div class="fd:w-[50%] flex flex-col gap-3 fd:mt-8">
          <div class="flex flex-col items-center gap-8">
            <div class="flex justify-center">
              <span class="text-lg !font-medium">امتیاز دهید!: {{ratingSelectText}}</span>
            </div>
            <ul class="flex items-center">
              <li @click="ratingSelect('1','خیلی بد')" :class="['flex flex-col gap-1 items-center p-2 cursor-pointer w-[6rem] h-[5rem]', ratingSelected === '1' ? 'border-2 border-[#19bfd3] rounded-lg' : '']">
                <span><i class="far fa-face-angry text-3xl fm:text-lg text-red-500"></i></span>
                <span class="fm:text-sm text-red-500">خیلی بد</span>
              </li>
              <li @click="ratingSelect('2','بد')" :class="['flex flex-col gap-1 items-center p-2 cursor-pointer w-[6rem] h-[5rem]', ratingSelected === '2' ? 'border-2 border-[#19bfd3] rounded-lg' : '']">
                <span><i class="far fa-face-frown text-3xl fm:text-lg text-orange-500"></i></span>
                <span class="fm:text-sm text-orange-500">بد</span>
              </li>
              <li @click="ratingSelect('3','معمولی')" :class="['flex flex-col gap-1 items-center p-2 cursor-pointer w-[6rem] h-[5rem]', ratingSelected === '3' ? 'border-2 border-[#19bfd3] rounded-lg' : '']">
                <span><i class="far text-gray-700  fa-face-meh text-3xl fm:text-lg"></i></span>
                <span class="text-gray-700 fm:text-sm">معمولی</span>
              </li>
              <li @click="ratingSelect('4','خوب')" :class="['flex flex-col gap-1 items-center p-2 cursor-pointer w-[6rem] h-[5rem]', ratingSelected === '4' ? 'border-2 border-[#19bfd3] rounded-lg' : '']">
                <span><i class="far fa-face-smile text-3xl fm:text-lg text-blue-500"></i></span>
                <span class="fm:text-sm text-blue-500">خوب</span>
              </li>
              <li @click="ratingSelect('5','خیلی خوب')" :class="['flex flex-col gap-1 items-center p-2 cursor-pointer w-[6rem] h-[5rem]', ratingSelected === '5' ? 'border-2 border-[#19bfd3] rounded-lg' : '']">
                <span><i class="far fa-face-laugh-beam text-3xl fm:text-lg text-green-500"></i></span>
                <span class="fm:text-sm text-green-500">خیلی خوب</span>
              </li>
            </ul>
          </div>
          <div class="border-b border-gray-200 my-1"></div>
          <div v-if="props.purchased">
            <div>
              <span class="text-sm !font-medium">خرید این کالا را به دیگران ...</span>
            </div>
            <div class="mt-8 flex items-center gap-4 justify-center">
              <div @click="selectSuggest('yes')" :class="['cursor-pointer w-[8rem] h-[8rem] p-2 border-2 rounded-lg flex flex-col justify-center items-center gap-3',suggest === 'yes' ? 'border-[#19bfd3]' : 'border-gray-300']">
                <span><i class="text-3xl text-gray-400 far fa-thumbs-up"></i></span>
                <span class="text-sm !font-medium text-gray-500">پیشنهاد می‌کنم</span>
              </div>
              <div @click="selectSuggest('not_sure')" :class="['cursor-pointer w-[8rem] h-[8rem] p-2 border-2 rounded-lg flex flex-col justify-center items-center gap-3',suggest === 'not_sure' ? 'border-[#19bfd3]' : 'border-gray-300']">
                <div class="flex items-center">
                  <span><i class="text-3xl text-gray-400 far fa-question"></i></span>
                  <span><i class="text-3xl text-gray-400 far fa-exclamation"></i></span>
                </div>
                <span class="text-sm !font-medium text-gray-500">مطمئن نیستم</span>
              </div>
              <div @click="selectSuggest('no')" :class="['cursor-pointer w-[8rem] h-[8rem] p-2 border-2 rounded-lg flex flex-col justify-center items-center gap-3',suggest === 'no' ? 'border-[#19bfd3]' : 'border-gray-300']">
                <span><i class="text-3xl text-gray-400 far fa-thumbs-down"></i></span>
                <span class="text-sm !font-medium text-gray-500">پیشنهاد نمی‌کنم</span>
              </div>
            </div>
          </div>
          <div class="border-b border-gray-200 my-1"></div>
          <div>
            <div class="mb-8">
              <span class="text-lg !font-medium fm:text-md">دیدگاه خود را شرح دهید</span>
            </div>
            <div class="flex flex-col gap-8">
              <div>
                <Input label="نکات مثبت" placeholder="متن را وارد کرده و اینتر بزند" :required="false" @keyup.enter="addStrengths($event)" id="strengths" />
                <div class="flex flex-wrap gap-2">
                  <div class="flex items-center gap-1 bg-green-100 rounded px-2 py-1" v-for="(strength,index) in strengths" :key="index">
                    <span class="text-green-500 text-sm">{{strength}}</span>
                    <span class="cursor-pointer" @click="removeStrengths(index)"><i class="far fa-trash text-red-500 text-sm"></i></span>
                  </div>
                </div>
              </div>
              <div>
                <Input label="نکات ضعف" placeholder="متن را وارد کرده و اینتر بزند" :required="false" @keyup.enter="addWeakPoints($event)" id="property" />
                <div class="flex flex-wrap gap-2">
                  <div class="flex items-center gap-1 bg-red-100 rounded px-2 py-1" v-for="(weakPoint,index) in weakPoints" :key="index">
                    <span class="text-red-500 text-sm">{{weakPoint}}</span>
                    <span class="cursor-pointer" @click="removeWeakPoints(index)"><i class="far fa-trash text-red-500 text-sm"></i></span>
                  </div>
                </div>
              </div>
              <div>
                <Textarea label="متن نظر" v-model="comment" :maxlength="30000" placeholder="برای ما بنویسید..."/>
              </div>
            </div>
            <div  v-if="props.purchased">
              <div class="my-8">
                <span class="text-lg !font-medium fm:text-md">ارسال محتوا</span>
              </div>
              <div class="flex flex-col gap-8">
                <Button @click="addCommentImageCont()" text="افزودن تصویر" my_class="!bg-white !text-red-500 !border !border-red-500 !text-sm !px-2 !py-2"/>
                <div class="flex justify-between gap-3" v-for="count in commentImageCount" :key="count-1">
                  <div class="flex flex-col gap-2 w-full">
                    <Input type="file" label="تصاویر" :required="false" @change="getFile($event, count-1)" :key="commentImagesKey[count-1]" placeholder="متن را وارد کرده و اینتر بزنید"/>
                    <div v-if="commentImagesPreview[count-1] !== ''">
                      <img :src="commentImagesPreview[count-1]" class="w-[70px] h-[70px]"/>
                    </div>
                  </div>
                  <span class="cursor-pointer" @click="removeCommentImage(count-1)"><i class="far fa-trash text-sm text-red-500"></i></span>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="fd:w-[50%] p-3">
          <div class="border rounded-lg p-2 flex flex-col gap-3">
            <h2 class="text-lg !font-bold fm:text-sm">
              دیگران را با نوشتن نظرات خود، برای انتخاب این کالا راهنمایی کنید.
            </h2>
            <span class="text-red-500 fm:text-sm !font-medium">
                افزودن عکس به نظرات:
            </span>
            <ul class="flex flex-col gap-2">
              <li class="text-orange-500 fm:text-sm">
                فرمت تصاویر: jpg,jpeg,png باشد
              </li>
              <li class="text-orange-500  fm:text-sm">
                حجم هر تصویر: {{$store.state.imageSize}}
              </li>
            </ul>
            <p class="no-tailwindcss" v-html="$store.state.configSite.comment_rule"></p>
          </div>
        </div>
      </div>
    </Modal>
    <ValidationError />
  </div>
</template>

<script>
export default {name:'Comment'}
</script>
<script setup>
import {ref, defineProps, defineEmits, defineExpose} from 'vue'
import Modal from "@/components/Modal";
import store from "@/store";
import axios from "@/plugins/axios";
import Toast from "@/plugins/toast";
import Button from "@/components/Button.vue";
import Input from "@/components/Input.vue";
import Textarea from "@/components/Textarea.vue";
import ValidationError from "@/components/ValidationError.vue";

const props = defineProps({
  purchased:{
    type:Boolean,
    default:false,
  },
  productId:{
    type:Number,
    required:true,
  }
});

const emits = defineEmits(['parentMethod']);

defineExpose({
  openCommentModal
})


let btnLoading = ref(false)

let commentModal = ref(null)
let comment = ref('')
let ratingSelected = ref('')
let ratingSelectText = ref('')
let suggest = ref('')
let strengths = ref([]);
let weakPoints = ref([]);
let commentImageCount = ref(0);
let commentImages = ref([]);
let commentImagesPreview = ref([]);
let commentImagesKey = ref([]);
let commentSubject = ref(0);

async function addComment(){
  btnLoading.value = true;
  if(store.state.user.token){
    await axios.post(`${store.state.api}add-comment/${props.productId}`,commentFormData()).then(async resp=>{
      clearCommentData();
      Toast.success('با موفقیت ثبت شد بعد از تایید نمایش داده میشود')
      await emits('parentMethod',false, store.state.current)
    }).catch(err=>{
      store.commit('handleError',err)
    })

  }else{
    Toast.error('لطفا وارد حساب کاربری خود شوید')
  }
  btnLoading.value = false;
}


function commentFormData(){
  let frm = new FormData()
  frm.append('rating',ratingSelected.value)
  frm.append('suggest',suggest.value)
  frm.append('purchased',props.purchased)
  frm.append('comment',comment.value)
  frm.append('weak_points',JSON.stringify(weakPoints.value));
  frm.append('strengths',JSON.stringify(strengths.value));
  if(commentImages.value.length > 0){
    commentImages.value.forEach( img => {
      frm.append('images[]', img);
    })
  }else{
    frm.append('images[]', []);
  }
  return frm;
}

function clearCommentData() {
  ratingSelected.value = ratingSelectText.value = suggest.value = comment.value = ''
  strengths.value = weakPoints.value = commentImages.value = commentImagesPreview.value = commentImagesKey.value = [];
  commentImageCount.value = commentSubject.value = 0;
}


function ratingSelect(rating, text){
  ratingSelected.value = ratingSelected.value === rating ? '' : rating;
  ratingSelectText.value = ratingSelectText.value === text ? '' : text;
}

function selectSuggest(_suggest){
  suggest.value = suggest.value === _suggest ? '' : _suggest;
}

function addCommentImageCont(){
  commentImages.value[commentImageCount.value] = '';
  commentImagesPreview.value[commentImageCount.value] = '';
  commentImagesKey.value[commentImageCount.value] = 0;
  commentImageCount.value++;
}

function getFile(event, index){
  let image = event.target.files[0];
  commentImages.value[index] = image;
  commentImagesPreview.value[index] = URL.createObjectURL(image);
  commentImagesKey.value[index]++;
}

function removeCommentImage(index){
  commentImages.value = commentImages.value.filter((i,k)=>k!==index);
  commentImagesPreview.value = commentImagesPreview.value.filter((i,k)=>k!==index);
  commentImagesKey.value = commentImagesKey.value.filter((i,k)=>k!==index);
  commentImageCount.value--;
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

function openCommentModal(){
  commentModal.value.toggleModal();
}
</script>