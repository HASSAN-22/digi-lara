import './plugins/axios'
import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import store from './store'
import '@/assets/css/tailwind.css'
import '@/assets/import.js'
import VueSweetalert2 from 'vue-sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';
import Toast from "vue-toastification";
import "vue-toastification/dist/index.css";
import { createHead } from "@vueuse/head"
import { vue3Debounce } from 'vue-debounce'
import { QuillEditor } from '@vueup/vue-quill'
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import VueCountdown from '@chenfengyuan/vue-countdown';
import Vue3PersianDatetimePicker from 'vue3-persian-datetime-picker'

const SweetAlert = {
    confirmButtonColor: '#41b882',
    cancelButtonColor: '#ff7674',
};
const debounceConfig = {
    lock: true,
    listenTo: 'keyup',
    defaultTime: '500ms',
    fireOnEmpty: false,
    trim: false
}

const dateTimeConfig = {
    name: 'CustomDatePicker',
    props: {
        format: 'YYYY-MM-DD',
        displayFormat: 'jYYYY-jMM-jDD',
        editable: false,
        inputClass: 'form-control my-custom-class-name',
        placeholder: '',
        altFormat: 'YYYY-MM-DD',
        color: '#e1e1e1',
        autoSubmit: true,
    }
}

const app = createApp(App)
// app.config.errorHandler = (err, instance, info) => {
//     // report error to tracking services
// }
const head = createHead()

app.component('QuillEditor', QuillEditor)
app.component(VueCountdown.name, VueCountdown);
app.component('DatePicker', Vue3PersianDatetimePicker)

app.use(store)
    .use(router)
    .use(VueSweetalert2, SweetAlert)
    .use(Toast)
    .use(head)
    .use(Vue3PersianDatetimePicker, dateTimeConfig)
    .directive('debounce', vue3Debounce(debounceConfig))
    .mount('#app')