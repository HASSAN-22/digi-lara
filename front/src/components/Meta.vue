<template>

</template>
<script>
import { computed, reactive } from 'vue'
import { useHead } from '@vueuse/head'
export default {
  name:"Meta",
  props:{
    title:String,
    description:{
      type:String,
      description: ''
    },
    type:{
      type:String,
      default:'name'
    }
  },
  setup(props) {
    const siteData = reactive({
      title: props.title,
      description: props.description,
    })
    const myMeta = ()=>{
      if(props.type === 'name'){
        return {
          name: props.title,
          content: computed(() => siteData.description),
        }
      }else{
        return {
          property:  props.title,
          content: computed(() => siteData.description),
        }
      }
    }
    useHead({
      title: computed(() => siteData.title),
      meta: [
        myMeta()
      ],
    })

  }
}
</script>
