<template>
  <div>
    <div v-show="showModal"  id="modal" class="overflow-x-hidden overflow-y-auto fixed inset-0 z-600 outline-none focus:outline-none justify-center flex">
      <div :class="['relative my-6 mx-auto fm:w-[98%]',props.width]">
        <!--content-->
        <div class="bg-white border-0 rounded-lg relative flex flex-col w-full bg-d-color-blue-lighter-dark outline-none focus:outline-none">
          <!--header-->
          <div class="flex items-start justify-between py-5 px-10 border-b border-solid bg-d-color-blue-light rounded-t">
            <h3 class="text-3xl font-semibold fm:text-sm text-crimson-200">
              {{ props.title }}
            </h3>
            <span @click="showModal=false" class="cursor-pointer"><i class="far fa-times text-2xl"></i></span>
          </div>
          <!--body-->
          <div class="relative p-6 flex-auto bg-d-color-blue-dark">
            <slot></slot>
          </div>
          <!--footer-->
          <div class="flex items-center justify-start gap-4 p-6 border-t border-solid bg-d-color-blue-light rounded-b">
              <div v-if="props.permission === '' || $store.getters.userCan(props.permission)">
                <Button v-show="props.save !== ''" :text="props.save" :btnLoading="props.btnLoading" @callback="$emit('callback')" :my_class="props.btnLoading ? '!cursor-not-allowed !opacity-80 !bg-white border border-green-500 !text-green-500' : '!cursor-pointer !opacity-100 !bg-white border border-green-500 !text-green-500'"></Button>
              </div>
              <div v-else>
                <Button v-show="props.save !== ''" :text="props.save" my_class="!cursor-not-allowed !opacity-80 !bg-black  border border-black !text-white"></Button>
              </div>
              <Button text="بستن" my_class="!bg-white border border-red-500 !text-red-500 " @click="$emit('cancel', showModal=false)" />
          </div>
        </div>
      </div>
    </div>
    <div v-show="showModal" class="opacity-25 fixed inset-0 z-40 bg-d-color-blue-lighter-dark"></div>
    <BgBlur :show="showModal" />
  </div>
</template>
<script>
export default {
  name: "Modal",
}
</script>
<script setup>
import Button from '@/components/Button'
import BgBlur from '@/components/BgBlur'
import { ref, defineProps, defineExpose } from 'vue';
const props = defineProps({
  title:String,
  save:{
    type:String,
    default: ''
  },
  btnLoading:{
    type:Boolean,
    default:false
  },
  width:{
    type:String,
    default:'w-[50%]'
  },
  permission:{
    type:String,
    default: ''
  },
})

let showModal = ref(false)

defineExpose({
  toggleModal,
  closeModal
});

function toggleModal(){
  showModal.value = !showModal.value;
}

function closeModal(){
  showModal.value = false;
}
</script>