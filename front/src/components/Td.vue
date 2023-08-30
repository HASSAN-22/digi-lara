<template>
    <td class="p-2 fm:flex text-center fd:border border-gray-200 fm:text-right block lg:table-cell xl:table-cell 2xl:table-cell md:table-cell">
        <span class="inline-block w-1/3 lg:hidden xl:hidden 2xl:hidden md:hidden font-bold fm:text-sm">{{ title }}: </span>
        <slot></slot>
    </td>
</template>
<script>
export default {
    name:'Td',
    props:['title']
}
</script>