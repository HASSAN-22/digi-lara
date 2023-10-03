import Axios from "axios";
import store from "@/store";
const axios = Axios.create({
  baseURL: store.state.api,
  withCredentials: true,
  headers:{
    'X-Requested-With':'XMLHttpRequest',
  }
});
axios.interceptors.request.use(function (config) {
  let token = localStorage.getItem('token');
  config.headers.Authorization =  token ? `Bearer ${token}` : '';
  return config;
});
export default axios;