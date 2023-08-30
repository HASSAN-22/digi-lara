<template>

  <div v-if="props.widget !== undefined" class="mt-16 fd:mx-8">
    <div class="text-center">
      <span class="text-2xl fm:text-lg !font-medium">{{props.widget.title}}</span>
    </div>
    <div class="flex fd:gap-12 fm:gap-6 justify-between flex-wrap mt-12">
      <div v-for="category in props.widget.categories" :key="category.id" class="fd:w-[12rem] fd:h-[12rem] fm:w-[7rem] fm:h-[7rem] mb-4 fm:mb-10 text-center">
        <div class="flex flex-col gap-4 fm:gap-2">
          <routerLink :to="{name:'ProductsList', params:{slug:category.slug}}">
            <img :src="$store.state.url + category.image"/>
          </routerLink>
          <routerLink :to="{name:'ProductsList', params:{slug:category.slug}}"><span>{{category.title}}</span></routerLink>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {name:"SelectedCategory"}
</script>

<script setup>
import {defineProps} from 'vue'

let props = defineProps(['widget']);


</script>