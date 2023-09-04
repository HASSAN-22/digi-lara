<template>
    <div class="flex flex-col gap-2 w-full relative">
        <div>
            <label class="text-md fm:text-sm">{{ label }} 
                <b v-show="required" class="text-crimson-100 fm:text-sm !font-bold">*</b>
            </label>
        </div>
        <input 
        :class="['border focus:outline-none p-2 p-1 w-full rounded-lg mb-1 text-sm', my_class]"
        :type="type"
        :disabled="disabled"
        :v-model="modelValue" 
        :value="modelValue" 
        @input="$emit('update:modelValue', $event.target.value)" 
        :placeholder="placeholder"  
        :id="id" />
        <span v-show="alert" class="text-red-500 fm:text-xs text-sm">{{alert}}</span>

    </div>
</template>
<script>
export default {
    name:'Input',
    props:{
        modelValue:String|Number,
        my_class:{
            type:String,
            default:''
        },
        alert:{
            type:String,
            default:''
        },
        type:{
            type:String,
            default:'text'
        },
        placeholder:{
            type:String,
            default:' '
        },
        required:{
            type:Boolean,
            default:true
        },
        disabled:{
            type:Boolean,
            default:false
        },
        label:String,
        id:String,
    },
}
</script>
