<template>
  <div>
    <vue-countdown :time="getDifferenceTime(parseInt(props.expireAt))" v-slot="{ hours, minutes, seconds }">
      <span class="text-sm text-red-500 !font-medium">{{ hours }}:{{ minutes }}:{{ seconds }}</span>
    </vue-countdown>
  </div>
</template>

<script>
export default {name:"Countdown"}
</script>

<script setup>
import {defineProps} from 'vue';

const props = defineProps(['expireAt'])


function getDifferenceTime(end) {
  end *= 1000;
  let difference = 0;
  if (new Date() < new Date(end)) {
    let a = new Date(end).getTime();
    let b = new Date().getTime();
    difference = a - b;
  }
  return difference;
}
</script>