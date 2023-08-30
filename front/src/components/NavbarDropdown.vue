<template>
  <ul :class="['dropdown-content fd:absolute hidden bg-white shadow-lg fd:w-[15rem]', props.isFirstCall ? 'top-7 right-0' : 'top-0 right-[15rem]']">
    <li class="relative dropdown" v-for="children in props.childrens" :key="children.id">
      <routerLink :to="{name:'NewsList', params:{slug:children.slug}}" class="hover:bg-gray-100 py-2 px-4 block whitespace-no-wrap">{{children.title}}</routerLink>
      <NavbarDropdown :childrens="children.children" :isFirstCall="false" />
    </li>
  </ul>
</template>

<script>
export default {name:'NavbarDropdown'}
</script>

<script setup>
import {defineProps} from "vue";

const props = defineProps({
  'childrens':{
    type:Array,
    required:true
  },
  'isFirstCall':{
    type:Boolean,
    default:false,
  }
})
</script>