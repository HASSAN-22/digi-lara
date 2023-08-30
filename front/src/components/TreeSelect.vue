<template>
  <div class="relative">
    <label class="fm:text-sm">{{props.title}}<b v-if="props.required" class="text-red-500 !font-bold">*</b></label>
    <div>
      <div @click="showCategory = !showCategory" class="border border-gray-200 p-2 outline-none rounded-lg w-full fm:text-sm">
        <div v-if="categoryTitles.length > 0">
          <span>{{categoryTitles.map(item=>item.title).join(',')}}</span>
        </div>
        <div v-else>
          <span>--- انتخاب کنید ---</span>
        </div>
      </div>
      <div :class="['absolute bg-white z-200 w-full border border-gray-200 p-2 h-[200px] overflow-y-scroll', showCategory ? 'block' : 'hidden']">
        <Input type="text" @keyup="search($event)" placeholder="جستجو ..." :required="false" />
        <div v-if="props.headCategory" :class="['flex items-center gap-1 p-1 w-full cursor-pointer hover:bg-gray-200']" @click="props.clickChild ? '' : selectItem([0, 'سردسته',0])">
          <span v-if="$store.state.selectIds.includes(0)"><i class="far fa-check text-sm text-green-500"></i></span>
          <span class="!font-medium fm:text-sm">سردسته</span>
        </div>
        <div :class="[props.headCategory ? 'mr-[.5rem]' : '']" v-for="category in allCategories" :key="category.id">
          <ul  v-if="category.parent_id === '0'">
            <li :class="['flex items-center gap-1 p-1 w-full cursor-pointer hover:bg-gray-200']" @click="props.clickChild ? '' : selectItem([category.id, category.title, category.commission])">
              <span v-if="$store.state.selectIds.includes(category.id)"><i class="far fa-check text-sm text-green-500"></i></span>
              <span class="!font-medium fm:text-sm">{{category.title}}</span>
            </li>
            <li>
              <SubTreeSelect :clickChild="props.clickChild" @selectItem="selectItem" :key="category.id" :categories="category.children"/>
            </li>
          </ul>
          <ul v-else-if="isSearch">
            <li>
              <SubTreeSelect :clickChild="false" @selectItem="selectItem" :key="category.id" :categories="[category]"/>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
export default {name:"TreeSelect"}
</script>

<script setup>
import {ref, defineProps, defineEmits, onMounted} from 'vue';
import SubTreeSelect from "@/components/SubTreeSelect";
import store from "@/store";
import Input from "@/components/Input.vue";

let showCategory = ref(false)
let isSearch = ref(false)
let categoryTitles = ref([])
const props = defineProps({
  'title':{
    type:String,
    default:'title',
  },
  'required':{
    type:Boolean,
    default:true,
  },
  'multi':{
    type:Boolean,
    default:true,
  },
  'categories':{
    type:Array
  },
  'clickChild':{
    type:Boolean,
    default:false,
  },
  'headCategory':{
    type:Boolean,
    default:false,
  }
})

let allCategories = ref([])

const emit = defineEmits(['selectItem'])

onMounted(()=>{
  allCategories.value = props.categories;
  let category = props.categories.filter(item=>store.state.selectIds.includes(item.id));
  categoryTitles.value = category.map((item)=>{
    return {id:item.id, title:item.title}
  });
  if(categoryTitles.value.length <= 0){
    allCategories.value.push({'id':0,'title':'سردسته'})
  }
});

function search(event){
  isSearch.value = event.target.value.length > 0;
  allCategories.value = props.categories.filter(item=>item.title.includes(event.target.value))
}

function selectItem(data){
  let _data = {id:data[0], title:data[1]};
  if(props.multi){
    let existCategory = categoryTitles.value.filter((item)=>item.id === _data.id).length > 0;
    if(existCategory){
      categoryTitles.value = categoryTitles.value.filter(item=>item.id !== _data.id)
      store.state.selectIds = [_data.id]
    }else{
      categoryTitles.value.push(_data)
      store.state.selectIds.push(_data.id)
    }
  }else{
    let prevId = categoryTitles.value.length > 0 ? categoryTitles.value[0].id : [];
    categoryTitles.value = prevId === _data.id ? [] : [_data];
    store.state.selectIds = prevId === _data.id ? [] : [_data.id];
  }
  data.push(props.multi);
  emit('selectItem', data)
}
</script>