<template>
  <div>
    <div class="container mx-auto">
      <div class="mx-4 mt-10">
        <div :class="['rounded-lg p-4 w-full text-center', status === 'success' ? 'bg-green-100' : 'bg-red-100']">
          <span :class="['text-lg fm:text-sm !font-medium', status === 'success' ? 'text-green-500' : 'text-red-500']">{{msg}}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {name:'PaymentAlert'}
</script>

<script setup>
import { useRoute } from "vue-router"
import {ref} from "vue";
import Button from "@/components/Button";
const route = useRoute();

let msg = ref(route.query['msg'])
let status = ref(route.query['status'])
</script>