<?php

namespace App\Services;

use App\Enums\BecomeSellerTypeEnum;
use App\Enums\BrandTypeEnum;
use App\Enums\CategoryTypeEnum;
use App\Enums\CompanyTypeEnum;
use App\Enums\CouponTypeEnum;
use App\Enums\DebtorStatusEnum;
use App\Enums\PaidByEnum;
use App\Enums\SentStatusEnum;
use App\Enums\GenderEnum;
use App\Enums\IsLockedEnum;
use App\Enums\PriorityEnum;
use App\Enums\PublishEnum;
use App\Enums\ShippingEnum;
use App\Enums\StatusEnum;
use App\Enums\UserAccessEnum;
use App\Enums\WeightTypeEnum;
use App\Enums\WithdrawStatusEnum;
use App\Enums\YesOrNoEnum;

class TypeService
{
    private string $data = '';

    public function __construct(string|null $data){
        $this->data = trim($data);
    }

    /**
     * Convert Persian `category type` to English or vice versa
     * @param string $lang
     * @return $this
     */
    public function categoryType(string $lang = 'en'){
        if($lang == 'fa'){
            $this->data = match ($this->data){
                CategoryTypeEnum::CATEGORY->value => 'دسته بندی',
                CategoryTypeEnum::FAQ->value => 'سوالات متداول',
                CategoryTypeEnum::NEWS->value => 'اخبار',
                default =>  $this->data,
            };
        }else{
            $this->data = match ($this->data){
                'دسته بندی'=>CategoryTypeEnum::CATEGORY->value,
                'سوالات متداول'=>CategoryTypeEnum::FAQ->value,
                'اخبار'=>CategoryTypeEnum::NEWS->value,
                default =>  $this->data,
            };
        }
        return $this;
    }

    /**
     * Convert Persian `ticket priority` to English or vice versa
     * @param string $lang
     * @return $this
     */
    public function priority(string $lang = 'en'){
        if($lang == 'fa'){
            $this->data = match ($this->data){
                PriorityEnum::HIGH->value => 'بالا',
                PriorityEnum::MEDIUM->value => 'متوسط',
                PriorityEnum::LOW->value => 'کم',
                default =>  $this->data,
            };
        }else{
            $this->data = match ($this->data){
                'بالا'=>PriorityEnum::HIGH->value,
                'متوسط'=>PriorityEnum::MEDIUM->value,
                'کم'=>PriorityEnum::LOW->value,
                default =>  $this->data,
            };
        }
        return $this;
    }

    /**
     * Convert Persian `ticket is locked` to English or vice versa
     * @param string $lang
     * @return $this
     */
    public function isLocked(string $lang = 'en'){
        if($lang == 'fa'){
            $this->data = match ($this->data){
                IsLockedEnum::LOCKED->value => 'بسته',
                IsLockedEnum::OPEN->value => 'باز',
                default =>  $this->data,
            };
        }else{
            $this->data = match ($this->data){
                'بسته'=>IsLockedEnum::LOCKED->value,
                'باز'=>IsLockedEnum::OPEN->value,
                default =>  $this->data,
            };
        }
        return $this;
    }


    /**
     * Convert Persian `company type` to English or vice versa
     * @param string $lang
     * @return $this
     */
    public function companyType(string $lang = 'en'){
        if($lang == 'fa'){
            $this->data = match ($this->data){
                CompanyTypeEnum::PUBLIC->value => 'سهامی عام',
                CompanyTypeEnum::JOINT_STOCK->value => 'سهامی خاص',
                CompanyTypeEnum::LTD->value => 'مسولیت محدود',
                CompanyTypeEnum::COOP->value => 'تعاونی',
                CompanyTypeEnum::SOLIDARITY->value => 'تضامنی',
                CompanyTypeEnum::INSTITUTION->value => 'موسسه',
                default =>  $this->data,
            };
        }else{
            $this->data = match ($this->data){
                'سهامی عام'=>CompanyTypeEnum::PUBLIC->value,
                'سهامی خاص'=>CompanyTypeEnum::JOINT_STOCK->value,
                'مسولیت محدود'=>CompanyTypeEnum::LTD->value,
                'تعاونی'=>CompanyTypeEnum::COOP->value,
                'تضامنی'=>CompanyTypeEnum::SOLIDARITY->value,
                'موسسه'=>CompanyTypeEnum::INSTITUTION->value,
                default =>  $this->data,
            };
        }
        return $this;
    }

