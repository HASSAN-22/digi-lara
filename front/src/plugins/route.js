import { useRoute } from 'vue-router'

const Route = {
    param(paramName){
        return (useRoute()).params[paramName];
    },

    hasParam(paramName){
        return (useRoute()).params[paramName] !== undefined;
    },

}
export default Route;

