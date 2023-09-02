import { createStore } from 'vuex'
import axios from '@/plugins/axios';
import Toast from "@/plugins/toast";
import Swal from "sweetalert2";
export default createStore({
  state: {
    siteName:'فروشگاه لارا کالا',
    user:{
      'name':localStorage.getItem('name'),
      'id':localStorage.getItem('id'),
      'token':localStorage.getItem('token'),
      'access':localStorage.getItem('access'),
      'avatar':localStorage.getItem('avatar'),
      'status':localStorage.getItem('status'),
    },
    colors:[
      {id:1, color:'gray', fa:'طوسی'},
      {id:2, color:'black', fa:'مشکی'},
      {id:3, color:'blue', fa:'ابی'},
      {id:4, color:'green', fa:'سبز'},
      {id:5, color:'white', fa:'سفید'},
      {id:6, color:'silver', fa:'نقره ای'},
      {id:7, color:'purple', fa:'بنفش'},
      {id:8, color:'yellow', fa:'زرد'},
      {id:9, color:'orange', fa:'نارنجی'},
      {id:10, color:'pink', fa:'صورتی'},
      {id:11, color:'brown', fa:'قهوه ای'},
      {id:12, color:'reed', fa:'قرمز'},
    ],
    configSite:{
      'logo':null,
      'footer_logo':null,
      'shop_name':null,
      'shop_name_ir':null,
      'comment_rule':null,
      'shop_email':null,
      'support_phone':null,
      'shop_address':null,
      'enamad':null,
      'samandehi':null,
      'shop_bio':null,
      'mojavez':null,
      'copy_right':null,
      'telegram':null,
      'whatsapp':null,
      'instagram':null,
      'twitter':null,
      'facebook':null,
      'footer_box':{
        'express_name':null,
        'express_image':null,
        'support_every_day':null,
        'support_every_day_image':null,
        'guarantee':null,
        'guarantee_image':null,
        'original':null,
        'original_image':null,
      }
    },
    pickColors:['red','blue'],
    sizes:['S','M','L','XL','2XL','3XL','4XL'],
    api:process.env.VUE_APP_BACKEND+'/api/',
    url:process.env.VUE_APP_BACKEND,
    errors:[],
    searchModal:false,
    searchText:'',
    transportAmount:0,
    addressComponentKey:0,
    selectIds:[],
    baskets:[],
    basketAmount:0,
    basketDiscount:0,
    basketOriginalAmount:0,
    discounts:0,
    validationErrorModal:false,
    basketLoading:false,
    basketLoadingAction:false,
    showBasket:false,
    addBasketLoading:false,
    minusBasketLoading:false,
    removeBasketLoading:false,
    basketId:0,
    basketCount:0,
    current:1,
    previous:"",
    next:null,
    imageSize:'1 مگابایت',
    oneMgByte: 1024,
    total:1,
    largeSize:400,
    mediumSize:287,
    smallSize:150,
    permissions:[],
    userPermissions:[],
    faPermissions:[
      {view_any_categories:'لیست دسته ها',view_categories:'مشاهده دسته',create_categories:'ثبت دسته', update_categories:'ویرایش دسته',delete_categories:'حذف دسته'},
      {view_any_properties:'لیست ویژگی ها',view_properties:'مشاهده ویژگی',create_properties:'ثبت ویژگی', update_properties:'ویرایش ویژگی',delete_properties:'حذف ویژگی'},
      {view_any_users:'لیست کاربران',view_users:'مشاهده کاربر',create_users:'ثبت کاربر', update_users:'ویرایش کاربر',delete_users:'حذف کاربر'},
    ]
  },
  mutations: {
    paginate(state, {current_page, to, per_page, last_page}){
      state.current = current_page;
      state.next = to;
      state.previous = per_page;
      state.total = last_page;
    },

    handleError(state,payload){
      const response = payload.response;
      console.log(response)
      if(response === undefined || response.status === 500){
        let msg = response.data.msg;
        if(msg){
          Toast.error(msg)
        }else{
          Toast.error('یک خطای غیر منتظره رخ داده است')
        }
      }else if(response.status === 422 || response.status === 413){
        state.errors = Object.values(response.data.errors)
        state.validationErrorModal = true;
      }else if(response.status === 403 || response.status === 401){
        window.location.href = `/error/${response.status}/دسترسی شما غیر مجاز است برای بررسی بیشتر با ما تماس بگیرید`;
      }
    },

    async setStorage(state, payload){
      const setToken = payload[0]
      const userData = payload[1]
      localStorage.setItem('name',userData.name);
      localStorage.setItem('id',userData.id);
      localStorage.setItem('avatar',userData.avatar);
      localStorage.setItem('access',userData.access);
      localStorage.setItem('status',userData.status);
      state.user.name = userData.name;
      state.user.id = userData.id;
      state.user.avatar = userData.avatar;
      state.user.access = userData.access;
      state.user.status = userData.status;

      if(setToken){
        localStorage.setItem('token',userData.token)
        state.user.token = userData.token;
      }

      await this.dispatch('setCookie', 'expire_token');

    },
    clearStorage(state){
      document.cookie = 'expire_token=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
      state.user.name = null;
      state.user.id = null;
      state.user.avatar = null;
      state.user.access = null;
      state.user.status = null;
      state.user.token = null;
      localStorage.clear()
      state.basketCount = 0;
      state.baskets = [];
      state.basketAmount = 0;
      state.basketDiscount = 0;
      state.basketOriginalAmount = 0;
      state.discounts = 0;
      state.transportAmount = 0;
    }
  },
  actions: {
    async setCookie({state},name) {
      let expires = "";
      let date = new Date();
      let timestamp = date.setTime(date.getTime() + (24*60*60*100));
      expires = "; expires=" + date.toUTCString();
      document.cookie = name + "=" + timestamp  + expires + "; path=/";
    },

    pickColor({state}, payload){
      let color = state.colors.filter((item)=>item.id === payload[0]);
      let type = payload[1];
      if(type === 'single'){
        state.pickColors = color;
      }else{
        let hasColor = state.pickColors.filter((item)=>item.id === color.id).length > 0;
        if(hasColor){
          state.pickColors = state.pickColors.filter((item)=>item.id !== color.id).length > 0;
        }else{
          state.pickColors.push(color);
        }
      }
    },

    async deleteRecord({commit,state},payload){
      await Swal.fire({
        title: "آیا از حذف این ایتم مطمئنید؟",
        text: payload[1],
        icon: "warning",
        showDenyButton: true,
        showCancelButton: false,
        confirmButtonText: 'بله',
        denyButtonText: `خیر`,

      }).then((willDelete) => {
        if (willDelete.isConfirmed) {
          state.isLoading  = true;
          axios.delete(state.api+payload[0]).then(resp=>{
            if(resp.data.status === 'success'){
              Swal.fire({
                title:'ایتم مورد نظر با موفقیت حذف شد',
                icon: "success",
                showDenyButton: true,
                showConfirmButton: false,
                denyButtonText: `بستن`,
              });
            }else {
              Swal.fire({
                title:'حذف با خطا مواجه شد.',
                icon: "error",
                showDenyButton: true,
                showConfirmButton: false,
                denyButtonText: `بستن`,
              });
            }
          }).catch(err=>{
            state.isLoading = false;
            commit('handleError',err)
          })
        }
      });
    },

    async sleep({state},payload=700) {
      await new Promise(resolve => setTimeout(resolve, payload));
    },

    async checkToken({state,commit}){
      await axios.get(`${state.api}check-token`).then(async resp=>{
        const data = resp.data.data;
        if(data.token_valid){
          if(data.status === 'no'){
            commit('clearStorage')
            window.location.href = '/error/403/دسترسی شما غیر فعال میباشد برای بررسی بیشتر با ما تماس بگیرید'
          }else{
            state.userPermissions = data.permissions;
            await commit('setStorage',[false,data])
          }
        }else{
          commit('clearStorage')
        }
      }).catch(err=>{
        commit('clearStorage')
      })
    },
    async getBasket({state},{loading, withCoupon=false, withCount=false,withProduct=false}){
      state.basketLoading = loading;
      await axios.get(`${state.api}get-basket?with_coupon=${withCoupon}&with_count=${withCount}&with_product=${withProduct}`).then(resp=>{
        let data = resp.data.data;
        state.baskets = data.baskets;
        state.basketAmount = data.amount;
        state.basketDiscount = data.discount;
        state.basketOriginalAmount = data.originalAmount;
      }).catch(err=>{})
      state.basketLoading = false;
    },
    async removeBasket({state,dispatch},{basketId, showBasket,withCount=false,withProduct=false}){
      state.basketId = basketId;
      state.removeBasketLoading = state.basketLoadingAction = true;
      await axios.post(`${state.api}remove-basket/${basketId}`).then(async resp=>{
        Toast.success('با موفقیت حذف شد')
        await dispatch('getBasket',{loading:false,withCoupon:false,withCount:withCount,withProduct:withProduct})
        state.basketCount = resp.data.data;
        if(showBasket){
          state.showBasket = state.basketCount > 0;
        }

      }).catch(err=>{
        Toast.error('یک خطای غیر منتظره رخ داده است')
      })
      state.removeBasketLoading = false;
      await dispatch('sleep',1000)
      state.basketLoadingAction = false
    },
    async addBasket({state,dispatch},{productId,color,size,basketId,withProduct=false}){
      state.basketId = basketId;
      state.basketLoadingAction = true;
      state.addBasketLoading = true

      await axios.post(`${state.api}add-basket/${productId}`,{size:size, color:color}).then(async resp=>{
        Toast.success('با موفقیت به سبد افزوده شد')
        await dispatch('getBasket',{loading:false,withCount:true,withProduct:withProduct})
        state.basketCount = resp.data.data;
      }).catch(err=>{
        const response = err.response;
        if(response === undefined || response.data.data !== 'product_count_not_enough'){
          Toast.error('یک خطای غیر منتظره رخ داده است')
        }else if(response.data.data === 'product_count_not_enough'){
          Toast.error('موجودی کالا کافی نیست');
        }
      })
      state.addBasketLoading = false;
      await dispatch('sleep')
      state.basketLoadingAction = false
    },
    async minusBasket({state,dispatch},{productId,color,size,basketId,withProduct=false}){
      state.basketId = basketId;
      state.basketLoadingAction = true;
      state.minusBasketLoading = true

      await axios.post(`${state.api}minus-basket/${productId}`,{size:size, color:color}).then(async resp=>{
        Toast.success('با موفقیت از سبد کم شد')
        await dispatch('getBasket',{loading:false,withCount:true,withProduct:withProduct})
        state.basketCount = resp.data.data;
      }).catch(err=>{
        const response = err.response;
        if(response === undefined || response.data.data !== 'product_minimum'){
          Toast.error('یک خطای غیر منتظره رخ داده است')
        }else if(response.data.data === 'minimum'){
          Toast.error('به حداقل تعداد رسیدید');
        }
      })
      state.minusBasketLoading = false;
      await dispatch('sleep')
      state.basketLoadingAction = false
    }
  },
  getters: {
    numberFormat:(state)=>(number)=>{
      return new Intl.NumberFormat().format(number);
    },

    getDiscount:(state,getters)=>(price, discount, numberFormat=true)=>{
      price = price - ((discount / 100) * price);
      return numberFormat ? getters.numberFormat(price) : price;
    },

    getColors:(state)=>(type, index)=>{
      return type === 'single' ? state.pickColors[0][index] : state.pickColors;
    },
    getToken(state){
      return state.user.token;
    },
    getErrors(state){
      return state.errors;
    },
    getCookie: (state) => (name) => {
      let nameEQ = name + "=";
      let ca = document.cookie.split(';');
      for(let i=0;i < ca.length;i++) {
        let c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) {
          return c.substring(nameEQ.length,c.length)
        }
      }
      return null;
    },
    getSelectItem: (state) => (data) => {
      if(data[2]){
        let existCategory = state.selectIds.filter((item)=>item === data[0]).length > 0;
        if(existCategory){
          state.selectIds = state.selectIds.filter(item=>item !== data[0])
        }else{
          state.selectIds.push(data[0])
        }
      }else{
        let prevId = state.selectIds.length > 0 ? state.selectIds[0] : [];
        state.selectIds = prevId === data[0] ? [] : [data[0]];
      }
      return state.selectIds;
    },
    userCan: (state) => (permission,filter='') => {
      if(state.user.access === 'admin'){
        if(filter === 'include'){
          return state.userPermissions.filter(item=>item.includes(permission)).length > 0
        }else{
          return state.userPermissions.filter(item=>item === permission).length > 0
        }
      }else{
        return true;
      }
    },
    getBasketAmount: (state,getters) => (baskets,format=true) => {
      let amount = 0;
      state.discounts = 0;
      baskets.map(item=>{
        let product = item.product;
        if(item.product_size !== null){
          amount += item.product_size.price
        }else if(item.product_color !== null){
          amount += item.product_color.price
        }
        if(product.amazing_offer_status === 'yes'){
          amount += getters.getDiscount(product.price, product.amazing_offer_percent, false);
        }else if(product.amazing_price !== null && product.amazing_price > 0){
          amount += product.amazing_price;
        }else{
          amount += product.price;
        }
        amount *= item.count;
      })

      return format ? getters.numberFormat(amount) : amount;
    },
    getBasket: (state) => () => {
      return state.baskets;
    }
  }
})
