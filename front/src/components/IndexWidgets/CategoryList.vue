<template>
  <div v-if="props.widget !== undefined" class="mt-6 flex flex-row fm:gap-2 justify-between fm:justify-around flex-wrap">
    <div class="">
      <a  :href="props.widget.link_one" >
        <img :src="props.widget.image_one" class="rounded-xl w-[300px] h-[300px] fm:w-[150px] fm:h-[150px]"/>
      </a>
    </div>
    <div class="">
      <a  :href="props.widget.link_two" >
        <img :src="props.widget.image_two" class="rounded-xl w-[300px] h-[300px] fm:w-[150px] fm:h-[150px]"/>
      </a>
    </div>
    <div class="">
      <a  :href="props.widget.link_three" >
        <img :src="props.widget.image_three" class="rounded-xl w-[300px] h-[300px] fm:w-[150px] fm:h-[150px]"/>
      </a>
    </div>
    <div class="">
      <a  :href="props.widget.link_four" >
        <img :src="props.widget.image_four" class="rounded-xl w-[300px] h-[300px] fm:w-[150px] fm:h-[150px]"/>
      </a>
    </div>
  </div>
</template>

<script>
export default {name:"CategoryList"}
</script>

<script setup>
import {defineProps} from 'vue'

let props = defineProps(['widget']);


</script>