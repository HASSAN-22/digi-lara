<template>
  <div>
    <div v-show="$store.state.validationErrorModal"  id="modal" class="overflow-x-hidden overflow-y-auto fixed inset-0 z-600 outline-none focus:outline-none justify-center flex">
      <div :class="['relative my-6 mx-auto fm:!w-[90%]',props.width]">
        <!--content-->
        <div class="bg-[#f5f5f5] shadow-lg border-0 rounded-lg relative flex flex-col w-full bg-d-color-blue-lighter-dark outline-none focus:outline-none">
          <!--header-->
          <div class="flex items-start justify-between py-5 px-10 border-b border-solid bg-d-color-blue-light rounded-t">
            <h3 class="text-3xl font-semibold fm:text-sm text-crimson-200">
              خطاها
            </h3>
            <span @click="$store.state.validationErrorModal=false" class="cursor-pointer"><i class="far fa-times text-2xl"></i></span>
          </div>
          <!--body-->
          <div class="relative p-6 flex-auto bg-[#ffbcc8] mb-3 w-full text-center rounded p-3 scroll overflow-y-scroll h-[30rem]" v-if="$store.state.errors.length > 0">
            <ul class="flex flex-col gap-3">
              <li class="text-crimson-200" v-for="(error,i) in $store.state.errors" :key="i">{{ error[0] }}</li>
            </ul>
          </div>
          <!--footer-->
          <div class="flex items-center justify-start gap-4 p-6 border-t border-solid bg-d-color-blue-light rounded-b">
            <Button text="بستن" my_class="!hover:bg-crimson-300 border-crimson-200" @click="$emit('cancel', $store.state.validationErrorModal=false)" />
          </div>
        </div>
      </div>
    </div>
    <div v-show="$store.state.validationErrorModal" class="opacity-25 fixed inset-0 z-40 bg-d-color-blue-lighter-dark"></div>
    <BgBlur :show="$store.state.validationErrorModal" />
  </div>
</template>
<script>
export default {
  name: "ValidationError",
}
</script>
<script setup>
import Button from '@/components/Button'
import BgBlur from '@/components/BgBlur'
import { defineProps } from 'vue';
import store from "@/store";
const props = defineProps({
  width:{
    type:String,
    default:'w-[50%]'
  }
})

function toggleModal(){
  store.state.validationErrorModal = !store.state.validationErrorModal;
}

function closeModal(){
  store.state.validationErrorModal = false;
}
</script>