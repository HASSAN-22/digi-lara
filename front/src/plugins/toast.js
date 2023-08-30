
import { useToast } from "vue-toastification";
const toast = useToast();
const config = {
    timeout: 5000,
    closeOnClick: true,
    pauseOnFocusLoss: false,
    pauseOnHover: false,
    draggable: false,
    draggablePercent: 0.6,
    showCloseButtonOnHover: false,
    hideProgressBar: false,
    closeButton: "button",
    icon: true,
    rtl: true
};
const Toast = {
    success(message = 'با موفقیت ثبت شد', timeout=5000, position = "bottom-right"){
        config.position = position;
        config.timeout = timeout;
        toast.success(message, config);
    },
    info(message = 'با موفقیت ثبت شد', timeout=5000, position = "bottom-right"){
        config.position = position;
        config.timeout = timeout;
        toast.info(message, config);
    },
    warning(message = 'با موفقیت ثبت شد', timeout=5000, position = "bottom-right"){
        config.position = position;
        config.timeout = timeout;
        toast.warning(message, config);
    },
    error(message = 'یک خطای غیر منتظره رخ داده است', timeout=5000, position = "bottom-right"){
        config.position = position;
        config.timeout = timeout;
        toast.error(message, config);
    }
}
export default Toast;
