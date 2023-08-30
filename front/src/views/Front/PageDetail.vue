<template>
  <div>
    <div v-if="page !== null">
      <div class="container mx-auto">
        <div class="mt-20 mx-10">
          <div class="text-center flex flex-col gap-2 items-center mb-20">
            <h3 class="!font-medium fm:text-md fd:text-lg">{{page.title}}</h3>
          </div>
          <div class="flex flex-col w-full gap-5">
            <p class="break-all" v-html="page.description"></p>
          </div>
        </div>
      </div>
      <Meta :title="$store.state.siteName + ` | ${page.title} `"/>
      <Loading :loading="loading" />
    </div>
  </div>
</template>

<script>
export default{name: "PageDetail"}
</script>

<script setup>
import {onMounted, ref, watch} from "vue";
import Meta from "@/components/Meta.vue";
import Loading from "@/components/Loading";
import axios from "@/plugins/axios";
import store from "@/store";
import Toast from "@/plugins/toast";
import { useRoute } from "vue-router"

let loading = ref(false);
const route = useRoute();
let postId = ref(route.params['id'])
let page = ref(null);

onMounted(async ()=>{
  await getFaqs()
})


watch(route, async ( current ) => {
  postId.value = current.params['id'];
  if(current.name === 'PageDetail'){
    await getFaqs()
  }
})

async function getFaqs(){
  loading.value = true;
  await axios.get(`${store.state.api}get-page/${postId.value}`).then(resp=>{
    page.value = resp.data.data;
  }).catch(err=>{
    Toast.error()
  })
  loading.value = false;
}

</script>
