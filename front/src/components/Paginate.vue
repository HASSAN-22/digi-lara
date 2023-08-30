<template>
  <div class="container mx-auto px-4 fm:px-5 text-center" v-if="props.current > 1 || props.current < props.total">
    <nav class="py-[2rem]">
      <span v-if="props.current > 1" @click="callGetPage(props.current - 1)" class="rounded-full cursor-pointer bg-white border border-red-500 px-8 py-3 text-center text-red-500">
        <span class="font-bold opacity-90">قبلی</span>
      </span>
      <span v-else class="rounded-full cursor-pointer bg-white border border-gray-300 px-8 py-3 text-center text-gray-500 focus:outline-none" disabled>
        <span class="font-bold opacity-90">قبلی</span>
      </span>
      <span class="rounded-full bg-d-color-blue-light px-8 py-3 text-center m-1">{{props.current+' از '+props.total}}</span>
      <span v-if="props.current < props.total" @click="callGetPage(props.current + 1)" class="rounded-full cursor-pointer bg-white border border-red-500 px-8 py-3 text-center text-red-500">
        <span class="font-bold opacity-90">بعدی</span>
      </span>
      <span v-else class="rounded-full cursor-pointer bg-white border border-gray-300 px-8 py-3 text-center text-gray-500 focus:outline-none" disabled>
        <span class="font-bold opacity-90">بعدی</span>
      </span>
    </nav>
  </div>
</template>
<script>
export default {
  name:"Paginate"
}
</script>
<script setup>
import { defineProps, defineEmits } from 'vue';

const props = defineProps({current:Number,total:Number})
const emits = defineEmits(['callGetPage'])
function callGetPage(page){
  emits('callGetPage', true, page)
}
</script>