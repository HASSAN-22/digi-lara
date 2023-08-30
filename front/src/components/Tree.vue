<template>
  <ul class="tree" v-for="category in props.categories" :key="category.id">
    <li class="tree-item">
      <a  href="javascript:void(0)" :class="['trigger flex items-center gap-1']">
        <span class="mt-[.4rem] fm:mt-[.3rem] fm:text-sm"><i class="fas fa-angle-right fm:text-sm text-blue-500"></i></span>
        <span :class="['fm:text-sm text-blue-400 !font-medium',category.status === 'yes' ? 'text-green-500' : 'text-red-500']">{{ category.title }}</span>
        <UserCanAction action="show" @show="show(category.id)" permission="view_categories" :postId="category.id" my_class="!border-none !px-0 !py-0" />
        <UserCanAction action="destroy" @destroy="destroy(category.id)" permission="delete_categories" :postId="category.id" my_class="!border-none !px-0 !py-0" />
      </a>
      <ul class="tree-parent open" v-for="item in category.children" :key="item.id">
        <li class="tree-item view">
          <a href="javascript:void(0)" :class="['flex items-center gap-1']">
            <span class="mt-[.4rem] fm:mt-[.3rem] fm:text-sm"><i class="fas fm:text-sm fa-angle-right text-blue-500"></i></span>
            <span :class="['fm:text-sm text-blue-400 !font-medium',item.status === 'yes' ? 'text-green-500' : 'text-red-500']">{{ item.title}}</span>
            <UserCanAction action="show" @show="show(item.id)" permission="view_categories" :postId="item.id" my_class="!border-none !px-0 !py-0" />
            <UserCanAction action="destroy" @destroy="destroy(item.id)" permission="delete_categories" :postId="item.id" my_class="!border-none !px-0 !py-0" />
          </a>
          <Tree @show="show" @destroy="destroy" :categories="item.children"></Tree>
        </li>
      </ul>

    </li>
  </ul>
</template>
<script>
export default {
  name:"Tree"
}
</script>
<script setup>
import {defineProps, defineEmits} from 'vue';
import UserCanAction from "@/components/UserCanAction.vue";
const props = defineProps(['categories']);
const emit = defineEmits(['show','destroy'])

function show(postId){
  emit('show', postId)
}

function destroy(postId){
  emit('destroy', postId)
}

</script>