    /**
     * Convert Persian `gender` to English or vice versa
     * @param string $lang
     * @return $this
     */
    public function gender(string $lang = 'en'){
        if($lang == 'fa'){
            $this->data = match ($this->data){
                GenderEnum::MAN->value => 'اقا',
                GenderEnum::WOMAN->value => 'خانم',
                default =>  $this->data,
            };
        }else{
            $this->data = match ($this->data){
                'اقا'=>GenderEnum::MAN->value,
                'خانم'=>GenderEnum::WOMAN->value,
                default =>  $this->data,
            };
        }
        return $this;
    }

    /**
     * Convert Persian `seller type` to English or vice versa
     * @param string $lang
     * @return $this
     */
    public function sellerType(string $lang = 'en'){
        if($lang == 'fa'){
            $this->data = match ($this->data){
                BecomeSellerTypeEnum::LEGAL->value => 'حقوقی',
                BecomeSellerTypeEnum::REAL->value => 'حقیقی',
                default =>  $this->data,
            };
        }else{
            $this->data = match ($this->data){
                'حقوقی'=>BecomeSellerTypeEnum::LEGAL->value,
                'حقیقی'=>BecomeSellerTypeEnum::REAL->value,
                default =>  $this->data,
            };
        }
        return $this;
    }

    /**
     * Convert Persian `shipping status` to English or vice versa
     * @param string $lang
     * @return $this
     */
    public function shippingStatus(string $lang = 'en'){
        if($lang == 'fa'){
            $this->data = match ($this->data){
                ShippingEnum::PAYMENT_PENDING->value => 'در انتظار پرداخت',
                ShippingEnum::REVIEW_QUEUE->value => 'در صف بررسی',
                ShippingEnum::ORDER_CONFIRMATION->value => 'تایید سفارش',
                ShippingEnum::ORDER_PREPARATION->value => 'آماده‌سازی سفارش',
                ShippingEnum::LEAVING_CENTER->value => 'خروج از مرکز پردازش',
                ShippingEnum::PUADC->value => 'دریافت در مرکز توزیع',
                ShippingEnum::DELIVERY_SENDER->value => 'تحویل به مامور ارسال',
                ShippingEnum::DELIVERED_CUSTOMER->value => 'تحویل داده شد',
                ShippingEnum::CANCELLED->value => 'لغو شد',
                ShippingEnum::AUTO_CANCELLED->value => 'لغو سیستمی',
                ShippingEnum::RETURNED->value => 'مرجوع شد',
                ShippingEnum::PENDING_DELIVERY_TO_STORE->value => 'درانتظار ارسال به فروشگاه',
                ShippingEnum::PENDING_PAY_BACK->value => 'در انتظار بازگشت مبلغ سفارش',
                ShippingEnum::SUCCESS_PAY_BACK->value => 'مبلغ سفارش با موفقیت بازگردانده شد',
                default =>  $this->data,
            };
        }else{
            $this->data = match ($this->data){
                'در انتظار پرداخت'=>ShippingEnum::PAYMENT_PENDING->value,
                'در صف بررسی'=>ShippingEnum::REVIEW_QUEUE->value,
                'تایید سفارش'=>ShippingEnum::ORDER_CONFIRMATION->value,
                'آماده‌سازی سفارش'=>ShippingEnum::ORDER_PREPARATION->value,
                'خروج از مرکز پردازش'=>ShippingEnum::LEAVING_CENTER->value,
                'دریافت در مرکز توزیع'=>ShippingEnum::PUADC->value,
                'تحویل به مامور ارسال'=>ShippingEnum::DELIVERY_SENDER->value,
                'تحویل داده شد'=>ShippingEnum::DELIVERED_CUSTOMER->value,
                'لغو شد'=>ShippingEnum::CANCELLED->value,
                'لغو سیستمی'=>ShippingEnum::AUTO_CANCELLED->value,
                'مرجوع شد'=>ShippingEnum::RETURNED->value,
                'درانتظار ارسال به فروشگاه'=>ShippingEnum::PENDING_DELIVERY_TO_STORE->value,
                'در انتظار بازگشت مبلغ سفارش'=>ShippingEnum::PENDING_PAY_BACK->value,
                'مبلغ سفارش با موفقیت بازگردانده شد'=>ShippingEnum::SUCCESS_PAY_BACK->value,
                default =>  $this->data,
            };
        }
        return $this;
    }

