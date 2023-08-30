<template>
  <div>
    <div class="container mx-auto">
      <div class="mx-4 mt-10">
        <div class="bg-blue-50 rounded-lg p-4 w-full text-center">
          <h4 class="text-2xl fm:text-lg text-blue-500">{{msg}}</h4>
          <div class="mt-5">
            <Button text="پرداخت" my_class="!shadow-none !outline-none !border-none" />
          </div>
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

let msg = ref(route.params['msg'])
</script>