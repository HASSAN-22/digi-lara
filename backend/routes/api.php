<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//################################ Auth routes ######################################
Route::group([],function(){
   Route::controller(\App\Http\Controllers\Auth\LoginController::class)->group(function(){
       route::post('login-register','loginOrRegister');
       route::post('logout','logout')->middleware('auth:sanctum');
   });

    Route::controller(\App\Http\Controllers\Auth\ForgotPasswordController::class)->group(function(){
        route::post('forgot-password/send-code','sendCode');
        route::post('forgot-password','forgotPassword');
    });
});

//################################ Front routes ######################################
Route::group([],function(){

    Route::controller(\App\Http\Controllers\SiteController::class)->group(function(){
        route::get('transaction-verify','transactionVerity')->name('transaction.verify');
        route::get('search-product','searchProduct');
        route::post('send-code','sendCode');
        route::post('resend-code','reSendCode');
        route::get('content','content');
        route::get('index','index');
        route::get('index-header','indexHeader');
        route::get('get-page/{page}','getPage');
        route::get('best-sellings','bestSellings');
        route::get('incredible-offers','incredibleOffer');
        route::get('special-products','specialProduct');
        route::get('product-list','productList');
        route::get('get-product-properties/{slug}','getProductProperties');
        route::post('product-filter/{slug}','productFilter');
        route::get('get-product-detail/{slug}','getProductDetail');
        route::get('get-product-comments/{product}','getProductComments');
        route::get('get-comment/{productcomment}','getComment');
        route::get('get-product-questions/{product}','getProductQuestions');
        route::get('get-cities/{province}','getCities');
        route::get('get-faq-categories','getFaqCategories');
        route::get('get-faqs/{slug}','getFaqs');
        route::get('get-news-list/{slug?}','getNewsList');
        route::get('get-news-detail/{slug}','getNewsDetail');
        route::post('send-contact-us','sendContact');
        route::post('add-newsletter','addNewsletter');
    });

    //################################ Front routes that require auth ######################################
    Route::group(['middleware'=>'auth:sanctum'],function (){
        Route::controller(\App\Http\Controllers\SiteController::class)->group(function(){
            route::get('check-token','checkToken');
            route::get('get-basket-extra-data','getBasketExtraData');
            route::get('basket-count','basketCount');
            route::get('get-basket','getBasket');
            route::get('get-user-data/{product}','getUserData');
            route::get('get-addresses','getAddresses');
            route::post('toggle-wishlist/{product}','toggleWishlist');
            route::post('toggle-amazing-alert/{product}','toggleAmazingAlert');
            route::post('add-compare/{product}','addCompare');
            route::post('remove-compare/{product}','removeCompare');
            route::get('get-compares','getCompares');
            route::post('add-comment/{product}','addComment');
            route::post('like-or-dislike','likeOrDislike');
            route::post('add-product-question/{product}','addProductQuestion');
            route::post('add-product-question-answer/{productquestion}','addProductQuestionAnswer');
            route::post('send-report','sendReport');
            route::post('toggle-exist-notify-product/{product}','toggleProductNotifyExist');
            route::post('add-basket/{product}','addBasket');
            route::post('minus-basket/{product}','minusBasket');
            route::post('remove-basket/{basket}','removeBasket');
            route::post('choose-address/{address}','chooseAddress');
            route::post('check-coupon','checkCoupon');
            route::post('add-order','addOrder');
        });

        Route::controller(\App\Http\Controllers\DashboardController::class)->group(function(){
            Route::get('dashboard','index');
        });

    });
});

//################################ Dashboard routes ######################################
Route::group(['middleware'=>'auth:sanctum'],function(){
    Route::controller(\App\Http\Controllers\DashboardController::class)->group(function(){
        Route::get('dashboard','index');
        Route::get('dashboard/content','content');
    });
});