    /**
     * Convert Persian `status` to English or vice versa
     * @param string $lang
     * @return $this
     */
    public function status(string $lang = 'en'){
        if($lang == 'fa'){
            $this->data = match ($this->data){
                StatusEnum::ACTIVE->value => 'فعال',
                StatusEnum::DEACTIVATED->value => 'غیر فعال',
                StatusEnum::PENDING->value => 'در حال بررسی',
                default =>  $this->data,
            };
        }else{
            $this->data = match ($this->data){
                'فعال'=>StatusEnum::ACTIVE->value,
                'غیر فعال'=>StatusEnum::DEACTIVATED->value,
                'در حال بررسی'=>StatusEnum::PENDING->value,
                default =>  $this->data,
            };
        }
        return $this;
    }

    /**
     * Convert Persian `email status` to English or vice versa
     * @param string $lang
     * @return $this
     */
    public function emailStatus(string $lang = 'en'){
        if($lang == 'fa'){
            $this->data = match ($this->data){
                SentStatusEnum::SENT->value => 'ارسال شد',
                SentStatusEnum::NOT_SENT->value => 'ارسال نشد',
                SentStatusEnum::PENDING->value => 'در حال بررسی',
                default =>  $this->data,
            };
        }else{
            $this->data = match ($this->data){
                'ارسال شد'=>SentStatusEnum::SENT->value,
                'ارسال نشد'=>SentStatusEnum::NOT_SENT->value,
                'در حال بررسی'=>SentStatusEnum::PENDING->value,
                default =>  $this->data,
            };
        }
        return $this;
    }

    /**
     * Convert Persian `withdraw status` to English or vice versa
     * @param string $lang
     * @return $this
     */
    public function withdrawStatus(string $lang = 'en'){
        if($lang == 'fa'){
            $this->data = match ($this->data){
                WithdrawStatusEnum::SUCCESS->value => 'پرداخت شد',
                WithdrawStatusEnum::CANCEL->value => 'لغو شد',
                WithdrawStatusEnum::PENDING->value => 'در حال بررسی',
                default =>  $this->data,
            };
        }else{
            $this->data = match ($this->data){
                'پرداخت شد'=>WithdrawStatusEnum::SUCCESS->value,
                'لغو شد'=>WithdrawStatusEnum::CANCEL->value,
                'در حال بررسی'=>WithdrawStatusEnum::PENDING->value,
                default =>  $this->data,
            };
        }
        return $this;
    }


    /**
     * Convert Persian `transaction status` to English or vice versa
     * @param string $lang
     * @return $this
     */
    public function transactionStatus(string $lang = 'en'){
        if($lang == 'fa'){
            $this->data = match ($this->data){
                StatusEnum::ACTIVE->value => 'پرداخت شد',
                StatusEnum::DEACTIVATED->value => 'پرداخت نشد',
                StatusEnum::PENDING->value => 'در انتظار پرداخت',
                default =>  $this->data,
            };
        }else{
            $this->data = match ($this->data){
                'پرداخت شد'=>StatusEnum::ACTIVE->value,
                'پرداخت نشد'=>StatusEnum::DEACTIVATED->value,
                'در انتظار پرداخت'=>StatusEnum::PENDING->value,
                default =>  $this->data,
            };
        }
        return $this;
    }

