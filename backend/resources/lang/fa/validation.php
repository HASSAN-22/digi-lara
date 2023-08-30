<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    "accepted"         => ":attribute باید پذیرفته شده باشد.",
    "active_url"       => "آدرس :attribute معتبر نیست",
    "after"            => ":attribute باید تاریخی بعد از :date باشد.",
    "alpha"            => ":attribute باید شامل حروف الفبا باشد.",
    "alpha_dash"       => ":attribute باید شامل حروف الفبا و عدد و خظ تیره(-) باشد.",
    "alpha_num"        => ":attribute باید شامل حروف الفبا و عدد باشد.",
    "array"            => ":attribute باید شامل آرایه باشد.",
    "before"           => ":attribute باید تاریخی قبل از :date باشد.",
    "between"          => [
        "numeric" => ":attribute باید بین :min و :max باشد.",
        "file"    => ":attribute باید بین :min و :max کیلوبایت باشد.",
        "string"  => ":attribute باید بین :min و :max کاراکتر باشد.",
        "array"   => ":attribute باید بین :min و :max آیتم باشد.",
    ],
    "boolean"          => "فیلد :attribute فقط میتواند صحیح و یا غلط باشد",
    "confirmed"        => ":attribute با تاییدیه مطابقت ندارد.",
    "date"             => ":attribute یک تاریخ معتبر نیست.",
    "date_format"      => ":attribute با الگوی :format مطاقبت ندارد.",
    "different"        => ":attribute و :other باید متفاوت باشند.",
    "digits"           => ":attribute باید :digits رقم باشد.",
    "digits_between"   => ":attribute باید بین :min و :max رقم باشد.",
    'dimensions'       => ':attribute طول و عرض تصویر مناسب نیست.',
    "email"            => "فرمت :attribute معتبر نیست.",
    "exists"           => ":attribute انتخاب شده، معتبر نیست.",
    "filled"           => "فیلد :attribute الزامی است",
    "image"            => ":attribute باید تصویر باشد.",
    "in"               => ":attribute انتخاب شده، معتبر نیست.",
    "integer"          => ":attribute باید نوع داده ای عددی (integer) باشد.",
    "ip"               => ":attribute باید IP آدرس معتبر باشد.",
    "max"              => [
        "numeric" => ":attribute نباید بزرگتر از :max باشد.",
        "file"    => ":attribute نباید بزرگتر از :max کیلوبایت باشد.",
        "string"  => ":attribute نباید بیشتر از :max کاراکتر باشد.",
        "array"   => ":attribute نباید بیشتر از :max آیتم باشد.",
    ],
    "mimes"            => ":attribute باید یکی از فرمت های :values باشد.",
    "min"              => [
        "numeric" => ":attribute نباید کوچکتر از :min باشد.",
        "file"    => ":attribute نباید کوچکتر از :min کیلوبایت باشد.",
        "string"  => ":attribute نباید کمتر از :min کاراکتر باشد.",
        "array"   => ":attribute نباید کمتر از :min آیتم باشد.",
    ],
    "not_in"           => ":attribute انتخاب شده، معتبر نیست.",
    "numeric"          => ":attribute باید شامل عدد باشد.",
    "regex"            => ":attribute یک فرمت معتبر نیست",
    "required"         => "فیلد :attribute الزامی است",
    "required_if"      => "فیلد :attribute هنگامی که :other برابر با :value است، الزامیست.",
    "required_with"    => ":attribute الزامی است زمانی که :values موجود است.",
    "required_with_all" => ":attribute الزامی است زمانی که :values موجود است.",
    "required_without" => ":attribute الزامی است زمانی که :values موجود نیست.",
    "required_without_all" => ":attribute الزامی است زمانی که :values موجود نیست.",
    "same"             => ":attribute و :other باید مانند هم باشند.",
    "size"             => [
        "numeric" => ":attribute باید برابر با :size باشد.",
        "file"    => ":attribute باید برابر با :size کیلوبایت باشد.",
        "string"  => ":attribute باید برابر با :size کاراکتر باشد.",
        "array"   => ":attribute باسد شامل :size آیتم باشد.",
    ],
    "string"           => "فیلد :attribute باید رشته باشد.",
    "timezone"         => "فیلد :attribute باید یک منطقه صحیح باشد.",
    "unique"           => ":attribute قبلا انتخاب شده است.",
    "url"              => "فرمت آدرس :attribute اشتباه است.",

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */
    'attributes' => [
        "email" => "ایمیل",
        "first_name" => "نام",
        "last_name" => "نام خانوادگی",
        "family" => "نام خانوادگی",
        "password" => "رمز عبور",
        "password_confirmation" => "تاییدیه ی رمز عبور",
        "city" => "شهرستان",
        "country" => "کشور",
        "address" => "نشانی",
        "phone" => "تلفن",
        "mobile" => "موبایل",
        "age" => "سن",
        "sex" => "جنسیت",
        "gender" => "جنسیت",
        "day" => "روز",
        "month" => "ماه",
        "year" => "سال",
        "hour" => "ساعت",
        "minute" => "دقیقه",
        "second" => "ثانیه",
        "title" => "عنوان",
        "text" => "متن",
        "content" => "محتوا",
        "description" => "توضیحات",
        "excerpt" => "گلچین کردن",
        "date" => "تاریخ",
        "time" => "زمان",
        "available" => "موجود",
        "size" => "اندازه",
        "parent_id"=>"دسته",
        "category_type"=>"نوع دسته",
        "status"=>"وضعیت",
        "property"=>"ویژگی",
        "properties.*"=>"ویژگی",
        "properties"=>"ویژگی",
        "access"=>"دسترسی",
        "role"=>"تفش",
        'subject'=>'موضوع',
        'comment_subject'=>'موضوع دیدگاه',
        "registration_form"=>"برگه ثبت برند",
        "reason_rejection"=>"دلیل رد",
        "brand_type"=>"نوع برند",
        "en_name"=>"نام انگلیسی",
        "ir_name"=>"نام پارسی",

        'seller_id'=>'فروشنده',
        'brand_id'=>'برند',
        'category_id'=>'دسته',
        'guarantee_id'=>'گارانتی',
        'sku'=>'شناسه sku',
        'price'=>'قیمت',
        'amazing_price'=>'قیمت ویژه',
        'packing_length'=>'ابعاد بسته بندی (طول)',
        'packing_width'=>'ابعاد بسته بندی (عرض)',
        'packing_height'=>'ابعاد بسته بندی (ارتفاع)',
        'packing_weight'=>'ابعاد بسته بندی (وزن)',
        'physical_length'=>'مشخصات فیزکی کالا (طول)',
        'physical_width'=>'مشخصات فیزکی کالا (عرض)',
        'physical_height'=>'مشخصات فیزکی کالا (ارتفاع)',
        'physical_weight'=>'مشخصات فیزکی کالا (وزن)',
        'publish'=>'وضعیت',
        'count'=>'تعداد در انبار',
        'image'=>'تصویر',
        'is_original'=>'کالا اصل میباشد',
        'strengths'=>'نکات مثبت',
        'weak_points'=>'نکات ضعف',
        'more_description'=>'سایر توضیحات',
        'images'=>'تصاویر',
        'images.*'=>'تصاویر',
        'specification_ames'=>'مشخصات کلی (عنوان)',
        'specification_ames.*'=>'مشخصات کلی (توضیح)',
        'property_ids'=>'ویژگی ها',
        'property_ids.*'=>'ویژگی ها',
        'property_types'=>'ویژگی ها(نوع)',
        'property_types.*.*'=>'ویژگی ها(نوع)',
        'property_pricing_ids'=>'ویژگی های دارای قیمت',
        'property_pricing_ids.*'=>'ویژگی های دارای قیمت',
        'property_pricing_types'=>'ویژگی های دارای قیمت (نوع)',
        'property_pricing_types.*.*'=>'ویژگی های دارای قیمت (نوع)',
        'property_prices'=>'ویژگی ها(قیمت)',
        'property_prices.*.*'=>'ویژگی ها(قیمت)',
        'link'=>'لینک',
        'permissions'=>'دسترسی',
        'permissions.*'=>'دسترسی',
        'name'=>'نام',
        'logo'=>'لوگو',
        'user_id'=>'برای کاربر',
        'guarantee'=>'گارانتی',
        'role_name'=>'نقش',
        'property__types'=>'مقادیر ویژگی',
        'commission'=>'کمیسیون',
        'amazing_offer_percent'=>'درصد تخفیف شگفت انگیز',
        'amazing_offer_expire'=>'به روز رسانی انقضاء',
        'product_ids'=>'کالا ها',
        'product_ids.*'=>'کالا ها',
        'guarantee_month'=>'مدت گارانتی',
        'question'=>'پرسش',
        'answer'=>'پاسخ',
        'rating'=>'امتیاز',
        'suggest'=>'پیشنهاد کالا به دیگران',
        'comment'=>'نظر',
        'report_description'=>'شرح مشکل',
        'province_id'=>'استان',
        'city_id'=>'شهرستان',
        'plaque'=>'پلاک',
        'unit'=>'واحد',
        'postal_code'=>'کد پستی',
        'receiver_name'=>'نام گیرنده',
        'receiver_last_name'=>'نام خانوادگی گیرنده',
        'work_name'=>'شغل',
        'shaba'=>'شماره شبا',
        'refund_method'=>'روش بازگرداندن پول من',
        'work_id'=>'شغل',
        'birth_day'=>'تاریخ تولد',
        'prev_password'=>'رمز عبور قبلی',
        'national_id'=>'کد ملی',
        'organization_name'=>'نام سازمان',
        'registration_id'=>'شناسه ثبت',
        'amount'=>'مبلغ',
        'company_name'=>'نام شرکت',
        'company_type'=>'نوع شرکت',
        'registration_number'=>'شماره ثبت',
        'economic_number'=>'کد اقتصادی',
        'authorized_representative'=>'صاحبان حق امضا',
        'identity_card_number'=>'کد ملی',
        'shop_name'=>'نام انگلیسی فروشگاه',
        'seller_type'=>'چه نوع فروشنده ای هستید',
        'identity_card_front'=>'تصویر کارت ملی',
        'identity_card_back'=>'تصویر پشت کارت ملی',
        'short_description'=>'توضیحات کوتاه',
        'username'=>'نام و نام خانوادگی',
        'message'=>'متن پیام',
        'footer_logo'=>'لوگو فوتر',
        'ticket_category_id'=>'موضوع',
        'priority'=>'اولویت',
        'shop_name_ir'=>'نام پارسی فروشگاه',
        'shop_address'=>'ادرس فروشگاه',
        'shop_url'=>'لینک  بک اند فروشگاه',
        'shop_front_url'=>'لینک فرات فروشگاه',
        'comment_rule'=>'قوانین ثبت دیدگاه',
        'problem_description'=>'توضیح مشکلات'
    ],
];