//################################ Admin routes ######################################
Route::group(['middleware'=>'auth:sanctum'],function (){
    Route::apiResource('category',\App\Http\Controllers\Admin\CategoryController::class);
    Route::get('get-categories',[\App\Http\Controllers\Admin\CategoryController::class,'getCategories']);
    Route::apiResource('property',\App\Http\Controllers\Admin\PropertyController::class);
    Route::apiResource('user',\App\Http\Controllers\Admin\UserController::class);
    Route::apiResource('guarantee',\App\Http\Controllers\Admin\GuaranteeController::class);
    Route::apiResource('slider',\App\Http\Controllers\Admin\SliderController::class);
    Route::apiResource('color',\App\Http\Controllers\Admin\ColorController::class);
    Route::apiResource('size',\App\Http\Controllers\Admin\SizeController::class);
    Route::apiResource('work',\App\Http\Controllers\Admin\WorkController::class);
    Route::apiResource('transport',\App\Http\Controllers\Admin\TransportController::class);
    Route::apiResource('faq',\App\Http\Controllers\Admin\FaqController::class);
    Route::apiResource('news',\App\Http\Controllers\Admin\NewsController::class);
    Route::apiResource('page',\App\Http\Controllers\Admin\PageController::class);
    Route::apiResource('ticketcategory',\App\Http\Controllers\Admin\TicketcategoryController::class);
    Route::apiResource('newsletter',\App\Http\Controllers\Admin\NewsletterController::class)->only('index');
    Route::apiResource('productcomment',\App\Http\Controllers\Admin\ProductCommentController::class)->only(['index','update']);

    Route::apiResource('coupon',\App\Http\Controllers\Admin\CouponController::class);
    Route::controller(\App\Http\Controllers\Admin\CouponController::class)->group(function(){
        Route::get('coupon-get-categories', 'getCategories');
        Route::get('coupon-get-products', 'getProducts');
    });

    Route::controller(\App\Http\Controllers\Admin\SmsController::class)->group(function(){
        Route::get('sms', 'index');
        Route::post('sms', 'store');
    });

    Route::controller(\App\Http\Controllers\Admin\EmailController::class)->group(function(){
        Route::get('email', 'index');
        Route::post('email', 'store');
        Route::post('email/re-sent', 'reSend');
        Route::get('email/get-emails', 'getEmails');
    });


    Route::controller(\App\Http\Controllers\Admin\ContactController::class)->group(function(){
        Route::get('contact', 'index');
        Route::get('contact/{contact}', 'show');
        Route::patch('contact/{contact}', 'update');
    });

    Route::controller(\App\Http\Controllers\Admin\ReportController::class)->group(function(){
        Route::get('product-reports', 'productReport');
        Route::get('comment-reports', 'commentReports');
    });

    Route::controller(\App\Http\Controllers\Admin\PermissionAndRoleController::class)->group(function(){
        Route::get('role','indexRole');
        Route::post('role','storeRole');
        Route::get('role/{role}','showRole');
        Route::patch('role/{role}','updateRole');
        Route::delete('role-permission/{role}','destroy');

        Route::get('permission','indexPermission');
        Route::post('create','createPermission');
        Route::patch('update','updatePermission');
        Route::get('permission/{role}','showPermission');
        Route::get('get-roles','getRoles');
    });

    Route::controller(\App\Http\Controllers\Admin\WidgetController::class)->group(function(){
       Route::get('widget/{widget?}', 'show');
       Route::get('widget/get-product/{category}', 'getProduct');
       Route::post('widget', 'store');
    });

    Route::controller(\App\Http\Controllers\Admin\ShopconfigController::class)->group(function(){
       Route::get('config/legal', 'showLegal');
       Route::post('config/legal', 'insertLegal');
       Route::get('config/store-detail', 'showStoreDetail');
       Route::post('config/insert-store-detail', 'insertStoreDetail');
       Route::get('config/social-media', 'showSocialMedia');
       Route::post('config/social-media', 'insertSocialMedia');
       Route::get('config/footer-box', 'showFooterBox');
       Route::post('config/footer-box', 'insertFooterBox');
       Route::get('config/sms', 'showSms');
       Route::post('config/sms', 'insertSms');
       Route::get('config/other-setting', 'showOtherSetting');
       Route::post('config/other-setting', 'insertOtherSetting');
    });
});




