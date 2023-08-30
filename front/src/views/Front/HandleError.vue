<template>
  <div>

    <div class="container mx-auto">
      <div class="mx-4 my-20 flex justify-center">
        <div class="flex flex-col bg-red-100 border border-red-100 rounded-lg p-3 items-center w-[40%] fm:w-full">
          <span class="!font-medium text-3xl text-red-500">{{code}}</span>
          <span class="!font-medium text-lg text-red-500">{{msg}}</span>
        </div>
      </div>
    </div>
    <Meta :title="$store.state.siteName + ` | خطای  ${code}`"/>
  </div>
</template>

<script>
export default{name: "HandleError"}
</script>

<script setup>
import {ref} from "vue";
import Meta from "@/components/Meta.vue"
import { useRoute } from "vue-router"

const route = useRoute();
let code = ref(route.params['code'])
let msg = ref(route.params['msg'])


</script>