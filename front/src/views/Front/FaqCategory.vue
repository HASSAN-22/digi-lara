<template>
  <div>
    <div class="container mx-auto">
      <div class="mt-20 mx-10">
        <div class="text-center">
          <h3 class="!font-medium fm:text-md fd:text-lg">دسته بندی پرسش ها</h3>
        </div>
        <div class="flex flex-wrap gap-5">
          <div class="border border-gray-200 rounded-lg p-2" v-for="category in faqCategories" :key="category.id">
            <routerLink :to="{name:'FaqDetail',params:{slug:category.slug}}" class="w-[10rem] h-[10rem] fm:w-[8rem] fm:h-[8rem] flex flex-col items-center gap-3 justify-center">
              <div v-if="category.image !== null">
                <img :src="$store.state.url + category.image" :alt="category.title" />
              </div>
              <div v-else>
                <span><i :class="category.icon"></i></span>
              </div>
              <span class="fm:text-sm text-gray-500">{{category.title}}</span>
            </routerLink>
          </div>
        </div>
      </div>
    </div>
    <Meta :title="$store.state.siteName + ` دسته بندی پرسش ها `"/>
    <Loading :loading="loading" />
    <ValidationError/>
  </div>
</template>

<script>
export default{name: "FaqCategory"}
</script>

<script setup>
import {onMounted, ref} from "vue";
import Meta from "@/components/Meta.vue";
import Loading from "@/components/Loading";
import ValidationError from "@/components/ValidationError";
import axios from "@/plugins/axios";
import store from "@/store";
import Toast from "@/plugins/toast";

let loading = ref(false);
let faqCategories = ref([]);

onMounted(async ()=>{
  await getFaqCategories()
})

async function getFaqCategories(){
  loading.value = true;
  await axios.get(`${store.state.api}get-faq-categories`).then(resp=>{
    faqCategories.value = resp.data.data;
  }).catch(err=>{
    Toast.error()
  })
  loading.value = false;
}

</script>

<style scoped>
#router-link {
  display: inline-block;
}
</style>