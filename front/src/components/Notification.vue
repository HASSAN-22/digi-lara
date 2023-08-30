<template>
  <div class="flex justify-center items-center gap-2">
    <div class="flex flex-col">
      <slot></slot>
    </div>
    <div v-for="notification in props.notifications" :key="notification.id">
      <span class="bg-red-500 px-2 text-xs rounded-full text-white" v-if="JSON.parse(notification.data)[props.type]['id'] === props.postId">جدید</span>
    </div>
  </div>
</template>

<script>
export default {name:'Notification'}
</script>

<script setup>
import {defineProps} from 'vue';
const props = defineProps(['notifications','postId','type'])
</script>