<template>
  <ul class="border-b border-b-gray-200 py-3">
    <li class="red">{{ props.title }}</li>
    <li class="mt-3 px-5">
      <ul class="flex fm:flex-col fd:gap-8 fm:gap-2">

        <li class="flex items-center gap-2" v-for="permission in props.permissions" :key="permission.id">
          <label class="text-gray-700 fm:text-sm" :for="permission.perm[0]">{{permission.label}}</label>
          <input type="checkbox" :id="permission.perm[0]" @change="updatePermission($event,permission.perm)"  v-if="$store.state.permissions.find(item=>permission.perm.includes(item)) !== undefined" checked>
          <input type="checkbox" :id="permission.perm[0]" @change="updatePermission($event,permission.perm)"  v-else>
        </li>
      </ul>
    </li>
  </ul>
</template>
<script>
export default {name:'PermissionComponent'}
</script>

<script setup>
import {defineProps} from 'vue';
import store from '@/store'

const props = defineProps(["permissions","title"])

function updatePermission(event,perm) {
  if (event.target.checked) {
    store.state.permissions.push(...perm)
  } else {
    store.state.permissions = store.state.permissions.filter((item) => item !== perm)
  }
}
</script>