    /**
     * Convert Persian `transaction status` to English or vice versa
     * @param string $lang
     * @return $this
     */
    public function transactionPaidBy(string $lang = 'en'){
        if($lang == 'fa'){
            $this->data = match ($this->data){
                PaidByEnum::USER->value => 'کاربر',
                PaidByEnum::ADMIN->value => 'ادمین',
                default =>  $this->data,
            };
        }else{
            $this->data = match ($this->data){
                'کاربر'=>PaidByEnum::USER->value,
                'ادمین'=>PaidByEnum::ADMIN->value,
                default =>  $this->data,
            };
        }
        return $this;
    }

    /**
     * Convert Persian `status confirm` to English or vice versa
     * @param string $lang
     * @return $this
     */
    public function statusConfirm(string $lang = 'en'){
        if($lang == 'fa'){
            $this->data = match ($this->data){
                StatusEnum::ACTIVE->value => 'تایید',
                StatusEnum::DEACTIVATED->value => 'رد',
                StatusEnum::PENDING->value => 'در حال بررسی',
                default =>  $this->data,
            };
        }else{
            $this->data = match ($this->data){
                'تایید'=>StatusEnum::ACTIVE->value,
                'رد'=>StatusEnum::DEACTIVATED->value,
                'در حال بررسی'=>StatusEnum::PENDING->value,
                default =>  $this->data,
            };
        }
        return $this;
    }

    /**
     * Convert Persian `access` to English or vice versa
     * @param string $lang
     * @return $this
     */
    public function access(string $lang = 'en'){
        if($lang == 'fa'){
            $this->data = match ($this->data){
                UserAccessEnum::ADMIN->value => 'ادمین',
                UserAccessEnum::USER->value => 'مشتری',
                UserAccessEnum::SELLER->value => 'فروشنده',
                default =>  $this->data,
            };
        }else{
            $this->data = match ($this->data){
                'ادمین'=>UserAccessEnum::ADMIN->value,
                'مشتری'=>UserAccessEnum::USER->value,
                'فروشنده'=>UserAccessEnum::SELLER->value,
                default =>  $this->data,
            };
        }
        return $this;
    }

    /**
     * Convert Persian `brand type` to English or vice versa
     * @param string $lang
     * @return $this
     */
    public function brandType(string $lang = 'en'){
        if($lang == 'fa'){
            $this->data = match ($this->data){
                BrandTypeEnum::IR->value => 'ایرانی',
                BrandTypeEnum::EN->value => 'خارجی',
                default =>  $this->data,
            };
        }else{
            $this->data = match ($this->data){
                'ایرانی'=>BrandTypeEnum::IR->value,
                'خارجی'=>BrandTypeEnum::EN->value,
                default =>  $this->data,
            };
        }
        return $this;
    }

    /**
     * Convert Persian `transport weight type` to English or vice versa
     * @param string $lang
     * @return $this
     */
    public function transportWeightType(string $lang = 'en'){
        if($lang == 'fa'){
            $this->data = match ($this->data){
                WeightTypeEnum::STYLE->value => 'سبک',
                WeightTypeEnum::HEAVY->value => 'سنگین',
                WeightTypeEnum::SUPER_HEAVY->value => 'فوق سنگین',
                default =>  $this->data,
            };
        }else{
            $this->data = match ($this->data){
                'سبک'=>WeightTypeEnum::STYLE->value,
                'سنگین'=>WeightTypeEnum::HEAVY->value,
                'فوق سنگین'=>WeightTypeEnum::SUPER_HEAVY->value,
                default =>  $this->data,
            };
        }
        return $this;
    }


