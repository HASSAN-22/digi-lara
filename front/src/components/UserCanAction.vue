<template>
  <div>
    <div v-if="props.action === 'show'">
      <Button v-if="$store.getters.userCan(props.permission)" @click="show(props.postId)" :my_class="'!bg-white border border-blue-500 !py-2 !px-2 fm:py-1 fm:px-1 '+props.my_class"><i class="far fm:text-sm fa-edit text-blue-500"></i></Button>
      <Button v-else :my_class="'!cursor-not-allowed !bg-white border border-black !py-2 !px-2 fm:py-1 fm:px-1 '+props.my_class"><i class="far fm:text-sm fa-edit text-black"></i></Button>
    </div>
    <div v-else-if="props.action === 'destroy'">
      <Button v-if="$store.getters.userCan(props.permission)" @click="destroy(props.postId)" :my_class="'!bg-white border border-red-500 !py-2 !px-2 fm:py-1 fm:px-1 '+props.my_class"><i class="far fm:text-sm fa-trash text-red-500"></i></Button>
      <Button v-else :my_class="'!cursor-not-allowed !bg-white border border-black !py-2 !px-2 fm:py-1 fm:px-1 '+props.my_class"><i class="far fm:text-sm fa-trash text-black"></i></Button>
    </div>
    <div v-else-if="props.action === 'create'">
      <Button v-if="$store.getters.userCan(props.permission)" text="اضافه کردن" @click="create()"></Button>
      <Button v-else text="اضافه کردن" :my_class="'!cursor-not-allowed !bg-black '+props.my_class"></Button>
    </div>
  </div>
</template>

<script>
export default {name:'UserCanAction'}
</script>

<script setup>
import {defineEmits, defineProps} from 'vue';
import Button from "@/components/Button.vue"

const props = defineProps({
  my_class:{
    type:String
  },
  action:{
    type:String
  },
  permission:{
    type:String
  },
  postId:{
    type:String,
    default:'',
  }
})
const emits = defineEmits(['show','destroy','create'])
function show(postId){
  emits('show', postId)
}
function destroy(postId){
  emits('destroy', postId)
}
function create(){
  emits('create')
}
</script>