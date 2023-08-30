<template>
    <div v-show="show === true">
        <div class="backdrop">
        </div>
    </div>
</template>
<script>
    export default {
        name:"BgBlur",
        props:["show"]
    }
</script>
<style scoped>
    .backdrop {
        position: fixed;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        background-color: #0b0f158f;
        backdrop-filter: blur(3px);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 500;
    }
    .loader {
        border: 2px solid #f3f3f3;
        border-radius: 50%;
        border-top: 2px solid red;
        width: 80px;
        height: 80px;
        -webkit-animation: spin 2s linear infinite; /* Safari */
        animation: spin 2s linear infinite;
    }

    @media only screen and (max-width: 768px) {
        .loader {
            width: 60px;
            height: 60px;
        }
    }
    /* Safari */
    @-webkit-keyframes spin {
        0% { -webkit-transform: rotate(0deg); }
        100% { -webkit-transform: rotate(360deg); }
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>