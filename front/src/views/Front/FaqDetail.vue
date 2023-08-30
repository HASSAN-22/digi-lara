<template>
  <div>
    <div class="container mx-auto"  v-if="category !== null">
      <div class="mt-20 mx-10">
        <div class="text-center flex flex-col gap-2 items-center mb-20">
          <div v-if="category.image !== null" class="h-[2rem] w-[2rem]">
            <img :src="$store.state.url + category.image" :alt="category.title" />
          </div>
          <div v-else class="h-[2rem] w-[2rem]">
            <span><i :class="category.icon"></i></span>
          </div>
          <h3 class="!font-medium fm:text-md fd:text-lg">{{category.title}}</h3>
        </div>
        <div class="flex flex-col w-full gap-5">
          <div class="border-b border-gray-200 w-full p-3 rounded-lg" v-for="faq in category.faqs" :key="faq.id">
            <div class="flex justify-between items-center cursor-pointer" @click="openDetail(faq.id)">
              <span class="fm:text-sm !font-medium">{{faq.title}}</span>
              <span v-if="detailTab === faq.id"><i class="far fa-chevron-down text-sm"></i></span>
              <span v-else><i class="far fa-chevron-left text-sm"></i></span>
            </div>
            <div :class="['mt-5', detailTab === faq.id ? 'block' : 'hidden']">
              <p class="fm:text-sm break-words">{{faq.description}}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
    <Meta :title="$store.state.siteName + ` سوالات متداول `"/>
    <Loading :loading="loading" />
    <ValidationError/>
  </div>
</template>

<script>
export default{name: "FaqDetail"}
</script>

<script setup>
import {onMounted, ref} from "vue";
import Meta from "@/components/Meta.vue";
import Loading from "@/components/Loading";
import ValidationError from "@/components/ValidationError";
import axios from "@/plugins/axios";
import store from "@/store";
import Toast from "@/plugins/toast";
import { useRoute } from "vue-router"

let loading = ref(false);
let category = ref(null);
const route = useRoute();
let slug = ref(route.params['slug'])
let detailTab = ref(0);

onMounted(async ()=>{
  await getFaqs()
})

async function getFaqs(){
  loading.value = true;
  await axios.get(`${store.state.api}get-faqs/${slug.value}`).then(resp=>{
    category.value = resp.data.data;
  }).catch(err=>{
    Toast.error()
  })
  loading.value = false;
}

function openDetail(tab){
  detailTab.value = detailTab.value === tab ? 0 : tab;
}
</script>

<style scoped>
#router-link {
  display: inline-block;
}
</style>