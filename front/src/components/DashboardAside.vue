<template>
  <li :class="['hover:bg-gray-50 p-2 cursor-pointer', tab === props.tab ? 'bg-gray-100' : '']" @click="changeTab(props.tab)">
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-1">
        <span class="text-gray-600"><i :class="props.icon"></i></span>
        <span class="text-gray-600">{{props.menu}}</span>
      </div>
      <span v-if="tab !== props.tab"><i class="far text-sm fa-chevron-left text-gray-600"></i></span>
      <span v-else><i class="far text-sm fa-chevron-down text-gray-600"></i></span>
    </div>
  </li>
  <li v-if="tab === props.tab">
    <ul class="flex flex-col">
      <li v-for="submenu in props.submenus" :key="submenu.id">
        <div class="hover:bg-gray-200 p-2 pr-6 cursor-pointer" v-if="submenu.perm === undefined || store.getters.userCan(submenu.perm,'include')">
          <routerLink :to="submenu.route" class="text-gray-600">{{submenu.title}}</routerLink>
        </div>
      </li>
    </ul>
  </li>
</template>

<script>
export default{name:"DashboardAside"}
</script>

<script setup>
import {ref, defineProps} from "vue";
import store from "@/store";

const props = defineProps({
  "tab":{
    type:Number,
    default:0
  },
  "icon":{
    type:String,
    default:""
  },
  "menu":{
    type:String,
    default:""
  },
  "submenus":{
    type:Array
  }
});
let tab = ref(0);

function changeTab(_tab){
  tab.value = _tab === tab.value ? 0 : _tab;
}

</script>