    /**
     * Convert Persian `transport is free` to English or vice versa
     * @param string $lang
     * @return $this
     */
    public function isFree(string $lang = 'en'){
        if($lang == 'fa'){
            $this->data = match ($this->data){
                YesOrNoEnum::YES->value => 'رایگان',
                YesOrNoEnum::NO->value => 'دارای هزینه',
                default =>  '---',
            };
        }else{
            $this->data = match ($this->data){
                'رایگان'=>YesOrNoEnum::YES->value,
                'دارای هزینه'=>YesOrNoEnum::NO->value,
                default =>  '---',
            };
        }
        return $this;
    }

    /**
     * Convert Persian `transport is freight` to English or vice versa
     * @param string $lang
     * @return $this
     */
    public function isFreight(string $lang = 'en'){
        if($lang == 'fa'){
            $this->data = match ($this->data){
                YesOrNoEnum::YES->value => 'پس کریه',
                YesOrNoEnum::NO->value => 'دارای هزینه',
                default =>  '---',
            };
        }else{
            $this->data = match ($this->data){
                'پس کرایه'=>YesOrNoEnum::YES->value,
                'دارای هزینه'=>YesOrNoEnum::NO->value,
                default =>  '---',
            };
        }
        return $this;
    }

    /**
     * Convert Persian `publish` to English or vice versa
     * @param string $lang
     * @return $this
     */
    public function publish(string $lang = 'en'){
        if($lang == 'fa'){
            $this->data = match ($this->data){
                PublishEnum::PUBLISHED->value => 'منتشر شده',
                PublishEnum::DRAFT->value => 'پیش نویس',
                default =>  $this->data,
            };
        }else{
            $this->data = match ($this->data){
                'منتشر شده'=>PublishEnum::PUBLISHED->value,
                'پیش نویس'=>PublishEnum::DRAFT->value,
                default =>  $this->data,
            };
        }
        return $this;
    }

    /**
     * Convert Persian `status` to English or vice versa
     * @param string $lang
     * @return $this
     */
    public function amazingOfferSatus(string $lang = 'en'){
        if($lang == 'fa'){
            $this->data = match ($this->data){
                StatusEnum::ACTIVE->value => 'بله',
                StatusEnum::PENDING->value => 'در حال بررسی',
                StatusEnum::DEACTIVATED->value => 'خیر',
                default => $this->data,
            };
        }else{
            $this->data = match ($this->data){
                'بله'=>StatusEnum::ACTIVE->value,
                'خیر'=>StatusEnum::DEACTIVATED->value,
                'در حال بررسی'=>StatusEnum::PENDING->value,
                default => $this->data,
            };
        }
        return $this;
    }

    /**
     * Convert Persian `debtor status` to English or vice versa
     * @param string $lang
     * @return $this
     */
    public function debtorSatus(string $lang = 'en'){
        if($lang == 'fa'){
            $this->data = match ($this->data){
                DebtorStatusEnum::SETTLED->value => 'تسویه شد',
                DebtorStatusEnum::PENDING->value => 'در انتظار تسویه',
                default =>  'خیر',
            };
        }else{
            $this->data = match ($this->data){
                'تسویه شد'=>DebtorStatusEnum::SETTLED->value,
                'در انتظار تسویه'=>DebtorStatusEnum::PENDING->value,
                default => $this->data,
            };
        }
        return $this;
    }

    /**
     * Convert Persian `couponType` to English or vice versa
     * @param string $lang
     * @return $this
     */
    public function couponType(string $lang = 'en'){
        if($lang == 'fa'){
            $this->data = match ($this->data){
                CouponTypeEnum::PERCENT->value => 'درصد',
                CouponTypeEnum::FIXED->value => 'ثابت',
                default =>  $this->data,
            };
        }else{
            $this->data = match ($this->data){
                'درصد'=>CouponTypeEnum::PERCENT->value,
                'ثابت'=>CouponTypeEnum::FIXED->value,
                default => $this->data,
            };
        }
        return $this;
    }

    /**
     * Remove the `comma` from numbers
     * @return $this
     */
    public function fixPriceFormat(){
        $this->data = str_replace(',','',$this->data,);
        return $this;
    }

    /**
     * return the data;
     * @return string
     */
    public function get(){
        return $this->data;
    }
}