//################################ Related routes ######################################
Route::group(['middleware'=>'auth:sanctum'],function (){

    Route::apiResource('wishlist',\App\Http\Controllers\Related\WishlistController::class)->only(['index','destroy']);
    Route::apiResource('brand',\App\Http\Controllers\Related\BrandController::class);
    Route::apiResource('address',\App\Http\Controllers\Related\AddressController::class);
    Route::apiResource('wallet',\App\Http\Controllers\Related\WalletController::class)->only(['index','store']);
    Route::apiResource('withdrawwallet',\App\Http\Controllers\Related\WithdrawwalletController::class)->only(['index','store','update']);
    Route::get('get-brand/{brand}',[\App\Http\Controllers\Related\BrandController::class,'showBrand']);
    Route::apiResource('ticket',\App\Http\Controllers\Related\TicketController::class);
    Route::post('ticket-send-message/{ticket}',[\App\Http\Controllers\Related\TicketController::class,'sendMessage']);


    Route::controller(\App\Http\Controllers\Related\ProductQuestionController::class)->group(function(){
        Route::get('productquestion', 'index');
        Route::patch('productquestion/{productquestion}', 'update');
        Route::get('productquestionanswer/{productquestion}', 'answer');
        Route::post('productquestionanswer/{productquestion}', 'sendAnswer');
        Route::patch('productquestionanswer/{productquestionanswer}', 'updateAnswer');
    });

    Route::controller(\App\Http\Controllers\Related\DebtorController::class)->group(function(){
        Route::get('debtor', 'index');
        Route::post('debtor/{debtor}', 'pay');
    });

    Route::controller(\App\Http\Controllers\Related\ProductController::class)->group(function(){
        Route::get('product/get-property/{category}','getProperties');
        Route::get('product/get-property-type/{property}','getPropertyTypes');
        Route::delete('product/delete-image/{productimage}','destroyImage');
        Route::get('product/check-amazing-offer','checkAmazingOffer');
        Route::get('product','index');
        Route::post('product','store');
        Route::get('product/{product}','show');
        Route::patch('product/{product}','update');
        Route::delete('product/{product}','destroy');
    });

    Route::controller(\App\Http\Controllers\Related\AmazingProductController::class)->group(function(){
        Route::get('amazing_product/get-product/{category}','getProduct');
        Route::get('amazing_product','index');
        Route::post('amazing_product','store');
        Route::delete('amazing_product/{product}','destroy');
    });

    Route::controller(\App\Http\Controllers\Related\ProfileController::class)->group(function(){
        Route::get('profile/get-profile','getProfile');
        Route::post('profile/insert-name','insertName');
        Route::post('profile/insert-national-id','insertNationalId');
        Route::post('profile/insert-password','insertPassword');
        Route::post('profile/insert-birthday','insertBirthday');
        Route::post('profile/insert-mobile','insertMobile');
        Route::post('profile/insert-email','insertEmail');
        Route::post('profile/insert-work','insertWork');
        Route::post('profile/insert-refund-method','refundMethod');
        Route::post('profile/insert-legal-info','legalInfo');
    });

    Route::controller(\App\Http\Controllers\Related\ReturnedController::class)->group(function(){
        Route::post('returned','store');
        Route::post('returned-status/{returned}','updateStatus');
        Route::get('returned-problem/{returned}','orderProblem');
    });

    Route::controller(\App\Http\Controllers\Related\OrderController::class)->group(function(){
        Route::get('order/detail/{order}','getDetail');
        Route::get('order/current','getCurrent');
        Route::get('order/delivered','getDelivered');
        Route::get('order/returned','getReturned');
        Route::get('order/canceled','getCanceled');

        Route::get('order/customer-detail/{order}','getCustomerDetail');
        Route::get('order/customer-current','getCustomerCurrent');
        Route::get('order/customer-delivered','getCustomerDelivered');
        Route::get('order/customer-returned','getCustomerReturned');
        Route::get('order/customer-canceled','getCustomerCanceled');

        Route::post('order/cancel/{order}','cancelOrder');
        Route::get('order/show-order/{order}','showOrder');
        Route::post('order/update-order/{order}','updateOrder');
        Route::get('order/invoice/{order}','invoice');

        Route::post('order/cancel-detail/{orderdetail}','cancelDetailOrder');
        Route::get('order/show-order-detail/{orderdetail}','showDetailOrder');
        Route::post('order/update-order-detail/{orderdetail}','updateDetailOrder');
    });

    Route::controller(\App\Http\Controllers\Related\BecomeSellerController::class)->group(function(){
        Route::get('become-seller/admin','indexAdmin');
        Route::get('become-seller','index');
        Route::post('become-seller/insert','store');
        Route::get('become-seller/show/{becomeseller}','show');
        Route::patch('become-seller/update/{becomeseller}','update');
    });
});
