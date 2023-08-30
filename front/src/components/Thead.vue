<template>
<thead class="block lg:table-header-group xl:table-header-group 2xl:table-header-group md:table-header-group">
    <tr class="border border-gray-200 lg:border-none xl:border-none 2xl:border-none md:border-none lg:table-row 2xl:table-row xl:table-row md:table-row absolute -top-full lg:top-auto 2xl:top-auto xl:top-auto md:top-auto  -left-full lg:left-auto xl:left-auto 2xl:left-auto md:left-auto  lg:relative xl:relative 2xl:relative md:relative ">
        <th :class="['bg-white text-gray-700 !font-medium p-2 font-bold lg:border xl:border 2xl:border md:border lg:border-gray-200 xl:border-gray-200 2xl:border-gray-200 md:border-gray-200 text-center block lg:table-cell xl:table-cell 2xl:table-cell md:table-cell fm:text-sm',props.textClass]" v-for="(head, i) in headers" :key="i">
          {{head}}
        </th>
    </tr>
</thead>

</template>
<script>
export default {
    name:'Thead',
}
</script>
<script setup>
import {ref, onMounted, defineProps} from 'vue'

const props = defineProps({
  'titles':{
    type:Array,
    required:true
  },
  'textClass':{
    type:String,
    default:''
  },
  'except':{
    type:Array,
    default:[],
  }
});

let headers = ref([]);

onMounted(()=>{
  headers.value = props.titles.filter(x => !props.except.includes(x));
})
</script>