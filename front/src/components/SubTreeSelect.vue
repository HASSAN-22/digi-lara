<template>
  <ul class="sub-category" v-for="category in props.categories" :key="category.id">
    <li
        :class="['flex items-center gap-1 p-1 hover:bg-gray-200 w-full cursor-pointer']"
        @click="props.clickChild ? '' : selectItem([category.id, category.title,category.commission])">
        <span v-if="$store.state.selectIds.includes(category.id)"><i class="far fa-check text-sm text-green-500"></i></span>
        <span :class="['fm:text-sm',category.children.length > 0 ? '!font-medium' : '']">{{category.title}}</span>
    </li>

    <li>
      <ul class="sub-category" v-for="subCategory in category.children" :key="subCategory.id">
        <li
            :class="['flex items-center gap-1 p-1 hover:bg-gray-200 w-full cursor-pointer']"
            @click="props.clickChild && subCategory.children.length > 0 ? '' : selectItem([subCategory.id, subCategory.title,subCategory.commission])">
            <span v-if="$store.state.selectIds.includes(subCategory.id)"><i class="far fa-check text-sm text-green-500"></i></span>
            <span :class="['fm:text-sm',subCategory.children.length > 0 ? '!font-medium' : '']">{{subCategory.title}}</span>
        </li>
        <SubTreeSelect @selectItem="selectItem" :categories="subCategory.children" :key="subCategory.id"/>
      </ul>
    </li>
  </ul>
</template>


<script>
export default {name:"SubTreeSelect"}
</script>

<script setup>
import {defineProps, defineEmits} from 'vue';

const props = defineProps({
  'categories':{
    type:Array
  },
  'clickChild':{
    type:Boolean,
    default:false,
  }
})

const emit = defineEmits(['selectItem'])

function selectItem(data){
  emit('selectItem', data)
}

</script>

<style lang="scss">
.sub-category{
  margin-right: .5rem;
}
</style>