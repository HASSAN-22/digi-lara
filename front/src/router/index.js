import { createRouter, createWebHistory } from 'vue-router';
import FrontLayout from '@/components/FrontLayout';
import DashboardLayout from '@/components/DashboardLayout';
import Invoice from '@/views/Panel/Invoice.vue';
import store from '@/store';
const routes = [


  {
    path: '/',
    component: FrontLayout,
    children:[
      {
        path: '/',
        name: 'Index',
        component: () => import(/* webpackChunkName: "index" */ '@/views/Front/Index.vue')
      },
      {
        path: '/products-list/:slug?',
        name: 'ProductsList',
        component: () => import(/* webpackChunkName: "products-list" */ '@/views/Front/ProductsList.vue')
      },
      {
        path: '/product-detail/:slug',
        name: 'ProductDetail',
        component: () => import(/* webpackChunkName: "product-detail" */ '@/views/Front/ProductDetail.vue')
      },
      {
        path: '/basket',
        name: 'Basket',
        component: () => import(/* webpackChunkName: "basket" */ '@/views/Front/Basket.vue')
      },
      {
        path: '/shipping',
        name: 'Shipping',
        component: () => import(/* webpackChunkName: "shipping" */ '@/views/Front/Shipping.vue')
      },
      {
        path: '/error/:code/:msg',
        name: 'HandleError',
        component: () => import(/* webpackChunkName: "error" */ '@/views/Front/HandleError.vue')
      },
      {
        path: '/payment-alert/:msg',
        name: 'paymentAlert',
        component: () => import(/* webpackChunkName: "payment-alert" */ '@/views/Front/paymentAlert.vue')
      },

      {
        path: '/login',
        name: 'Login',
        meta: { requiresLogin: true },
        component: () => import(/* webpackChunkName: "login" */ '@/views/Auth/Login.vue')
      },
      {
        path: '/forgot/password',
        name: 'ForgotPassword',
        meta: { requiresLogin: true },
        component: () => import(/* webpackChunkName: "forgot/password" */ '@/views/Auth/ForgotPassword.vue')
      },
      {
        path: '/contact-us',
        name: 'ContactUs',
        component: () => import(/* webpackChunkName: "contact-us" */ '@/views/Front/ContactUs.vue')
      },
      {
        path: '/faq-categories',
        name: 'FaqCategory',
        component: () => import(/* webpackChunkName: "faq-categories" */ '@/views/Front/FaqCategory.vue')
      },
      {
        path: '/faq-detail/:slug',
        name: 'FaqDetail',
        component: () => import(/* webpackChunkName: "faq-detail" */ '@/views/Front/FaqDetail.vue')
      },
      {
        path: '/news-list/:slug?',
        name: 'NewsList',
        component: () => import(/* webpackChunkName: "news-list" */ '@/views/Front/NewsList.vue')
      },
      {
        path: '/news-detail/:slug',
        name: 'NewsDetail',
        component: () => import(/* webpackChunkName: "news-detail" */ '@/views/Front/NewsDetail.vue')
      },
      {
        path: '/compares',
        name: 'Compare',
        component: () => import(/* webpackChunkName: "compare" */ '@/views/Front/Compare.vue')
      },
      {
        path: '/best-selling',
        name: 'BestSelling',
        component: () => import(/* webpackChunkName: "best-selling" */ '@/views/Front/BestSelling.vue')
      },
      {
        path: '/incredible-offers',
        name: 'IncredibleOffer',
        component: () => import(/* webpackChunkName: "incredible-offers" */ '@/views/Front/IncredibleOffer.vue')
      },
      {
        path: '/special-products',
        name: 'SpecialProduct',
        component: () => import(/* webpackChunkName: "special-products" */ '@/views/Front/SpecialProduct.vue')
      },
      {
        path: '/page-detail/:id',
        name: 'PageDetail',
        component: () => import(/* webpackChunkName: "page-detail" */ '@/views/Front/PageDetail.vue')
      },
    ]
  },

  {
    path: '/dashboard',
    meta: {requiresAuth: true},
    component: DashboardLayout,
    children: [
      {
        path: '/dashboard',
        name: 'Dashboard',
        component: () => import(/* webpackChunkName: "dashboard" */ '@/views/Panel/Dashboard.vue')
      },
      {
        path: '/categories',
        name: 'Category',
        component: () => import(/* webpackChunkName: "categories" */ '@/views/Panel/Category.vue')
      },
      {
        path: '/properties',
        name: 'Property',
        component: () => import(/* webpackChunkName: "properties" */ '@/views/Panel/Property.vue')
      },
      {
        path: '/users',
        name: 'User',
        component: () => import(/* webpackChunkName: "users" */ '@/views/Panel/User.vue')
      },
      {
        path: '/roles',
        name: 'Role',
        component: () => import(/* webpackChunkName: "roles" */ '@/views/Panel/Role.vue')
      },
      {
        path: '/permissions',
        name: 'Permission',
        component: () => import(/* webpackChunkName: "permissions" */ '@/views/Panel/Permission.vue')
      },
      {
        path: '/brands',
        name: 'Brand',
        component: () => import(/* webpackChunkName: "brands" */ '@/views/Panel/Brand.vue')
      },
      {
        path: '/guarantees',
        name: 'Guarantee',
        component: () => import(/* webpackChunkName: "guarantee" */ '@/views/Panel/Guarantee.vue')
      },
      {
        path: '/products',
        name: 'Product',
        component: () => import(/* webpackChunkName: "products" */ '@/views/Panel/Product.vue')
      },
      {
        path: '/amazing-products',
        name: 'AmazingProduct',
        component: () => import(/* webpackChunkName: "amazing-products" */ '@/views/Panel/AmazingProduct.vue')
      },
      {
        path: '/widgets',
        name: 'Widget',
        component: () => import(/* webpackChunkName: "widgets" */ '@/views/Panel/Widget.vue')
      },
      {
        path: '/sliders',
        name: 'Slider',
        component: () => import(/* webpackChunkName: "sliders" */ '@/views/Panel/Slider.vue')
      },
      {
        path: '/sizes',
        name: 'Size',
        component: () => import(/* webpackChunkName: "sizes" */ '@/views/Panel/Size.vue')
      },
      {
        path: '/colors',
        name: 'Color',
        component: () => import(/* webpackChunkName: "colors" */ '@/views/Panel/Color.vue')
      },
      {
        path: '/coupon',
        name: 'Coupon',
        component: () => import(/* webpackChunkName: "coupon" */ '@/views/Panel/Coupon.vue')
      },
      {
        path: '/profile',
        name: 'Profile',
        component: () => import(/* webpackChunkName: "profile" */ '@/views/Panel/Profile.vue')
      },
      {
        path: '/address',
        name: 'Address',
        component: () => import(/* webpackChunkName: "address" */ '@/views/Panel/Address.vue')
      },
      {
        path: '/works',
        name: 'Work',
        component: () => import(/* webpackChunkName: "works" */ '@/views/Panel/Work.vue')
      },
      {
        path: '/wallet',
        name: 'Wallet',
        component: () => import(/* webpackChunkName: "wallet" */ '@/views/Panel/Wallet.vue')
      },
      {
        path: '/withdraws',
        name: 'Withdraw',
        component: () => import(/* webpackChunkName: "withdraws" */ '@/views/Panel/Withdraw.vue')
      },
      {
        path: '/orders',
        name: 'Order',
        component: () => import(/* webpackChunkName: "orders" */ '@/views/Panel/Order.vue')
      },
      {
        path: '/order-details/:order_id',
        name: 'OrderDetail',
        component: () => import(/* webpackChunkName: "order-details" */ '@/views/Panel/OrderDetail.vue')
      },
      {
        path: '/transports',
        name: 'Transport',
        component: () => import(/* webpackChunkName: "transports" */ '@/views/Panel/Transport.vue')
      },
      {
        path: '/shop-configs',
        name: 'ShopConfig',
        component: () => import(/* webpackChunkName: "shop-configs" */ '@/views/Panel/ShopConfig.vue')
      },
      {
        path: '/become-seller',
        name: 'BecomeSeller',
        component: () => import(/* webpackChunkName: "become-seller" */ '@/views/Panel/BecomeSeller.vue')
      },
      {
        path: '/faqs',
        name: 'Faq',
        component: () => import(/* webpackChunkName: "faqs" */ '@/views/Panel/Faq.vue')
      },
      {
        path: '/news',
        name: 'News',
        component: () => import(/* webpackChunkName: "news" */ '@/views/Panel/News.vue')
      },
      {
        path: '/pages',
        name: 'Page',
        component: () => import(/* webpackChunkName: "pages" */ '@/views/Panel/Page.vue')
      },
      {
        path: '/ticket-categories',
        name: 'TicketCategory',
        component: () => import(/* webpackChunkName: "ticket-categories" */ '@/views/Panel/TicketCategory.vue')
      },
      {
        path: '/tickets',
        name: 'Ticket',
        component: () => import(/* webpackChunkName: "tickets" */ '@/views/Panel/Ticket.vue')
      },
      {
        path: '/contacts',
        name: 'Contact',
        component: () => import(/* webpackChunkName: "contacts" */ '@/views/Panel/Contact.vue')
      },
      {
        path: '/newsletters',
        name: 'Newsletter',
        component: () => import(/* webpackChunkName: "newsletters" */ '@/views/Panel/Newsletter.vue')
      },
      {
        path: '/product-reports',
        name: 'ProductReport',
        component: () => import(/* webpackChunkName: "product-reports" */ '@/views/Panel/ProductReport.vue')
      },
      {
        path: '/comment-reports',
        name: 'CommentReport',
        component: () => import(/* webpackChunkName: "comment-reports" */ '@/views/Panel/CommentReport.vue')
      },
      {
        path: '/product-comments',
        name: 'ProductComment',
        component: () => import(/* webpackChunkName: "product-comments" */ '@/views/Panel/ProductComment.vue')
      },
      {
        path: '/product-questions',
        name: 'ProductQuestion',
        component: () => import(/* webpackChunkName: "product-questions" */ '@/views/Panel/ProductQuestion.vue')
      },
      {
        path: '/product-question-answers/:question_id',
        name: 'ProductQuestionAnswer',
        component: () => import(/* webpackChunkName: "product-question-answers" */ '@/views/Panel/ProductQuestionAnswer.vue')
      },
      {
        path: '/wishlists',
        name: 'Wishlist',
        component: () => import(/* webpackChunkName: "wishlists" */ '@/views/Panel/Wishlist.vue')
      },
      {
        path: '/emails',
        name: 'Email',
        component: () => import(/* webpackChunkName: "emails" */ '@/views/Panel/Email.vue')
      },
      {
        path: '/sms',
        name: 'SMS',
        component: () => import(/* webpackChunkName: "sms" */ '@/views/Panel/SMS.vue')
      },
      {
        path: '/debtor',
        name: 'Debtor',
        component: () => import(/* webpackChunkName: "debtor" */ '@/views/Panel/Debtor.vue')
      },
    ]

  },

  {
    path: '/invoice/:order',
    meta: {requiresAuth: true},
    component: Invoice
  },

]

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes,
  scrollBehavior () {
    return { top: 0 };
  }
})

async function checkExpireCookie(){
  let expireAt = parseInt(store.getters.getCookie('expire_token'));
  let now = (new Date()).getTime();
  expireAt = isNaN(expireAt) ? now : expireAt;
  if(expireAt > now ){
    await store.dispatch('setCookie', 'expire_token')
  }else{
    store.commit('clearStorage')
  }
  // expireAt > now ? await store.dispatch('setCookie', 'expire_token') : store.commit('clearStorage')
}

router.beforeEach(async (to,from,next)=>{
  await checkExpireCookie();

  if(!to.matched.length && to.fullPath !== '/'){
    next({name:'HandleError',params:{code:404,msg:'صفحه مورد نظر یافت نشد'}});
  }

  if(to.meta.requiresAuth === true){
    await store.dispatch('checkToken')
  }
  if(to.meta.requiresLogin && store.getters.getToken !== null){
    next({name:'Index'});
  }else if(to.meta.requiresAuth && !store.getters.getToken){
    next({name:'Index'});
  }else{
    next();
  }

});

export default router
